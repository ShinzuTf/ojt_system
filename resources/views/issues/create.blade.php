@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-circle"></i> Report an Issue</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('issues.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="issueType">Issue Type <span class="text-danger">*</span></label>
                            <select id="issueType" name="issue_type" class="form-control @error('issue_type') is-invalid @enderror" required>
                                <option value="">-- Select Type --</option>
                                <option value="absence" {{ old('issue_type') === 'absence' ? 'selected' : '' }}>Absence</option>
                                <option value="drop" {{ old('issue_type') === 'drop' ? 'selected' : '' }}>Student Drop-out</option>
                                <option value="transfer" {{ old('issue_type') === 'transfer' ? 'selected' : '' }}>Transfer Request</option>
                                <option value="behavioral" {{ old('issue_type') === 'behavioral' ? 'selected' : '' }}>Behavioral Issue</option>
                                <option value="performance" {{ old('issue_type') === 'performance' ? 'selected' : '' }}>Performance Concern</option>
                                <option value="other" {{ old('issue_type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('issue_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(auth()->user()->isCoordinator())
                        <div class="form-group">
                            <label for="student">Student <span class="text-danger">*</span></label>
                            <select id="student" name="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                <option value="">-- Select Student --</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->full_name }} ({{ $student->id_number }})
                                </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="issueDate">Date of Issue <span class="text-danger">*</span></label>
                            <input type="date" id="issueDate" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror" 
                                   value="{{ old('issue_date', \Carbon\Carbon::now()->toDateString()) }}" required>
                            @error('issue_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="4" placeholder="Provide detailed description of the issue" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="severity">Severity Level <span class="text-danger">*</span></label>
                            <select id="severity" name="severity" class="form-control @error('severity') is-invalid @enderror" required>
                                <option value="">-- Select Level --</option>
                                <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            @error('severity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="desiredOutcome">Desired Resolution</label>
                            <textarea id="desiredOutcome" name="desired_resolution" class="form-control @error('desired_resolution') is-invalid @enderror" 
                                      rows="3" placeholder="What resolution would you like?">{{ old('desired_resolution') }}</textarea>
                            @error('desired_resolution')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> This issue will be assigned to the coordinator for resolution.
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-danger btn-lg btn-block">
                                <i class="fas fa-send"></i> Report Issue
                            </button>
                            <a href="{{ route('issues.index') }}" class="btn btn-secondary btn-block mt-2">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
