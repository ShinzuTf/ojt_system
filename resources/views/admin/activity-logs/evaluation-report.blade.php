@extends('layouts.app')

@section('title', 'Evaluation Activity Report')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <a href="{{ route('admin.activity-logs') }}">Activity Logs</a>
    <span>›</span>
    <span class="current">Evaluation Report</span>
@endsection

@section('content')
<div class="page-header-row mb-3">
    <div class="page-header">
        <h1 class="page-title">Evaluation Activity Report</h1>
        <p class="page-subtitle">Student evaluation creation, submission, and updates</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.activity-logs') }}" class="btn btn-ghost">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>
</div>

{{-- Date Filter --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.evaluations') }}" style="display:flex; gap:16px; align-items:flex-end;">
            <div>
                <label for="from_date" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div>
                <label for="to_date" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.reports.evaluations') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </div>
</div>

{{-- Activity Statistics --}}
@if($stats->count() > 0)
<div class="card mb-3">
    <div class="card-header">
        <h2 class="card-title">Activity Summary (Last 30 Days)</h2>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
            @foreach($stats as $stat)
            <div style="padding:16px; background:var(--green-50); border-radius:8px; border-left:3px solid var(--green-500);">
                <div style="color:var(--gray-600); font-size:0.9rem; margin-bottom:6px;">{{ str_replace('_', ' ', ucfirst($stat->activity)) }}</div>
                <div style="font-size:1.8rem; font-weight:700; color:var(--green-600);">{{ $stat->count }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Evaluation Activities Table --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Evaluation Activities</h2>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Activity</th>
                        <th>Evaluator</th>
                        <th>Student</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $eval)
                    <tr>
                        <td>
                            <span class="monospace" style="font-size:0.84rem;">{{ $eval->created_at->format('M d, Y H:i:s') }}</span>
                        </td>
                        <td>
                            <span class="badge" style="background:var(--green-100); color:var(--green-700);">
                                {{ str_replace('_', ' ', ucfirst($eval->activity)) }}
                            </span>
                        </td>
                        <td>
                            @if($eval->user)
                                <div style="font-size:0.9rem;">
                                    <strong>{{ $eval->user->short_name }}</strong>
                                    <div style="color:var(--gray-400); font-size:0.84rem;">{{ $eval->user->role }}</div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($eval->targetUser)
                                <div style="font-size:0.9rem;">
                                    <strong>{{ $eval->targetUser->short_name }}</strong>
                                    <div style="color:var(--gray-400); font-size:0.84rem;">{{ $eval->targetUser->email }}</div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($eval->status === 'success')
                                <span class="badge badge-approved"><span class="badge-dot"></span> Completed</span>
                            @elseif($eval->status === 'pending')
                                <span class="badge badge-pending"><span class="badge-dot"></span> Pending</span>
                            @else
                                <span class="badge badge-rejected"><span class="badge-dot"></span> Failed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 24px; color:var(--gray-400);">No evaluation activities found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($evaluations->hasPages())
<div style="display:flex; justify-content:center; margin-top:24px; gap:8px;">
    @if($evaluations->onFirstPage())
        <button class="btn btn-sm" disabled style="opacity:0.5;">← Previous</button>
    @else
        <a href="{{ $evaluations->previousPageUrl() }}" class="btn btn-sm">← Previous</a>
    @endif

    @foreach($evaluations->getUrlRange(max(1, $evaluations->currentPage() - 2), min($evaluations->lastPage(), $evaluations->currentPage() + 2)) as $page => $url)
        @if($page == $evaluations->currentPage())
            <button class="btn btn-sm active" style="background:var(--blue-600); color:white;">{{ $page }}</button>
        @else
            <a href="{{ $url }}" class="btn btn-sm">{{ $page }}</a>
        @endif
    @endforeach

    @if($evaluations->hasMorePages())
        <a href="{{ $evaluations->nextPageUrl() }}" class="btn btn-sm">Next →</a>
    @else
        <button class="btn btn-sm" disabled style="opacity:0.5;">Next →</button>
    @endif
</div>
@endif

@endsection
