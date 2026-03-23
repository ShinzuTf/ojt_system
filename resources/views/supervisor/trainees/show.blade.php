@extends('layouts.app')

@section('title', 'Trainee: ' . $trainee->full_name)

@section('breadcrumb')
    <a href="{{ route('supervisor.dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <a href="{{ route('supervisor.trainees') }}">Trainees</a>
    <span class="separator">/</span>
    <span class="current">{{ $trainee->fname }}</span>
@endsection

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start;">
    <div>
        <h1 class="page-title">{{ $trainee->full_name }}</h1>
        <p class="page-subtitle">Performance evaluations and feedback</p>
    </div>
    <a href="{{ route('supervisor.evaluations.create', $trainee->id) }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px; display:inline-block;"><path d="M12 4v16m8-8H4"/></svg>
        Add Evaluation
    </a>
</div>

{{-- Trainee Info --}}
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:22px;">
    <div class="card">
        <div class="card-body" style="padding:16px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Student Number</div>
            <div style="font-size:1rem; font-weight:600; color:var(--gray-800);">{{ $trainee->ojtInfo?->student_number ?? '—' }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:16px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Course</div>
            <div style="font-size:1rem; font-weight:600; color:var(--gray-800);">{{ $trainee->ojtInfo?->course ?? '—' }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:16px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Company</div>
            <div style="font-size:1rem; font-weight:600; color:var(--gray-800);">{{ $trainee->ojtInfo?->company_name ?? '—' }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:16px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Email</div>
            <div style="font-size:0.9rem; color:var(--primary); word-break:break-all;">{{ $trainee->email }}</div>
        </div>
    </div>
</div>

{{-- Evaluation Statistics --}}
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:22px;">
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Total Evaluations</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--success);">{{ $stats['total_evaluations'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Approved Evaluations</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--success);">{{ $stats['approved'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Pending Evaluations</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--warning);">{{ $stats['pending'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Needs Revision</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--danger);">{{ $stats['needs_revision'] }}</div>
        </div>
    </div>
</div>

{{-- Average Ratings --}}
<div class="card" style="margin-bottom:22px;">
    <div class="card-header">
        <h2 class="card-title">Performance Ratings (Average)</h2>
    </div>
    <div class="card-body" style="padding:24px; display:grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap:20px;">
        <div style="text-align:center;">
            <div style="font-size:2.5rem; font-weight:800; color:var(--primary); margin-bottom:8px;">
                {{ number_format($avgRatings['technical_skills'] ?? 0, 1) }}/5
            </div>
            <div style="font-size:0.9rem; color:var(--gray-600); font-weight:500;">Technical Skills</div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:2.5rem; font-weight:800; color:var(--info); margin-bottom:8px;">
                {{ number_format($avgRatings['communication'] ?? 0, 1) }}/5
            </div>
            <div style="font-size:0.9rem; color:var(--gray-600); font-weight:500;">Communication</div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:2.5rem; font-weight:800; color:var(--success); margin-bottom:8px;">
                {{ number_format($avgRatings['teamwork'] ?? 0, 1) }}/5
            </div>
            <div style="font-size:0.9rem; color:var(--gray-600); font-weight:500;">Teamwork</div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:2.5rem; font-weight:800; color:var(--warning); margin-bottom:8px;">
                {{ number_format($avgRatings['professionalism'] ?? 0, 1) }}/5
            </div>
            <div style="font-size:0.9rem; color:var(--gray-600); font-weight:500;">Professionalism</div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:2.5rem; font-weight:800; color:var(--secondary); margin-bottom:8px;">
                {{ number_format($avgRatings['initiative'] ?? 0, 1) }}/5
            </div>
            <div style="font-size:0.9rem; color:var(--gray-600); font-weight:500;">Initiative</div>
        </div>
    </div>
</div>

{{-- Evaluations List --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Evaluation History</h2>
    </div>
    <div class="card-body" style="padding:0;">
        @forelse($evaluations as $eval)
            <div style="border-bottom:1px solid var(--gray-100); padding:18px 24px; display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="flex:1;">
                    <div style="font-weight:600; color:var(--gray-800); margin-bottom:8px;">
                        {{ $eval->evaluation_date->format('F d, Y') }}
                    </div>
                    <div style="font-size:0.85rem; color:var(--gray-600); line-height:1.5;">
                        <strong>Strengths:</strong> {{ Str::limit($eval->strengths, 80) }}<br>
                        <strong>Avg Rating:</strong> {{ number_format($eval->average_rating, 1) }}/5
                    </div>
                    @if($eval->areas_for_improvement)
                        <div style="font-size:0.8rem; background:var(--gray-50); padding:10px 12px; margin-top:10px; border-left:3px solid var(--primary); border-radius:0 4px 4px 0;">
                            <strong>Areas for Improvement:</strong> {{ Str::limit($eval->areas_for_improvement, 100) }}
                        </div>
                    @endif
                </div>
                <div style="margin-left:16px; text-align:right;">
                    @if($eval->status === 'approved')
                        <span style="background:var(--success-light); color:var(--success); padding:6px 12px; border-radius:16px; font-size:0.75rem; font-weight:600; display:inline-block; margin-bottom:12px;">✓ Approved</span>
                    @elseif($eval->status === 'pending')
                        <span style="background:var(--warning-light); color:var(--warning); padding:6px 12px; border-radius:16px; font-size:0.75rem; font-weight:600; display:inline-block; margin-bottom:12px;">⏱ Pending</span>
                    @else
                        <span style="background:var(--danger-light); color:var(--danger); padding:6px 12px; border-radius:16px; font-size:0.75rem; font-weight:600; display:inline-block; margin-bottom:12px;">⚠ Revision</span>
                    @endif
                    <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                        <a href="{{ route('supervisor.evaluations.show', $eval->id) }}" class="btn btn-sm btn-ghost" style="font-size:0.75rem;">View</a>
                        @if($eval->status !== 'approved')
                            <a href="{{ route('supervisor.evaluations.edit', $eval->id) }}" class="btn btn-sm btn-ghost" style="font-size:0.75rem;">Edit</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="padding:40px 24px; text-align:center; color:var(--gray-400);">
                No evaluations yet. <a href="{{ route('supervisor.evaluations.create', $trainee->id) }}" style="color:var(--primary); text-decoration:none;">Create one</a>
            </div>
        @endforelse

        @if($evaluations->hasPages())
            <div style="padding:18px 24px; border-top:1px solid var(--gray-100); display:flex; justify-content:center;">
                {{ $evaluations->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
