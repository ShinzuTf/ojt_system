@extends('layouts.app')

@section('title', 'Document Templates')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Templates</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Document Templates</h1>
    <p class="page-subtitle">View the required templates that are automatically assigned to students</p>
</div>

<div class="alert alert-success" style="margin-bottom:24px;">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span><strong>Automatic Assignment:</strong> The following templates are automatically assigned to students upon completion of their OJT profile. Students can generate and download them from their dashboard.</span>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 22px;">
    @php
        $templateDescriptions = [
            'Training Agreement (MOA)' => 'Legal agreement between the College, Student, and Company outlining OJT terms and conditions.',
            'NBI Endorsement Letter' => 'Formal letter from the Dean to the host company endorsing the student for internship.',
            'Parental Consent Form' => 'Document securing parental approval for the student\'s OJT participation.',
            'Communication Letter (Single)' => 'Letter of authorization from the College to the host company for a single student.',
        ];
    @endphp

    @foreach($templates ?? [] as $template)
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">{{ $template }}</h2>
            </div>
            <div class="card-body">
                <p style="font-size:0.88rem; color:var(--gray-500); margin-bottom:16px;">
                    {{ $templateDescriptions[$template] ?? 'Required OJT document template.' }}
                </p>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge badge-approved">Auto-Assigned</span>
                    <span class="badge" style="background-color:#e8f3ff; color:#0066cc;">Required</span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="alert alert-info" style="margin-top:24px;">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>These templates are managed by the system. Students will receive them automatically once they complete their OJT profile data. No manual assignment is needed.</span>
</div>
@endsection
