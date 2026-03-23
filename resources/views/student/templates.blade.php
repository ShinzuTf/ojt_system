@extends('layouts.app')

@section('title', 'Templates & Downloads')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Templates & Downloads</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Templates & Downloads</h1>
    <p class="page-subtitle">Generate personalized documents pre-filled with your OJT information</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <p><strong>Error:</strong> {{ $errors->first() }}</p>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Available Templates</h2>
        <p style="margin: 8px 0 0 0; font-size: 13px; color: #666;">Click "Generate" to create a document with your personal information pre-filled</p>
    </div>
    <div class="card-body" style="padding: 8px 22px;">
        <div class="doc-list">
            <div class="doc-item">
                <div class="doc-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="doc-info">
                    <div class="doc-name">MOA / Training Agreement</div>
                    <div class="doc-meta">DOCX • Pre-filled with your OJT details</div>
                </div>
                <div class="doc-actions">
                    <a href="{{ route('student.templates.generate', 'Training Agreement (MOA)') }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg>
                        Generate
                    </a>
                </div>
            </div>

            <div class="doc-item">
                <div class="doc-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="doc-info">
                    <div class="doc-name">NBI Endorsement Letter</div>
                    <div class="doc-meta">DOCX • Pre-filled with your information</div>
                </div>
                <div class="doc-actions">
                    <a href="{{ route('student.templates.generate', 'Endorsement Letter') }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg>
                        Generate
                    </a>
                </div>
            </div>

            <div class="doc-item">
                <div class="doc-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="doc-info">
                    <div class="doc-name">NBI Communication Letter (Single)</div>
                    <div class="doc-meta">DOCX • Pre-filled with your details</div>
                </div>
                <div class="doc-actions">
                    <a href="{{ route('student.templates.generate', 'Communication Letter (Single)') }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg>
                        Generate
                    </a>
                </div>
            </div>

            <div class="doc-item">
                <div class="doc-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="doc-info">
                    <div class="doc-name">Communication Letter (Group)</div>
                    <div class="doc-meta">DOCX • Pre-filled for group endorsements</div>
                </div>
                <div class="doc-actions">
                    <a href="{{ route('student.templates.generate', 'Communication Letter (Group)') }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg>
                        Generate
                    </a>
                </div>
            </div>

            <div class="doc-item">
                <div class="doc-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="doc-info">
                    <div class="doc-name">Parental Consent Form</div>
                    <div class="doc-meta">DOCX • Pre-filled with your information</div>
                </div>
                <div class="doc-actions">
                    <a href="{{ route('student.templates.generate', 'Parental Consent Form') }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14m-7-7h14"/></svg>
                        Generate
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
