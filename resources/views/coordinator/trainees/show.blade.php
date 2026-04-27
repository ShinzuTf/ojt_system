@extends('layouts.app')

@section('title', $trainee->fname . ' ' . $trainee->lname . ' - OJT System')
@section('page-title', 'Trainee Profile')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h4"><i class="bi bi-person-badge"></i> {{ $trainee->fname }} {{ $trainee->lname }}</h2>
            <p class="text-muted">Student ID: <code>{{ $trainee->id }}</code></p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('coordinator.trainees.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Placement Info -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-briefcase"></i> Placement Information</h6>
                </div>
                <div class="card-body">
                    @if($placement)
                        <div class="mb-3">
                            <small class="text-muted">Company</small>
                            <p class="h6">{{ $placement->company->fname ?? 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Supervisor</small>
                            <p class="h6">{{ $placement->supervisor->fname ?? 'N/A' }} {{ $placement->supervisor->lname ?? '' }}</p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Status</small>
                            <p class="h6">
                                <span class="badge badge-{{ $placement->status === 'active' ? 'success' : ($placement->status === 'completed' ? 'info' : 'warning') }}">
                                    {{ ucfirst($placement->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Start Date</small>
                            <p class="h6">{{ $placement->start_date ? $placement->start_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">End Date</small>
                            <p class="h6">{{ $placement->end_date ? $placement->end_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Required Hours</small>
                            <p class="h6">{{ $placement->total_required_hours ?? 0 }} hours</p>
                        </div>
                    @else
                        <p class="text-muted">No active placement found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- DTR Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-calendar-event"></i> Recent DTRs</h6>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if($dtr->isEmpty())
                        <p class="text-muted small">No DTR records found</p>
                    @else
                        @foreach($dtr as $record)
                            <div class="mb-3 pb-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">{{ $record->record_date ? $record->record_date->format('M d, Y') : 'N/A' }}</small>
                                        <p class="h6 mb-0">
                                            @if($record->time_in && $record->time_out)
                                                {{ $record->time_in->format('g:i A') }} - {{ $record->time_out->format('g:i A') }}
                                            @else
                                                <span class="text-warning">Incomplete</span>
                                            @endif
                                        </p>
                                    </div>
                                    <span class="badge badge-info">{{ $record->hours_worked ?? 0 }}h</span>
                                </div>
                                <small class="text-muted">Status: 
                                    <span class="badge badge-{{ $record->status === 'verified' ? 'success' : ($record->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </small>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Reports & Issues -->
        <div class="col-lg-4 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6><i class="bi bi-file-earmark-text"></i> Recent Reports</h6>
                </div>
                <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                    @if($reports->isEmpty())
                        <p class="text-muted small">No reports submitted</p>
                    @else
                        @foreach($reports as $report)
                            <div class="mb-2 pb-2 border-bottom">
                                <small>
                                    <strong>{{ $report->report_type }}</strong>
                                    <span class="badge badge-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </small>
                                <p class="text-muted small mb-0">{{ $report->created_at->format('M d, Y') }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-exclamation-triangle"></i> Issues</h6>
                </div>
                <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                    @if($issues->isEmpty())
                        <p class="text-muted small">No issues reported</p>
                    @else
                        @foreach($issues as $issue)
                            <div class="mb-2 pb-2 border-bottom">
                                <small>
                                    <strong>{{ ucfirst($issue->issue_type) }}</strong>
                                    <span class="badge badge-{{ $issue->status === 'resolved' ? 'success' : 'warning' }}">
                                        {{ ucfirst($issue->status) }}
                                    </span>
                                </small>
                                <p class="text-muted small mb-0">{{ $issue->created_at->format('M d, Y') }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-success { background-color: #198754; }
    .badge-warning { background-color: #ffc107; color: #333; }
    .badge-info { background-color: #0dcaf0; }
    .badge-danger { background-color: #dc3545; }
</style>
@endsection
