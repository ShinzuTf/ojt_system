<?php
$zip = new ZipArchive;
$file = 'public/templates/NBI ENDORSEMENT.docx';
if ($zip->open($file) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    $search = 'Bachelor';
    $pos = strpos($xml, $search);
    if ($pos !== false) {
        $context = substr($xml, max(0, $pos - 100), 400);
        file_put_contents('bachelor_xml.txt', $context);
        echo "Found at $pos";
    } else {
        echo "Not found";
    }
}
