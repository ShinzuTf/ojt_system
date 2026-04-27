@extends('layouts.app')

@section('title', 'Issue Details - OJT System')
@section('page-title', 'Issue Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-shield-exclamation"></i> {{ ucfirst($issue->issue_type) }} Issue</h5>
                        <span class="badge badge-{{ $issue->status === 'resolved' ? 'success' : 'warning' }}">
                            {{ ucfirst($issue->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Student</label>
                            <p class="h6">{{ $issue->student->fname ?? 'N/A' }} {{ $issue->student->lname ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Issue Date</label>
                            <p class="h6">{{ $issue->issue_date ? $issue->issue_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Issue Type</label>
                            <p class="h6"><span class="badge">{{ ucfirst($issue->issue_type) }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Reported By</label>
                            <p class="h6">{{ $issue->reportedBy->fname ?? 'N/A' }} {{ $issue->reportedBy->lname ?? '' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Description</label>
                        <div class="p-3" style="background: #f8f9fa; border-radius: 0.375rem;">
                            <p>{{ $issue->description ?? 'No description provided' }}</p>
                        </div>
                    </div>

                    @if($issue->action_taken)
                    <div class="mb-4">
                        <label class="form-label text-muted">Action Taken</label>
                        <div class="p-3" style="background: #f8f9fa; border-radius: 0.375rem;">
                            <p>{{ $issue->action_taken }}</p>
                        </div>
                    </div>
                    @endif

                    @if($issue->resolution_notes)
                    <div class="mb-4">
                        <label class="form-label text-muted">Resolution Notes</label>
                        <div class="p-3" style="background: #e8f5e9; border-radius: 0.375rem;">
                            <p>{{ $issue->resolution_notes }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Assigned To</label>
                            <p class="h6">{{ $issue->assignedTo->fname ?? 'Unassigned' }} {{ $issue->assignedTo->lname ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Student Status</label>
                            <p class="h6"><span class="badge badge-{{ $issue->student_status === 'dropped' ? 'danger' : 'info' }}">
                                {{ ucfirst($issue->student_status ?? 'active') }}
                            </span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-info-circle"></i> Issue Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Status</small>
                        <p class="h6">
                            <span class="badge badge-{{ $issue->status === 'resolved' ? 'success' : ($issue->status === 'investigating' ? 'warning' : 'info') }}">
                                {{ ucfirst($issue->status) }}
                            </span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Created</small>
                        <p class="h6">{{ $issue->created_at ? $issue->created_at->format('M d, Y g:i A') : 'N/A' }}</p>
                    </div>

                    @if($issue->resolution_date)
                    <div class="mb-3">
                        <small class="text-muted">Resolved</small>
                        <p class="h6">{{ $issue->resolution_date->format('M d, Y g:i A') }}</p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted">Company</small>
                        <p class="h6">{{ $issue->company->fname ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('supervisor.issues.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-3">
                <i class="bi bi-arrow-left"></i> Back to Issues
            </a>
        </div>
    </div>
</div>
@endsection
