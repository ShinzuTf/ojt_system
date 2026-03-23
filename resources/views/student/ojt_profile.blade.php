@extends('layouts.app')

@section('title', 'My OJT Profile')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">OJT Profile</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">My OJT Profile</h1>
    <p class="page-subtitle">Complete your OJT information so the admin can process your documents and requirements</p>
</div>

{{-- Completion Notice --}}
@php
    $isComplete = $ojt->company_name && $ojt->student_number && $ojt->course;
@endphp
@if(!$isComplete)
<div class="alert alert-warning" style="margin-bottom:20px;">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>Your OJT profile is <strong>incomplete</strong>. Please fill in your student number, course, and company details to proceed with document submissions.</span>
</div>
@else
<div class="alert alert-success" style="margin-bottom:20px; background:var(--success-light); border-color:var(--success);">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>Your OJT profile is complete. You can update your information at any time.</span>
</div>
@endif

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px; background:var(--success-light); border-color:var(--success);">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<form method="POST" action="{{ route('student.ojt-profile.update') }}">
    @csrf
    @method('PUT')

    {{-- Section 1: Academic Info --}}
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <h2 class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--primary);"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                Academic Information
            </h2>
        </div>
        <div class="card-body">
            <div class="form-grid" style="margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" for="student_number">Student Number <span class="required">*</span></label>
                    <input type="text" id="student_number" name="student_number" class="form-input {{ $errors->has('student_number') ? 'input-error' : '' }}"
                        placeholder="e.g. 00038630" value="{{ old('student_number', $ojt->student_number) }}" required>
                    @error('student_number') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="course">Course <span class="required">*</span></label>
                    <select id="course" name="course" class="form-select {{ $errors->has('course') ? 'input-error' : '' }}" required>
                        <option value="">Select Course</option>
                        @foreach(['BSIT' => 'BS Information Technology', 'BSCS' => 'BS Computer Science'] as $val => $label)
                            <option value="{{ $val }}" {{ old('course', $ojt->course) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('course') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="year_level">Year Level <span class="required">*</span></label>
                    <select id="year_level" name="year_level" class="form-select {{ $errors->has('year_level') ? 'input-error' : '' }}" required>
                        <option value="">Select Year</option>
                        <option value="3" {{ old('year_level', $ojt->year_level) == '3' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ old('year_level', $ojt->year_level) == '4' ? 'selected' : '' }}>4th Year</option>
                    </select>
                    @error('year_level') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Company / Employer Info --}}
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <h2 class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--primary);"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Company / Employer Information
            </h2>
        </div>
        <div class="card-body">
            <div class="form-grid" style="margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" for="company_name">Company Name <span class="required">*</span></label>
                    <input type="text" id="company_name" name="company_name" class="form-input {{ $errors->has('company_name') ? 'input-error' : '' }}"
                        placeholder="e.g. Accenture Philippines, Inc." value="{{ old('company_name', $ojt->company_name) }}" required>
                    @error('company_name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="company_email">Company Email <span class="required">*</span></label>
                    <input type="email" id="company_email" name="company_email" class="form-input {{ $errors->has('company_email') ? 'input-error' : '' }}"
                        placeholder="e.g. hr@company.com" value="{{ old('company_email', $ojt->company_email) }}" required>
                    @error('company_email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group full-width">
                    <label class="form-label" for="company_address">Company Address</label>
                    <input type="text" id="company_address" name="company_address" class="form-input"
                        placeholder="Full address of the company" value="{{ old('company_address', $ojt->company_address) }}">
                    @error('company_address') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Section 3: Supervisor & OJT Period --}}
    <div class="card" style="margin-bottom:22px;">
        <div class="card-header">
            <h2 class="card-title">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--primary);"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Supervisor & OJT Period
            </h2>
        </div>
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="supervisor_name">Immediate Supervisor</label>
                    <input type="text" id="supervisor_name" name="supervisor_name" class="form-input"
                        placeholder="Full name of your supervisor" value="{{ old('supervisor_name', $ojt->supervisor_name) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="supervisor_contact">Supervisor Contact</label>
                    <input type="text" id="supervisor_contact" name="supervisor_contact" class="form-input"
                        placeholder="Phone or email" value="{{ old('supervisor_contact', $ojt->supervisor_contact) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="ojt_start">OJT Start Date</label>
                    <input type="date" id="ojt_start" name="ojt_start" class="form-input"
                        value="{{ old('ojt_start', $ojt->ojt_start?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="ojt_end">OJT End Date</label>
                    <input type="date" id="ojt_end" name="ojt_end" class="form-input"
                        value="{{ old('ojt_end', $ojt->ojt_end?->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div style="display:flex; justify-content:flex-end; gap:12px;">
        <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary" id="btn-save-ojt">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
            Save OJT Profile
        </button>
    </div>
</form>
@endsection
