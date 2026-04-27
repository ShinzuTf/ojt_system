@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-exclamation-circle"></i> Issue Details</h4>
                    <a href="{{ route('issues.index') }}" class="btn btn-sm btn-light">Back</a>
                </div>

                <div class="card-body">
                    <!-- Issue Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Reported By:</strong><br>
                                {{ $issue->reportedBy->full_name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Student Involved:</strong><br>
                                {{ $issue->student->full_name }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Issue Type:</strong><br>
                                <span class="badge badge-secondary" style="font-size: 1rem;">{{ ucfirst($issue->issue_type) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Severity:</strong><br>
                                @if($issue->severity === 'critical')
                                    <span class="badge badge-danger" style="font-size: 1rem;">{{ ucfirst($issue->severity) }}</span>
                                @elseif($issue->severity === 'high')
                                    <span class="badge badge-warning" style="font-size: 1rem;">{{ ucfirst($issue->severity) }}</span>
                                @else
                                    <span class="badge badge-info" style="font-size: 1rem;">{{ ucfirst($issue->severity) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Date Reported:</strong><br>
                                {{ $issue->issue_date->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Status:</strong><br>
                                @php
                                    $statusColor = [
                                        'reported' => 'warning',
                                        'acknowledged' => 'info',
                                        'investigating' => 'primary',
                                        'resolved' => 'success',
                                        'closed' => 'secondary'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $statusColor[$issue->status] }}" style="font-size: 1rem;">{{ ucfirst($issue->status) }}</span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Issue Description -->
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Description</h5>
                        <p class="text-muted">{{ $issue->description }}</p>
                    </div>

                    @if($issue->desired_resolution)
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Desired Resolution</h5>
                        <p class="text-muted">{{ $issue->desired_resolution }}</p>
                    </div>
                    @endif

                    <!-- Assigned To -->
                    @if($issue->assigned_to)
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Assigned To</h5>
                        <p>{{ $issue->assignee->full_name }} <small class="text-muted">({{ $issue->assignee->role }})</small></p>
                    </div>
                    @endif

                    <!-- Issue Updates Timeline -->
                    @if($issue->updates->count() > 0)
                    <hr class="my-4">
                    <h5 class="font-weight-bold mb-3">Resolution Timeline</h5>
                    <div class="timeline">
                        @foreach($issue->updates->reverse() as $update)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $update->updatedBy->short_name }}</h6>
                                    <small class="text-muted">{{ $update->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                                <span class="badge badge-secondary">{{ ucfirst($update->update_type) }}</span>
                            </div>
                            <p class="mt-2 text-muted">{{ $update->description }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Actions -->
                    <hr class="my-4">
                    <div class="mt-4">
                        @if(auth()->user()->isCoordinator())
                            @if($issue->status === 'reported')
                            <form action="{{ route('issues.acknowledge', $issue) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-info btn-lg">
                                    <i class="fas fa-check"></i> Acknowledge Issue
                                </button>
                            </form>
                            @endif

                            @if($issue->status !== 'closed' && $issue->status !== 'resolved')
                            <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#updateModal">
                                <i class="fas fa-edit"></i> Add Update
                            </button>

                            @if($issue->issue_type === 'drop')
                            <form action="{{ route('issues.mark-dropped', $issue) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Mark student as dropped?')">
                                    <i class="fas fa-ban"></i> Mark Student as Dropped
                                </button>
                            </form>
                            @elseif($issue->issue_type === 'transfer')
                            <button class="btn btn-warning btn-lg" data-toggle="modal" data-target="#transferModal">
                                <i class="fas fa-exchange-alt"></i> Process Transfer
                            </button>
                            @endif

                            <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#resolveModal">
                                <i class="fas fa-check-circle"></i> Mark as Resolved
                            </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('issues.update-status', $issue) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Issue Update</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="updateType">Update Type</label>
                        <select id="updateType" name="update_type" class="form-control" required>
                            <option value="comment">Comment</option>
                            <option value="action">Action Taken</option>
                            <option value="contact">Contact Made</option>
                            <option value="follow-up">Follow-up</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('issues.resolve', $issue) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Mark Issue as Resolved</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="resolution">Resolution Details <span class="text-danger">*</span></label>
                        <textarea id="resolution" name="resolution_details" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Resolved</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('issues.mark-transferred', $issue) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Process Transfer Request</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="newCompany">Transfer to Company <span class="text-danger">*</span></label>
                        <select id="newCompany" name="new_company_id" class="form-control" required>
                            <option value="">-- Select Company --</option>
                            @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transferNotes">Transfer Notes (optional)</label>
                        <textarea id="transferNotes" name="transfer_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
