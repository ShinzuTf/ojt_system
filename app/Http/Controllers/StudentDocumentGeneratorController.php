<?php

namespace App\Http\Controllers;

use App\Services\DocumentGeneratorService;
use Illuminate\Http\Request;
use Exception;

class StudentDocumentGeneratorController extends Controller
{
    protected DocumentGeneratorService $documentGenerator;

    public function __construct(DocumentGeneratorService $documentGenerator)
    {
        $this->documentGenerator = $documentGenerator;
    }

    /**
     * Show the document generator page
     */
    public function index()
    {
        $student = auth()->user();
        
        return view('student.documents.generate', [
            'student' => $student,
            'templates' => [
                'Training Agreement (MOA)' => 'Memorandum of Agreement between College, Student, and Company',
                'Endorsement Letter' => 'NBI Endorsement Letter from Dean to Host Company',
                'Communication Letter (Single)' => 'Communication Letter for Single Student',
                'Communication Letter (Group)' => 'Communication Letter for Group of Students',
            ]
        ]);
    }

    /**
     * Generate selected documents
     */
    public function generate(Request $request)
    {
        try {
            $validated = $request->validate([
                'templates' => 'required|array|min:1',
                'templates.*' => 'string|in:Training Agreement (MOA),Endorsement Letter,Communication Letter (Single),Communication Letter (Group)',
            ]);

            $student = auth()->user();
            
            if (!$student) {
                return back()->with('error', 'Unauthorized access');
            }

            // Check if OJT profile is complete
            $ojtInfo = $student->ojtInfo;
            if (!$ojtInfo || !$ojtInfo->company_name || !$ojtInfo->student_number) {
                return back()->with('error', 'Please complete your OJT Profile before generating documents.');
            }

            // Generate documents
            $results = [];
            foreach ($validated['templates'] as $template) {
                try {
                    $filePath = $this->documentGenerator->generateForStudent(
                        $student,
                        $template,
                        []
                    );
                    $results[$template] = [
                        'success' => true,
                        'file_path' => $filePath,
                        'download_url' => route('document.download', ['file' => basename($filePath)]),
                    ];
                } catch (Exception $e) {
                    $results[$template] = [
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            // Check if all were successful
            $allSuccess = collect($results)->every(fn($r) => $r['success']);
            
            if ($allSuccess) {
                return back()->with('success', count($results) . ' document(s) generated successfully!');
            } else {
                $failedCount = collect($results)->filter(fn($r) => !$r['success'])->count();
                return back()->with('warning', $failedCount . ' document(s) failed to generate. Please try again.');
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error generating documents: ' . $e->getMessage());
        }
    }
}
