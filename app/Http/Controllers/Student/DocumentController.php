<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequiredDocument;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function submit()
    {
        $user = Auth::user();
        
        // Fetch only documents the admin has "given" (assigned as required)
        $requiredDocs = $user->requiredDocuments()
            ->with(['assignedBy'])
            ->get();

        // For each requirement, let's explicitly find the latest submission
        foreach ($requiredDocs as $req) {
            $req->submission = Document::where('user_id', $user->id)
                ->where('required_document_id', $req->id)
                ->latest()
                ->first();
        }

        return view('student.documents.submit', compact('requiredDocs'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'document'             => 'required|file|mimes:pdf,docx|max:10240',
            'required_document_id' => 'required|exists:required_documents,id',
        ]);

        $user = Auth::user();
        $reqDoc = RequiredDocument::findOrFail($request->required_document_id);

        if ($reqDoc->student_id !== $user->id) {
            return back()->with('error', 'Unauthorized access to document slot.');
        }

        $file = $request->file('document');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents/' . $user->id, $fileName, 'public');

        Document::create([
            'user_id'              => $user->id,
            'required_document_id' => $reqDoc->id,
            'document_type'        => $reqDoc->document_name, // fallback for legacy
            'file_name'            => $file->getClientOriginalName(),
            'file_path'            => $filePath,
            'file_type'            => $file->getClientOriginalExtension(),
            'file_size'            => $file->getSize(),
            'status'               => 'submitted',
        ]);

        return back()->with('success', "Document for '{$reqDoc->document_name}' uploaded successfully.");
    }

    public function history()
    {
        $documents = Auth::user()->documents()->latest()->get();
        return view('student.documents.history', compact('documents'));
    }
}
