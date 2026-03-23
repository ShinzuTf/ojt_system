<?php

namespace App\Http\Controllers;

use App\Services\DocumentGeneratorService;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class DocumentGeneratorController extends Controller
{
    protected DocumentGeneratorService $documentGenerator;

    public function __construct(DocumentGeneratorService $documentGenerator)
    {
        $this->documentGenerator = $documentGenerator;
    }

    /**
     * Show list of available templates
     */
    public function listTemplates()
    {
        $templates = $this->documentGenerator->getAvailableTemplates();
        
        return response()->json([
            'success' => true,
            'templates' => $templates,
        ]);
    }

    /**
     * Generate a document for the authenticated student
     */
    public function generateDocument(Request $request)
    {
        try {
            $validated = $request->validate([
                'template' => 'required|string',
                'custom_data' => 'nullable|array',
                'download' => 'boolean|nullable',
            ]);

            $student = auth()->user();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Generate document
            $filePath = $this->documentGenerator->generateForStudent(
                $student,
                $validated['template'],
                $validated['custom_data'] ?? []
            );

            // If download requested, download the file
            if ($validated['download'] ?? false) {
                return $this->documentGenerator->downloadDocument(
                    $filePath,
                    basename($filePath)
                );
            }

            // Otherwise return file path
            return response()->json([
                'success' => true,
                'message' => 'Document generated successfully',
                'file_path' => $filePath,
                'download_url' => route('document.download', ['file' => basename($filePath)]),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating document: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate multiple documents
     */
    public function generateBatch(Request $request)
    {
        try {
            $validated = $request->validate([
                'templates' => 'required|array|min:1',
                'templates.*' => 'string',
            ]);

            $student = auth()->user();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Generate documents
            $results = $this->documentGenerator->generateBatch(
                $student,
                $validated['templates']
            );

            return response()->json([
                'success' => true,
                'message' => 'Batch generation completed',
                'results' => $results,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating documents: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download generated document
     */
    public function downloadDocument(Request $request)
    {
        try {
            $filename = $request->query('file');
            
            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not specified',
                ], 400);
            }

            $filePath = storage_path('app/documents/' . basename($filename));

            // Security check: ensure file is in documents directory
            if (!str_starts_with(realpath($filePath), realpath(storage_path('app/documents')))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file path',
                ], 403);
            }

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found',
                ], 404);
            }

            return $this->documentGenerator->downloadDocument($filePath, $filename);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading document: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get preview of template placeholders for a student
     */
    public function previewTemplate(Request $request)
    {
        try {
            $validated = $request->validate([
                'template' => 'required|string',
            ]);

            $student = auth()->user();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Get student data
            $data = $this->documentGenerator->getStudentData($student);

            // Get template placeholders
            $templatePath = public_path('templates/' . $validated['template']);
            if (!file_exists($templatePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found',
                ], 404);
            }

            $generator = app(\App\Services\DocxTemplateGenerator::class);
            $placeholders = $generator->getPlaceholders($templatePath);

            // Build preview data
            $preview = [];
            foreach ($placeholders as $placeholder) {
                $preview[$placeholder] = $data[$placeholder] ?? '[NOT FOUND: ' . $placeholder . ']';
            }

            return response()->json([
                'success' => true,
                'template' => $validated['template'],
                'placeholders' => $placeholders,
                'preview_data' => $preview,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
