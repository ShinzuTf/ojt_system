@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Reported Issues</h2>
                <a href="{{ route('issues.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Report Issue
                </a>
            </div>

            <!-- Filters -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="{{ route('issues.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="filterType" class="mr-2">Issue Type:</label>
                            <select id="filterType" name="issue_type" class="form-control">
                                <option value="">All</option>
                                <option value="absence" {{ request('issue_type') === 'absence' ? 'selected' : '' }}>Absence</option>
                                <option value="drop" {{ request('issue_type') === 'drop' ? 'selected' : '' }}>Drop</option>
                                <option value="transfer" {{ request('issue_type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="behavioral" {{ request('issue_type') === 'behavioral' ? 'selected' : '' }}>Behavioral</option>
                                <option value="performance" {{ request('issue_type') === 'performance' ? 'selected' : '' }}>Performance</option>
                                <option value="other" {{ request('issue_type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group mr-3">
                            <label for="filterStatus" class="mr-2">Status:</label>
                            <select id="filterStatus" name="status" class="form-control">
                                <option value="">All</option>
                                <option value="reported" {{ request('status') === 'reported' ? 'selected' : '' }}>Reported</option>
                                <option value="acknowledged" {{ request('status') === 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
                                <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                </div>
            </div>

            <!-- Issues List -->
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($issues as $issue)
                            <tr>
                                <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                                <td><strong>{{ $issue->student->short_name }}</strong></td>
                                <td>
                                    <span class="badge badge-secondary">{{ ucfirst($issue->issue_type) }}</span>
                                </td>
                                <td>
                                    <small>{{ Str::limit($issue->description, 50) }}</small>
                                </td>
                                <td>
                                    @if($issue->status === 'reported')
                                        <span class="badge badge-warning">Reported</span>
                                    @elseif($issue->status === 'acknowledged')
                                        <span class="badge badge-info">Acknowledged</span>
                                    @elseif($issue->status === 'investigating')
                                        <span class="badge badge-primary">Investigating</span>
                                    @elseif($issue->status === 'resolved')
                                        <span class="badge badge-success">Resolved</span>
                                    @else
                                        <span class="badge badge-secondary">Closed</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $issue->assigned_to ? $issue->assignee->short_name : '-' }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('issues.show', $issue) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($issue->status !== 'closed' && auth()->user()->isCoordinator())
                                        <a href="{{ route('issues.edit', $issue) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox"></i> No issues found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($issues->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $issues->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
