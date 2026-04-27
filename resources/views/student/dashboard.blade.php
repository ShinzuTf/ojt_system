@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('breadcrumb')
    <span class="current">Dashboard</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Welcome back, {{ auth()->user()->fname }}!</h1>
    <p class="page-subtitle">View your performance evaluations and feedback</p>
</div>

{{-- Profile Completion Alert --}}
@php
    $ojt = auth()->user()->ojtInfo;
    $isProfileComplete = $ojt && $ojt->student_number && $ojt->company_name;
@endphp

@if(!$isProfileComplete)
<div class="alert alert-warning" style="margin-bottom:22px; display:flex; align-items:center; justify-content:space-between; gap:16px;">
    <div style="display:flex; align-items:center; gap:12px;">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <div style="font-weight:700;">Complete your OJT Profile</div>
            <div style="font-size:0.85rem; opacity:0.9;">Please provide your student number, course, and company details to complete your OJT setup.</div>
        </div>
    </div>
    <a href="{{ route('student.ojt-profile') }}" class="btn btn-sm" style="background:rgba(0,0,0,0.1); border:1px solid rgba(0,0,0,0.2); color:var(--gray-800); white-space:nowrap;">Finish Profile</a>
</div>
@endif
{{-- Template Generation Section --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Required OJT Templates</h2>
                <p class="text-small text-muted">Generate and download your required pre-filled forms</p>
            </div>
        </div>
        <div class="card-body" style="padding: 22px;">
            @php
                $isProfileComplete = $ojt && $ojt->student_number && $ojt->company_name;
                $requiredTemplates = [
                    ['name' => 'Training Agreement (MOA)', 'icon' => 'document'],
                    ['name' => 'NBI Endorsement Letter', 'icon' => 'document'],
                    ['name' => 'Parental Consent Form', 'icon' => 'document'],
                    ['name' => 'Communication Letter (Single)', 'icon' => 'document'],
                ];
            @endphp

            @if(!$isProfileComplete)
                <div style="background:var(--warning-light); color:var(--warning); padding:16px; border-radius:8px; border-left:4px solid var(--warning); margin-bottom:16px;">
                    <div style="font-weight:600; margin-bottom:8px;">⚠ Complete Your Profile First</div>
                    <p style="font-size:0.9rem; margin:0;">Please complete your OJT profile to enable template generation and access your required documents.</p>
                    <a href="{{ route('student.ojt-profile') }}" class="btn btn-sm" style="background:rgba(0,0,0,0.1); border:1px solid rgba(0,0,0,0.2); color:var(--gray-800); margin-top:10px;">Complete Profile</a>
                </div>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px;">
                    @foreach($requiredTemplates as $template)
                        <div style="border: 1px solid var(--gray-200); border-radius: 8px; padding: 16px; display: flex; flex-direction: column; background: var(--gray-50); transition: all 0.2s;">
                            <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:12px;">
                                <div style="background:var(--primary-light); color:var(--primary); padding:8px; border-radius:6px; flex-shrink:0;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <div style="font-weight:700; color:var(--gray-800); font-size:0.95rem;">{{ $template['name'] }}</div>
                                    <div style="font-size:0.8rem; color:var(--gray-500); margin-top:2px;">Auto-assigned • Required</div>
                                </div>
                            </div>

                            <p style="font-size:0.85rem; color:var(--gray-600); margin:0 0 12px 0;">Pre-filled with your student information from your OJT profile.</p>

                            <button type="button" onclick="generateAndDownload(event, '{{ $template['name'] }}')" class="btn btn-primary btn-sm" style="width:100%; justify-content:center; font-size:0.85rem; color:black; background:var(--primary); margin-top:auto; border:none; cursor:pointer;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px; color:black; display:inline-block;"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/></svg>
                                Generate and Download
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

{{-- Performance Evaluations Section --}}
    <div class="card">
        <div class="card-header" style="justify-content: space-between;">
            <div>
                <h2 class="card-title">My Performance Evaluations</h2>
                <p class="text-small text-muted">View feedback on your performance during your OJT from your supervisor/coordinator.</p>
            </div>
            <a href="{{ route('student.evaluations') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="card-body" style="padding: 22px;">
            @php
                $recentEvaluations = auth()->user()->evaluationsReceived()
                    ->with('supervisor')
                    ->latest('evaluation_date')
                    ->limit(5)
                    ->get();
            @endphp
            
            @forelse($recentEvaluations as $eval)
                <div style="border-bottom: 1px solid var(--gray-100); padding: 14px 0; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex: 1;">
                        <div style="font-size: 0.9rem; font-weight: 600; color: var(--gray-800);">
                            {{ $eval->evaluation_date->format('M d, Y') }}
                        </div>
                        <div style="font-size: 0.8rem; color: var(--gray-500); margin-top: 4px;">
                            Evaluated by: {{ $eval->supervisor->full_name }}
                        </div>
                        @if($eval->strengths)
                            <div style="font-size: 0.8rem; color: var(--gray-600); margin-top: 6px;">
                                <strong>Strengths:</strong> {{ Str::limit($eval->strengths, 60) }}
                            </div>
                        @endif
                        @if($eval->technical_skills_rating || $eval->communication_rating || $eval->teamwork_rating)
                            <div style="font-size: 0.75rem; color: var(--gray-400); margin-top: 4px;">
                                Avg Rating: {{ number_format($eval->average_rating, 1) }}/5
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 32px; color: var(--gray-400);">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:12px; opacity:0.3; margin-left:auto; margin-right:auto;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3"/></svg>
                    <p style="font-size: 0.88rem;">No performance evaluations yet. Check back after your supervisor completes your evaluations.</p>
                </div>
            @endforelse
        </div>
    </div>

    
{{-- Document Preview Modal --}}
<div id="previewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; max-width:900px; width:90%; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        {{-- Modal Header --}}
        <div style="display:flex; justify-content:space-between; align-items:center; padding:20px; border-bottom:1px solid var(--gray-200); background:var(--gray-50); flex-shrink:0;">
            <h3 style="margin:0; font-size:1.2rem; color:var(--gray-800);">Document Preview</h3>
            <button type="button" onclick="closePreviewModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--gray-400);">×</button>
        </div>

        {{-- Modal Body - Scrollable Preview Area --}}
        <div id="documentPreviewContent" style="flex:1; overflow-y:auto; padding:24px; background:white;">
            <div id="previewPlaceholder" style="background:var(--gray-50); border:2px dashed var(--gray-300); border-radius:8px; padding:40px; text-align:center;">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin:0 auto 16px; color:var(--primary); animation:spin 0.8s linear infinite;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <p style="font-size:0.95rem; color:var(--gray-600); margin:12px 0 0 0;">
                    Loading document preview...
                </p>
            </div>
            <div id="previewDocument" style="display:none;"></div>

            <div style="background:#f0f9ff; border-left:4px solid var(--primary); padding:12px; border-radius:4px; margin-top:20px;">
                <p style="margin:0; font-size:0.85rem; color:var(--primary);">
                    <strong>✓ Pre-filled Information:</strong> This document has been automatically filled with your OJT profile data and is ready to use.
                </p>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div style="display:flex; justify-content:flex-end; gap:12px; padding:20px; border-top:1px solid var(--gray-200); background:var(--gray-50); flex-shrink:0;">
            <button type="button" onclick="closePreviewModal()" class="btn btn-secondary" style="border:1px solid var(--gray-300); background:white; color:var(--gray-700); padding:10px 20px; border-radius:6px; cursor:pointer;">
                Cancel
            </button>
            <a id="downloadLink" href="#" download class="btn btn-primary" style="background:var(--primary); color:black; padding:10px 24px; border-radius:6px; text-decoration:none; display:inline-block; cursor:pointer; border:none;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px; vertical-align:middle; display:inline-block;"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/></svg>
                Download Document
            </a>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>

@push('scripts')
<script>
function generateAndDownload(event, templateName) {
    event.preventDefault();
    
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px; display:inline-block; animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Generating...';

    fetch("{{ route('student.templates.generate', ':name') }}".replace(':name', templateName), {
        method: 'GET'
    })
    .then(response => response.blob())
    .then(blob => {
        // Create download link and trigger download immediately
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = templateName.replace(/\s+/g, '_') + '.docx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px; color:black; display:inline-block;"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/></svg> Generate and Download';
    })
    .catch(error => {
        alert('Error generating document: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px; color:black; display:inline-block;"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/></svg> Generate and Download';
    });
}

function closePreviewModal() {
    document.getElementById('previewModal').style.display = 'none';
}

const previewModal = document.getElementById('previewModal');
if (previewModal) {
    previewModal.addEventListener('click', function(event) {
        if (event.target === this) closePreviewModal();
    });
}
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

#previewDocument {
    background: white;
    padding: 40px;
    border-radius: 6px;
    line-height: 1.6;
    color: #333;
    font-size: 14px;
}

#previewDocument p {
    margin: 12px 0;
}

#previewDocument h1, #previewDocument h2, #previewDocument h3, #previewDocument h4 {
    margin: 20px 0 12px 0;
    font-weight: 600;
}

#previewDocument table {
    width: 100%;
    border-collapse: collapse;
    margin: 16px 0;
}

#previewDocument table td, #previewDocument table th {
    border: 1px solid #ddd;
    padding: 8px;
}

#previewDocument table th {
    background: #f5f5f5;
    font-weight: 600;
}
</style>

@endpush

@endsection
