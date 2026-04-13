@extends('layouts.app')

@section('title', 'Activity Logs')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Activity Logs</span>
@endsection

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1 class="page-title">Activity Logs</h1>
        <p class="page-subtitle">Audit trail of system activities and user actions</p>
    </div>
    <div class="page-header-actions">
        <form method="GET" action="{{ route('admin.activity-logs.export') }}" style="display:inline;">
            @foreach(request()->query() as $key => $value)
                @if($key !== 'page')
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <button type="submit" class="btn btn-ghost">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2m0 0v-8"/></svg>
                Export CSV
            </button>
        </form>
    </div>
</div>

{{-- Filters Section --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.activity-logs') }}" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="activity">Activity Type</label>
                    <select name="activity" id="activity" class="form-select">
                        <option value="">All Activities</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type }}" {{ request('activity') === $type ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="module">Module</label>
                    <select name="module" id="module" class="form-select">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="user_id">User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->short_name }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="days">Date Range</label>
                    <select name="days" id="days" class="form-select">
                        <option value="">Custom Range</option>
                        <option value="1" {{ request('days') === '1' ? 'selected' : '' }}>Last 24 Hours</option>
                        <option value="7" {{ request('days') === '7' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30" {{ request('days') === '30' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90" {{ request('days') === '90' ? 'selected' : '' }}>Last 90 Days</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="from_date">From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="filter-group">
                    <label for="to_date">To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="filter-actions" style="display:flex; align-items:flex-end; gap:8px;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-ghost btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Activity Logs Table --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Activity</th>
                        <th>Module</th>
                        <th>Affected User</th>
                        <th>Status</th>
                        <th>IP Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        <td>
                            <span class="monospace" style="font-size:0.84rem;">{{ $activity->created_at->format('M d, Y H:i:s') }}</span>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $activity->user?->short_name ?? 'System' }}</strong>
                                <div style="font-size:0.84rem; color:var(--gray-400);">{{ $activity->user?->email ?? '—' }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background:var(--blue-100); color:var(--blue-700);">
                                {{ str_replace('_', ' ', ucfirst($activity->activity)) }}
                            </span>
                        </td>
                        <td><span class="monospace" style="font-size:0.84rem;">{{ ucfirst($activity->module) }}</span></td>
                        <td>
                            @if($activity->targetUser)
                                <div>
                                    <strong>{{ $activity->targetUser->short_name }}</strong>
                                    <div style="font-size:0.84rem; color:var(--gray-400);">{{ $activity->targetUser->email }}</div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($activity->status === 'success')
                                <span class="badge badge-approved"><span class="badge-dot"></span> Success</span>
                            @elseif($activity->status === 'failed')
                                <span class="badge badge-rejected"><span class="badge-dot"></span> Failed</span>
                            @else
                                <span class="badge badge-pending"><span class="badge-dot"></span> Pending</span>
                            @endif
                        </td>
                        <td><span class="monospace" style="font-size:0.84rem;">{{ $activity->ip_address ?? '—' }}</span></td>
                        <td>
                            <a href="{{ route('admin.activity-logs.show', $activity->id) }}" class="btn btn-ghost btn-sm">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7C7.523 19 3.732 16.057 2.458 12z"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding: 24px; color:var(--gray-400);">No activities found.</td>
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
