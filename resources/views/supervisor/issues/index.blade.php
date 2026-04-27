@extends('layouts.app')

@section('title', 'Issues Management - OJT System')
@section('page-title', 'Issues & Concerns')

@section('content')
<div class="container-fluid">
    <!-- Header with Action Button -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Student Issues</h4>
            <small class="text-muted">Manage and report trainee issues</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('supervisor.issues.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Report Issue
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Open Issues to Address</h5>
        </div>
        <div class="card-body">
            @if($issues && $issues->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Trainee</th>
                                <th>Reported</th>
                                <th>Impact</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($issues as $issue)
                                <tr>
                                    <td>
                                        @if($issue->issue_type === 'absence')
                                            <span class="badge bg-warning">Absence</span>
                                        @elseif($issue->issue_type === 'drop_transfer')
                                            <span class="badge bg-danger">Drop/Transfer</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $issue->issue_type)) }}</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $issue->student->fname ?? 'N/A' }} {{ $issue->student->lname ?? '' }}</strong></td>
                                    <td>{{ $issue->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($issue->impact === 'high')
                                            <span class="badge bg-danger">High</span>
                                        @elseif($issue->impact === 'medium')
                                            <span class="badge bg-warning">Medium</span>
                                        @else
                                            <span class="badge bg-info">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($issue->status === 'reported')
                                            <span class="badge bg-warning">Reported</span>
                                        @elseif($issue->status === 'acknowledged')
                                            <span class="badge bg-info">Acknowledged</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($issue->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($issue->status === 'reported')
                                            <form action="{{ route('supervisor.issues.acknowledge', $issue->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Acknowledge</button>
                                            </form>
                                        @else
                                            <a href="{{ route('supervisor.issues.show', $issue->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        @endif
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
                    <i class="bi bi-check-circle" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3"><strong>No open issues</strong></p>
                    <small class="text-secondary">All trainee issues have been resolved</small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
