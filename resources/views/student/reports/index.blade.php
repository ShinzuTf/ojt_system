@extends('layouts.app')

@section('title', 'Progress Reports - OJT System')
@section('page-title', 'My Progress Reports')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="mb-0"><i class="bi bi-file-earmark-pdf"></i> Progress Reports</h4>
            <small class="text-muted">Weekly and monthly reports submitted for supervisor review</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('student.reports.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-cloud-arrow-up"></i> Upload Report
            </a>
        </div>
    </div>

    @if($reports && $reports->count())
        <div class="card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th style="padding: 12px 16px;">Type</th>
                            <th style="padding: 12px 16px;">Period</th>
                            <th style="padding: 12px 16px;">Submitted</th>
                            <th style="padding: 12px 16px;">File</th>
                            <th style="padding: 12px 16px;">Status</th>
                            <th style="padding: 12px 16px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td style="padding: 12px 16px;">
                                    @if($report->report_type === 'weekly')
                                        <span class="badge bg-info"><i class="bi bi-calendar-week"></i> Weekly</span>
                                    @else
                                        <span class="badge bg-warning"><i class="bi bi-calendar-month"></i> Monthly</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px;">
                                    @if($report->report_period_start && $report->report_period_end)
                                        <small>{{ $report->report_period_start->format('M d') }} - {{ $report->report_period_end->format('M d, Y') }}</small>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td style="padding: 12px 16px;">
                                    <small>{{ $report->created_at->format('M d, Y') }}</small>
                                </td>
                                <td style="padding: 12px 16px;">
                                    @if($report->file_path)
                                        <div>
                                            <i class="bi bi-file-earmark"></i>
                                            <small>{{ basename($report->file_path) }}</small>
                                        </div>
                                    @else
                                        <small class="text-muted">No file</small>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px;">
                                    @if($report->status === 'approved')
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Approved</span>
                                    @elseif($report->status === 'rejected')
                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Rejected</span>
                                    @elseif($report->status === 'submitted')
                                        <span class="badge bg-info"><i class="bi bi-clock"></i> Under Review</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="bi bi-file"></i> Draft</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px;">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('student.reports.show', $report->id) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($report->file_path && Storage::disk('public')->exists($report->file_path))
                                            <a href="{{ route('student.reports.download', $report->id) }}" class="btn btn-outline-success">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <nav class="mt-3">
            {{ $reports->links() }}
        </nav>
    @else
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="bi bi-file-earmark-x" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3"><strong>No reports yet</strong></p>
                <small class="text-secondary">Submit your weekly or monthly progress reports</small>
                <div class="mt-4">
                    <a href="{{ route('student.reports.create') }}" class="btn btn-primary">
                        <i class="bi bi-cloud-arrow-up"></i> Upload First Report
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
