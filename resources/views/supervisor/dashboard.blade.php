@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@section('breadcrumb')
    <span class="current">Dashboard</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Welcome, {{ auth()->user()->fname }}!</h1>
    <p class="page-subtitle">Manage trainee performance evaluations</p>
</div>

{{-- Quick Stats --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 22px; margin-bottom: 22px;">
    <div class="card card-status">
        <div class="card-body" style="padding: 20px 24px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size:0.82rem; color:var(--gray-500); font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.04em;">Total Trainees</div>
                    <div style="font-size:1.8rem; font-weight:800; color:var(--gray-800);">{{ $totalTrainees }}</div>
                </div>
                <div style="background:var(--primary-light); color:var(--primary); padding:10px; border-radius:12px;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-status">
        <div class="card-body" style="padding: 20px 24px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size:0.82rem; color:var(--gray-500); font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.04em;">Pending Evaluations</div>
                    <div style="font-size:1.8rem; font-weight:800; color:var(--warning);">{{ $pendingEvaluations }}</div>
                </div>
                <div style="background:var(--warning-light); color:var(--warning); padding:10px; border-radius:12px;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-status">
        <div class="card-body" style="padding: 20px 24px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size:0.82rem; color:var(--gray-500); font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.04em;">Approved Evaluations</div>
                    <div style="font-size:1.8rem; font-weight:800; color:var(--success);">{{ $approvedEvaluations }}</div>
                </div>
                <div style="background:var(--success-light); color:var(--success); padding:10px; border-radius:12px;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-status">
        <div class="card-body" style="padding: 20px 24px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <div style="font-size:0.82rem; color:var(--gray-500); font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.04em;">Evaluated Today</div>
                    <div style="font-size:1.8rem; font-weight:800; color:var(--info);">{{ $todayEvaluated }}</div>
                </div>
                <div style="background:var(--info-light); color:var(--info); padding:10px; border-radius:12px;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Evaluations --}}
<div class="card">
    <div class="card-header" style="justify-content:space-between;">
        <div>
            <h2 class="card-title">Recent Evaluations</h2>
            <p class="text-small text-muted">Latest performance evaluations</p>
        </div>
        <a href="{{ route('supervisor.evaluations') }}" class="btn btn-ghost btn-sm">View All</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Trainee</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Date</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Overall Rating</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Status</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentEvaluations as $eval)
                        <tr style="border-bottom:1px solid var(--gray-100);">
                            <td style="padding:14px 24px;">
                                <a href="{{ route('supervisor.trainees.show', $eval->trainee_id) }}" style="color:var(--primary); text-decoration:none; font-weight:600;">
                                    {{ $eval->trainee->full_name }}
                                </a>
                            </td>
                            <td style="padding:14px 24px; font-size:0.9rem;">
                                {{ $eval->evaluation_date->format('M d, Y') }}
                            </td>
                            <td style="padding:14px 24px; font-size:0.9rem; font-weight:600; color:var(--primary);">
                                {{ number_format($eval->average_rating ?? 0, 1) }}/5
                            </td>
                            <td style="padding:14px 24px;">
                                @if($eval->status === 'approved')
                                    <span style="background:var(--success-light); color:var(--success); padding:4px 10px; border-radius:12px; font-size:0.75rem; font-weight:600;">✓ Approved</span>
                                @elseif($eval->status === 'pending')
                                    <span style="background:var(--warning-light); color:var(--warning); padding:4px 10px; border-radius:12px; font-size:0.75rem; font-weight:600;">⏱ Pending</span>
                                @else
                                    <span style="background:var(--danger-light); color:var(--danger); padding:4px 10px; border-radius:12px; font-size:0.75rem; font-weight:600;">⚠ Revision</span>
                                @endif
                            </td>
                            <td style="padding:14px 24px;">
                                <a href="{{ route('supervisor.evaluations.show', $eval->id) }}" class="btn btn-sm btn-ghost" style="font-size:0.75rem;">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:40px 24px; text-align:center; color:var(--gray-400);">
                                No recent evaluations yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- All Trainees --}}
<div class="card" style="margin-top: 22px;">
    <div class="card-header">
        <div>
            <h2 class="card-title">Your Trainees</h2>
            <p class="text-small text-muted">Click "Add Evaluation" to create a new performance evaluation</p>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table" style="width:100%; table-layout:fixed;">
                <thead>
                    <tr>
                        <th style="width:35%; padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Name</th>
                        <th style="width:15%; padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Student #</th>
                        <th style="width:15%; padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Course</th>
                        <th style="width:35%; padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainees as $trainee)
                        <tr style="border-bottom:1px solid var(--gray-100);">
                            <td style="width:35%; padding:14px 24px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                <a href="{{ route('supervisor.trainees.show', $trainee->id) }}" style="color:var(--primary); text-decoration:none; font-weight:600; display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $trainee->full_name }}
                                </a>
                                <div style="font-size:0.8rem; color:var(--gray-500); margin-top:2px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $trainee->email }}</div>
                            </td>
                            <td style="width:15%; padding:14px 24px; font-size:0.9rem; text-align:center;">
                                {{ $trainee->ojtInfo?->student_number ?? '—' }}
                            </td>
                            <td style="width:15%; padding:14px 24px; font-size:0.9rem; text-align:center;">
                                {{ $trainee->ojtInfo?->course ?? '—' }}
                            </td>
                            <td style="width:35%; padding:14px 24px; text-align:center; white-space:nowrap;">
                                <a href="{{ route('supervisor.evaluations.create', $trainee->id) }}" class="btn btn-sm btn-primary" style="font-size:0.75rem; display:inline-block; margin-right:8px;">Add Evaluation</a>
                                <a href="{{ route('supervisor.trainees.show', $trainee->id) }}" class="btn btn-sm btn-ghost" style="font-size:0.75rem; display:inline-block;">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding:40px 24px; text-align:center; color:var(--gray-400);">
                                No trainees assigned.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
