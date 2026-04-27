<?php

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'OJT FILE';
$outputCsv = __DIR__ . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'ojt-file-records.csv';

if (!is_dir($baseDir)) {
    fwrite(STDERR, "OJT FILE directory not found: {$baseDir}\n");
    exit(1);
}

$genericNames = [
    'comletter',
    'comlettergroup',
    'memorandum of agreement',
    'moa',
    'training agreement',
    'endorsement letter for on the job training',
    'endorsement letter',
];

$records = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (! $fileInfo->isFile()) {
        continue;
    }

    $extension = strtolower($fileInfo->getExtension());
    if (! in_array($extension, ['docx', 'doc', 'pdf'], true)) {
        continue;
    }

    $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $fileInfo->getPathname());
    $pathParts = explode(DIRECTORY_SEPARATOR, $relativePath);
    $companyName = trim($pathParts[0] ?? '');
    if ($companyName === '') {
        continue;
    }

    $baseName = pathinfo($fileInfo->getFilename(), PATHINFO_FILENAME);
    $normalizedBase = strtolower(trim($baseName));

    $studentName = null;
    if (strpos($baseName, '.') !== false) {
        $candidate = trim(strtok($baseName, '.'));
        $candidateLower = strtolower($candidate);

        if ($candidate !== '' && ! in_array($candidateLower, $genericNames, true)) {
            $studentName = $candidate;
        }
    }

    if ($studentName === null) {
        continue;
    }

    $key = strtolower($companyName . '|' . $studentName);
    if (! isset($records[$key])) {
        $records[$key] = [
            'company_name' => $companyName,
            'student_name' => $studentName,
            'source_file' => $relativePath,
        ];
    }
}

ksort($records);

if (! is_dir(dirname($outputCsv))) {
    mkdir(dirname($outputCsv), 0777, true);
}

$csvHandle = fopen($outputCsv, 'w');
fputcsv($csvHandle, ['company_name', 'student_name', 'source_file']);

echo "OJT FILE record inventory\n";
echo str_repeat('=', 80) . "\n\n";

foreach (array_values($records) as $record) {
    fputcsv($csvHandle, $record);
    echo $record['company_name'] . ' | ' . $record['student_name'] . ' | ' . $record['source_file'] . "\n";
}

fclose($csvHandle);

echo "\nUnique records: " . count($records) . "\n";
echo "CSV exported to: {$outputCsv}\n";
