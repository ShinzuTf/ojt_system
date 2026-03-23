@extends('layouts.app')

@section('title', 'Evaluations')

@section('breadcrumb')
    <a href="{{ route('supervisor.dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <span class="current">Evaluations</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class=\"page-title\">Performance Evaluations</h1>
    <p class=\"page-subtitle\">Manage and track all trainee performance evaluations</p>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:22px;">
    <div class="card-body" style="padding:18px 24px;">
        <form method="GET" action="{{ route('supervisor.evaluations') }}" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">
            <div>
                <label style="font-size:0.8rem; font-weight:600; color:var(--gray-600); display:block; margin-bottom:6px;">Trainee</label>
                <select name="trainee_id" style="width:100%; padding:8px 12px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem;">
                    <option value="">All Trainees</option>
                    @foreach($trainees as $id => $name)
                        <option value="{{ $id }}" {{ request('trainee_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size:0.8rem; font-weight:600; color:var(--gray-600); display:block; margin-bottom:6px;">Status</label>
                <select name="status" style="width:100%; padding:8px 12px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem;">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="needs_revision" {{ request('status') == 'needs_revision' ? 'selected' : '' }}>Needs Revision</option>
                </select>
            </div>

            <div>
                <label style="font-size:0.8rem; font-weight:600; color:var(--gray-600); display:block; margin-bottom:6px;">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    style="width:100%; padding:8px 12px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem;">
            </div>

            <div>
                <label style="font-size:0.8rem; font-weight:600; color:var(--gray-600); display:block; margin-bottom:6px;">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    style="width:100%; padding:8px 12px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem;">
            </div>

            <div style="display:flex; gap:8px; align-items:flex-end;">
                <button type="submit" class="btn btn-primary" style="flex:1;">Filter</button>
                @if(request()->query())
                    <a href="{{ route('supervisor.evaluations') }}" class="btn btn-ghost">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Evaluations Table --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Trainee</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Date</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Performance Rating</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Status</th>
                        <th style="padding:12px 24px; font-weight:600; font-size:0.85rem; color:var(--gray-600); border-bottom:1px solid var(--gray-100); background:var(--gray-50);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($evaluations as $eval)
                        <tr style="border-bottom:1px solid var(--gray-100);">
                            <td style="padding:14px 24px;">
                                <a href="{{ route('supervisor.trainees.show', $eval->trainee_id) }}" style="color:var(--primary); text-decoration:none; font-weight:600;">
                                    {{ $eval->trainee->full_name }}
                                </a>
                            </td>
                            <td style="padding:14px 24px; font-size:0.9rem;">
                                {{ $eval->evaluation_date->format('M d, Y') }}
                            </td>
                            <td style="padding:14px 24px; font-weight:600; color:var(--primary);">
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
                                <div style="display:flex; gap:6px;">
                                    <a href="{{ route('supervisor.evaluations.show', $eval->id) }}" class="btn btn-sm btn-ghost" style="font-size:0.75rem;">View</a>
                                    @if($eval->status !== 'approved')
                                        <a href="{{ route('supervisor.evaluations.edit', $eval->id) }}" class="btn btn-sm btn-ghost" style="font-size:0.75rem;">Edit</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:40px 24px; text-align:center; color:var(--gray-400);">
                                No evaluations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($evaluations->hasPages())
            <div style="padding:18px 24px; border-top:1px solid var(--gray-100); display:flex; justify-content:center;">
                {{ $evaluations->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
