@extends('layouts.app')

@section('title', 'Create User - OJT System')
@section('page-title', 'Create New User')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-person-plus"></i> Create New User</h5>
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

            <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('fname') is-invalid @enderror" 
                                   id="fname" name="fname" value="{{ old('fname') }}" required>
                            @error('fname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="lname" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('lname') is-invalid @enderror" 
                                   id="lname" name="lname" value="{{ old('lname') }}" required>
                            @error('lname')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-control @error('role') is-invalid @enderror" 
                                    id="role" name="role" required onchange="handleRoleChange()">
                                <option value="">-- Select Role --</option>
                                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="coordinator" {{ old('role') === 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                            </select>
                            @error('role')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Student-only fields -->
                <div id="studentFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="course" class="form-label">Course <span class="text-danger">*</span></label>
                                <select class="form-control @error('course') is-invalid @enderror" 
                                        id="course" name="course">
                                    <option value="">-- Select Course --</option>
                                    <option value="BSIT" {{ old('course') === 'BSIT' ? 'selected' : '' }}>BS Information Technology</option>
                                    <option value="BSCS" {{ old('course') === 'BSCS' ? 'selected' : '' }}>BS Computer Science</option>
                                </select>
                                @error('course')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coordinator-only fields -->
                <div id="coordinatorFields" style="display: none;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="company_email" class="form-label">Company Email <span class="text-danger">*</span></label>
                                <select class="form-control @error('company_email') is-invalid @enderror" 
                                        id="company_email" name="company_email">
                                    <option value="">-- Select Company --</option>
                                </select>
                                @error('company_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required 
                                   placeholder="Min 8 chars, uppercase, lowercase, numbers">
                            <small class="form-text text-muted">Must contain uppercase, lowercase, and numbers</small>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Create User
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function handleRoleChange() {
    const role = document.getElementById('role').value;
    const studentFields = document.getElementById('studentFields');
    const coordinatorFields = document.getElementById('coordinatorFields');
    const courseSelect = document.getElementById('course');
    const companyEmailSelect = document.getElementById('company_email');

    // Hide/show role-specific fields
    if (role === 'student') {
        studentFields.style.display = 'block';
        coordinatorFields.style.display = 'none';
        courseSelect.setAttribute('required', 'required');
        companyEmailSelect.removeAttribute('required');
    } else if (role === 'coordinator') {
        studentFields.style.display = 'none';
        coordinatorFields.style.display = 'block';
        courseSelect.removeAttribute('required');
        companyEmailSelect.setAttribute('required', 'required');
        loadCompanies();
    } else {
        studentFields.style.display = 'none';
        coordinatorFields.style.display = 'none';
        courseSelect.removeAttribute('required');
        companyEmailSelect.removeAttribute('required');
    }
}

function loadCompanies() {
    fetch("{{ route('admin.users.available-companies') }}")
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('company_email');
            select.innerHTML = '<option value="">-- Select Company --</option>';
            data.forEach(company => {
                const option = document.createElement('option');
                option.value = company.company_email;
                option.textContent = company.company_name;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading companies:', error));
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const role = document.getElementById('role').value;
    if (role) {
        handleRoleChange();
    }
});
</script>
@endsection
