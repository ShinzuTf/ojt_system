@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <span class="current">Admin Dashboard</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

{{-- Minimal Stat Cards (2 only) --}}
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197v-1"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Total Students</div>
            <div class="stat-value">{{ $totalStudents }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-content">
            <div class="stat-label">Profile Completed</div>
            <div class="stat-value">{{ $approvedTrainees }}</div>
        </div>
    </div>
</div>

{{-- Recent Students with Complete Profiles --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Students</h2>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Student No.</th>
                        <th>Course</th>
                        <th>Company</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complianceSummary as $student)
                    <tr>
                        <td><strong>{{ $student->full_name }}</strong></td>
                        <td>{{ $student->ojtInfo->student_number ?? '—' }}</td>
                        <td>{{ $student->ojtInfo->course ?? '—' }}</td>
                        <td>{{ $student->ojtInfo->company_name ?? '—' }}</td>
                        <td>
                            @if($student->ojtInfo && $student->ojtInfo->student_number && $student->ojtInfo->company_name)
                                <span class="badge badge-approved"><span class="badge-dot"></span> Profile Complete</span>
                            @else
                                <span class="badge badge-pending"><span class="badge-dot"></span> Incomplete</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 24px; color:var(--gray-400);">No students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
