@extends('layouts.app')

@section('title', 'User Management Activity Report')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <a href="{{ route('admin.activity-logs') }}">Activity Logs</a>
    <span>›</span>
    <span class="current">User Management Report</span>
@endsection

@section('content')
<div class="page-header-row mb-3">
    <div class="page-header">
        <h1 class="page-title">User Management Activity Report</h1>
        <p class="page-subtitle">All admin actions on user accounts (coordinators & students)</p>
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
        <form method="GET" action="{{ route('admin.reports.user-management') }}" style="display:flex; gap:16px; align-items:flex-end;">
            <div>
                <label for="from_date" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div>
                <label for="to_date" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.reports.user-management') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </div>
</div>

{{-- Activity Statistics --}}
@if($stats->count() > 0)
<div class="card mb-3">
    <div class="card-header">
        <h2 class="card-title">Admin Activity Summary (Last 30 Days)</h2>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
            @foreach($stats as $stat)
            <div style="padding:16px; background:var(--purple-50); border-radius:8px; border-left:3px solid var(--purple-500);">
                <div style="color:var(--gray-600); font-size:0.9rem; margin-bottom:6px;">{{ str_replace('_', ' ', ucfirst($stat->activity)) }}</div>
                <div style="font-size:1.8rem; font-weight:700; color:var(--purple-600);">{{ $stat->count }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- User Management Activities Table --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Admin Actions</h2>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Action</th>
                        <th>Activity</th>
                        <th>Admin</th>
                        <th>Target User</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        <td>
                            <span class="monospace" style="font-size:0.84rem;">{{ $activity->created_at->format('M d, Y H:i:s') }}</span>
                        </td>
                        <td>
                            @if($activity->action === 'create')
                                <span class="badge badge-approved"><span class="badge-dot"></span> Create</span>
                            @elseif($activity->action === 'update')
                                <span class="badge badge-pending"><span class="badge-dot"></span> Update</span>
                            @elseif($activity->action === 'delete')
                                <span class="badge badge-rejected"><span class="badge-dot"></span> Delete</span>
                            @else
                                <span class="badge">{{ ucfirst($activity->action) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:var(--blue-100); color:var(--blue-700);">
                                {{ str_replace('_', ' ', ucfirst($activity->activity)) }}
                            </span>
                        </td>
                        <td>
                            @if($activity->user)
                                <div style="font-size:0.9rem;">
                                    <strong>{{ $activity->user->short_name }}</strong>
                                    <div style="color:var(--gray-400); font-size:0.84rem;">{{ $activity->user->email }}</div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($activity->targetUser)
                                <div style="font-size:0.9rem;">
                                    <strong>{{ $activity->targetUser->short_name }}</strong>
                                    <div style="color:var(--gray-400); font-size:0.84rem;">{{ $activity->targetUser->email }}</div>
                                    <div style="font-size:0.84rem; margin-top:3px;">
                                        <span class="badge" style="background:var(--purple-100); color:var(--purple-700);">{{ ucfirst($activity->targetUser->role) }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.activity-logs.show', $activity->id) }}" class="btn btn-ghost btn-sm">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7C7.523 19 3.732 16.057 2.458 12z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding: 24px; color:var(--gray-400);">No user management activities found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($activities->hasPages())
<div style="display:flex; justify-content:center; margin-top:24px; gap:8px;">
    @if($activities->onFirstPage())
        <button class="btn btn-sm" disabled style="opacity:0.5;">← Previous</button>
    @else
        <a href="{{ $activities->previousPageUrl() }}" class="btn btn-sm">← Previous</a>
    @endif

    @foreach($activities->getUrlRange(max(1, $activities->currentPage() - 2), min($activities->lastPage(), $activities->currentPage() + 2)) as $page => $url)
        @if($page == $activities->currentPage())
            <button class="btn btn-sm active" style="background:var(--blue-600); color:white;">{{ $page }}</button>
        @else
            <a href="{{ $url }}" class="btn btn-sm">{{ $page }}</a>
        @endif
    @endforeach

    @if($activities->hasMorePages())
        <a href="{{ $activities->nextPageUrl() }}" class="btn btn-sm">Next →</a>
    @else
        <button class="btn btn-sm" disabled style="opacity:0.5;">Next →</button>
    @endif
</div>
@endif

@endsection
