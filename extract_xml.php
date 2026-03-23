<?php
$zip = new ZipArchive;
$file = 'public/templates/NBI ENDORSEMENT.docx';
if ($zip->open($file) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    file_put_contents('template_xml_raw.xml', $xml);
    $zip->close();
    echo "Extracted XML";
}
