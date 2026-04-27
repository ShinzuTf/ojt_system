@extends('layouts.app')

@section('title', 'Report Details - OJT System')
@section('page-title', 'Report Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-file-earmark-text"></i> {{ ucfirst($report->report_type ?? 'Report') }}</h5>
                        <span class="badge badge-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                            {{ ucfirst($report->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Student</label>
                            <p class="h6">{{ $report->submittedBy->fname ?? 'N/A' }} {{ $report->submittedBy->lname ?? '' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Report Type</label>
                            <p class="h6"><span class="badge">{{ ucfirst($report->report_type ?? 'Report') }}</span></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted">Period Start</label>
                            <p class="h6">{{ $report->report_period_start ? $report->report_period_start->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Period End</label>
                            <p class="h6">{{ $report->report_period_end ? $report->report_period_end->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Submitted Date</label>
                        <p class="h6">{{ $report->created_at ? $report->created_at->format('M d, Y g:i A') : 'N/A' }}</p>
                    </div>

                    @if($report->file_path)
                    <div class="mb-4">
                        <label class="form-label text-muted">Submitted File</label>
                        <div class="p-3" style="background: #f8f9fa; border-radius: 0.375rem;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    @php
                                        $ext = strtolower(pathinfo($report->file_path, PATHINFO_EXTENSION));
                                        $icons = [
                                            'pdf' => ['icon' => 'bi-file-pdf', 'color' => 'danger'],
                                            'doc' => ['icon' => 'bi-file-word', 'color' => 'primary'],
                                            'docx' => ['icon' => 'bi-file-word', 'color' => 'primary'],
                                            'xls' => ['icon' => 'bi-file-excel', 'color' => 'success'],
                                            'xlsx' => ['icon' => 'bi-file-excel', 'color' => 'success'],
                                            'ppt' => ['icon' => 'bi-file-ppt', 'color' => 'warning'],
                                            'pptx' => ['icon' => 'bi-file-ppt', 'color' => 'warning'],
                                            'jpg' => ['icon' => 'bi-image', 'color' => 'info'],
                                            'jpeg' => ['icon' => 'bi-image', 'color' => 'info'],
                                            'png' => ['icon' => 'bi-image', 'color' => 'info'],
                                        ];
                                        $fileInfo = $icons[$ext] ?? ['icon' => 'bi-file', 'color' => 'secondary'];
                                    @endphp
                                    <i class="bi {{ $fileInfo['icon'] }} text-{{ $fileInfo['color'] }}" style="font-size: 1.5rem; margin-right: 12px;"></i>
                                    <div>
                                        <p class="mb-0"><strong>{{ basename($report->file_path) }}</strong></p>
                                        <small class="text-muted">{{ $report->file_type ?? 'File' }}</small>
                                    </div>
                                </div>
                                @if($report->file_path)
                                    <a href="{{ asset('storage/' . $report->file_path) }}" class="btn btn-sm btn-outline-primary" download>
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($report->reviewer_comments)
                    <div class="mb-4">
                        <label class="form-label text-muted">Reviewer Comments</label>
                        <div class="p-3" style="background: #f0f4ff; border-radius: 0.375rem;">
                            <p>{{ $report->reviewer_comments }}</p>
                        </div>
                    </div>
                    @endif

                    @if($report->status === 'submitted')
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="mb-3">Review Decision</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('coordinator.reports.approve', $report->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check"></i> Approve
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bi bi-x"></i> Reject
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="bi bi-info-circle"></i> Report Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Status</small>
                        <p class="h6">
                            <span class="badge badge-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Submitted By</small>
                        <p class="h6">{{ $report->submittedBy->fname ?? 'N/A' }} {{ $report->submittedBy->lname ?? '' }}</p>
                    </div>

                    @if($report->reviewed_by)
                    <div class="mb-3">
                        <small class="text-muted">Reviewed By</small>
                        <p class="h6">{{ $report->reviewedBy->fname ?? 'N/A' }} {{ $report->reviewedBy->lname ?? '' }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Reviewed Date</small>
                        <p class="h6">{{ $report->reviewed_at ? $report->reviewed_at->format('M d, Y g:i A') : 'N/A' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <a href="{{ route('coordinator.reports.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-3">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('coordinator.reports.reject', $report->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comments" class="form-label">Feedback for Student</label>
                        <textarea name="reviewer_comments" id="comments" class="form-control" rows="4" placeholder="Explain why the report is rejected..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
