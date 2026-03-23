<?php

namespace App\Services;

use ZipArchive;
use Exception;
use Illuminate\Support\Facades\Storage;

class DocxTemplateGenerator
{
    /**
     * Generate a DOCX document from template by replacing placeholders (optimized)
     * 
     * @param string $templatePath Path to template in public/templates
     * @param array $data Data to replace placeholders with
     * @param string $outputName Output filename
     * @return string Path to generated file
     */
    public function generate(string $templatePath, array $data, string $outputName): string
    {
        $this->ensureOutputDir();
        $outputPath = storage_path('app/documents/' . $outputName);
        
        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
        
        try {
            // Copy template directly to temp location
            copy($templatePath, $tempFile);

            // Open the template as a ZIP archive
            $zip = new ZipArchive();
            if ($zip->open($tempFile) !== true) {
                throw new Exception("Could not open DOCX as ZIP archive");
            }

            // Extract document.xml
            $xmlContent = $zip->getFromName('word/document.xml');
            if ($xmlContent === false) {
                $zip->close();
                throw new Exception("Could not find document.xml in template");
            }

            // Replace placeholders (now optimized with simple str_replace)
            $xmlContent = $this->replacePlaceholders($xmlContent, $data);

            // Update the XML in the archive
            $zip->addFromString('word/document.xml', $xmlContent);
            $zip->close();

            // Move to final output location
            rename($tempFile, $outputPath);

            return $outputPath;
        } catch (Exception $e) {
            // Clean up temp file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            throw $e;
        }
    }

    /**
     * Replace all placeholders in XML content (handles split placeholders)
     * 
     * @param string $xmlContent XML content from document.xml
     * @param array $data Replacement data
     * @return string Modified XML
     */
    private function replacePlaceholders(string $xmlContent, array $data): string
    {
        // First, normalize the XML by temporarily removing tag boundaries within placeholders
        // This allows us to find split placeholders like ${COMPANY_<closing_tag>NAME}
        
        // Pattern: find all placeholder-like content even if split by XML tags
        // ${...} where ... can contain <tags>
        $xmlContent = preg_replace_callback(
            '/\$\{[^}]*(?:<[^>]*>[^}]*)*\}/i',
            function ($matches) use ($data) {
                $placeholder = $matches[0];
                // Remove XML tags from the placeholder to get the actual key
                $key = preg_replace('/<[^>]*>/', '', $placeholder);
                
                // Extract just the placeholder name (e.g., "FULL_NAME" from "${FULL_NAME}")
                if (preg_match('/\$\{([A-Z_]+)\}/', $key, $keyMatches)) {
                    $placeholderKey = $keyMatches[1];
                    
                    if (isset($data[$placeholderKey])) {
                        // Replace with the actual value, sanitized for XML
                        return $this->sanitizeForXml($data[$placeholderKey]);
                    }
                }
                
                // If no match, leave the placeholder as-is
                return $placeholder;
            },
            $xmlContent
        );

        // Also do direct text replacements for continuous placeholders
        foreach ($data as $placeholder => $value) {
            $search = '${' . $placeholder . '}';
            $sanitizedValue = $this->sanitizeForXml($value);
            $xmlContent = str_replace($search, $sanitizedValue, $xmlContent);
        }

        return $xmlContent;
    }

    /**
     * Sanitize value for XML
     * 
     * @param mixed $value
     * @return string
     */
    private function sanitizeForXml($value): string
    {
        $value = (string) $value;
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }

    /**
     * Get available templates (with cached placeholders)
     * 
     * @return array List of template files
     */
    public function getAvailableTemplates(): array
    {
        $templateDir = public_path('templates');
        $templates = [];

        if (is_dir($templateDir)) {
            $files = glob($templateDir . '/*.docx');
            foreach ($files as $file) {
                $templates[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'displayName' => $this->getTemplateDisplayName(basename($file)),
                    'placeholders' => $this->getPlaceholders($file),
                ];
            }
        }

        return $templates;
    }

    /**
     * Extract placeholders from a template (optimized with caching)
     * 
     * @param string $templatePath
     * @return array List of placeholders
     */
    public function getPlaceholders(string $templatePath): array
    {
        // Cache key based on file path and modification time
        $cacheKey = 'docx_placeholders_' . md5($templatePath . filemtime($templatePath));
        
        // Try to get from cache
        $placeholders = apcu_fetch($cacheKey);
        if ($placeholders !== false) {
            return $placeholders;
        }

        $placeholders = [];
        try {
            $zip = new ZipArchive();
            if ($zip->open($templatePath) !== true) {
                return [];
            }

            $xmlContent = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($xmlContent === false) {
                return [];
            }

            // Find all ${...} placeholders - fast regex
            if (preg_match_all('/\$\{([A-Z_]+)\}/', $xmlContent, $matches)) {
                $placeholders = array_unique($matches[1] ?? []);
            }
            
            // Cache for 1 hour
            apcu_store($cacheKey, $placeholders, 3600);
        } catch (Exception $e) {
            return [];
        }

        return $placeholders;
    }

    /**
     * Get template display name from filename
     * 
     * @param string $filename
     * @return string
     */
    private function getTemplateDisplayName(string $filename): string
    {
        $name = str_replace('.docx', '', $filename);
        $name = str_replace('_', ' ', $name);
        return ucwords($name);
    }

    /**
     * Ensure output directory exists
     */
    private function ensureOutputDir(): void
    {
        $dir = storage_path('app/documents');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
