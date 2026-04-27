@extends('layouts.app')

@section('title', 'Issues - OJT System')
@section('page-title', 'System Issues')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-shield-exclamation"></i> System Issues</h5>
                <div>
                    <select class="form-select d-inline" style="width: auto;" onchange="location = '?status=' + this.value">
                        <option value="">All Issues</option>
                        <option value="reported" {{ request('status') === 'reported' ? 'selected' : '' }}>Reported</option>
                        <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($issues && $issues->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Student</th>
                                <th>Description</th>
                                <th>Impact</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($issues as $issue)
                                <tr>
                                    <td><span class="badge">{{ ucfirst($issue->issue_type) }}</span></td>
                                    <td><strong>{{ $issue->student->name }}</strong></td>
                                    <td>{{ substr($issue->description, 0, 50) }}...</td>
                                    <td>
                                        <span class="badge badge-{{ $issue->impact === 'high' ? 'danger' : ($issue->impact === 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($issue->impact) }}
                                        </span>
                                    </td>
                                    <td>{{ $issue->assigned_to ? $issue->assignedTo->name : 'Unassigned' }}</td>
                                    <td><span class="badge badge-{{ $issue->status === 'resolved' ? 'verified' : 'pending' }}">{{ ucfirst($issue->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('coordinator.issues.show', $issue->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <nav>
                    {{ $issues->links() }}
                </nav>
            @else
                <div class="empty-state">
                    <p>No issues found</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
