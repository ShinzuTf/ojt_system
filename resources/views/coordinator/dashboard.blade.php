@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 font-weight-bold text-dark">Coordinator Dashboard</h1>
                    <p class="text-muted">Monitor all OJT placements and validations across the school.</p>
                </div>
                <div class="badge badge-success p-2">
                    <i class="fas fa-graduation-cap"></i> School Coordinator
                </div>
            </div>
        </div>
    </div>

    <!-- System-Wide Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-primary font-weight-bold text-uppercase mb-1">Total Students</div>
                    <div class="h3 mb-0">{{ $stats['total_students'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-success font-weight-bold text-uppercase mb-1">Active Placements</div>
                    <div class="h3 mb-0">{{ $stats['active_placements'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-info font-weight-bold text-uppercase mb-1">Completed</div>
                    <div class="h3 mb-0">{{ $stats['completed_placements'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-danger font-weight-bold text-uppercase mb-1">Pending Issues</div>
                    <div class="h3 mb-0">{{ $stats['pending_issues'] ?? 0 }}</div>
                    <a href="#pending-issues" class="text-danger"><small>View →</small></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Actions Row -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> <strong>Coming Soon:</strong> Certification and completion approval features will be available in the next release.
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Quick Links -->
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-link"></i> Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('coordinator.trainees.index') }}" class="btn btn-primary w-100">
                                <i class="fas fa-users"></i> View Trainees
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('coordinator.supervisor-reports.index') }}" class="btn btn-warning w-100">
                                <i class="fas fa-file"></i> Supervisor Reports
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('coordinator.placements.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-briefcase"></i> Set Student Company
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('coordinator.issues.index') }}" class="btn btn-danger w-100">
                                <i class="fas fa-exclamation-circle"></i> View Issues
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Issues -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow" id="pending-issues">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Reported Issues Requiring Action</h5>
                </div>
                <div class="card-body p-0">
                    @if($pendingIssues && count($pendingIssues) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($pendingIssues as $issue)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <h6 class="mb-1">
                                            <span class="badge badge-secondary">{{ ucfirst($issue->issue_type) }}</span>
                                            {{ $issue->student->short_name }}
                                        </h6>
                                        <small class="text-muted">{{ $issue->issue_date->format('M d, Y') }} - {{ $issue->description }}</small>
                                    </div>
                                    <div class="col-md-5 text-right">
                                        <span class="badge badge-warning">{{ ucfirst($issue->status) }}</span>
                                        <div class="btn-group btn-group-sm ml-2" role="group">
                                            <a href="{{ route('issues.show', $issue) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Review
                                            </a>
                                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#resolveModal" data-issue="{{ $issue->id }}">
                                                <i class="fas fa-check"></i> Resolve
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-check-circle"></i> No pending issues
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary { border-left: 4px solid #007bff !important; }
    .border-left-success { border-left: 4px solid #28a745 !important; }
    .border-left-info { border-left: 4px solid #17a2b8 !important; }
    .border-left-danger { border-left: 4px solid #dc3545 !important; }
    .border-left-warning { border-left: 4px solid #ffc107 !important; }
</style>
@endsection
