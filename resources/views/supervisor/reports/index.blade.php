@extends('layouts.app')

@section('title', 'Report Review - OJT System')
@section('page-title', 'Progress Reports for Review')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-file-earmark-text"></i> Progress Reports Awaiting Review</h5>
        </div>
        <div class="card-body">
            @if($reports && $reports->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Trainee</th>
                                <th>Report Title</th>
                                <th>Period</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td><strong>{{ $report->student->name }}</strong></td>
                                    <td>{{ $report->title }}</td>
                                    <td>{{ $report->period_start->format('M d') }} - {{ $report->period_end->format('M d, Y') }}</td>
                                    <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                                    <td><span class="badge badge-pending">{{ ucfirst($report->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('supervisor.reports.show', $report->id) }}" class="btn btn-sm btn-primary">Review</a>
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
                    <i class="bi bi-check-circle"></i>
                    <p>No pending reports</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
