<?php
function getFullRedText($file) {
    $zip = new ZipArchive;
    if ($zip->open($file) === TRUE) {
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        
        // Find all r nodes
        $runs = $xpath->query('//w:r');
        $redBlocks = [];
        $currentBlock = "";
        $isRed = false;

        foreach ($runs as $run) {
            $color = $xpath->query('w:rPr/w:color/@w:val', $run);
            $runIsRed = ($color->length > 0 && strtoupper($color->item(0)->nodeValue) === 'FF0000');
            
            $text = $xpath->evaluate('string(w:t)', $run);

            if ($runIsRed) {
                $currentBlock .= $text;
                $isRed = true;
            } else {
                if ($isRed && trim($currentBlock) !== "") {
                    $redBlocks[] = $currentBlock;
                }
                $currentBlock = "";
                $isRed = false;
            }
        }
        if ($isRed && trim($currentBlock) !== "") {
            $redBlocks[] = $currentBlock;
        }
        
        return array_unique($redBlocks);
    }
    return ["Failed to open $file"];
}

$templates = [
    'public/templates/NBI ENDORSEMENT.docx',
    'public/templates/MOA NBI.docx',
    'public/templates/PARENT consent.docx'
];

foreach ($templates as $t) {
    echo "--- $t ---\n";
    $red = getFullRedText($t);
    foreach($red as $r) {
        echo "RED: [$r]\n";
    }
}
