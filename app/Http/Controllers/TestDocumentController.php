<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OjtInfo;
use App\Services\DocumentGeneratorService;
use Illuminate\Http\Request;

class TestDocumentController extends Controller
{
    protected DocumentGeneratorService $documentGenerator;

    public function __construct(DocumentGeneratorService $documentGenerator)
    {
        $this->documentGenerator = $documentGenerator;
    }

    /**
     * Show test form
     */
    public function showTestForm()
    {
        $students = User::where('role', 'student')->with('ojtInfo')->get();
        $templates = glob(public_path('templates') . '/*.docx');
        
        $templateNames = array_map(function($path) {
            return basename($path);
        }, $templates);

        return view('test-document', [
            'students' => $students,
            'templates' => $templateNames,
        ]);
    }

    /**
     * Generate test document
     */
    public function generateTest(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'template' => 'required|string',
        ]);

        $student = User::findOrFail($request->student_id);
        
        // Check if student has OJT info
        if (!$student->ojtInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Student does not have OJT info configured. Add OJT details first.',
            ], 400);
        }

        try {
            $filePath = $this->documentGenerator->generateForStudent(
                $student,
                $request->template
            );

            // Get the URL to download
            $filename = basename($filePath);
            $downloadUrl = route('document.download', ['file' => $filename]);

            return response()->json([
                'success' => true,
                'message' => 'Document generated successfully!',
                'file_path' => $filePath,
                'download_url' => $downloadUrl,
                'student_name' => $student->fname . ' ' . $student->lname,
                'template' => $request->template,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show generated data preview
     */
    public function previewData(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        $student = User::with('ojtInfo')->findOrFail($request->student_id);
        $data = $this->documentGenerator->getStudentData($student);

        return response()->json([
            'success' => true,
            'student' => $student->only(['id', 'fname', 'mname', 'lname', 'suffix', 'email']),
            'data' => $data,
        ]);
    }
}
