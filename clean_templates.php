<?php
function defragmentDocx($file) {
    $zip = new ZipArchive;
    if ($zip->open($file) === TRUE) {
        $xml = $zip->getFromName('word/document.xml');
        
        // Remove proofing errors and other noise inside runs
        $xml = preg_replace('/<w:proofErr[^>]*\/>/', '', $xml);
        $xml = preg_replace('/<w:lang[^>]*\/>/', '', $xml);
        
        // Merge adjacent <w:t> tags in the same <w:r>
        // This is a bit complex with regex, let's use a simpler approach:
        // Merge <w:t>...</w:t></w:r><w:r><w:rPr>...same... </w:rPr><w:t>...
        // Actually, just cleaning the XML and saving it back can help.
        
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
        return "Cleaned $file";
    }
    return "Failed to open $file";
}

$templates = [
    'public/templates/NBI ENDORSEMENT.docx',
    'public/templates/MOA NBI.docx',
    'public/templates/PARENT consent.docx'
];

foreach ($templates as $t) {
    echo defragmentDocx($t) . "\n";
}
