<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use Illuminate\Http\Request;

class CoordinatorCertificationController extends Controller
{
    public function index()
    {
        $certifications = Certification::with('student', 'placement', 'issuedBy', 'verifiedBy')
            ->paginate(20);
        
        return view('coordinator.certifications.index', ['certifications' => $certifications]);
    }

    public function show(Certification $certification)
    {
        return view('coordinator.certifications.show', ['certification' => $certification]);
    }

    public function approve(Certification $certification)
    {
        $certification->update([
            'status' => 'approved',
            'verified_by' => auth()->id(),
        ]);

        return back()->with('success', 'Certification approved.');
    }

    public function reject(Certification $certification)
    {
        $certification->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
        ]);

        return back()->with('error', 'Certification rejected.');
    }
}
