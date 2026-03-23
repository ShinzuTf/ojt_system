@extends('layouts.app')

@section('title', 'Submit Documents')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Submit Documents</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Submit OJT Documents</h1>
    <p class="page-subtitle">Upload your required On-the-Job Training documents for review</p>
</div>

{{-- Important Notice --}}
<div class="alert alert-info">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>
        <strong>Accepted file formats:</strong> PDF and DOCX only. Maximum file size: 10MB per document.
        Please ensure all documents are properly signed before uploading.
    </div>
</div>

{{-- Document Upload Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
    @forelse($requiredDocs as $req)
        @php
            $sub = $req->submission;
        @endphp
        <div class="card" style="display: flex; flex-direction: column; {{ $sub && $sub->status === 'rejected' ? 'border-color: var(--danger); border-width: 1.5px;' : '' }}">
            <div class="card-header">
                <h3 class="card-title" style="font-size:0.92rem;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:-2px; margin-right:4px;"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $req->document_name }}
                </h3>
                @if(!$sub)
                    <span class="badge badge-not-submitted"><span class="badge-dot"></span> Pending Input</span>
                @else
                    <span class="badge badge-{{ $sub->status }}"><span class="badge-dot"></span> {{ ucfirst($sub->status) }}</span>
                @endif
            </div>
            <div class="card-body" style="flex:1; display:flex; flex-direction:column;">
                @if($req->description)
                    <p class="text-small text-muted mb-2">{{ $req->description }}</p>
                @endif

                {{-- Template Download Link (if applicable) --}}
                @php
                    $templateMap = [
                        'MOA / Training Agreement' => ['file' => 'MOA NBI.docx', 'label' => 'Agreement Template'],
                        'Parental Consent Form'   => ['file' => 'PARENT consent.docx', 'label' => 'Consent Form Template'],
                        'NBI Clearance'           => ['file' => 'NBI ENDORSEMENT.docx', 'label' => 'NBI Endorsement Template'],
                    ];
                    $tpl = $templateMap[$req->document_name] ?? null;
                @endphp

                @if($tpl && (!$sub || $sub->status === 'rejected'))
                    <a href="{{ asset('templates/' . $tpl['file']) }}" download class="btn btn-ghost btn-sm" style="margin-bottom:12px; justify-content:flex-start; padding-left:0; color:var(--purple-600);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg>
                        Download {{ $tpl['label'] }}
                    </a>
                @endif

                @if($sub && $sub->remarks)
                    <div class="alert alert-{{ $sub->status === 'rejected' ? 'danger' : 'info' }}" style="margin-bottom:14px; padding: 10px 14px;">
                        <div style="font-size: 0.8rem;"><strong>Admin Remark:</strong> {{ $sub->remarks }}</div>
                    </div>
                @endif

                @if(!$sub || $sub->status === 'rejected')
                    <form action="{{ route('student.documents.upload') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                        @csrf
                        <input type="hidden" name="required_document_id" value="{{ $req->id }}">
                        <div class="file-upload-zone" style="padding: 24px; position:relative;" onclick="this.querySelector('input').click()">
                            <input type="file" name="document" accept=".pdf,.docx" style="display:none;" onchange="this.form.submit()">
                            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <div class="upload-text">
                                {{ $sub && $sub->status === 'rejected' ? 'Re-upload document' : 'Drag & drop or browse' }}
                            </div>
                            <div class="upload-hint">PDF, DOCX only · Max 10MB</div>
                        </div>
                    </form>
                @else
                    <div class="file-upload-zone" style="padding: 24px; border-style: solid; border-color: var(--gray-100); background: var(--gray-50);">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: {{ $sub->status === 'approved' ? 'var(--success)' : 'var(--purple-500)' }};">
                            @if($sub->status === 'approved')
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @else
                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            @endif
                        </svg>
                        <div class="upload-text">{{ $sub->file_name }}</div>
                        <div class="upload-hint" style="color: {{ $sub->status === 'approved' ? 'var(--success)' : 'var(--purple-600)' }}; font-weight: 600;">
                            {{ ucfirst($sub->status) }} · {{ $sub->created_at->format('M d, Y') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card" style="grid-column: 1 / -1; padding: 48px; text-align: center;">
            <div style="background: var(--gray-50); width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color: var(--gray-400);"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <h3 style="font-weight: 700; color: var(--gray-800); margin-bottom: 8px;">No requirements assigned yet</h3>
            <p style="color: var(--gray-500); max-width: 400px; margin: 0 auto;">The admin hasn't given you any document requirements yet. Please wait for the admin to assign your OJT documents.</p>
        </div>
    @endforelse
</div>

{{-- Submit Button (Footer) --}}
@if($requiredDocs->count() > 0)
<div style="margin-top: 32px; display:flex; justify-content:flex-end; gap:12px; padding: 20px; background: var(--gray-50); border-radius: var(--border-radius-lg); border: 1px solid var(--gray-200);">
    <div style="flex: 1; align-self: center;">
        <p class="text-small text-muted">Submit all your documents for final clearance after OJT completion.</p>
    </div>
    <button class="btn btn-primary" onclick="window.location.reload()" style="padding: 10px 24px; box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
        Check for Updates
    </button>
</div>
@endif
@endsection
