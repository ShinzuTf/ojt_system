<?php
$zip = new ZipArchive;
$file = 'public/templates/PARENT consent.docx';
if ($zip->open($file) === TRUE) {
    echo strip_tags($zip->getFromName('word/document.xml'));
    $zip->close();
}
