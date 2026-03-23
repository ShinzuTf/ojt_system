<?php
$zip = new ZipArchive;
$file = 'public/templates/NBI ENDORSEMENT.docx';
if ($zip->open($file) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    $search = 'Jericho';
    $pos = strpos($xml, $search);
    if ($pos !== false) {
        $context = substr($xml, max(0, $pos - 400), 800);
        file_put_contents('xml_context.txt', $context);
        echo "Found at $pos. Context saved to xml_context.txt";
    } else {
        echo "Not found";
    }
}
