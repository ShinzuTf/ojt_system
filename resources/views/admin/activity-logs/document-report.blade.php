@extends('layouts.app')

@section('title', 'Document Activity Report')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <a href="{{ route('admin.activity-logs') }}">Activity Logs</a>
    <span>›</span>
    <span class="current">Document Report</span>
@endsection

@section('content')
<div class="page-header-row mb-3">
    <div class="page-header">
        <h1 class="page-title">Document Activity Report</h1>
        <p class="page-subtitle">Document generation, submission, and approval tracking</p>
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
        <form method="GET" action="{{ route('admin.reports.documents') }}" style="display:flex; gap:16px; align-items:flex-end;">
            <div>
                <label for="from_date" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div>
                <label for="to_date" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.reports.documents') }}" class="btn btn-ghost btn-sm">Reset</a>
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
            <div style="padding:16px; background:var(--blue-50); border-radius:8px; border-left:3px solid var(--blue-500);">
                <div style="color:var(--gray-600); font-size:0.9rem; margin-bottom:6px;">{{ str_replace('_', ' ', ucfirst($stat->activity)) }}</div>
                <div style="font-size:1.8rem; font-weight:700; color:var(--blue-600);">{{ $stat->count }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Document Activities Table --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Document Activities</h2>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Activity</th>
                        <th>Performed By</th>
                        <th>Student</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr>
                        <td>
                            <span class="monospace" style="font-size:0.84rem;">{{ $doc->created_at->format('M d, Y H:i:s') }}</span>
                        </td>
                        <td>
                            <span class="badge" style="background:var(--blue-100); color:var(--blue-700);">
                                {{ str_replace('_', ' ', ucfirst($doc->activity)) }}
                            </span>
                        </td>
                        <td>
                            @if($doc->user)
                                <div style="font-size:0.9rem;">
                                    <strong>{{ $doc->user->short_name }}</strong>
                                    <div style="color:var(--gray-400); font-size:0.84rem;">{{ $doc->user->email }}</div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($doc->targetUser)
                                <div style="font-size:0.9rem;">
                                    <strong>{{ $doc->targetUser->short_name }}</strong>
                                    <div style="color:var(--gray-400); font-size:0.84rem;">{{ $doc->targetUser->email }}</div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($doc->data)
                                <span class="monospace" style="font-size:0.84rem; color:var(--gray-600);">
                                    @if(isset($doc->data['document_type']))
                                        {{ $doc->data['document_type'] }}
                                    @else
                                        View
                                    @endif
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 24px; color:var(--gray-400);">No document activities found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($documents->hasPages())
<div style="display:flex; justify-content:center; margin-top:24px; gap:8px;">
    @if($documents->onFirstPage())
        <button class="btn btn-sm" disabled style="opacity:0.5;">← Previous</button>
    @else
        <a href="{{ $documents->previousPageUrl() }}" class="btn btn-sm">← Previous</a>
    @endif

    @foreach($documents->getUrlRange(max(1, $documents->currentPage() - 2), min($documents->lastPage(), $documents->currentPage() + 2)) as $page => $url)
        @if($page == $documents->currentPage())
            <button class="btn btn-sm active" style="background:var(--blue-600); color:white;">{{ $page }}</button>
        @else
            <a href="{{ $url }}" class="btn btn-sm">{{ $page }}</a>
        @endif
    @endforeach

    @if($documents->hasMorePages())
        <a href="{{ $documents->nextPageUrl() }}" class="btn btn-sm">Next →</a>
    @else
        <button class="btn btn-sm" disabled style="opacity:0.5;">Next →</button>
    @endif
</div>
@endif

@endsection
