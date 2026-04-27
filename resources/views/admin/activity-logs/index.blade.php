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
            <div class="filters-grid" style="display: flex; flex-wrap: nowrap; gap: 10px; align-items: flex-end; overflow-x: auto; padding-bottom: 5px;">
                <div class="filter-group" style="flex: 0 0 auto; width: 130px;">
                    <label for="activity" style="font-size: 12px;">Activity Type</label>
                    <select name="activity" id="activity" class="form-select" style="font-size: 13px;">
                        <option value="">All Activities</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type }}" {{ request('activity') === $type ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group" style="flex: 0 0 auto; width: 110px;">
                    <label for="module" style="font-size: 12px;">Module</label>
                    <select name="module" id="module" class="form-select" style="font-size: 13px;">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group" style="flex: 0 0 auto; width: 120px;">
                    <label for="user_id" style="font-size: 12px;">User</label>
                    <select name="user_id" id="user_id" class="form-select" style="font-size: 13px;">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->short_name }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group" style="flex: 0 0 auto; width: 100px;">
                    <label for="status" style="font-size: 12px;">Status</label>
                    <select name="status" id="status" class="form-select" style="font-size: 13px;">
                        <option value="">All Statuses</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="filter-group" style="flex: 0 0 auto; width: 120px;">
                    <label for="days" style="font-size: 12px;">Date Range</label>
                    <select name="days" id="days" class="form-select" style="font-size: 13px;">
                        <option value="">Custom Range</option>
                        <option value="1" {{ request('days') === '1' ? 'selected' : '' }}>Last 24 Hours</option>
                        <option value="7" {{ request('days') === '7' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30" {{ request('days') === '30' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="90" {{ request('days') === '90' ? 'selected' : '' }}>Last 90 Days</option>
                    </select>
                </div>

                <div class="filter-group" style="flex: 0 0 auto; width: 105px;">
                    <label for="from_date" style="font-size: 12px;">From</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" style="font-size: 13px;" value="{{ request('from_date') }}">
                </div>

                <div class="filter-group" style="flex: 0 0 auto; width: 105px;">
                    <label for="to_date" style="font-size: 12px;">To</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" style="font-size: 13px;" value="{{ request('to_date') }}">
                </div>

                <div class="filter-actions" style="display:flex; align-items:flex-end; gap:6px; flex: 0 0 auto;">
                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 6px 12px; font-size: 13px;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-ghost btn-sm" style="padding: 6px 12px; font-size: 13px;">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Activity Logs Table --}}
<div class="card" style="border: 1px solid #ddd; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
    <div style="background: #f5f5f5; padding: 16px; border-bottom: 1px solid #ddd;">
        <h3 style="margin: 0; font-size: 14px; font-weight: 600; color: #333;">Activity Audit Log</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; line-height: 1.6;">
                <thead>
                    <tr style="background: #f9f9f9; border-bottom: 2px solid #999;">
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600; color: #333; border-right: 1px solid #ddd;">Timestamp</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600; color: #333; border-right: 1px solid #ddd;">User</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600; color: #333; border-right: 1px solid #ddd;">Activity</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600; color: #333; border-right: 1px solid #ddd;">Affected User</th>
                        <th style="padding: 12px 14px; text-align: left; font-weight: 600; color: #333;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr style="border-bottom: 1px solid #e0e0e0;">
                        <td style="padding: 10px 14px; border-right: 1px solid #e8e8e8; color: #555; font-family: 'Courier New', monospace; font-size: 12px;">{{ $activity->created_at->format('M d, Y H:i:s') }}</td>
                        <td style="padding: 10px 14px; border-right: 1px solid #e8e8e8; color: #333;">
                            <div style="font-weight: 500;">{{ $activity->user?->short_name ?? 'System' }}</div>
                            <div style="font-size: 11px; color: #999;">{{ $activity->user?->email ?? '—' }}</div>
                        </td>
                        <td style="padding: 10px 14px; border-right: 1px solid #e8e8e8; color: #333; text-transform: capitalize;">{{ str_replace('_', ' ', $activity->activity) }}</td>
                        <td style="padding: 10px 14px; border-right: 1px solid #e8e8e8; color: #333;">
                            @if($activity->targetUser)
                                <div style="font-weight: 500;">{{ $activity->targetUser->short_name }}</div>
                                <div style="font-size: 11px; color: #999;">{{ $activity->targetUser->email }}</div>
                            @else
                                <span style="color: #999;">—</span>
                            @endif
                        </td>
                        <td style="padding: 10px 14px; color: #333; font-weight: 500;">
                            @if($activity->status === 'success')
                                <span style="color: #228B22;">✓ Success</span>
                            @elseif($activity->status === 'failed')
                                <span style="color: #D32F2F;">✕ Failed</span>
                            @else
                                <span style="color: #F57C00;">◐ Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: #999;">No activities found.</td>
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
