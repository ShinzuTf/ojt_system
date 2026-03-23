<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

class DebugPlaceholderReplacement extends Command
{
    protected $signature = 'templates:debug-replace {template : Template filename} {--show-xml : Show full XML content}';
    protected $description = 'Debug placeholder replacement process';

    public function handle()
    {
        $templateName = $this->argument('template');
        $showXml = $this->option('show-xml');
        
        $templatePath = public_path('templates/' . $templateName);
        
        if (!file_exists($templatePath)) {
            $this->error("Template not found: {$templateName}");
            return;
        }

        $this->info("📋 Debugging: {$templateName}\n");

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

            // Show original placeholders
            $this->info("Original placeholders in template:");
            preg_match_all('/\$\{[A-Z_]+\}/', $xmlContent, $matches);
            foreach (array_unique($matches[0]) as $placeholder) {
                $this->line("  ✓ {$placeholder}");
            }

            // Now simulate replacement
            $this->newLine();
            $this->info("Testing replacement with dummy data:");
            
            $testData = [
                'FULL_NAME' => 'TEST STUDENT NAME',
                'SUPERVISOR_NAME' => 'TEST SUPERVISOR',
                'COMPANY_NAME' => 'TEST COMPANY',
                'CURRENT_DATE' => 'February 20, 2026',
                'OJT_START' => 'January 19, 2026',
                'OJT_END' => 'June 4, 2026',
                'COURSE' => 'BSIT',
                'YEAR_LEVEL' => '4',
                'COMPANY_ADDRESS' => 'Test Address',
                'REQUIRED_HOURS' => '720',
                'SUPERVISOR_TITLE' => 'Manager',
            ];

            // Test the regex replacement
            $modified = preg_replace_callback(
                '/\$\{[^}]*(?:<[^>]*>[^}]*)*\}/i',
                function ($matches) use ($testData) {
                    $placeholder = $matches[0];
                    $key = preg_replace('/<[^>]*>/', '', $placeholder);
                    
                    if (preg_match('/\$\{([A-Z_]+)\}/', $key, $keyMatches)) {
                        $placeholderKey = $keyMatches[1];
                        
                        if (isset($testData[$placeholderKey])) {
                            $this->line("  ✓ {$placeholderKey} → {$testData[$placeholderKey]}");
                            return $testData[$placeholderKey];
                        } else {
                            $this->line("  ✗ {$placeholderKey} → NOT IN DATA");
                        }
                    }
                    return $placeholder;
                },
                $xmlContent
            );

            // Check if replacements happened
            $this->newLine();
            $this->info("Checking if replacements worked:");
            
            $replacementTests = [
                'FULL_NAME' => 'TEST STUDENT NAME',
                'SUPERVISOR_NAME' => 'TEST SUPERVISOR',
                'COMPANY_NAME' => 'TEST COMPANY',
            ];

            foreach ($replacementTests as $placeholder => $expectedValue) {
                if (strpos($modified, $expectedValue) !== false) {
                    $this->line("  ✅ {$placeholder} successfully replaced");
                } else {
                    $this->line("  ❌ {$placeholder} NOT replaced - check placeholder format");
                }
            }

            // Show context around a placeholder
            if ($showXml) {
                $this->newLine();
                $this->info("First 50 text nodes:");
                preg_match_all('/<w:t[^>]*>([^<]*)<\/w:t>/', $xmlContent, $textMatches);
                foreach (array_slice($textMatches[1], 0, 50) as $i => $text) {
                    if (strlen($text) > 0) {
                        $this->line("[" . ($i+1) . "] " . htmlspecialchars(substr($text, 0, 60)));
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
