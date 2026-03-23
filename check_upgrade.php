<?php
$zip = new ZipArchive;
$file = 'public/templates/NBI ENDORSEMENT.docx';
if ($zip->open($file) === TRUE) {
    echo strip_tags($zip->getFromName('word/document.xml'));
    $zip->close();
}
