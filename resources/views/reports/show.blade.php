@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-alt"></i> Report Details</h4>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-light">Back</a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Submitted By:</strong><br>
                                {{ $report->submittedBy->full_name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Period:</strong><br>
                                {{ $report->report_period_start->format('M d, Y') }} - {{ $report->report_period_end->format('M d, Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p>
                                <strong>Report Type:</strong><br>
                                <span class="badge badge-info" style="font-size: 1rem;">{{ ucfirst($report->report_type) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Status:</strong><br>
                                @if($report->status === 'approved')
                                    <span class="badge badge-success" style="font-size: 1rem;">Approved</span>
                                @elseif($report->status === 'submitted')
                                    <span class="badge badge-info" style="font-size: 1rem;">Submitted</span>
                                @elseif($report->status === 'rejected')
                                    <span class="badge badge-danger" style="font-size: 1rem;">Rejected</span>
                                @else
                                    <span class="badge badge-secondary" style="font-size: 1rem;">Draft</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Accomplishments</h5>
                        <p class="text-muted">{{ $report->accomplishments }}</p>
                    </div>

                    @if($report->key_learnings)
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Key Learnings</h5>
                        <p class="text-muted">{{ $report->key_learnings }}</p>
                    </div>
                    @endif

                    @if($report->challenges_faced)
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Challenges Encountered</h5>
                        <p class="text-muted">{{ $report->challenges_faced }}</p>
                    </div>
                    @endif

                    @if($report->next_steps)
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Next Steps</h5>
                        <p class="text-muted">{{ $report->next_steps }}</p>
                    </div>
                    @endif

                    @if($report->status === 'rejected' && $report->rejection_comments)
                    <div class="alert alert-danger mb-4">
                        <h5 class="alert-heading">Rejection Reason</h5>
                        {{ $report->rejection_comments }}
                    </div>
                    @endif

                    <!-- Actions for Supervisors/Coordinators -->
                    @if(auth()->user()->isSupervisor() && $report->status === 'submitted')
                    <div class="mt-4">
                        <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#approveModal">
                            <i class="fas fa-check"></i> Approve Report
                        </button>
                        <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#rejectModal">
                            <i class="fas fa-times"></i> Reject Report
                        </button>
                        <button class="btn btn-warning btn-lg" data-toggle="modal" data-target="#escalateModal">
                            <i class="fas fa-arrow-up"></i> Escalate to Coordinator
                        </button>
                    </div>
                    @endif

                    <!-- Actions for Students -->
                    @if(auth()->user()->isStudent() && $report->status !== 'approved')
                    <div class="mt-4">
                        @if($report->status === 'draft')
                        <a href="{{ route('reports.edit', $report) }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-edit"></i> Edit Report
                        </a>
                        @endif

                        @if($report->status === 'draft' || $report->status === 'rejected')
                        <form action="{{ route('reports.submit', $report) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Submit Report
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif

                    <!-- Report History -->
                    @if($report->history->count() > 0)
                    <hr class="mt-4">
                    <h5 class="font-weight-bold mt-4 mb-3">Report History</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Changed At</th>
                                    <th>Changed By</th>
                                    <th>Action</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report->history as $history)
                                <tr>
                                    <td><small>{{ $history->created_at->format('M d, Y h:i A') }}</small></td>
                                    <td><small>{{ $history->changedBy->short_name ?? 'System' }}</small></td>
                                    <td><span class="badge badge-secondary">{{ ucfirst($history->action_type) }}</span></td>
                                    <td><small>{{ $history->notes ?? '-' }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('reports.approve', $report) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Approve Report</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this report?</p>
                    <div class="form-group">
                        <label for="approvalNotes">Comments (optional)</label>
                        <textarea id="approvalNotes" name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('reports.reject', $report) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Report</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejectionReason">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea id="rejectionReason" name="rejection_comments" class="form-control" rows="4" required></textarea>
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

<!-- Escalate Modal -->
<div class="modal fade" id="escalateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('reports.escalate', $report) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Escalate to Coordinator</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This report will be escalated to the school coordinator for review.</p>
                    <div class="form-group">
                        <label for="escalationReason">Reason for Escalation (optional)</label>
                        <textarea id="escalationReason" name="escalation_reason" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Escalate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
