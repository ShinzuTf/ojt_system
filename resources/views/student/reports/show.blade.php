@extends('layouts.app')

@section('title', 'Report Details - OJT System')
@section('page-title', 'Report Details')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            @if($report->report_type === 'weekly')
                                <i class="bi bi-calendar-week"></i> Weekly Report
                            @else
                                <i class="bi bi-calendar-month"></i> Monthly Report
                            @endif
                        </h5>
                        <small class="text-muted">
                            {{ $report->report_period_start->format('M d, Y') }} - {{ $report->report_period_end->format('M d, Y') }}
                        </small>
                    </div>
                    <a href="{{ route('student.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <!-- Status Section -->
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted">Report Status</small>
                                <div class="mt-2">
                                    @if($report->status === 'approved')
                                        <span class="badge bg-success" style="padding: 8px 16px; font-size: 0.9rem;">
                                            <i class="bi bi-check-circle"></i> Approved
                                        </span>
                                    @elseif($report->status === 'rejected')
                                        <span class="badge bg-danger" style="padding: 8px 16px; font-size: 0.9rem;">
                                            <i class="bi bi-x-circle"></i> Rejected
                                        </span>
                                    @elseif($report->status === 'submitted')
                                        <span class="badge bg-info" style="padding: 8px 16px; font-size: 0.9rem;">
                                            <i class="bi bi-clock"></i> Under Review
                                        </span>
                                    @else
                                        <span class="badge bg-secondary" style="padding: 8px 16px; font-size: 0.9rem;">
                                            <i class="bi bi-file"></i> Draft
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Submitted On</small>
                                <div class="mt-2">
                                    <strong>{{ $report->created_at->format('M d, Y h:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Section -->
                    @if($report->file_path && Storage::disk('public')->exists($report->file_path))
                        <div class="mb-4 pb-4 border-bottom">
                            <small class="text-muted mb-3 d-block">Report Document</small>
                            <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; display: flex; align-items: center; gap: 16px;">
                                <div style="width: 50px; height: 50px; background: #e8eeff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    @php
                                        $ext = strtolower($report->file_type ?? pathinfo($report->file_path, PATHINFO_EXTENSION));
                                    @endphp
                                    @if(in_array($ext, ['pdf']))
                                        <i class="bi bi-file-pdf" style="font-size: 1.8rem; color: #dc2626;"></i>
                                    @elseif(in_array($ext, ['doc', 'docx']))
                                        <i class="bi bi-file-word" style="font-size: 1.8rem; color: #2563eb;"></i>
                                    @elseif(in_array($ext, ['xlsx', 'xls']))
                                        <i class="bi bi-file-earmark-spreadsheet" style="font-size: 1.8rem; color: #059669;"></i>
                                    @elseif(in_array($ext, ['ppt', 'pptx']))
                                        <i class="bi bi-file-powerpoint" style="font-size: 1.8rem; color: #d97706;"></i>
                                    @elseif(in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <i class="bi bi-file-image" style="font-size: 1.8rem; color: #7c3aed;"></i>
                                    @else
                                        <i class="bi bi-file" style="font-size: 1.8rem; color: #6b7280;"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="mb-2">
                                        <strong>{{ basename($report->file_path) }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $report->file_type ? strtoupper($report->file_type) : 'File' }} Document</small>
                                </div>
                                <a href="{{ route('student.reports.download', $report->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Review Information -->
                    @if($report->reviewed_by)
                        <div class="mb-4 pb-4 border-bottom">
                            <small class="text-muted mb-3 d-block">Reviewer Feedback</small>
                            <div style="background: #f8f9fa; padding: 16px; border-radius: 8px;">
                                <div class="mb-3">
                                    <strong>Reviewed By:</strong> {{ $report->reviewedBy->fname }} {{ $report->reviewedBy->lname }}
                                </div>
                                <div class="mb-3">
                                    <strong>Reviewed On:</strong> {{ $report->reviewed_at->format('M d, Y h:i A') }}
                                </div>
                                @if($report->reviewer_comments)
                                    <div>
                                        <strong>Comments:</strong>
                                        <p class="mt-2 mb-0">{{ $report->reviewer_comments }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Period Information -->
                    <div class="mb-4">
                        <small class="text-muted mb-3 d-block">Report Period</small>
                        <div class="row">
                            <div class="col-md-6">
                                <div style="background: #f8f9fa; padding: 12px; border-radius: 6px;">
                                    <small class="text-muted">Start Date</small>
                                    <div class="mt-2"><strong>{{ $report->report_period_start->format('M d, Y') }}</strong></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #f8f9fa; padding: 12px; border-radius: 6px;">
                                    <small class="text-muted">End Date</small>
                                    <div class="mt-2"><strong>{{ $report->report_period_end->format('M d, Y') }}</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('student.reports.index') }}" class="btn btn-outline-secondary">
                            Back to Reports
                        </a>
                        @if($report->file_path && Storage::disk('public')->exists($report->file_path))
                            <a href="{{ route('student.reports.download', $report->id) }}" class="btn btn-primary">
                                <i class="bi bi-download"></i> Download Report
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
