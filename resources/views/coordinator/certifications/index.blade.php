@extends('layouts.app')

@section('title', 'Certifications - OJT System')
@section('page-title', 'OJT Certifications')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="bi bi-award"></i> OJT Certifications</h5>
        </div>
        <div class="card-body">
            @if($certifications && $certifications->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Company</th>
                                <th>Placement</th>
                                <th>Rating</th>
                                <th>Supervisor</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($certifications as $cert)
                                <tr>
                                    <td><strong>{{ $cert->student->name }}</strong></td>
                                    <td>{{ $cert->placement->company_name }}</td>
                                    <td>{{ $cert->placement->start_date->format('M d, Y') }} - {{ $cert->placement->end_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge" style="background-color: #ffc107; color: #000;">
                                            {{ $cert->final_rating }} / 5 ⭐
                                        </span>
                                    </td>
                                    <td>{{ $cert->issuedBy->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($cert->status === 'approved')
                                            <span class="badge badge-verified">Approved</span>
                                        @elseif($cert->status === 'verified')
                                            <span class="badge badge-approved">Verified</span>
                                        @else
                                            <span class="badge badge-pending">{{ ucfirst($cert->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('coordinator.certifications.show', $cert->id) }}" class="btn btn-sm btn-outline-primary">Review</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <nav>
                    {{ $certifications->links() }}
                </nav>
            @else
                <div class="empty-state">
                    <i class="bi bi-award"></i>
                    <p>No certifications yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
