@extends('layouts.app')

@section('title', 'Login Activity Report')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <a href="{{ route('admin.activity-logs') }}">Activity Logs</a>
    <span>›</span>
    <span class="current">Login Report</span>
@endsection

@section('content')
<div class="page-header-row mb-3">
    <div class="page-header">
        <h1 class="page-title">Login Activity Report</h1>
        <p class="page-subtitle">User authentication attempts and login patterns</p>
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
        <form method="GET" action="{{ route('admin.reports.logins') }}" style="display:flex; gap:16px; align-items:flex-end;">
            <div>
                <label for="from_date" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div>
                <label for="to_date" style="display:block; margin-bottom:6px; font-size:0.9rem; font-weight:600;">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.reports.logins') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </div>
</div>

{{-- Daily Statistics --}}
@if($loginStats->count() > 0)
<div class="card mb-3">
    <div class="card-header">
        <h2 class="card-title">Daily Login Statistics (Last 30 Days)</h2>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Attempts</th>
                        <th>Successful</th>
                        <th>Failed</th>
                        <th>Success Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loginStats as $stat)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($stat->date)->format('F d, Y') }}</strong></td>
                        <td>{{ $stat->total }}</td>
                        <td><span class="badge badge-approved">{{ $stat->successful }}</span></td>
                        <td><span class="badge badge-rejected">{{ $stat->total - $stat->successful }}</span></td>
                        <td>
                            @php
                                $successRate = ($stat->successful / $stat->total) * 100;
                            @endphp
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:100%; max-width:100px; height:24px; background:var(--gray-200); border-radius:4px; overflow:hidden; position:relative;">
                                    <div style="width:{{ $successRate }}%; height:100%; background:var(--green-500); transition:width 0.3s;"></div>
                                </div>
                                <span style="font-weight:600;">{{ round($successRate, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Login Attempts Table --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Login Attempts</h2>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User Email</th>
                        <th>User Role</th>
                        <th>Status</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logins as $login)
                    <tr>
                        <td>
                            <span class="monospace" style="font-size:0.84rem;">{{ $login->created_at->format('M d, Y H:i:s') }}</span>
                        </td>
                        <td>
                            @if($login->data && isset($login->data['email']))
                                {{ $login->data['email'] }}
                            @else
                                {{ $login->user?->email ?? '—' }}
                            @endif
                        </td>
                        <td>
                            @if($login->data && isset($login->data['user_role']))
                                <span class="badge" style="background:var(--purple-100); color:var(--purple-700);">{{ ucfirst($login->data['user_role']) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($login->status === 'success')
                                <span class="badge badge-approved"><span class="badge-dot"></span> Success</span>
                            @else
                                <span class="badge badge-rejected"><span class="badge-dot"></span> Failed</span>
                            @endif
                        </td>
                        <td><span class="monospace" style="font-size:0.84rem;">{{ $login->ip_address ?? '—' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 24px; color:var(--gray-400);">No login attempts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination --}}
@if($logins->hasPages())
<div style="display:flex; justify-content:center; margin-top:24px; gap:8px;">
    @if($logins->onFirstPage())
        <button class="btn btn-sm" disabled style="opacity:0.5;">← Previous</button>
    @else
        <a href="{{ $logins->previousPageUrl() }}" class="btn btn-sm">← Previous</a>
    @endif

    @foreach($logins->getUrlRange(max(1, $logins->currentPage() - 2), min($logins->lastPage(), $logins->currentPage() + 2)) as $page => $url)
        @if($page == $logins->currentPage())
            <button class="btn btn-sm active" style="background:var(--blue-600); color:white;">{{ $page }}</button>
        @else
            <a href="{{ $url }}" class="btn btn-sm">{{ $page }}</a>
        @endif
    @endforeach

    @if($logins->hasMorePages())
        <a href="{{ $logins->nextPageUrl() }}" class="btn btn-sm">Next →</a>
    @else
        <button class="btn btn-sm" disabled style="opacity:0.5;">Next →</button>
    @endif
</div>
@endif

@endsection
