@extends('layouts.app')

@section('title', 'OJT Progress')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">OJT Progress</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">OJT Progress Monitoring</h1>
    <p class="page-subtitle">Track your overall On-the-Job Training document compliance and progress</p>
</div>

{{-- Progress Overview --}}
<div class="card mb-3">
    <div class="card-header">
        <h2 class="card-title">Overall Compliance Progress</h2>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:28px; align-items: center;">
            {{-- Circle Progress (visual) --}}
            <div style="text-align:center;">
                <div style="position:relative; display:inline-block; width:160px; height:160px;">
                    <svg width="160" height="160" viewBox="0 0 160 160" style="transform: rotate(-90deg);">
                        <circle cx="80" cy="80" r="70" fill="none" stroke="var(--gray-200)" stroke-width="12"/>
                        <circle cx="80" cy="80" r="70" fill="none" stroke="var(--purple-600)" stroke-width="12" stroke-dasharray="440" stroke-dashoffset="149" stroke-linecap="round"/>
                    </svg>
                    <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span style="font-size:2rem; font-weight:700; color:var(--gray-900);">66%</span>
                        <span style="font-size:0.78rem; color:var(--gray-500);">Completed</span>
                    </div>
                </div>
            </div>

            {{-- Breakdown --}}
            <div>
                <div style="margin-bottom:16px;">
                    <div class="d-flex justify-between items-center mb-1">
                        <span class="text-small fw-600" style="color:var(--success);">Approved</span>
                        <span class="text-small fw-600">2 / 6</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill green" style="width:33%;"></div></div>
                </div>
                <div style="margin-bottom:16px;">
                    <div class="d-flex justify-between items-center mb-1">
                        <span class="text-small fw-600" style="color:var(--purple-600);">Under Review</span>
                        <span class="text-small fw-600">1 / 6</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill" style="width:16%;"></div></div>
                </div>
                <div style="margin-bottom:16px;">
                    <div class="d-flex justify-between items-center mb-1">
                        <span class="text-small fw-600" style="color:var(--danger);">Rejected</span>
                        <span class="text-small fw-600">1 / 6</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill red" style="width:16%;"></div></div>
                </div>
                <div>
                    <div class="d-flex justify-between items-center mb-1">
                        <span class="text-small fw-600" style="color:var(--gray-400);">Not Submitted</span>
                        <span class="text-small fw-600">2 / 6</span>
                    </div>
                    <div class="progress-bar"><div class="progress-fill" style="width:33%; background:var(--gray-300);"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Document Checklist --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Document Checklist</h2>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:center; color:var(--success);"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></td>
                        <td><strong>Application Letter</strong></td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Approved</span></td>
                        <td>Feb 10, 2026</td>
                        <td>Feb 11, 2026</td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:var(--success);"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></td>
                        <td><strong>Training Agreement</strong></td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Approved</span></td>
                        <td>Feb 10, 2026</td>
                        <td>Feb 11, 2026</td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:var(--danger);"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg></td>
                        <td><strong>Consent Form</strong></td>
                        <td><span class="badge badge-rejected"><span class="badge-dot"></span> Rejected</span></td>
                        <td>Feb 12, 2026</td>
                        <td>Feb 13, 2026</td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:var(--purple-500);"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></td>
                        <td><strong>Progress Report</strong></td>
                        <td><span class="badge badge-review"><span class="badge-dot"></span> Under Review</span></td>
                        <td>Feb 14, 2026</td>
                        <td>Feb 14, 2026</td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:var(--gray-300);"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg></td>
                        <td><strong>Accomplishment Report</strong></td>
                        <td><span class="badge badge-not-submitted"><span class="badge-dot"></span> Not Submitted</span></td>
                        <td class="text-muted">—</td>
                        <td class="text-muted">—</td>
                    </tr>
                    <tr>
                        <td style="text-align:center; color:var(--gray-300);"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg></td>
                        <td><strong>Final Evaluation</strong></td>
                        <td><span class="badge badge-not-submitted"><span class="badge-dot"></span> Not Submitted</span></td>
                        <td class="text-muted">—</td>
                        <td class="text-muted">—</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
