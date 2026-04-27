@extends('layouts.app')

@section('title', 'Coordinator Dashboard - OJT System')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-mortarboard"></i></div>
                <div class="stat-number">{{ $studentCount ?? 0 }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-briefcase"></i></div>
                <div class="stat-number">{{ $activePlacementCount ?? 0 }}</div>
                <div class="stat-label">Active Placements</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-exclamation-lg"></i></div>
                <div class="stat-number">{{ $openIssueCount ?? 0 }}</div>
                <div class="stat-label">Open Issues</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-award"></i></div>
                <div class="stat-number">{{ $certificationCount ?? 0 }}</div>
                <div class="stat-label">Certifications</div>
            </div>
        </div>
    </div>

    <!-- At-Risk Students -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-exclamation-circle"></i> Students Requiring Attention</h5>
                </div>
                <div class="card-body">
                    @if($atRiskStudents && $atRiskStudents->count())
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Company</th>
                                        <th>Hours</th>
                                        <th>Progress</th>
                                        <th>Days Remaining</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($atRiskStudents as $student)
                                        <tr>
                                            <td><strong>{{ $student->name }}</strong></td>
                                            <td>{{ $student->currentPlacement->company_name ?? 'N/A' }}</td>
                                            <td>{{ $student->totalHours ?? 0 }} / {{ $student->currentPlacement->total_required_hours ?? 480 }} hrs</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" style="width: {{ ($student->totalHours ?? 0) / ($student->currentPlacement->total_required_hours ?? 480) * 100 }}%"></div>
                                                </div>
                                            </td>
                                            <td>{{ $student->currentPlacement->getDaysRemaining() ?? 0 }} days</td>
                                            <td>
                                                <a href="{{ route('coordinator.placements.show', $student->currentPlacement->id ?? 0) }}" class="btn btn-sm btn-primary">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <p>All students are on track</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-graph-up"></i> System Overview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Placements On Track</p>
                        <p><strong style="font-size: 20px;">{{ $onTrackCount ?? 0 }}</strong> / {{ $activePlacementCount ?? 0 }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Reports Submitted</p>
                        <p><strong style="font-size: 20px;">{{ $reportCount ?? 0 }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Issues Resolved</p>
                        <p><strong style="font-size: 20px;">{{ $resolvedIssueCount ?? 0 }}</strong></p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Avg Completion %</p>
                        <p><strong style="font-size: 20px;">{{ round($avgCompletion ?? 0) }}%</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Issue Resolution -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-shield-exclamation"></i> Issues Requiring Resolution</h5>
                </div>
                <div class="card-body">
                    @if($pendingIssues && $pendingIssues->count())
                        <div class="timeline">
                            @foreach($pendingIssues->take(5) as $issue)
                                <div class="timeline-item mb-3">
                                    <p class="mb-1"><strong>{{ ucfirst($issue->issue_type) }}</strong> - {{ $issue->student->name }}</p>
                                    <small class="text-muted">{{ $issue->created_at->diffForHumans() }}</small>
                                    <p class="mt-2 mb-0">
                                        <a href="{{ route('coordinator.issues.show', $issue->id) }}" class="btn btn-sm btn-primary">Review</a>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        @if($openIssueCount > 5)
                            <a href="{{ route('coordinator.issues.index') }}" class="btn btn-outline-primary btn-sm mt-3">View All ({{ $openIssueCount }})</a>
                        @endif
                    @else
                        <div class="empty-state">
                            <p>No pending issues</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-award"></i> Pending Certifications</h5>
                </div>
                <div class="card-body">
                    @if($pendingCertifications && $pendingCertifications->count())
                        @foreach($pendingCertifications->take(5) as $cert)
                            <div class="mb-3 pb-3" style="border-bottom: 1px solid #dee2e6;">
                                <p class="mb-1"><strong>{{ $cert->student->name }}</strong></p>
                                <p class="text-muted small mb-2">{{ $cert->placement->company_name }}</p>
                                <p class="mb-2">Rating: <strong>{{ $cert->final_rating }} / 5</strong></p>
                                <a href="{{ route('coordinator.certifications.approve', $cert->id) }}" class="btn btn-sm btn-success">Approve</a>
                                <a href="{{ route('coordinator.certifications.reject', $cert->id) }}" class="btn btn-sm btn-danger">Reject</a>
                            </div>
                        @endforeach
                        @if($certificationCount > 5)
                            <a href="{{ route('coordinator.certifications.index') }}" class="btn btn-outline-primary btn-sm mt-2">View All ({{ $certificationCount }})</a>
                        @endif
                    @else
                        <div class="empty-state">
                            <p>No pending certifications</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-lightning"></i> Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <a href="{{ route('coordinator.placements.create') }}" class="btn btn-primary w-100">
                        <i class="bi bi-plus"></i> Set Student Company
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('coordinator.placements.index') }}" class="btn btn-primary w-100">
                        <i class="bi bi-briefcase"></i> Company Assignments
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('coordinator.supervisor-reports.index') }}" class="btn btn-primary w-100">
                        <i class="bi bi-bar-chart"></i> Supervisor Reports
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="{{ route('coordinator.certifications.index') }}" class="btn btn-primary w-100">
                        <i class="bi bi-award"></i> Certifications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
