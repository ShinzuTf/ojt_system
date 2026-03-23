<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\DocumentGeneratorService;
use Exception;

class TemplateController extends Controller
{
    protected DocumentGeneratorService $documentGenerator;

    public function __construct(DocumentGeneratorService $documentGenerator)
    {
        $this->documentGenerator = $documentGenerator;
    }

    public function index()
    {
        return view('student.templates');
    }

    public function generate($documentName)
    {
        try {
            $user = auth()->user();

            if (!$user->ojtInfo || !$user->ojtInfo->company_name) {
                return redirect()->back()->with('error', 'Please complete your OJT profile first so we can fill the document for you.');
            }

            // Map document names to template files
            $templateFiles = [
                'Endorsement Letter' => 'NBI ENDORSEMENT.docx',
                'NBI Endorsement Letter' => 'NBI ENDORSEMENT.docx',
                'Training Agreement' => 'MOA NBI.docx',
                'Training Agreement (MOA)' => 'MOA NBI.docx',
                'MOA / Training Agreement' => 'MOA NBI.docx',
                'Parental Consent Form' => 'PARENT consent.docx',
                'Communication Letter' => 'NBI comletter single.docx',
                'Communication Letter (Single)' => 'NBI comletter single.docx',
                'Communication Letter (Group)' => 'comlettter group.docx',
            ];

            $templateFileName = $templateFiles[$documentName] ?? null;

            if (!$templateFileName || !file_exists(public_path('templates/' . $templateFileName))) {
                return redirect()->back()->with('error', 'Document template not found.');
            }

            \Log::info('Document Name: ' . $documentName);
            \Log::info('Template File Name: ' . $templateFileName);
            \Log::info('Template File Path: ' . public_path('templates/' . $templateFileName));

            // Use the new optimized document generator
            $filePath = $this->documentGenerator->generateForStudent(
                $user,
                $templateFileName
            );

            // Return download response
            $outputFileName = str_replace(' ', '_', $documentName) . '_' . str_replace(' ', '_', $user->lname) . '.docx';
            return response()->download($filePath, $outputFileName);

        } catch (Exception $e) {
            \Log::error("Student Document Generation Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating document: ' . $e->getMessage());
        }
    }
}
