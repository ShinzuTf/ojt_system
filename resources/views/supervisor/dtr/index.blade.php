@extends('layouts.app')

@section('title', 'DTR Verification - OJT System')
@section('page-title', 'DTR Verification')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-clock-history"></i> Daily Time Records for Verification</h5>
        </div>
        <div class="card-body">
            @if($dtrs && $dtrs->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Trainee</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Hours</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dtrs as $dtr)
                                <tr>
                                    <td><strong>{{ $dtr->student->fname ?? '' }} {{ $dtr->student->lname ?? '' }}</strong></td>
                                    <td>{{ $dtr->record_date ? $dtr->record_date->format('M d, Y') : '-' }}</td>
                                    <td>{{ $dtr->time_in ? \Carbon\Carbon::parse($dtr->time_in)->format('H:i') : '-' }}</td>
                                    <td>{{ $dtr->time_out ? \Carbon\Carbon::parse($dtr->time_out)->format('H:i') : '-' }}</td>
                                    <td>{{ $dtr->hours_worked ?? '0' }} hrs</td>
                                    <td>
                                        @if($dtr->status === 'verified')
                                            <span class="badge badge-verified">Verified</span>
                                        @elseif($dtr->status === 'rejected')
                                            <span class="badge badge-rejected">Rejected</span>
                                        @else
                                            <span class="badge badge-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dtr->status === 'pending')
                                            <div class="action-buttons">
                                                <a href="{{ route('supervisor.dtr.verify', $dtr->id) }}" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check"></i> Verify
                                                </a>
                                                <a href="{{ route('supervisor.dtr.reject', $dtr->id) }}" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x"></i> Reject
                                                </a>
                                            </div>
                                        @else
                                            <a href="{{ route('supervisor.dtr.show', $dtr->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <nav>
                    {{ $dtrs->links() }}
                </nav>
            @else
                <div class="empty-state">
                    <i class="bi bi-check-circle"></i>
                    <p>No pending DTR entries</p>
                    <small class="text-muted">All DTR entries have been verified</small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
