<?php

namespace App\Services;

use App\Models\User;
use App\Models\OjtInfo;
use Exception;

class DocumentGeneratorService
{
    protected DocxTemplateGenerator $templateGenerator;

    public function __construct(DocxTemplateGenerator $templateGenerator)
    {
        $this->templateGenerator = $templateGenerator;
    }

    /**
     * Generate document for a student
     * 
     * @param User $student
     * @param string $templateName Template filename (e.g., "PARENT consent.docx")
     * @param array $customData Additional custom data to merge
     * @return string Path to generated document
     */
    public function generateForStudent(User $student, string $templateName, array $customData = []): string
    {
        // Get student data
        $studentData = $this->getStudentData($student);
        
        // Merge with custom data (custom data takes priority)
        $data = array_merge($studentData, $customData);

        // Get template path
        $templatePath = public_path('templates/' . $templateName);
        
        if (!file_exists($templatePath)) {
            throw new Exception("Template not found: {$templateName}");
        }

        // Generate filename
        $outputName = $this->generateOutputFileName($student, $templateName);

        // Generate document
        return $this->templateGenerator->generate($templatePath, $data, $outputName);
    }

    /**
     * Generate multiple documents for a student
     * 
     * @param User $student
     * @param array $templates Array of template names or [templateName => customData]
     * @return array Array of generated file paths
     */
    public function generateBatch(User $student, array $templates): array
    {
        $results = [];

        foreach ($templates as $templateName => $customData) {
            try {
                // If key is numeric, value is template name
                if (is_numeric($templateName)) {
                    $templateName = $customData;
                    $customData = [];
                }

                $path = $this->generateForStudent($student, $templateName, $customData);
                $results[$templateName] = [
                    'success' => true,
                    'path' => $path,
                ];
            } catch (Exception $e) {
                $results[$templateName] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Get all data for a student from User and OjtInfo
     * 
     * @param User $student
     * @return array
     */
    public function getStudentData(User $student): array
    {
        $data = [
            // User data
            'FULL_NAME' => $student->getFullNameAttribute(),
            'SHORT_NAME' => $student->getShortNameAttribute(),
            'FIRST_NAME' => $student->fname,
            'MIDDLE_NAME' => $student->mname ?? '',
            'LAST_NAME' => $student->lname,
            'SUFFIX' => $student->suffix ?? '',
            'EMAIL' => $student->email,
            'ROLE' => $student->role,
            'STATUS' => $student->status,
        ];

        // Add OJT Info if available
        $ojtInfo = $student->ojtInfo;
        if ($ojtInfo) {
            $data = array_merge($data, $this->getOjtData($ojtInfo));
        }

        // Add current date/time
        $data['CURRENT_DATE'] = now()->format('F d, Y');
        $data['CURRENT_YEAR'] = now()->format('Y');
        $data['CURRENT_MONTH'] = now()->format('F');
        $data['CURRENT_DAY'] = now()->format('d');

        return $data;
    }

    /**
     * Get OJT Info data
     * 
     * @param OjtInfo $ojtInfo
     * @return array
     */
    private function getOjtData(OjtInfo $ojtInfo): array
    {
        return [
            'STUDENT_NUMBER' => $ojtInfo->student_number,
            'COURSE' => $ojtInfo->course,
            'YEAR_LEVEL' => $ojtInfo->year_level,
            'COMPANY_NAME' => $ojtInfo->company_name,
            'COMPANY_EMAIL' => $ojtInfo->company_email,
            'COMPANY_ADDRESS' => $ojtInfo->company_address,
            'SUPERVISOR_NAME' => $ojtInfo->supervisor_name,
            'SUPERVISOR_CONTACT' => $ojtInfo->supervisor_contact,
            'SUPERVISOR_TITLE' => $ojtInfo->supervisor_title ?? 'Supervisor', // Add default if not provided
            'OJT_START' => $ojtInfo->ojt_start ? $ojtInfo->ojt_start->format('F d, Y') : '',
            'OJT_START_DATE' => $ojtInfo->ojt_start ? $ojtInfo->ojt_start->format('m/d/Y') : '',
            'OJT_END' => $ojtInfo->ojt_end ? $ojtInfo->ojt_end->format('F d, Y') : '',
            'OJT_END_DATE' => $ojtInfo->ojt_end ? $ojtInfo->ojt_end->format('m/d/Y') : '',
            'REQUIRED_HOURS' => $ojtInfo->required_hours,
            'RENDERED_HOURS' => $ojtInfo->rendered_hours,
            'OJT_STATUS' => $ojtInfo->ojt_status,
            'PROGRESS_PERCENT' => $ojtInfo->getProgressPercentAttribute(),
        ];
    }

    /**
     * Generate output filename
     * 
     * @param User $student
     * @param string $templateName
     * @return string
     */
    private function generateOutputFileName(User $student, string $templateName): string
    {
        $baseName = str_replace('.docx', '', $templateName);
        $timestamp = now()->format('YmdHis');
        return "{$student->id}_{$baseName}_{$timestamp}.docx";
    }

    /**
     * Get available templates with their required placeholders
     * 
     * @return array
     */
    public function getAvailableTemplates(): array
    {
        return $this->templateGenerator->getAvailableTemplates();
    }

    /**
     * Download generated document
     * 
     * @param string $filePath
     * @param string $downloadName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDocument(string $filePath, string $downloadName = null)
    {
        if (!file_exists($filePath)) {
            throw new Exception("Generated document not found");
        }

        $downloadName = $downloadName ?? basename($filePath);
        
        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }
}
