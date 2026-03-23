@extends('layouts.app')

@section('title', 'Submission History')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">Submission History</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Submission History</h1>
    <p class="page-subtitle">View all your document submissions and their current statuses</p>
</div>

{{-- Filter Tabs --}}
<div class="tabs">
    <button class="tab-btn active" data-tab="tab-all">All Documents</button>
    <button class="tab-btn" data-tab="tab-approved">Approved</button>
    <button class="tab-btn" data-tab="tab-pending">Pending</button>
    <button class="tab-btn" data-tab="tab-rejected">Rejected</button>
</div>

{{-- Submissions Table --}}
<div class="card" id="tab-all">
    <div class="card-body" style="padding: 0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>File Name</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>NBI Endorsement Letter</strong></td>
                        <td class="text-muted">nbi_endorsement_final.pdf</td>
                        <td>Feb 10, 2026</td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Approved</span></td>
                        <td class="text-muted">—</td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn view" title="View Document">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button class="table-action-btn" title="Download">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>MOA / Training Agreement</strong></td>
                        <td class="text-muted">moa_nbi_signed.pdf</td>
                        <td>Feb 10, 2026</td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Approved</span></td>
                        <td class="text-muted">—</td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn view" title="View"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                <button class="table-action-btn" title="Download"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Parental Consent Form</strong></td>
                        <td class="text-muted">parent_consent_form.pdf</td>
                        <td>Feb 12, 2026</td>
                        <td><span class="badge badge-rejected"><span class="badge-dot"></span> Rejected</span></td>
                        <td style="max-width:220px; font-size:0.82rem; color:var(--danger);">Please use the official template from the downloads section.</td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn view" title="View"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                <a href="{{ route('student.documents.submit') }}" class="btn btn-danger btn-sm" style="font-size:0.76rem;">Re-upload</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>OJT Weekly Reports</strong></td>
                        <td class="text-muted">weekly_reports_complete.docx</td>
                        <td>Feb 14, 2026</td>
                        <td><span class="badge badge-review"><span class="badge-dot"></span> Under Review</span></td>
                        <td class="text-muted">—</td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn view" title="View"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                <button class="table-action-btn" title="Download"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3M3 17v3a2 2 0 002 2h14a2 2 0 002-2v-3"/></svg></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Communication Letter</strong></td>
                        <td class="text-muted">—</td>
                        <td class="text-muted">—</td>
                        <td><span class="badge badge-not-submitted"><span class="badge-dot"></span> Not Submitted</span></td>
                        <td class="text-muted">—</td>
                        <td>
                            <a href="{{ route('student.documents.submit') }}" class="btn btn-primary btn-sm" style="font-size:0.76rem;">Upload</a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Final Evaluation</strong></td>
                        <td class="text-muted">—</td>
                        <td class="text-muted">—</td>
                        <td><span class="badge badge-not-submitted"><span class="badge-dot"></span> Not Submitted</span></td>
                        <td class="text-muted">—</td>
                        <td>
                            <a href="{{ route('student.documents.submit') }}" class="btn btn-primary btn-sm" style="font-size:0.76rem;">Upload</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
