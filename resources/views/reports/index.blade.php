@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Reports</h2>
                <a href="{{ route('reports.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Report
                </a>
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('reports.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="filterType" class="mr-2">Type:</label>
                            <select id="filterType" name="report_type" class="form-control">
                                <option value="">All</option>
                                <option value="weekly" {{ request('report_type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ request('report_type') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            </select>
                        </div>
                        <div class="form-group mr-3">
                            <label for="filterStatus" class="mr-2">Status:</label>
                            <select id="filterStatus" name="status" class="form-control">
                                <option value="">All</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                </div>
            </div>

            <!-- Reports List -->
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Period</th>
                                <th>Type</th>
                                <th>Submitted By</th>
                                <th>Status</th>
                                <th>Reviewed By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td>
                                    <strong>{{ $report->report_period_start->format('M d') }} - {{ $report->report_period_end->format('M d, Y') }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($report->report_type) }}</span>
                                </td>
                                <td>
                                    {{ $report->submittedBy->short_name }}
                                </td>
                                <td>
                                    @if($report->status === 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($report->status === 'submitted')
                                        <span class="badge badge-info">Submitted</span>
                                    @elseif($report->status === 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $report->reviewed_by ? $report->reviewer->short_name : '-' }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('reports.show', $report) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($report->status === 'draft')
                                        <a href="{{ route('reports.edit', $report) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('reports.destroy', $report) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox"></i> No reports found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($reports->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $reports->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
