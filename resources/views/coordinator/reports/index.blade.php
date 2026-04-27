@extends('layouts.app')

@section('title', 'Reports - OJT System')
@section('page-title', 'System Reports')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-bar-chart"></i> Progress Reports</h5>
                <div>
                    <select class="form-select d-inline" style="width: auto;" onchange="location = '?status=' + this.value">
                        <option value="">All Reports</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Pending Review</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($reports && $reports->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Title</th>
                                <th>Period</th>
                                <th>Submitted</th>
                                <th>Reviewer</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td><strong>{{ $report->submittedBy->fname ?? 'N/A' }} {{ $report->submittedBy->lname ?? '' }}</strong></td>
                                    <td>{{ ucfirst($report->report_type) }}</td>
                                    <td>{{ $report->report_period_start ? $report->report_period_start->format('M d') : 'N/A' }} - {{ $report->report_period_end ? $report->report_period_end->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $report->created_at->format('M d, Y') }}</td>
                                    <td>{{ $report->reviewedBy->fname ?? 'Pending' }} {{ $report->reviewedBy->lname ?? '' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('coordinator.reports.show', $report) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <nav>
                    {{ $reports->links() }}
                </nav>
            @else
                <div class="empty-state">
                    <p>No reports found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
