@extends('layouts.app')

@section('title', 'Set Student Company - OJT System')
@section('page-title', 'Set Student Company')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-briefcase"></i> Set Student Company</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Please fix the following errors:
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('coordinator.placements.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                            <select class="form-control @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                                <option value="">-- Select Student --</option>
                                @php
                                    $students = \App\Models\User::where('role', 'student')->orderBy('lname')->get();
                                @endphp
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ ($studentId ?? old('student_id')) == $student->id ? 'selected' : '' }}>
                                        {{ $student->lname }}, {{ $student->fname }} ({{ $student->id_number ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
                            <div class="btn-group mb-2" role="group" style="width: 100%;">
                                <input type="radio" class="btn-check" name="company_option" id="company_select_option" value="select" checked onchange="toggleCompanyInput()">
                                <label class="btn btn-outline-primary" for="company_select_option">Select Existing</label>
                                
                                <input type="radio" class="btn-check" name="company_option" id="company_input_option" value="input" onchange="toggleCompanyInput()">
                                <label class="btn btn-outline-primary" for="company_input_option">Enter New</label>
                            </div>
                            
                            <!-- Select from past companies -->
                            <select class="form-control @error('company_name') is-invalid @enderror" 
                                    id="company_select" name="company_name" style="display: block;">
                                <option value="">-- Select Company --</option>
                                @foreach(($companies ?? collect()) as $company)
                                    <option value="{{ $company }}" {{ old('company_name') === $company && old('company_option', 'select') === 'select' ? 'selected' : '' }}>
                                        {{ $company }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Input new company -->
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                   id="company_input" name="company_name_input" placeholder="Enter company name"
                                   value="{{ old('company_option') === 'input' ? old('company_name') : '' }}"
                                   style="display: none;">
                            
                            @error('company_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="supervisor_fname" class="form-label">Supervisor First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('supervisor_fname') is-invalid @enderror"
                                   id="supervisor_fname" name="supervisor_fname" value="{{ old('supervisor_fname') }}" required>
                            @error('supervisor_fname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="supervisor_lname" class="form-label">Supervisor Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('supervisor_lname') is-invalid @enderror"
                                   id="supervisor_lname" name="supervisor_lname" value="{{ old('supervisor_lname') }}" required>
                            @error('supervisor_lname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="supervisor_email" class="form-label">Supervisor Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('supervisor_email') is-invalid @enderror"
                                   id="supervisor_email" name="supervisor_email" value="{{ old('supervisor_email') }}" required>
                            @error('supervisor_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="supervisor_password" class="form-label">Temporary Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('supervisor_password') is-invalid @enderror"
                                   id="supervisor_password" name="supervisor_password" required>
                            @error('supervisor_password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required onchange="calculateEndDate()">
                            @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span> (Auto-calculated)</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required readonly style="background-color: #f5f5f5;">
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Automatically calculated excluding Sundays and holidays</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="total_required_hours" class="form-label">Total Required Hours</label>
                            <input type="number" class="form-control @error('total_required_hours') is-invalid @enderror" 
                                   id="total_required_hours" name="total_required_hours" 
                                   value="{{ old('total_required_hours', 720) }}" min="100">
                            <small class="form-text text-muted">Default: 720 hours</small>
                            @error('total_required_hours')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Set Student Company
                    </button>
                    <a href="{{ route('coordinator.placements.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle between select and input for company name
function toggleCompanyInput() {
    const selectOption = document.getElementById('company_select_option').checked;
    const selectField = document.getElementById('company_select');
    const inputField = document.getElementById('company_input');
    
    if (selectOption) {
        selectField.style.display = 'block';
        inputField.style.display = 'none';
        selectField.name = 'company_name';
        inputField.name = 'company_name_input';
    } else {
        selectField.style.display = 'none';
        inputField.style.display = 'block';
        selectField.name = 'company_name_select';
        inputField.name = 'company_name';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const companyOption = document.querySelector('input[name="company_option"]:checked').value;
    if (companyOption === 'input') {
        toggleCompanyInput();
    }
    
    // Calculate end date if start_date has value
    if (document.getElementById('start_date').value) {
        calculateEndDate();
    }
});

// Holidays list (excluding Sundays)
const holidays = {!! json_encode(config('holidays')) !!};

function calculateEndDate() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (!startDateInput.value) {
        endDateInput.value = '';
        return;
    }
    
    let currentDate = new Date(startDateInput.value);
    let workingDays = 0;
    const requiredDays = 90; // 720 hours / 8 hours per day = 90 days (approximation for work days)
    
    while (workingDays < requiredDays) {
        currentDate.setDate(currentDate.getDate() + 1);
        
        // Check if it's a Sunday (0 = Sunday)
        if (currentDate.getDay() === 0) {
            continue;
        }
        
        // Format date as YYYY-MM-DD to check against holidays
        const dateStr = currentDate.toISOString().split('T')[0];
        
        // Check if it's a holiday
        if (holidays[dateStr]) {
            continue;
        }
        
        workingDays++;
    }
    
    // Format the end date as YYYY-MM-DD
    const year = currentDate.getFullYear();
    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
    const day = String(currentDate.getDate()).padStart(2, '0');
    endDateInput.value = `${year}-${month}-${day}`;
}
</script>
@endsection
