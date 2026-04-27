@extends('layouts.app')

@section('title', 'Report Issue - Supervisor Portal')
@section('page-title', 'Report Trainee Issue')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Report Trainee Issue</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('supervisor.issues.store') }}" method="POST">
                        @csrf

                        <!-- Trainee Selection -->
                        <div class="mb-4">
                            <label for="student_id" class="form-label"><strong>Trainee</strong></label>
                            <select id="student_id" name="student_id" class="form-select" required>
                                <option value="">-- Select a trainee --</option>
                                @foreach($trainees as $trainee)
                                    <option value="{{ $trainee['id'] }}" {{ old('student_id') == $trainee['id'] ? 'selected' : '' }}>
                                        {{ $trainee['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Issue Type -->
                        <div class="mb-4">
                            <label for="issue_type" class="form-label"><strong>Issue Type</strong></label>
                            <select id="issue_type" name="issue_type" class="form-select" required onchange="updateIssueDescription()">
                                <option value="">-- Select issue type --</option>
                                <option value="absence" {{ old('issue_type') == 'absence' ? 'selected' : '' }}>Absence</option>
                                <option value="drop_transfer" {{ old('issue_type') == 'drop_transfer' ? 'selected' : '' }}>Drop/Transfer of Company</option>
                            </select>
                            @error('issue_type')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                <strong>Absence:</strong> Trainee has been absent from their placement<br>
                                <strong>Drop/Transfer:</strong> Trainee is dropping out or transferring to another company
                            </small>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label"><strong>Description <span class="text-danger">*</span></strong></label>
                            <textarea id="description" name="description" class="form-control" rows="5" placeholder="Provide detailed information about the issue..." required>{{ old('description') }}</textarea>
                            <small class="text-muted d-block mt-2">Minimum 10 characters. Include dates, details, and any relevant context.</small>
                            @error('description')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Impact Level -->
                        <div class="mb-4">
                            <label for="impact" class="form-label"><strong>Impact Level</strong></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="impact" id="impact_low" value="low" {{ old('impact') == 'low' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-info" for="impact_low">
                                    <i class="bi bi-dash-circle"></i> Low
                                </label>

                                <input type="radio" class="btn-check" name="impact" id="impact_medium" value="medium" {{ old('impact') == 'medium' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-warning" for="impact_medium">
                                    <i class="bi bi-exclamation-circle"></i> Medium
                                </label>

                                <input type="radio" class="btn-check" name="impact" id="impact_high" value="high" {{ old('impact') == 'high' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-danger" for="impact_high">
                                    <i class="bi bi-x-circle"></i> High
                                </label>
                            </div>
                            @error('impact')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-3 mt-5">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Report Issue
                            </button>
                            <a href="{{ route('supervisor.issues.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateIssueDescription() {
    const issueType = document.getElementById('issue_type').value;
    const descriptionField = document.getElementById('description');
    
    if (issueType === 'absence') {
        descriptionField.placeholder = 'Example: The trainee was absent on April 15, 2026. This is their second absence. Last contact was April 14...';
    } else if (issueType === 'drop_transfer') {
        descriptionField.placeholder = 'Example: The trainee informed me they are dropping from the OJT program effective April 20, 2026. Reason: ...';
    }
}
</script>
@endsection
