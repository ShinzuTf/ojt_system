@extends('layouts.app')

@section('title', 'Evaluation Details')

@section('breadcrumb')
    <a href="{{ route('supervisor.dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <a href="{{ route('supervisor.evaluations') }}">Evaluations</a>
    <span class="separator">/</span>
    <span class="current">Details</span>
@endsection

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
    <div>
        <h1 class="page-title">Evaluation for {{ $evaluation->trainee->full_name }}</h1>
        <p class="page-subtitle">{{ $evaluation->evaluation_date->format('F d, Y') }}</p>
    </div>
    <div style="display:flex; gap:12px;">
        @if($evaluation->status === 'pending')
            <a href="{{ route('supervisor.evaluations.edit', $evaluation->id) }}" class="btn btn-ghost">Edit</a>
            <form method="POST" action="{{ route('supervisor.evaluations.approve', $evaluation->id) }}" style="display:inline;">
                @csrf
                <button class="btn btn-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px; display:inline-block;"><path d="M9 12l2 2 4-4"/></svg>
                    Approve
                </button>
            </form>
        @endif
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:22px; margin-bottom:22px;">
    <div class="card">
        <div class="card-body" style="padding:20px 24px;">
            <div style="font-size:0.8rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:8px;">Status</div>
            <div>
                @if($evaluation->status === 'approved')
                    <span style="background:var(--success-light); color:var(--success); padding:6px 12px; border-radius:12px; font-size:0.85rem; font-weight:600;">✓ Approved</span>
                @elseif($evaluation->status === 'pending')
                    <span style="background:var(--warning-light); color:var(--warning); padding:6px 12px; border-radius:12px; font-size:0.85rem; font-weight:600;">⏱ Pending</span>
                @else
                    <span style="background:var(--danger-light); color:var(--danger); padding:6px 12px; border-radius:12px; font-size:0.85rem; font-weight:600;">⚠ Needs Revision</span>
                @endif
            </div>
            @if($evaluation->approved_at)
                <div style="margin-top:16px; font-size:0.8rem; color:var(--gray-600);">
                    <strong>Approved on:</strong> {{ $evaluation->approved_at->format('F d, Y \a\t h:i A') }}
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding:20px 24px;">
            <div style="font-size:0.8rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:8px;">Overall Rating</div>
            <div style="font-size:1.8rem; font-weight:800; color:var(--primary);">{{ number_format($evaluation->average_rating, 1) }}<span style="font-size:0.9rem; color:var(--gray-500);">/5</span></div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:22px;">
    <div class="card-header">
        <h2 class="card-title">Trainee Information</h2>
    </div>
    <div class="card-body" style="padding:24px;">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
            <div>
                <div style="font-size:0.8rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Name</div>
                <div style="font-size:1rem; color:var(--gray-800); font-weight:600;">{{ $evaluation->trainee->full_name }}</div>
            </div>
            <div>
                <div style="font-size:0.8rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Email</div>
                <div style="font-size:0.9rem; color:var(--primary);">{{ $evaluation->trainee->email }}</div>
            </div>
            <div>
                <div style="font-size:0.8rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Student Number</div>
                <div style="font-size:0.95rem; color:var(--gray-800);">{{ $evaluation->trainee->ojtInfo?->student_number ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:0.8rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Company</div>
                <div style="font-size:0.95rem; color:var(--gray-800);">{{ $evaluation->trainee->ojtInfo?->company_name ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:22px;">
    <div class="card-header">
        <h2 class="card-title">Performance Feedback</h2>
    </div>
    <div class="card-body" style="padding:24px;">
        <div style="margin-bottom:20px;">
            <div style="font-size:0.9rem; color:var(--gray-600); font-weight:600; margin-bottom:8px;">Strengths</div>
            <div style="line-height:1.6; color:var(--gray-700);">
                {{ $evaluation->strengths }}
            </div>
        </div>
        <div style="margin-bottom:20px;">
            <div style="font-size:0.9rem; color:var(--gray-600); font-weight:600; margin-bottom:8px;">Areas for Improvement</div>
            <div style="line-height:1.6; color:var(--gray-700);">
                {{ $evaluation->areas_for_improvement }}
            </div>
        </div>
        <div style="margin-bottom:20px;">
            <div style="font-size:0.9rem; color:var(--gray-600); font-weight:600; margin-bottom:8px;">Skills to Develop</div>
            <div style="line-height:1.6; color:var(--gray-700);">
                {{ $evaluation->skills_to_develop }}
            </div>
        </div>
        @if($evaluation->overall_comments)
            <div>
                <div style="font-size:0.9rem; color:var(--gray-600); font-weight:600; margin-bottom:8px;">Overall Comments</div>
                <div style="line-height:1.6; color:var(--gray-700);">
                    {{ $evaluation->overall_comments }}
                </div>
            </div>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Performance Ratings</h2>
    </div>
    <div class="card-body" style="padding:24px;">
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap:20px; text-align:center;">
            <div>
                <div style="font-size:2rem; font-weight:800; color:var(--primary); margin-bottom:8px;">
                    {{ $evaluation->technical_skills_rating ?? '—' }}<span style="font-size:0.9rem; color:var(--gray-400);">/5</span>
                </div>
                <div style="font-size:0.9rem; color:var(--gray-600);">Technical Skills</div>
            </div>
            <div>
                <div style="font-size:2rem; font-weight:800; color:var(--info); margin-bottom:8px;">
                    {{ $evaluation->communication_rating ?? '—' }}<span style="font-size:0.9rem; color:var(--gray-400);">/5</span>
                </div>
                <div style="font-size:0.9rem; color:var(--gray-600);">Communication</div>
            </div>
            <div>
                <div style="font-size:2rem; font-weight:800; color:var(--success); margin-bottom:8px;">
                    {{ $evaluation->teamwork_rating ?? '—' }}<span style="font-size:0.9rem; color:var(--gray-400);">/5</span>
                </div>
                <div style="font-size:0.9rem; color:var(--gray-600);">Teamwork</div>
            </div>
            <div>
                <div style="font-size:2rem; font-weight:800; color:var(--warning); margin-bottom:8px;">
                    {{ $evaluation->professionalism_rating ?? '—' }}<span style="font-size:0.9rem; color:var(--gray-400);">/5</span>
                </div>
                <div style="font-size:0.9rem; color:var(--gray-600);">Professionalism</div>
            </div>
            <div>
                <div style="font-size:2rem; font-weight:800; color:var(--secondary); margin-bottom:8px;">
                    {{ $evaluation->initiative_rating ?? '—' }}<span style="font-size:0.9rem; color:var(--gray-400);">/5</span>
                </div>
                <div style="font-size:0.9rem; color:var(--gray-600);">Initiative</div>
            </div>
        </div>
    </div>
</div>

<div style="display:flex; gap:12px; justify-content:flex-end; margin-top:22px;">
    <a href="{{ route('supervisor.trainees.show', $evaluation->trainee_id) }}" class="btn btn-ghost">Back</a>
</div>

@endsection
