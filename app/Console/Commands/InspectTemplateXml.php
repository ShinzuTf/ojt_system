<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use DOMDocument;

class InspectTemplateXml extends Command
{
    protected $signature = 'templates:inspect {file? : Template filename}';
    protected $description = 'Inspect the actual XML structure of a template';

    public function handle()
    {
        $templateDir = public_path('templates');
        $file = $this->argument('file');

        if ($file) {
            $templatePath = $templateDir . '/' . $file;
            if (!file_exists($templatePath)) {
                $this->error("Template not found: {$file}");
                return;
            }
            $this->inspectTemplate($templatePath);
        } else {
            // List all templates
            $files = glob($templateDir . '/*.docx');
            $this->info("Available templates:");
            foreach ($files as $f) {
                $this->line("  " . basename($f));
            }
            $this->newLine();
            $this->info("Usage: php artisan templates:inspect <filename.docx>");
        }
    }

    private function inspectTemplate($templatePath)
    {
        $filename = basename($templatePath);
        $this->info("📄 Inspecting: {$filename}\n");

        try {
            $zip = new ZipArchive();
            if ($zip->open($templatePath) !== true) {
                $this->error("Could not open DOCX");
                return;
            }

            $xmlContent = $zip->getFromName('word/document.xml');
            $zip->close();

            if (!$xmlContent) {
                $this->error("No document.xml found");
                return;
            }

            // Search for a placeholder - look for patterns
            $this->info("Looking for placeholder patterns...\n");

            // Find all text content
            preg_match_all('/<w:t[^>]*>([^<]*\$\{[^<]*)<\/w:t>/', $xmlContent, $matches);
            
            if (!empty($matches[1])) {
                $this->info("✅ CONTINUOUS PLACEHOLDERS FOUND (easy to replace):");
                foreach (array_unique($matches[1]) as $match) {
                    $this->line("   " . htmlspecialchars($match));
                }
                $this->newLine();
            } else {
                $this->warn("❌ NO CONTINUOUS PLACEHOLDERS - they are likely SPLIT across XML tags!");
                $this->newLine();
            }

            // Look for split placeholders
            $this->info("🔍 Looking for SPLIT placeholders...\n");
            
            // Extract all text nodes
            preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/', $xmlContent, $textMatches);
            
            $textNodes = array_unique($textMatches[1]);
            
            // Find segments containing $ or { or }
            $suspiciousNodes = [];
            foreach ($textNodes as $node) {
                if (preg_match('/[\$\{\}]/', $node)) {
                    $suspiciousNodes[] = $node;
                }
            }

            if (!empty($suspiciousNodes)) {
                $this->warn("Found possible split placeholder segments:");
                foreach ($suspiciousNodes as $node) {
                    $this->line("   " . htmlspecialchars($node));
                }
                $this->newLine();
                $this->info("These segments are likely split across multiple <w:t> tags.");
                $this->info("This is why simple str_replace doesn't work!\n");
            }

            // Show first 100 text nodes
            $this->info("📝 Sample of all text nodes in document:");
            $this->line("─".str_repeat("─", 78));
            
            $count = 0;
            foreach ($textNodes as $node) {
                if ($count >= 50) {
                    $this->line("... and " . (count($textNodes) - 50) . " more nodes");
                    break;
                }
                $display = strlen($node) > 60 ? substr($node, 0, 57) . '...' : $node;
                $this->line("[" . ($count+1) . "] " . htmlspecialchars($display));
                $count++;
            }
            $this->line("─".str_repeat("─", 78));
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
