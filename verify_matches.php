<?php
$zip = new ZipArchive;
$file = 'public/templates/NBI ENDORSEMENT.docx';
if ($zip->open($file) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    // Check for some other strings
    $phrases = [
        'Bachelor of Science in Information Technology',
        '4th year',
        'HON. JOLLY R. RESUELLO',
        'Municipal Mayor',
        'January 19,2026',
        'June 4,2026'
    ];
    
    foreach($phrases as $p) {
        $pos = strpos($xml, $p);
        if ($pos !== false) {
            echo "MATCH: '$p' found at $pos\n";
        } else {
            // Try searching with potential XML tags in between
            echo "MISS: '$p' not found as a literal string\n";
        }
    }
}
