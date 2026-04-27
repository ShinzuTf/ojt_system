@extends('layouts.app')

@section('title', 'Supervisor Reports - OJT System')
@section('page-title', 'Supervisor Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h4"><i class="bi bi-file-earmark-text"></i> Supervisor Reports</h2>
            <p class="text-muted">All evaluations submitted by supervisors for their students.</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('coordinator.supervisor-reports.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('coordinator.supervisor-reports.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Student</th>
                    <th>Supervisor</th>
                    <th>Average Rating</th>
                    <th>Status</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->evaluation_date ? $report->evaluation_date->format('M d, Y') : '—' }}</td>
                        <td>{{ $report->trainee->full_name ?? '—' }}</td>
                        <td>{{ $report->supervisor->full_name ?? '—' }}</td>
                        <td>{{ $report->average_rating ? number_format($report->average_rating, 2) . ' / 5' : '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $report->status === 'approved' ? 'success' : ($report->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ ucfirst($report->status ?? 'pending') }}
                            </span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($report->overall_comments ?? '—', 70) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No supervisor reports found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $reports->links('pagination::bootstrap-5') }}
    </div>
</div>

<style>
    .badge-success { background-color: #198754; }
    .badge-warning { background-color: #ffc107; color: #333; }
    .badge-danger { background-color: #dc3545; }
</style>
@endsection