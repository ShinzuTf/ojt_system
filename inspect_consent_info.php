<?php
$zip = new ZipArchive;
$file = 'public/templates/PARENT consent.docx';
if ($zip->open($file) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    $search = 'Information';
    $pos = strpos($xml, $search);
    if ($pos !== false) {
        $context = substr($xml, max(0, $pos - 100), 400);
        file_put_contents('consent_info_context.txt', $context);
        echo "Found at $pos";
    } else {
        echo "Not found";
    }
}
