@extends('layouts.app')

@section('title', 'Reports & Export')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Reports & Export</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Reports & Export</h1>
    <p class="page-subtitle">Generate and export OJT trainee records and evaluation reports</p>
</div>

{{-- Quick Reports --}}
<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:18px; margin-bottom:28px;">
    <div class="card" style="cursor:pointer;">
        <div class="card-body" style="display:flex; align-items:center; gap:16px;">
            <div class="stat-icon purple">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <div style="font-weight:600; font-size:0.92rem; color:var(--gray-800);">Trainee Summary Report</div>
                <div style="font-size:0.78rem; color:var(--gray-500); margin-top:2px;">Complete list of all OJT trainees with placement</div>
            </div>
        </div>
    </div>
    <div class="card" style="cursor:pointer;">
        <div class="card-body" style="display:flex; align-items:center; gap:16px;">
            <div class="stat-icon green">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-weight:600; font-size:0.92rem; color:var(--gray-800);">Profile Completion Report</div>
                <div style="font-size:0.78rem; color:var(--gray-500); margin-top:2px;">Students with completed OJT profile information</div>
            </div>
        </div>
    </div>
    <div class="card" style="cursor:pointer;">
        <div class="card-body" style="display:flex; align-items:center; gap:16px;">
            <div class="stat-icon amber">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div style="font-weight:600; font-size:0.92rem; color:var(--gray-800);">Evaluation Summary</div>
                <div style="font-size:0.78rem; color:var(--gray-500); margin-top:2px;">Supervisor evaluations received by trainees</div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Report Generator --}}
<div class="card mb-3">
    <div class="card-header">
        <h2 class="card-title">Generate Custom Report</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.reports.generate') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Report Type <span class="required">*</span></label>
                    <select name="report_type" class="form-select" required>
                        <option value="">Select Report Type</option>
                        <option value="trainees_by_course">All Trainees by Course</option>
                        <option value="profile_completion">OJT Profile Completion Status</option>
                        <option value="evaluation_summary">Evaluation Summary by Supervisor</option>
                        <option value="trainee_placement">Trainee Placement Report</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Export Format <span class="required">*</span></label>
                    <select name="export_format" class="form-select" required>
                        <option value="">Select Format</option>
                        <option value="pdf">PDF Document</option>
                        <option value="xlsx">Excel Spreadsheet (.xlsx)</option>
                        <option value="csv">CSV File</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Course Filter</label>
                    <select name="course_filter" class="form-select">
                        <option value="">All Courses</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Academic Year</label>
                    <input type="text" name="academic_year" class="form-input" placeholder="e.g., 2024-2025">
                </div>
                <div class="form-group">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-input">
                </div>
            </div>
            <div class="form-actions" style="margin-top:20px;">
                <button type="reset" class="btn btn-secondary">Clear</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg>
                    Generate & Download
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Profile Completion Rate --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">OJT Profile Completion by Course</h2>
    </div>
    <div class="card-body">
        <div style="display:flex; flex-direction:column; gap:18px;">
            @php
                // Calculate profile completion rates  
                $bsit_total = \App\Models\User::where('role', 'student')->whereHas('ojtInfo', function($q) { $q->where('course', 'BSIT'); })->count();
                $bsit_complete = \App\Models\User::where('role', 'student')->whereHas('ojtInfo', function($q) { $q->where('course', 'BSIT')->whereNotNull('student_number')->whereNotNull('company_name'); })->count();
                $bsit_percent = $bsit_total > 0 ? round(($bsit_complete / $bsit_total) * 100) : 0;
                
                $bscs_total = \App\Models\User::where('role', 'student')->whereHas('ojtInfo', function($q) { $q->where('course', 'BSCS'); })->count();
                $bscs_complete = \App\Models\User::where('role', 'student')->whereHas('ojtInfo', function($q) { $q->where('course', 'BSCS')->whereNotNull('student_number')->whereNotNull('company_name'); })->count();
                $bscs_percent = $bscs_total > 0 ? round(($bscs_complete / $bscs_total) * 100) : 0;
            @endphp
            <div>
                <div class="d-flex justify-between items-center mb-1">
                    <span class="fw-600" style="font-size:0.88rem;">BS Information Technology (BSIT)</span>
                    <span class="text-small fw-600">{{ $bsit_complete }} / {{ $bsit_total }} students · <span style="color:var(--success);">{{ $bsit_percent }}%</span></span>
                </div>
                <div class="progress-bar" style="height:10px;">
                    <div class="progress-fill green" style="width:{{ $bsit_percent }}%;"></div>
                </div>
            </div>
            <div>
                <div class="d-flex justify-between items-center mb-1">
                    <span class="fw-600" style="font-size:0.88rem;">BS Computer Science (BSCS)</span>
                    <span class="text-small fw-600">{{ $bscs_complete }} / {{ $bscs_total }} students · <span style="color:var(--purple-600);">{{ $bscs_percent }}%</span></span>
                </div>
                <div class="progress-bar" style="height:10px;">
                    <div class="progress-fill" style="width:{{ $bscs_percent }}%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
