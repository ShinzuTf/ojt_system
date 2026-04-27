@extends('layouts.app')

@section('title', 'Supervisor Dashboard - OJT System')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-number">{{ $traineeCount ?? 0 }}</div>
                <div class="stat-label">Trainees</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clock"></i></div>
                <div class="stat-number">{{ $pendingDtrCount ?? 0 }}</div>
                <div class="stat-label">Pending DTR</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-file-text"></i></div>
                <div class="stat-number">{{ $pendingReportCount ?? 0 }}</div>
                <div class="stat-label">Pending Reports</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-exclamation-lg"></i></div>
                <div class="stat-number">{{ $openIssueCount ?? 0 }}</div>
                <div class="stat-label">Open Issues</div>
            </div>
        </div>
    </div>

    <!-- Trainees List -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-people"></i> Your Trainees</h5>
                <small class="text-muted">{{ $trainees->count() }} active</small>
            </div>
        </div>
        <div class="card-body">
            @if($trainees->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Placement</th>
                                <th>Progress</th>
                                <th>Hours</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainees as $trainee)
                                <tr>
                                    <td><strong>{{ $trainee->fname }} {{ $trainee->lname }}</strong></td>
                                    <td>{{ $trainee->currentPlacement->company->fname ?? 'N/A' }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: {{ ($trainee->currentPlacement ? $trainee->currentPlacement->getProgressPercentage() : 0) }}%"></div>
                                        </div>
                                    </td>
                                    <td>{{ $trainee->currentPlacement ? $trainee->dailyTimeRecords->sum(function($r) { return $r->hours_worked ?? 0; }) : 0 }} hrs</td>
                                    <td><span class="badge badge-verified">Active</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('supervisor.trainees.show', $trainee->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <p>No trainees assigned yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-clock-history"></i> DTR Awaiting Verification</h5>
                </div>
                <div class="card-body">
                    @if($pendingDtr && $pendingDtr->count())
                        <div class="timeline">
                            @foreach($pendingDtr->take(5) as $dtr)
                                <div class="timeline-item mb-3">
                                    <p class="mb-1"><strong>{{ $dtr->student->fname }} {{ $dtr->student->lname }}</strong> - {{ $dtr->record_date ? $dtr->record_date->format('M d, Y') : 'N/A' }}</p>
                                    <small class="text-muted">{{ $dtr->hours_worked ?? 0 }} hrs logged</small>
                                    <div class="mt-2">
                                        <a href="{{ route('supervisor.dtr.verify', $dtr->id) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-check"></i> Verify
                                        </a>
                                        <a href="{{ route('supervisor.dtr.reject', $dtr->id) }}" class="btn btn-sm btn-danger">
                                            <i class="bi bi-x"></i> Reject
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($pendingDtrCount > 5)
                            <a href="{{ route('supervisor.dtr.index') }}" class="btn btn-outline-primary btn-sm mt-3">View All ({{ $pendingDtrCount }})</a>
                        @endif
                    @else
                        <div class="empty-state">
                            <p>No pending DTR</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Open Issues -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-exclamation-triangle"></i> Open Issues Requiring Action</h5>
        </div>
        <div class="card-body">
            @if($openIssues && $openIssues->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Trainee</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($openIssues as $issue)
                                <tr>
                                    <td><span class="badge">{{ ucfirst($issue->issue_type) }}</span></td>
                                    <td>{{ $issue->student->fname ?? 'N/A' }} {{ $issue->student->lname ?? '' }}</td>
                                    <td>{{ substr($issue->description ?? '', 0, 40) }}...</td>
                                    <td><span class="badge badge-pending">{{ ucfirst($issue->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('supervisor.issues.acknowledge', $issue->id) }}" class="btn btn-sm btn-primary">Acknowledge</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <p>No open issues</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
