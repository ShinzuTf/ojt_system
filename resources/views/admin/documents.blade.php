@extends('layouts.app')

@section('title', 'Document Review')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Document Review</span>
@endsection

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1 class="page-title">Document Review</h1>
        <p class="page-subtitle">Review, approve, or reject student OJT document submissions</p>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="tabs">
    <button class="tab-btn active" data-tab="tab-pending">Pending Review <span style="background:var(--warning-light); color:var(--warning); padding:2px 8px; border-radius:10px; font-size:0.72rem; margin-left:4px;">23</span></button>
    <button class="tab-btn" data-tab="tab-approved">Approved</button>
    <button class="tab-btn" data-tab="tab-approved">Rejected</button>
    <button class="tab-btn" data-tab="tab-all">All</button>
</div>

{{-- Toolbar --}}
<div class="card mb-3" style="border:none; box-shadow:none; background:transparent;">
    <div class="table-toolbar">
        <div class="table-search">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="1 1 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" placeholder="Search by student name or document...">
        </div>
        <div class="table-filters">
            <select class="form-select" style="padding:8px 32px 8px 14px; font-size:0.84rem; width:auto;">
                <option value="">All Document Types</option>
                <option>NBI Endorsement Letter</option>
                <option>MOA / Training Agreement</option>
                <option>Communication Letter</option>
                <option>Parental Consent Form</option>
                <option>OJT Weekly Reports</option>
                <option>Final Evaluation</option>
            </select>
            <select class="form-select" style="padding:8px 32px 8px 14px; font-size:0.84rem; width:auto;">
                <option value="">All Courses</option>
                <option>BSIT</option>
                <option>BSCS</option>
                <option>BSIS</option>
                <option>ACT</option>
            </select>
        </div>
    </div>
</div>

{{-- Documents Table --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Document Name</th>
                        <th>File</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th style="width:200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr>
                        <td>
                            <div style="font-weight:700; color:var(--gray-800);">{{ $doc->student?->full_name }}</div>
                            <div style="font-size:0.75rem; color:var(--gray-500);">{{ $doc->student?->email }}</div>
                        </td>
                        <td>{{ $doc->type_label }}</td>
                        <td>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="d-flex items-center gap-1" style="color:var(--primary); text-decoration:none;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="text-small">{{ $doc->file_name }}</span>
                            </a>
                        </td>
                        <td>{{ $doc->created_at->format('M d, Y') }}</td>
                        <td><span class="badge badge-{{ $doc->status }}"><span class="badge-dot"></span> {{ ucfirst($doc->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                               @if($doc->status === 'submitted')
                                    <form action="{{ route('admin.documents.approve', $doc->id) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success" style="font-size:0.76rem;" title="Approve">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                            Approve
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-danger" style="font-size:0.76rem;" onclick="openRejectModal({{ $doc->id }}, '{{ addslashes($doc->type_label) }}', '{{ addslashes($doc->student?->full_name) }}')">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                        Reject
                                    </button>
                                @else
                                    <span class="text-muted text-small">Reviewed by {{ $doc->reviewer?->fname }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:48px; color:var(--gray-400);">
                            No document submissions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('modals')
{{-- Review Modal --}}
<div class="modal-overlay" id="reviewModal">
    <div class="modal" style="max-width:640px;">
        <div class="modal-header">
            <h3 class="modal-title">Review Document</h3>
            <button class="modal-close" onclick="closeModal('reviewModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-grid" style="margin-bottom:18px;">
                <div class="form-group">
                    <label class="form-label">Student</label>
                    <div style="padding:6px 0; font-weight:600;">Dela Cruz, Juan Andres</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Student No.</label>
                    <div style="padding:6px 0; font-weight:600;">00038630</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Document Type</label>
                    <div style="padding:6px 0; font-weight:600;">Progress Report</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Date Submitted</label>
                    <div style="padding:6px 0; font-weight:600;">Feb 14, 2026</div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:18px;">
                <label class="form-label">Uploaded File</label>
                <div style="display:flex; align-items:center; gap:10px; padding:12px 16px; background:var(--gray-50); border-radius:8px; border:1px solid var(--gray-200);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="color:var(--danger);"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <div style="flex:1;">
                        <div style="font-weight:600; font-size:0.88rem;">progress_report_feb2026.pdf</div>
                        <div style="font-size:0.75rem; color:var(--gray-400);">PDF • 1.2 MB</div>
                    </div>
                    <button class="btn btn-sm btn-secondary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg>
                        Download
                    </button>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:18px;">
                <label class="form-label">Decision <span class="required">*</span></label>
                <div style="display:flex; gap:12px;">
                    <label class="form-check" style="padding:10px 16px; border:1px solid var(--gray-200); border-radius:8px; flex:1; cursor:pointer;">
                        <input type="radio" name="decision" value="approve"> <span style="font-weight:600; color:var(--success);">Approve</span>
                    </label>
                    <label class="form-check" style="padding:10px 16px; border:1px solid var(--gray-200); border-radius:8px; flex:1; cursor:pointer;">
                        <input type="radio" name="decision" value="reject"> <span style="font-weight:600; color:var(--danger);">Reject</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Remarks / Feedback</label>
                <textarea class="form-input" placeholder="Add comments or feedback for the student..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('reviewModal')">Cancel</button>
            <button class="btn btn-primary">Submit Review</button>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal-overlay" id="rejectModal">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header">
            <h3 class="modal-title">Reject Document</h3>
            <button class="modal-close" onclick="closeModal('rejectModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="modal-body">
                <div class="alert alert-warning" style="margin-bottom:18px; display:flex; gap:10px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Rejecting <strong id="reject-doc-name"></strong> for <strong id="reject-student-name"></strong>. The student will be notified.</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Reason for Rejection <span class="required">*</span></label>
                    <textarea name="remarks" class="form-input" placeholder="Explain why this document is being rejected..." required></textarea>
                    <span class="form-help">This message will be visible to the student.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openRejectModal(id, docName, studentName) {
    document.getElementById('reject-doc-name').textContent = docName;
    document.getElementById('reject-student-name').textContent = studentName;
    document.getElementById('rejectForm').action = '/admin/documents/' + id + '/reject';
    openModal('rejectModal');
}
</script>
@endpush
