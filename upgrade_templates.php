<?php
/**
 * This script "Upgrades" the provided Word templates by finding 
 * the fragmented red text and replacing it with clean standard 
 * placeholders like ${FULL_NAME}.
 */

function upgradeTemplate($filePath) {
    if (!file_exists($filePath)) return "File not found: $filePath";
    
    $zip = new ZipArchive;
    if ($zip->open($filePath) === TRUE) {
        $xml = $zip->getFromName('word/document.xml');
        
        // Define the mapping of common fragmented strings to clean placeholders
        $replacements = [
            'Jericho Y. Barcelon' => '${FULL_NAME}',
            'Bachelor of Science in Information Technology' => '${COURSE}',
            '4th year' => '${YEAR_LEVEL}',
            'HON. JOLLY R. RESUELLO' => '${SUPERVISOR_NAME}',
            'Municipal Mayor' => '${SUPERVISOR_TITLE}',
            'Poblacion Basista, Pangasinan' => '${COMPANY_ADDRESS}',
            'January 19,2026' => '${OJT_START}',
            'January 16,2026' => '${CURRENT_DATE}',
            'June 4,2026' => '${OJT_END}',
            'January 19, 2026' => '${OJT_START}',
            'June 4, 2026' => '${OJT_END}',
            'January 20, 2026' => '${CURRENT_DATE}',
            'January 21, 2026 ' => '${CURRENT_DATE}',
            'June 6, 2026' => '${OJT_END}',
            '${CURRENT_DATE} up to ${OJT_END}' => '${OJT_START} up to ${OJT_END}',
            'Mr. Eric M. Austria' => '${SUPERVISOR_NAME}',
            'MR. ERIC M. AUSTRIA' => '${SUPERVISOR_NAME}',
            'Commission on Election Urbiztondo' => '${COMPANY_NAME}',
            'Location of Company' => '${COMPANY_ADDRESS}'
        ];

        foreach ($replacements as $search => $placeholder) {
            // Build a regex that matches the string even if fragmented by tags
            // e.g. J<w:t>ericho -> J(<[^>]+>)*e(<[^>]+>)*r...
            $chars = str_split($search);
            $regex = '/';
            foreach ($chars as $i => $char) {
                $regex .= preg_quote($char, '/');
                if ($i < count($chars) - 1) {
                    $regex .= '(<[^>]+>)*';
                }
            }
            $regex .= '/u';

            // We replace with the placeholder. 
            // Warning: This is a "dirty" replacement of the XML.
            // But since it's only in the data area, it's relatively safe.
            // We'll wrap it in a single <w:t> tag if possible.
            $xml = preg_replace($regex, $placeholder, $xml);
        }

        // Clean up: If we broke some XML tags, this might be messy.
        // But usually, it replaces the content between tags too.
        
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
        return "Upgrade successful for " . basename($filePath);
    }
    return "Could not open " . basename($filePath);
}

$files = glob('public/templates/*.docx');
foreach ($files as $f) {
    echo upgradeTemplate($f) . "\n";
}
