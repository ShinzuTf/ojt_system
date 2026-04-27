@extends('layouts.app')

@section('title', 'Student Dashboard - OJT System')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="bi bi-clock-history"></i></div>
                <div class="stat-content">
                    <div class="stat-label">DTR Entries</div>
                    <div class="stat-value">{{ $dtrCount ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="bi bi-file-text"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Reports</div>
                    <div class="stat-value">{{ $reportCount ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon amber"><i class="bi bi-star"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Evaluations</div>
                    <div class="stat-value">{{ $evaluationCount ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon red"><i class="bi bi-exclamation-circle"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Issues</div>
                    <div class="stat-value">{{ $issueCount ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Current Placement Status -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0"><i class="bi bi-briefcase-fill"></i> Current OJT Placement</h5>
                </div>
                <div class="card-body">
                    @if($placement)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Company:</strong></p>
                                <p class="text-muted">{{ $placement->company_name }}</p>
                                <p class="mb-2 mt-3"><strong>Position:</strong></p>
                                <p class="text-muted">{{ $placement->position ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Supervisor:</strong></p>
                                <p class="text-muted">{{ $placement->supervisor->name ?? 'N/A' }}</p>
                                <p class="mb-2 mt-3"><strong>Duration:</strong></p>
                                <p class="text-muted">{{ $placement->start_date->format('M d, Y') }} - {{ $placement->end_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Progress -->
                        <h6 class="mb-3">Placement Progress</h6>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Hours Completed</span>
                                <span><strong>{{ $hoursLogged ?? 0 }} / {{ $placement->total_required_hours ?? 480 }} hrs</strong></span>
                            </div>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(($hoursLogged ?? 0) / ($placement->total_required_hours ?? 480) * 100, 100) }}%" 
                                     aria-valuenow="{{ min(($hoursLogged ?? 0) / ($placement->total_required_hours ?? 480) * 100, 100) }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format(($hoursLogged ?? 0) / ($placement->total_required_hours ?? 480) * 100, 0) }}%
                                </div>
                            </div>
                        </div>

                        <div class="row text-center mt-4">
                            <div class="col-md-4">
                                <p class="text-muted mb-1 small">Days Elapsed</p>
                                <p><strong style="font-size: 24px;">{{ $placement->getDaysElapsed() }}</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1 small">Days Remaining</p>
                                <p><strong style="font-size: 24px;">{{ $placement->getDaysRemaining() }}</strong></p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1 small">Avg Hours/Day</p>
                                <p><strong style="font-size: 24px;">{{ number_format(($hoursLogged ?? 0) / max($placement->getDaysElapsed(), 1), 1) }}</strong></p>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-briefcase" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                            <p class="text-muted"><strong>No active OJT placement</strong></p>
                            <small class="text-secondary">Contact your coordinator to begin your OJT</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Today's DTR -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0"><i class="bi bi-clock-fill"></i> Today's Time Record</h5>
                </div>
                <div class="card-body">
                    @if($todayDtr)
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Time In</p>
                            <p><strong style="font-size: 18px;">{{ $todayDtr->time_in->format('H:i') }}</strong></p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Time Out</p>
                            <p><strong style="font-size: 18px;">{{ $todayDtr->time_out ? $todayDtr->time_out->format('H:i') : 'Pending' }}</strong></p>
                        </div>
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Hours Worked</p>
                            <p><strong style="font-size: 18px;">{{ $todayDtr->hours_worked ?? 0 }} hrs</strong></p>
                        </div>
                        <p>Status: 
                            @if($todayDtr->status === 'verified')
                                <span class="badge bg-success">{{ ucfirst($todayDtr->status) }}</span>
                            @elseif($todayDtr->status === 'pending')
                                <span class="badge bg-warning">{{ ucfirst($todayDtr->status) }}</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($todayDtr->status) }}</span>
                            @endif
                        </p>
                    @else
                        <div class="empty-state" style="padding: 30px 0;">
                            <i class="bi bi-plus-circle" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                            <p class="text-muted"><strong>No DTR entry today</strong></p>
                            <a href="{{ route('student.dtr.create') }}" class="btn btn-primary btn-sm mt-2">Create DTR Entry</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Recent Reports</h5>
                </div>
                <div class="card-body">
                    @if($recentReports && $recentReports->count())
                        @foreach($recentReports->take(5) as $report)
                            <div class="mb-3 pb-3" style="border-bottom: 1px solid #dee2e6;">
                                <p class="mb-1"><strong>{{ $report->title }}</strong></p>
                                <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                <p class="mt-2 mb-0">
                                    @if($report->status === 'approved')
                                        <span class="badge bg-success">{{ ucfirst($report->status) }}</span>
                                    @elseif($report->status === 'submitted')
                                        <span class="badge bg-info">{{ ucfirst($report->status) }}</span>
                                    @elseif($report->status === 'rejected')
                                        <span class="badge bg-danger">{{ ucfirst($report->status) }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($report->status) }}</span>
                                    @endif
                                </p>
                            </div>
                        @endforeach
                        <a href="{{ route('student.reports.index') }}" class="btn btn-outline-primary btn-sm mt-3">View All Reports</a>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-file-earmark" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3"><strong>No reports yet</strong></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0"><i class="bi bi-exclamation-lg"></i> Open Issues</h5>
                </div>
                <div class="card-body">
                    @if($openIssues && $openIssues->count())
                        @foreach($openIssues->take(5) as $issue)
                            <div class="mb-3 pb-3" style="border-bottom: 1px solid #dee2e6;">
                                <p class="mb-1"><strong>{{ ucfirst($issue->issue_type) }}</strong></p>
                                <p class="text-muted small mb-2">{{ substr($issue->description, 0, 80) }}...</p>
                                @if($issue->status === 'resolved')
                                    <span class="badge bg-success">{{ ucfirst($issue->status) }}</span>
                                @elseif($issue->status === 'acknowledged')
                                    <span class="badge bg-info">{{ ucfirst($issue->status) }}</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($issue->status) }}</span>
                                @endif
                            </div>
                        @endforeach
                        <a href="{{ route('student.issues.index') }}" class="btn btn-outline-primary btn-sm mt-2">View All Issues</a>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-3"><strong>No open issues</strong></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
