@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-clock"></i> Daily Time Record Details</h4>
                    <a href="{{ route('dtr.index') }}" class="btn btn-sm btn-light">Back</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Date:</strong><br>
                                {{ $dtr->record_date->format('l, F d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Student:</strong><br>
                                {{ $dtr->student->full_name }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p>
                                <strong>Time In:</strong><br>
                                <span class="h5">{{ \Carbon\Carbon::parse($dtr->time_in)->format('h:i A') }}</span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p>
                                <strong>Time Out:</strong><br>
                                <span class="h5">{{ \Carbon\Carbon::parse($dtr->time_out)->format('h:i A') }}</span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p>
                                <strong>Hours Worked:</strong><br>
                                <span class="h5 text-success">{{ $dtr->hours_worked }}h</span>
                            </p>
                        </div>
                    </div>

                    @if($dtr->break_minutes > 0)
                    <div class="alert alert-warning mb-4">
                        <strong>Break Time:</strong> {{ $dtr->break_minutes }} minutes
                    </div>
                    @endif

                    <div class="mb-4">
                        <strong>Status:</strong><br>
                        @if($dtr->status === 'verified')
                            <span class="badge badge-success" style="font-size: 1rem;">Verified</span>
                        @elseif($dtr->status === 'pending')
                            <span class="badge badge-warning" style="font-size: 1rem;">Pending Review</span>
                        @else
                            <span class="badge badge-danger" style="font-size: 1rem;">Rejected</span>
                        @endif
                    </div>

                    @if($dtr->verified_by)
                    <div class="mb-4">
                        <strong>Verified By:</strong><br>
                        {{ $dtr->verifier->full_name }} on {{ $dtr->verified_at->format('M d, Y h:i A') }}
                    </div>
                    @endif

                    @if($dtr->remarks)
                    <div class="mb-4">
                        <strong>Remarks:</strong><br>
                        <p class="text-muted">{{ $dtr->remarks }}</p>
                    </div>
                    @endif

                    @if($dtr->rejection_reason && $dtr->status === 'rejected')
                    <div class="alert alert-danger mb-4">
                        <strong>Rejection Reason:</strong><br>
                        {{ $dtr->rejection_reason }}
                    </div>
                    @endif

                    <div class="mt-4">
                        @if(auth()->user()->isSupervisor() && $dtr->status === 'pending')
                        <form action="{{ route('dtr.verify', $dtr) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check"></i> Verify Entry
                            </button>
                        </form>
                        <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#rejectModal">
                            <i class="fas fa-times"></i> Reject Entry
                        </button>
                        @endif

                        @if(auth()->user()->isStudent() && $dtr->status === 'pending')
                        <a href="{{ route('dtr.edit', $dtr) }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-edit"></i> Edit Entry
                        </a>
                        <form action="{{ route('dtr.destroy', $dtr) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Delete entry?')">
                                <i class="fas fa-trash"></i> Delete Entry
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('dtr.reject', $dtr) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject DTR Entry</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejectionReason">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea id="rejectionReason" name="rejection_reason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
