<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\RequiredDocument;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with(['student', 'reviewer', 'requiredDocument'])
            ->latest()
            ->get();
        return view('admin.documents', compact('documents'));
    }

    public function approve($id)
    {
        $doc = Document::findOrFail($id);
        $doc->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Mark the required document slot as fulfilled
        if ($doc->required_document_id) {
            RequiredDocument::where('id', $doc->required_document_id)
                ->update(['is_fulfilled' => true]);
        }

        NotificationLog::create([
            'user_id' => $doc->user_id,
            'title'   => 'Document Approved',
            'message' => "Your document '{$doc->type_label}' has been <strong>approved</strong>.",
            'type'    => 'success',
        ]);

        return back()->with('success', 'Document approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:500',
        ]);

        $doc = Document::findOrFail($id);
        $doc->update([
            'status'      => 'rejected',
            'remarks'     => $request->remarks,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        NotificationLog::create([
            'user_id' => $doc->user_id,
            'title'   => 'Document Rejected',
            'message' => "Your document '{$doc->type_label}' was <strong>rejected</strong>. Reason: {$request->remarks}",
            'type'    => 'danger',
        ]);

        return back()->with('success', 'Document rejected. Student has been notified.');
    }
}
