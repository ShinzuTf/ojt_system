@extends('layouts.app')

@section('title', 'Admin Dashboard - OJT System')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-number">{{ $totalUsers ?? 0 }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
                <div class="stat-number">{{ $usersByRole['student'] ?? 0 }}</div>
                <div class="stat-label">Students</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-briefcase"></i></div>
                <div class="stat-number">{{ $activePlacements ?? 0 }}</div>
                <div class="stat-label">Active Placements</div>
            </div>
        </div>
    </div>

    <!-- System Monitoring -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-activity"></i> System Activity</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">DTR Entries (Today)</p>
                        <p><strong>{{ $dtrToday ?? 0 }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Reports Submitted (Today)</p>
                        <p><strong>{{ $reportsToday ?? 0 }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Issues Reported (Today)</p>
                        <p><strong>{{ $issuesReportedToday ?? 0 }}</strong></p>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-journal-text"></i> Recent Activity Log</h5>
                </div>
                <div class="card-body">
                    @if($recentLogs && $recentLogs->count())
                        <div class="timeline">
                            @foreach($recentLogs->whereNotIn('user.role', ['admin'])->take(5) as $log)
                                <div class="timeline-item mb-2 pb-2" style="border-bottom: 1px solid #dee2e6;">
                                    <p class="mb-0 small">
                                        <strong>{{ $log->user->fname ?? 'System' }} {{ $log->user->lname ?? '' }}</strong>
                                    </p>
                                    <small class="text-muted">{{ $log->action ?? 'No action' }}</small>
                                    <br>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-primary btn-sm mt-3">View All Logs</a>
                    @else
                        <div class="empty-state">
                            <p>No activity yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
