<?php
function getFullRedText($file) {
    if (!file_exists($file)) return ["File not found"];
    $zip = new ZipArchive;
    if ($zip->open($file) === TRUE) {
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if (!$xml) return ["No document.xml"];
        
        $dom = new DOMDocument();
        @$dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        
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
                if ($isRed && trim($currentBlock) !== "") $redBlocks[] = $currentBlock;
                $currentBlock = "";
                $isRed = false;
            }
        }
        if ($isRed && trim($currentBlock) !== "") $redBlocks[] = $currentBlock;
        return array_unique($redBlocks);
    }
    return ["Failed to open $file"];
}

$files = glob('public/templates/*.docx');
foreach ($files as $f) {
    echo "=== $f ===\n";
    $red = getFullRedText($f);
    foreach($red as $r) {
        echo "RED: [$r]\n";
    }
}
