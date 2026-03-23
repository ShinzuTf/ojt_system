<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DocxTemplateGenerator;
use ZipArchive;

class DiagnoseTemplates extends Command
{
    protected $signature = 'templates:diagnose';
    protected $description = 'Check what placeholders exist in your templates';

    public function handle(DocxTemplateGenerator $generator)
    {
        $this->info('🔍 Scanning templates in public/templates...\n');

        $templateDir = public_path('templates');
        
        if (!is_dir($templateDir)) {
            $this->error('Templates directory not found!');
            return;
        }

        $files = glob($templateDir . '/*.docx');
        
        if (empty($files)) {
            $this->error('No DOCX templates found!');
            return;
        }

        foreach ($files as $file) {
            $filename = basename($file);
            $this->info("📄 Template: {$filename}");
            
            try {
                $zip = new ZipArchive();
                if ($zip->open($file) === true) {
                    $xmlContent = $zip->getFromName('word/document.xml');
                    $zip->close();
                    
                    if ($xmlContent) {
                        // Extract all placeholders
                        preg_match_all('/\$\{([A-Z_]+)\}/', $xmlContent, $matches);
                        
                        if (!empty($matches[1])) {
                            $placeholders = array_unique($matches[1]);
                            $this->info("   ✅ Found " . count($placeholders) . " placeholders:");
                            foreach ($placeholders as $placeholder) {
                                $this->line("      - \${" . $placeholder . "}");
                            }
                        } else {
                            $this->warn("   ⚠️  NO PLACEHOLDERS FOUND!");
                            $this->info("   This template still has hardcoded data.");
                            $this->info("   Update it to use \${FULL_NAME}, \${COMPANY_NAME}, etc.");
                        }
                    }
                } else {
                    $this->error("   ❌ Could not open DOCX file");
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
            }
            
            $this->line('');
        }

        // Show available placeholders
        $this->info('📋 Available placeholders for your templates:');
        $this->info("\nFrom User model:");
        $this->line("  \${FULL_NAME}      - Last Name, First Name M. (Suffix)");
        $this->line("  \${SHORT_NAME}     - First Name Last Name");
        $this->line("  \${FIRST_NAME}");
        $this->line("  \${MIDDLE_NAME}");
        $this->line("  \${LAST_NAME}");
        $this->line("  \${SUFFIX}");
        $this->line("  \${EMAIL}");
        
        $this->info("\nFrom OJT Info:");
        $this->line("  \${STUDENT_NUMBER}");
        $this->line("  \${COURSE}");
        $this->line("  \${YEAR_LEVEL}");
        $this->line("  \${COMPANY_NAME}");
        $this->line("  \${COMPANY_EMAIL}");
        $this->line("  \${COMPANY_ADDRESS}");
        $this->line("  \${SUPERVISOR_NAME}");
        $this->line("  \${SUPERVISOR_CONTACT}");
        $this->line("  \${OJT_START}         - Format: January 19, 2026");
        $this->line("  \${OJT_START_DATE}    - Format: 01/19/2026");
        $this->line("  \${OJT_END}           - Format: June 4, 2026");
        $this->line("  \${OJT_END_DATE}      - Format: 06/04/2026");
        $this->line("  \${REQUIRED_HOURS}");
        $this->line("  \${RENDERED_HOURS}");
        $this->line("  \${OJT_STATUS}");
        $this->line("  \${PROGRESS_PERCENT}  - 0-100");
        
        $this->info("\nDate/Time:");
        $this->line("  \${CURRENT_DATE}      - Format: February 20, 2026");
        $this->line("  \${CURRENT_YEAR}      - 2026");
        $this->line("  \${CURRENT_MONTH}     - February");
        $this->line("  \${CURRENT_DAY}       - 20");
    }
}
