@extends('layouts.app')

@section('title', 'Edit Evaluation')

@section('breadcrumb')
    <a href="{{ route('supervisor.dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <a href="{{ route('supervisor.trainees') }}">Trainees</a>
    <span class="separator">/</span>
    <a href="{{ route('supervisor.trainees.show', $evaluation->trainee_id) }}">{{ $evaluation->trainee->fname }}</a>
    <span class="separator">/</span>
    <span class="current">Edit Evaluation</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Performance Evaluation</h1>
    <p class="page-subtitle">for {{ $evaluation->trainee->full_name }}</p>
</div>

@if($errors->any())
    <div style="background:var(--danger-light); color:var(--danger); padding:16px 20px; border-radius:8px; margin-bottom:22px; border-left:4px solid var(--danger);">
        <div style="font-weight:600; margin-bottom:8px;">Validation Errors:</div>
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('supervisor.evaluations.update', $evaluation->id) }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="trainee_id" value="{{ $evaluation->trainee_id }}">

    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <h2 class="card-title">Evaluation Information</h2>
        </div>
        <div class="card-body" style="padding:24px;">
            <div style="margin-bottom:24px;">
                <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:8px;">
                    Evaluation Date <span style="color:var(--danger);">*</span>
                </label>
                <input type="date" name="evaluation_date" 
                    value="{{ $evaluation->evaluation_date->format('Y-m-d') }}"
                    required max="{{ today()->format('Y-m-d') }}"
                    style="width:100%; max-width:300px; padding:12px 14px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem;">
                @error('evaluation_date')
                    <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <h2 class="card-title">Performance Feedback</h2>
            <p class="text-small text-muted">Provide constructive feedback on the trainee's performance during their OJT</p>
        </div>
        <div class="card-body" style="padding:24px;">
            <div style="margin-bottom:24px;">
                <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:8px;">
                    Strengths <span style="color:var(--danger);">*</span>
                </label>
                <textarea name="strengths" rows="4" required minlength="10"
                    placeholder="What specific strengths did the trainee demonstrate? (e.g., attention to detail, problem-solving skills, collaborative approach)..."
                    style="width:100%; padding:12px 14px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem; font-family:inherit;">{{ $evaluation->strengths }}</textarea>
                @error('strengths')
                    <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:8px;">
                    Areas for Improvement <span style="color:var(--danger);">*</span>
                </label>
                <textarea name="areas_for_improvement" rows="4" required minlength="10"
                    placeholder="What areas should the trainee improve on? (e.g., time management, technical knowledge, communication skills)..."
                    style="width:100%; padding:12px 14px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem; font-family:inherit;">{{ $evaluation->areas_for_improvement }}</textarea>
                @error('areas_for_improvement')
                    <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:8px;">
                    Skills to Develop <span style="color:var(--danger);">*</span>
                </label>
                <textarea name="skills_to_develop" rows="4" required minlength="10"
                    placeholder="What specific skills should the trainee focus on developing? (e.g., advanced programming, leadership, client interaction)..."
                    style="width:100%; padding:12px 14px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem; font-family:inherit;">{{ $evaluation->skills_to_develop }}</textarea>
                @error('skills_to_develop')
                    <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:8px;">
                    Overall Comments
                </label>
                <textarea name="overall_comments" rows="3" maxlength="1500"
                    placeholder="Any additional comments or observations (optional)..."
                    style="width:100%; padding:12px 14px; border:1px solid var(--gray-200); border-radius:6px; font-size:0.9rem; font-family:inherit;">{{ $evaluation->overall_comments }}</textarea>
                @error('overall_comments')
                    <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <h2 class="card-title">Performance Ratings</h2>
            <p class="text-small text-muted">Rate the trainee on a scale of 1 (Needs Improvement) to 5 (Excellent)</p>
        </div>
        <div class="card-body" style="padding:24px;">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:24px;">
                {{-- Technical Skills Rating --}}
                <div>
                    <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:12px;">
                        Technical Skills <span style="color:var(--danger);">*</span>
                    </label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" id="technical_{{ $i }}" name="technical_skills_rating" value="{{ $i }}"
                                {{ $evaluation->technical_skills_rating == $i ? 'checked' : '' }}
                                required style="cursor:pointer;">
                            <label for="technical_{{ $i }}" style="cursor:pointer; font-weight:600; color:var(--gray-600); margin:0;">{{ $i }}</label>
                        @endfor
                    </div>
                    @error('technical_skills_rating')
                        <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Communication Rating --}}
                <div>
                    <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:12px;">
                        Communication <span style="color:var(--danger);">*</span>
                    </label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" id="communication_{{ $i }}" name="communication_rating" value="{{ $i }}"
                                {{ $evaluation->communication_rating == $i ? 'checked' : '' }}
                                required style="cursor:pointer;">
                            <label for="communication_{{ $i }}" style="cursor:pointer; font-weight:600; color:var(--gray-600); margin:0;">{{ $i }}</label>
                        @endfor
                    </div>
                    @error('communication_rating')
                        <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Teamwork Rating --}}
                <div>
                    <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:12px;">
                        Teamwork <span style="color:var(--danger);">*</span>
                    </label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" id="teamwork_{{ $i }}" name="teamwork_rating" value="{{ $i }}"
                                {{ $evaluation->teamwork_rating == $i ? 'checked' : '' }}
                                required style="cursor:pointer;">
                            <label for="teamwork_{{ $i }}" style="cursor:pointer; font-weight:600; color:var(--gray-600); margin:0;">{{ $i }}</label>
                        @endfor
                    </div>
                    @error('teamwork_rating')
                        <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Professionalism Rating --}}
                <div>
                    <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:12px;">
                        Professionalism <span style="color:var(--danger);">*</span>
                    </label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" id="professionalism_{{ $i }}" name="professionalism_rating" value="{{ $i }}"
                                {{ $evaluation->professionalism_rating == $i ? 'checked' : '' }}
                                required style="cursor:pointer;">
                            <label for="professionalism_{{ $i }}" style="cursor:pointer; font-weight:600; color:var(--gray-600); margin:0;">{{ $i }}</label>
                        @endfor
                    </div>
                    @error('professionalism_rating')
                        <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Initiative Rating --}}
                <div>
                    <label style="display:block; font-weight:600; font-size:0.9rem; color:var(--gray-700); margin-bottom:12px;">
                        Initiative & Proactivity <span style="color:var(--danger);">*</span>
                    </label>
                    <div style="display:flex; gap:10px; align-items:center;">
                        @for($i = 1; $i <= 5; $i++)
                            <input type="radio" id="initiative_{{ $i }}" name="initiative_rating" value="{{ $i }}"
                                {{ $evaluation->initiative_rating == $i ? 'checked' : '' }}
                                required style="cursor:pointer;">
                            <label for="initiative_{{ $i }}" style="cursor:pointer; font-weight:600; color:var(--gray-600); margin:0;">{{ $i }}</label>
                        @endfor
                    </div>
                    @error('initiative_rating')
                        <span style="color:var(--danger); font-size:0.85rem; margin-top:4px; display:block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:flex-end;">
        <a href="{{ route('supervisor.trainees.show', $evaluation->trainee_id) }}" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">
            Update Evaluation
        </button>
    </div>
</form>

@endsection
