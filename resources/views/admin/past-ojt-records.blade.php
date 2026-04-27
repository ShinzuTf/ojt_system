@extends('layouts.app')

@section('title', 'Past OJT Records')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <a href="{{ route('admin.students') }}">Student Records</a>
    <span>›</span>
    <span class="current">Past OJT Records</span>
@endsection

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1 class="page-title">Past OJT Records</h1>
        <p class="page-subtitle">Archived OJT records for {{ $student->full_name }}</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.students') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            Back to Students
        </a>
    </div>
</div>

{{-- Student Info Card --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Student Name</label>
                <div style="font-weight:600;">{{ $student->full_name }}</div>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <div style="font-weight:600;">{{ $student->email }}</div>
            </div>
            <div class="form-group">
                <label class="form-label">Total Past Records</label>
                <div style="font-weight:600;">{{ $pastRecords->count() }}</div>
            </div>
            <div class="form-group">
                <label class="form-label">Current Status</label>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width:10px; height:10px; border-radius:50%; background:{{ $student->status === 'active' ? 'var(--success)' : 'var(--gray-400)' }};"></span>
                    <span style="font-weight:600; text-transform:capitalize;">{{ $student->status }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Past OJT Records Table --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        @if($pastRecords->count() > 0)
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Course / Year</th>
                            <th>OJT Period</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Archived Date</th>
                            <th>Archived By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastRecords as $record)
                        <tr>
                            <td>
                                <div style="font-weight:600;">{{ $record->company_name ?? '—' }}</div>
                                @if($record->company_email)
                                    <div class="text-small" style="color:var(--gray-400);">{{ $record->company_email }}</div>
                                @endif
                            </td>
                            <td>
                                @if($record->course)
                                    <div>{{ $record->course }}</div>
                                    <div class="text-small" style="color:var(--gray-400);">{{ $record->year_level ? ordinal($record->year_level) . ' Year' : '' }}</div>
                                @else
                                    <span style="color:var(--gray-300);">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-small">
                                    @if($record->ojt_start && $record->ojt_end)
                                        <strong>{{ $record->ojt_start->format('M d, Y') }}</strong> to <strong>{{ $record->ojt_end->format('M d, Y') }}</strong>
                                    @else
                                        <span style="color:var(--gray-300);">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $record->rendered_hours ?? 0 }}</strong> / {{ $record->required_hours ?? 0 }} hrs
                                    @if($record->required_hours)
                                        <div class="text-small" style="color:var(--gray-400);">
                                            {{ $record->progress_percent }}% complete
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="display:inline-block; padding:4px 10px; border-radius:4px; font-size:0.8rem; font-weight:600; background:{{ $record->ojt_status === 'completed' ? 'var(--success-light)' : 'var(--gray-100)' }}; color:{{ $record->ojt_status === 'completed' ? 'var(--success)' : 'var(--gray-600)' }}; text-transform:capitalize;">
                                    {{ $record->ojt_status }}
                                </span>
                            </td>
                            <td>
                                {{ $record->archived_at->format('M d, Y h:i A') }}
                            </td>
                            <td>
                                @if($record->archivedBy)
                                    <div style="font-weight:600;">{{ $record->archivedBy->short_name }}</div>
                                    <div class="text-small" style="color:var(--gray-400);">{{ $record->archivedBy->email }}</div>
                                @else
                                    <span style="color:var(--gray-300);">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding:40px; text-align:center; color:var(--gray-400);">
                <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 16px; opacity:0.5;"><path d="M9 12h6m-6 4h6M7 20h10a2 2 0 002-2V4a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <p style="font-size:0.95rem;">No past OJT records found for this student.</p>
            </div>
        @endif
    </div>
</div>

@if($student->status === 'inactive' && $pastRecords->count() > 0)
<div class="card mt-3" style="background:var(--info-light,#eff6ff); border:1px solid var(--info,#3b82f6);">
    <div class="card-body">
        <div style="display:flex; gap:16px;">
            <div style="color:var(--info,#3b82f6); flex-shrink:0;">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <p style="margin:0; font-weight:600; color:var(--info-dark,#1e40af);">Account Currently Inactive</p>
                <p style="margin:6px 0 0 0; font-size:0.9rem; color:var(--info-dark,#1e40af);">This student can be reactivated for the next OJT semester if they re-enroll.</p>
                <form method="POST" action="{{ route('admin.students.reactivate', $student->id) }}" style="margin-top:12px;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="font-size:0.9rem; padding:6px 14px;">
                        Reactivate Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
