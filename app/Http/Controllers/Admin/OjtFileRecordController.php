<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OjtFileRecordController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'company' => 'nullable|string|max:255',
            'student' => 'nullable|string|max:255',
            'schoolyear' => 'nullable|string|max:20',
        ]);

        $records = $this->scanOjtFileRecords(
            $filters['company'] ?? null,
            $filters['student'] ?? null,
            $filters['schoolyear'] ?? null
        );

        // Get available school years
        $availableSchoolYears = $this->getAvailableSchoolYears();

        return view('admin.ojt-file-records', [
            'records' => $records,
            'companyFilter' => $filters['company'] ?? '',
            'studentFilter' => $filters['student'] ?? '',
            'schoolYearFilter' => $filters['schoolyear'] ?? '',
            'availableSchoolYears' => $availableSchoolYears,
            'totalCompanies' => collect($records)->pluck('company_name')->unique()->count(),
            'totalStudents' => collect($records)->pluck('student_name')->unique()->count(),
            'totalPairs' => count($records),
        ]);
    }

    /**
     * Get available school years based on current system logic
     * Returns years like: SY-2025-2026, SY-2026-2027, SY-2024-2025
     */
    private function getAvailableSchoolYears(): array
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // School year starts in June
        $startYear = $currentMonth >= 6 ? $currentYear : $currentYear - 1;

        $schoolYears = [];
        
        // Generate 5 years: current and previous 4 years
        for ($i = 0; $i < 5; $i++) {
            $year1 = $startYear - $i;
            $year2 = $year1 + 1;
            $schoolYears[] = "SY-{$year1}-{$year2}";
        }

        return $schoolYears;
    }

    private function scanOjtFileRecords(?string $companyFilter = null, ?string $studentFilter = null, ?string $schoolYearFilter = null): array
    {
        $baseDir = base_path('OJT FILE');

        if (! is_dir($baseDir)) {
            return [];
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

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir, \FilesystemIterator::SKIP_DOTS)
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

            // Skip RENO company records
            if (strtolower($companyName) === 'reno') {
                continue;
            }

            if ($companyFilter && stripos($companyName, $companyFilter) === false) {
                continue;
            }

            $baseName = pathinfo($fileInfo->getFilename(), PATHINFO_FILENAME);
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

            if ($studentFilter && stripos($studentName, $studentFilter) === false) {
                continue;
            }

            $key = strtolower($companyName . '|' . $studentName);

            if (! isset($records[$key])) {
                $records[$key] = [
                    'company_name' => $companyName,
                    'student_name' => $studentName,
                    'document_count' => 0,
                    'sample_file' => $relativePath,
                ];
            }

            $records[$key]['document_count']++;
            if ($records[$key]['sample_file'] === $relativePath) {
                $records[$key]['sample_file'] = $relativePath;
            }
        }

        usort($records, function (array $left, array $right) {
            return [$left['company_name'], $left['student_name']] <=> [$right['company_name'], $right['student_name']];
        });

        return $records;
    }
}