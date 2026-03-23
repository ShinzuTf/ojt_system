<?php
$zip = new ZipArchive;
$file = 'public/templates/MOA NBI.docx';
if ($zip->open($file) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    $phrases = [
        'Jericho Y. Barcelon',
        'Bachelor of Science in Information Technology',
        '4th year',
        'HON. JOLLY R. RESUELLO',
        'Municipal Mayor',
        'Poblacion Basista, Pangasinan',
        'January 19,2026',
        'June 4,2026'
    ];
    
    foreach($phrases as $p) {
        $pos = strpos($xml, $p);
        if ($pos !== false) {
            echo "MATCH: '$p' found in MOA at $pos\n";
        } else {
            echo "MISS: '$p' not found in MOA\n";
        }
    }
}
