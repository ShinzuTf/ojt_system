@extends('layouts.app')

@section('title', 'My Evaluations')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <span class="current">Evaluations</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">My Performance Evaluations</h1>
    <p class="page-subtitle">View your performance evaluations from your supervisor</p>
</div>

{{-- Evaluation Statistics --}}
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:22px;">
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Total Evaluations</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--primary);">{{ $stats['total_evaluations'] }}</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Avg. Technical Skills</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--primary);">{{ number_format($stats['avg_technical_skills'] ?? 0, 1) }}/5</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Avg. Communication</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--info);">{{ number_format($stats['avg_communication'] ?? 0, 1) }}/5</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Avg. Teamwork</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--success);">{{ number_format($stats['avg_teamwork'] ?? 0, 1) }}/5</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Avg. Professionalism</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--warning);">{{ number_format($stats['avg_professionalism'] ?? 0, 1) }}/5</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="padding:18px 20px;">
            <div style="font-size:0.75rem; color:var(--gray-500); font-weight:600; text-transform:uppercase; margin-bottom:6px;">Avg. Initiative</div>
            <div style="font-size:1.5rem; font-weight:800; color:var(--secondary);">{{ number_format($stats['avg_initiative'] ?? 0, 1) }}/5</div>
        </div>
    </div>
</div>

{{-- Evaluations List --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Evaluations</h2>
    </div>
    <div class="card-body" style="padding:0;">
        @forelse($evaluations as $eval)
            <div style="border-bottom:1px solid var(--gray-100); padding:20px 24px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px;">
                    <div>
                        <div style="font-weight:700; font-size:1rem; color:var(--gray-800);">
                            {{ $eval->evaluation_date->format('F d, Y') }}
                        </div>
                        <div style="font-size:0.85rem; color:var(--gray-500); margin-top:4px;">
                            by {{ $eval->supervisor->full_name }}
                        </div>
                    </div>
                </div>

                <div style="background:var(--gray-50); padding:16px; border-radius:8px; margin-bottom:12px;">
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap:16px; font-size:0.9rem;">
                        <div>
                            <div style="color:var(--gray-500); font-weight:600; font-size:0.8rem; margin-bottom:4px;">Technical Skills</div>
                            <div style="font-weight:700; color:var(--primary);">{{ $eval->technical_skills_rating ?? '—' }}/5</div>
                        </div>
                        <div>
                            <div style="color:var(--gray-500); font-weight:600; font-size:0.8rem; margin-bottom:4px;">Communication</div>
                            <div style="font-weight:700; color:var(--primary);">{{ $eval->communication_rating ?? '—' }}/5</div>
                        </div>
                        <div>
                            <div style="color:var(--gray-500); font-weight:600; font-size:0.8rem; margin-bottom:4px;">Teamwork</div>
                            <div style="font-weight:700; color:var(--info);">{{ $eval->teamwork_rating ?? '—' }}/5</div>
                        </div>
                        <div>
                            <div style="color:var(--gray-500); font-weight:600; font-size:0.8rem; margin-bottom:4px;">Professionalism</div>
                            <div style="font-weight:700; color:var(--success);">{{ $eval->professionalism_rating ?? '—' }}/5</div>
                        </div>
                        <div>
                            <div style="color:var(--gray-500); font-weight:600; font-size:0.8rem; margin-bottom:4px;">Initiative</div>
                            <div style="font-weight:700; color:var(--info);">{{ $eval->initiative_rating ?? '—' }}/5</div>
                        </div>
                    </div>
                </div>

                <div>
                    <div style="font-size:0.85rem; font-weight:600; color:var(--gray-700); margin-bottom:6px;">Strengths:</div>
                    <div style="font-size:0.9rem; color:var(--gray-600); line-height:1.5;">
                        {{ Str::limit($eval->strengths, 200) }}
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <div style="font-size:0.85rem; font-weight:600; color:var(--gray-700); margin-bottom:6px;">Areas for Improvement:</div>
                    <div style="font-size:0.9rem; color:var(--gray-600); line-height:1.5;">
                        {{ Str::limit($eval->areas_for_improvement, 200) }}
                    </div>
                </div>

                @if($eval->overall_comments)
                    <div style=\"margin-top:12px; padding:12px; background:var(--info-light); border-radius:6px; border-left:3px solid var(--info);\">
                        <div style=\"font-size:0.8rem; font-weight:600; color:var(--info-dark); margin-bottom:4px;\">Overall Comments:</div>
                        <div style=\"font-size:0.9rem; color:var(--info-dark);\">{{ $eval->overall_comments }}</div>
                    </div>
                @endif
            </div>
        @empty
            <div style="padding:40px 24px; text-align:center; color:var(--gray-400);">
                <svg width="50" height="50" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:auto; margin-bottom:16px; opacity:0.3;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <div style="font-size:1rem; font-weight:600; margin-bottom:4px;">No Evaluations Yet</div>
                <div style="font-size:0.9rem;">Your supervisor hasn't created any evaluations yet. Check back soon!</div>
            </div>
        @endforelse

        @if($evaluations->hasPages())
            <div style="padding:18px 24px; border-top:1px solid var(--gray-100); display:flex; justify-content:center;">
                {{ $evaluations->links() }}
            </div>
        @endif
    </div>
</div>

<div style="margin-top:22px;">
    <a href="{{ route('student.dashboard') }}" class="btn btn-ghost">← Back to Dashboard</a>
</div>

@endsection
