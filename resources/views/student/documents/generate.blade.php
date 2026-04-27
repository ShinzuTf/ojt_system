@extends('layouts.app')

@section('title', 'Generate Documents - OJT System')
@section('page-title', 'Generate Documents')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-file-earmark-text"></i> Generate OJT Documents</h5>
            <p style="margin: 8px 0 0 0; font-size: 0.9rem; color: #666;">Select the documents you want to generate. They will be pre-filled with your OJT information.</p>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Check OJT Profile Completion --}}
            @php
                $ojtInfo = $student->ojtInfo;
                $isComplete = $ojtInfo && $ojtInfo->company_name && $ojtInfo->student_number && $ojtInfo->course;
            @endphp

            @if (!$isComplete)
                <div class="alert alert-warning" style="margin-bottom: 24px;">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Incomplete OJT Profile!</strong>
                    <br>Please <a href="{{ route('student.ojt-profile') }}" class="alert-link">complete your OJT profile</a> before generating documents. You need to fill in:
                    <ul class="mb-0 mt-2">
                        @if (!$ojtInfo || !$ojtInfo->student_number)
                            <li>Student Number</li>
                        @endif
                        @if (!$ojtInfo || !$ojtInfo->course)
                            <li>Course</li>
                        @endif
                        @if (!$ojtInfo || !$ojtInfo->company_name)
                            <li>Company Name</li>
                        @endif
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('student.documents.generate.submit') }}">
                @csrf

                {{-- Student Information Display --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label text-muted">Student Name</label>
                            <p class="h6 mb-0">{{ $student->fname }} {{ $student->lname }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label text-muted">Student Number</label>
                            <p class="h6 mb-0">{{ $ojtInfo?->student_number ?? 'Not Set' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label text-muted">Company</label>
                            <p class="h6 mb-0">{{ $ojtInfo?->company_name ?? 'Not Set' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label text-muted">Course</label>
                            <p class="h6 mb-0">{{ $ojtInfo?->course ?? 'Not Set' }}</p>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Document Selection --}}
                <div class="mb-4">
                    <label class="form-label"><strong>Select Documents to Generate</strong></label>
                    <p class="text-muted mb-3">Check the documents you want to generate:</p>

                    <div class="row">
                        {{-- MOA --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="templates[]" 
                                               value="Training Agreement (MOA)" id="moa" {{ old('templates') && in_array('Training Agreement (MOA)', old('templates', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="moa">
                                            <strong>Memorandum of Agreement (MOA)</strong>
                                            <br>
                                            <small class="text-muted">Legal agreement between College, Student, and Company</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Endorsement Letter --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="templates[]" 
                                               value="Endorsement Letter" id="endorsement" {{ old('templates') && in_array('Endorsement Letter', old('templates', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="endorsement">
                                            <strong>Endorsement Letter</strong>
                                            <br>
                                            <small class="text-muted">NBI Endorsement Letter from Dean to Host Company</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Communication Letter (Single) --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="templates[]" 
                                               value="Communication Letter (Single)" id="comm_single" {{ old('templates') && in_array('Communication Letter (Single)', old('templates', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="comm_single">
                                            <strong>Communication Letter (Single)</strong>
                                            <br>
                                            <small class="text-muted">Communication Letter for Single Student</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Communication Letter (Group) --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="templates[]" 
                                               value="Communication Letter (Group)" id="comm_group" {{ old('templates') && in_array('Communication Letter (Group)', old('templates', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="comm_group">
                                            <strong>Communication Letter (Group)</strong>
                                            <br>
                                            <small class="text-muted">Communication Letter for Group of Students</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('templates')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <hr class="my-4">

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary" {{ !$isComplete ? 'disabled' : '' }}>
                        <i class="bi bi-download"></i> Generate Selected Documents
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Additional Info --}}
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-info-circle"></i> Document Information</h6>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Why do I need these documents?</strong></p>
            <ul class="mb-0">
                <li><strong>MOA:</strong> Establishes the terms and conditions of your OJT with the host company</li>
                <li><strong>Endorsement Letter:</strong> Formal endorsement from the college to your host company</li>
                <li><strong>Communication Letter:</strong> Additional communication document for your OJT requirements</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .card.border {
        border: 1px solid #dee2e6 !important;
    }
    
    .form-check {
        padding-left: 0;
    }
    
    .form-check-input {
        margin-top: 4px;
        margin-right: 10px;
    }
    
    .form-check-label {
        cursor: pointer;
        margin-bottom: 0;
    }
</style>
@endsection
