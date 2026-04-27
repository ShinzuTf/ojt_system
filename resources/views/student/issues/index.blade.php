@extends('layouts.app')

@section('title', 'My Issues - OJT System')
@section('page-title', 'Issues & Concerns')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-exclamation-circle"></i> My Issues & Concerns</h5>
                <a href="{{ route('student.issues.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Report Issue
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($issues && $issues->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Reported</th>
                                <th>Impact</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($issues as $issue)
                                <tr>
                                    <td><span class="badge">{{ ucfirst($issue->issue_type) }}</span></td>
                                    <td>{{ substr($issue->description, 0, 50) }}...</td>
                                    <td>{{ $issue->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $issue->impact === 'high' ? 'danger' : ($issue->impact === 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($issue->impact) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($issue->status === 'resolved')
                                            <span class="badge badge-verified">Resolved</span>
                                        @elseif($issue->status === 'investigating')
                                            <span class="badge badge-pending">Investigating</span>
                                        @elseif($issue->status === 'acknowledged')
                                            <span class="badge badge-approved">Acknowledged</span>
                                        @else
                                            <span class="badge badge-pending">Reported</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('student.issues.show', $issue->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <nav>
                    {{ $issues->links() }}
                </nav>
            @else
                <div class="empty-state">
                    <i class="bi bi-check-circle"></i>
                    <p>No issues reported</p>
                    <small class="text-muted">Report any concerns or issues you encounter during your OJT</small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
