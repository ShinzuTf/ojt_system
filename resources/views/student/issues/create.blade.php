@extends('layouts.app')

@section('title', 'Report Issue - OJT System')
@section('page-title', 'Report an Issue')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-exclamation-circle"></i> Report an Issue</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.issues.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="issue_type" class="form-label"><strong>Issue Type</strong></label>
                            <select class="form-select @error('issue_type') is-invalid @enderror" id="issue_type" name="issue_type" required>
                                <option value="">-- Select Issue Type --</option>
                                <option value="absence" {{ old('issue_type') === 'absence' ? 'selected' : '' }}>Absence</option>
                                <option value="performance" {{ old('issue_type') === 'performance' ? 'selected' : '' }}>Performance Issue</option>
                                <option value="workplace_issue" {{ old('issue_type') === 'workplace_issue' ? 'selected' : '' }}>Workplace Issue</option>
                                <option value="health_safety" {{ old('issue_type') === 'health_safety' ? 'selected' : '' }}>Health & Safety Concern</option>
                                <option value="schedule" {{ old('issue_type') === 'schedule' ? 'selected' : '' }}>Schedule Conflict</option>
                                <option value="other" {{ old('issue_type') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('issue_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><strong>Description</strong></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" 
                                      rows="5" placeholder="Describe the issue in detail..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="impact" class="form-label">Impact Level</label>
                            <select class="form-select @error('impact') is-invalid @enderror" id="impact" name="impact">
                                <option value="low" {{ old('impact', 'low') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('impact', 'low') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('impact', 'low') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                            @error('impact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attachments" class="form-label">Attachments (Optional)</label>
                            <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" 
                                   multiple accept=".pdf,.jpg,.png,.doc,.docx">
                            <small class="text-muted">Supporting documents/photos</small>
                            @error('attachments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Your supervisor and coordinator will be notified of this issue.
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('student.issues.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Submit Issue Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
