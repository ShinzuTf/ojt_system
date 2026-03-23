<?php
$zip = new ZipArchive;
$file = 'public/templates/NBI ENDORSEMENT.docx';
if ($zip->open($file) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    echo $xml;
    $zip->close();
}
