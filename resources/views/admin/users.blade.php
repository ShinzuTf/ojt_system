@extends('layouts.app')

@section('title', 'User Management')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <span>›</span>
    <span class="current">User Management</span>
@endsection

@section('content')
<div class="page-header-row">
    <div class="page-header">
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle">Manage student, coordinator, and supervisor accounts</p>
    </div>
    <div class="page-header-actions">
        <button class="btn btn-primary" onclick="openModal('addUserModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add User
        </button>
    </div>
</div>

{{-- Tabs --}}
<div class="tabs">
    <button class="tab-btn active" data-tab="tab-students">Students</button>
    <button class="tab-btn" data-tab="tab-coordinators">Coordinators</button>
    <button class="tab-btn" data-tab="tab-supervisors">Supervisors</button>
</div>

{{-- Toolbar --}}
<div class="card mb-3" style="border:none; box-shadow:none; background:transparent;">
    <div class="table-toolbar">
        <div class="table-search">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" placeholder="Search by name or email..." id="searchInput" oninput="filterTable()">
        </div>
        <div class="table-filters">
            <select class="form-select" id="courseFilter" onchange="filterTable()" style="padding:8px 32px 8px 14px; font-size:0.84rem; width:auto;">
                <option value="">All Courses</option>
                <option value="BSIT">BSIT</option>
                <option value="BSCS">BSCS</option>
            </select>
        </div>
    </div>
</div>

{{-- Students Tab --}}
<div class="card" id="tab-students">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table" id="studentsTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Student No.</th>
                        <th>Course</th>
                        <th>Coordinator</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    @php
                        $initials = substr($student->fname, 0, 1) . substr($student->lname, 0, 1);
                    @endphp
                    <tr class="student-row" data-name="{{ strtolower($student->full_name) }}" data-email="{{ strtolower($student->email) }}" data-course="{{ $student->ojtInfo?->course ?? '' }}">
                        <td>
                            <div class="d-flex items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:50%; background:var(--purple-100); color:var(--purple-700); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.72rem; flex-shrink:0;">{{ strtoupper($initials) }}</div>
                                <div>
                                    <div style="font-weight:600;">{{ $student->full_name }}</div>
                                    <div class="text-small text-muted">{{ $student->ojtInfo?->year_level ? ordinal($student->ojtInfo->year_level) . ' Year' : 'No Year' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->ojtInfo?->student_number ?? '—' }}</td>
                        <td>{{ $student->ojtInfo?->course ?? '—' }}</td>
                        <td>{{ $student->ojtInfo?->supervisor_name ?? '—' }}</td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Active</span></td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn" title="Edit" onclick="openEditUserModal({{ $student->id }}, '{{ addslashes($student->fname) }}', '{{ addslashes($student->lname) }}', '{{ $student->email }}', 'student', '{{ $student->ojtInfo?->course ?? 'BSIT' }}')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <form style="display:inline;" method="POST" action="{{ route('admin.users.destroy', $student->id) }}" onsubmit="return confirm('Are you sure?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="table-action-btn reject" title="Deactivate"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 24px; color:var(--gray-400);">No students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Coordinators Tab --}}
<div class="card" id="tab-coordinators" style="display:none;">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coordinators as $coordinator)
                    @php
                        $initials = substr($coordinator->fname, 0, 1) . substr($coordinator->lname, 0, 1);
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:50%; background:var(--green-100); color:var(--green-700); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.72rem; flex-shrink:0;">{{ strtoupper($initials) }}</div>
                                <div>
                                    <div style="font-weight:600;">{{ $coordinator->full_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $coordinator->email }}</td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Active</span></td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn" title="Edit" onclick="openEditUserModal({{ $coordinator->id }}, '{{ addslashes($coordinator->fname) }}', '{{ addslashes($coordinator->lname) }}', '{{ $coordinator->email }}', 'coordinator', '', '{{ $coordinator->company_email }}')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <form style="display:inline;" method="POST" action="{{ route('admin.users.destroy', $coordinator->id) }}" onsubmit="return confirm('Are you sure?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="table-action-btn reject" title="Deactivate"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 24px; color:var(--gray-400);">No coordinators found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Supervisors Tab --}}
<div class="card" id="tab-supervisors" style="display:none;">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supervisors as $supervisor)
                    @php
                        $initials = substr($supervisor->fname, 0, 1) . substr($supervisor->lname, 0, 1);
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex items-center gap-2">
                                <div style="width:32px; height:32px; border-radius:50%; background:var(--blue-100); color:var(--blue-700); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.72rem; flex-shrink:0;">{{ strtoupper($initials) }}</div>
                                <div>
                                    <div style="font-weight:600;">{{ $supervisor->full_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $supervisor->email }}</td>
                        <td><span class="badge badge-approved"><span class="badge-dot"></span> Active</span></td>
                        <td>
                            <div class="table-actions">
                                <button class="table-action-btn" title="Edit" onclick="openEditUserModal({{ $supervisor->id }}, '{{ addslashes($supervisor->fname) }}', '{{ addslashes($supervisor->lname) }}', '{{ $supervisor->email }}', 'supervisor')"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <form style="display:inline;" method="POST" action="{{ route('admin.users.destroy', $supervisor->id) }}" onsubmit="return confirm('Are you sure?');">@csrf<input type="hidden" name="_method" value="DELETE"><button type="submit" class="table-action-btn reject" title="Deactivate"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding: 24px; color:var(--gray-400);">No supervisors found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('modals')
{{-- Add User Modal --}}
<div class="modal-overlay" id="addUserModal">
    <div class="modal" style="max-width:600px; display:flex; flex-direction:column; max-height:90vh;">
        <div class="modal-header">
            <h3 class="modal-title">Add New User</h3>
            <button class="modal-close" onclick="closeModal('addUserModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addUserForm" method="POST" action="{{ route('admin.users.store') }}" onsubmit="return handleAddUserFormSubmit(event)" style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
            @csrf
            <input type="hidden" name="role" value="coordinator">
            <div class="modal-body" style="flex:1; overflow-y:auto; padding:20px; border-bottom:1px solid #e5e7eb;">
                <div id="formErrorAlert" style="display:none; background:#fee; border:1px solid #fcc; border-radius:6px; padding:12px; margin-bottom:16px; color:#c33; flex-shrink:0;">
                    <strong style="display:block; margin-bottom:6px;">Error:</strong>
                    <ul id="formErrorList" style="margin:0; padding-left:20px;"></ul>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="required">*</span></label>
                        <input type="text" name="fname" class="form-input" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="required">*</span></label>
                        <input type="text" name="lname" class="form-input" placeholder="Last Name" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input" placeholder="user@philcst.edu.ph" required>
                    </div>
                    <div class="form-group" id="companyField" style="display:block;">
                        <label class="form-label">Company <span class="required">*</span></label>
                        <select name="company_email" class="form-select" id="companySelect" required>
                            <option value="">Select Company</option>
                        </select>
                        <small style="color: var(--gray-500); display: block; margin-top: 6px;">Choose from companies submitted by students</small>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" id="passwordInput" class="form-input" placeholder="Min 8 chars: uppercase, lowercase, number (e.g., Coord123)" required>
                        <small style="color: var(--gray-600); display: block; margin-top: 6px;">
                            Must contain: uppercase (A-Z), lowercase (a-z), number (0-9)
                        </small>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Confirm Password <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Re-type password" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink:0; padding:16px 20px; border-top:1px solid #e5e7eb; background:#f9fafb; display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Create Account</button>
                <div id="loadingIndicator" style="display:none; align-items:center; gap:12px; margin-left:auto; color:#3b82f6; font-weight:500;">
                    <div style="width:18px; height:18px; border:2px solid #e5e7eb; border-top:2px solid #3b82f6; border-radius:50%; animation:spin 0.6s linear infinite;"></div>
                    <span>Creating account...</span>
                </div>
            </div>
        </form>
        <style>
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
        </style>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal-overlay" id="editUserModal">
    <div class="modal" style="max-width:600px; display:flex; flex-direction:column; max-height:90vh;">
        <div class="modal-header">
            <h3 class="modal-title">Edit User</h3>
            <button class="modal-close" onclick="closeModal('editUserModal')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="editUserForm" action="" onsubmit="return validateEditUserForm()" style="display:flex; flex-direction:column; flex:1; overflow:hidden;">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            <div class="modal-body" style="flex:1; overflow-y:auto; padding:20px; border-bottom:1px solid #e5e7eb;">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="required">*</span></label>
                        <input type="text" name="fname" class="form-input" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="required">*</span></label>
                        <input type="text" name="lname" class="form-input" placeholder="Last Name" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input" placeholder="user@philcst.edu.ph" required>
                    </div>
                    <div class="form-group" id="editCourseField" style="display:none;">
                        <label class="form-label">Course <span class="required">*</span></label>
                        <select name="course" class="form-select">
                            <option value="">Select Course</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSCS">BSCS</option>
                        </select>
                    </div>
                    <div class="form-group" id="editCompanyField" style="display:none;">
                        <label class="form-label">Company <span class="required">*</span></label>
                        <select name="company_email" class="form-select" id="editCompanySelect" required>
                            <option value="">Select Company</option>
                        </select>
                        <small style="color: var(--gray-500); display: block; margin-top: 6px;">Choose from companies submitted by students</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="flex-shrink:0; padding:16px 20px; border-top:1px solid #e5e7eb; background:#f9fafb; display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('[id^="tab-"]').forEach(tab => tab.style.display = 'none');
        document.getElementById(this.dataset.tab).style.display = 'block';
    });
});

// Initialize form on modal open
function initializeAddUserForm() {
    const companyField = document.getElementById('companyField');
    const companySelect = document.getElementById('companySelect');
    
    if (!companyField || !companySelect) {
        console.error('Form elements not found');
        return;
    }
    
    companyField.style.display = 'block';
    companySelect.required = true;
    
    // Load companies with error handling
    loadAvailableCompanies().catch(error => {
        console.error('Failed to load companies:', error);
        // Still allow form to be submitted, just warn user
    });
}

// Load available companies from the server
async function loadAvailableCompanies() {
    const companySelect = document.getElementById('companySelect');
    
    if (!companySelect) {
        console.error('Company select element not found');
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.users.available-companies") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const companies = await response.json();
        
        if (!Array.isArray(companies)) {
            throw new Error('Invalid response format');
        }
        
        // Clear existing options except the default one
        while (companySelect.options.length > 1) {
            companySelect.remove(1);
        }
        
        // Add new options
        if (companies.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No companies available';
            option.disabled = true;
            companySelect.appendChild(option);
        } else {
            companies.forEach(company => {
                if (company.company_email && company.company_name) {
                    const option = document.createElement('option');
                    option.value = company.company_email;
                    option.textContent = `${company.company_name} (${company.company_email})`;
                    companySelect.appendChild(option);
                }
            });
        }
    } catch (error) {
        console.error('Error loading companies:', error);
        // Add fallback option
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Error loading companies - please refresh and try again';
        option.disabled = true;
        companySelect.appendChild(option);
    }
}

// Filter table
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const course = document.getElementById('courseFilter').value;
    
    document.querySelectorAll('.student-row').forEach(row => {
        const name = row.dataset.name;
        const email = row.dataset.email;
        const rowCourse = row.dataset.course;
        
        const matchSearch = name.includes(search) || email.includes(search);
        const matchCourse = !course || rowCourse === course;
        
        row.style.display = (matchSearch && matchCourse) ? '' : 'none';
    });
}

// Edit user modal
function openEditUserModal(id, fname, lname, email, role, course = '', companyEmail = '') {
    const form = document.getElementById('editUserForm');
    form.action = `/admin/users/${id}`;
    form.fname.value = fname;
    form.lname.value = lname;
    form.email.value = email;
    
    const editCourseField = document.getElementById('editCourseField');
    const editCompanyField = document.getElementById('editCompanyField');
    const editCompanySelect = document.getElementById('editCompanySelect');
    
    if (role === 'student') {
        editCourseField.style.display = 'block';
        editCompanyField.style.display = 'none';
        form.course.value = course;
        form.course.required = true;
        editCompanySelect.required = false;
    } else if (role === 'coordinator') {
        editCourseField.style.display = 'none';
        editCompanyField.style.display = 'block';
        form.course.required = false;
        editCompanySelect.required = true;
        
        // Load companies and set current value
        loadEditAvailableCompanies(companyEmail);
    } else {
        editCourseField.style.display = 'none';
        editCompanyField.style.display = 'none';
    }
    
    openModal('editUserModal');
}

// Load available companies for edit form
async function loadEditAvailableCompanies(currentCompanyEmail = '') {
    const editCompanySelect = document.getElementById('editCompanySelect');
    
    if (!editCompanySelect) {
        console.error('Edit company select element not found');
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.users.available-companies") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const companies = await response.json();
        
        if (!Array.isArray(companies)) {
            throw new Error('Invalid response format');
        }
        
        // Clear existing options except the default one
        while (editCompanySelect.options.length > 1) {
            editCompanySelect.remove(1);
        }
        
        // Add new options
        if (companies.length > 0) {
            companies.forEach(company => {
                if (company.company_email && company.company_name) {
                    const option = document.createElement('option');
                    option.value = company.company_email;
                    option.textContent = `${company.company_name} (${company.company_email})`;
                    editCompanySelect.appendChild(option);
                }
            });
        }
        
        // Set current value if provided
        if (currentCompanyEmail) {
            editCompanySelect.value = currentCompanyEmail;
        }
    } catch (error) {
        console.error('Error loading companies:', error);
    }
}

function handleAddUserFormSubmit(event) {
    event.preventDefault();
    
    const form = document.getElementById('addUserForm');
    const formData = new FormData(form);
    const password = document.querySelector('input[name="password"]').value;
    const passwordConfirm = document.querySelector('input[name="password_confirmation"]').value;
    const company = document.getElementById('companySelect').value;
    const errorAlert = document.getElementById('formErrorAlert');
    const errorList = document.getElementById('formErrorList');
    
    // Clear previous errors
    errorAlert.style.display = 'none';
    errorList.innerHTML = '';
    
    // Validate required fields
    const errors = [];
    
    if (password.length < 8) {
        errors.push('Password must be at least 8 characters');
    }
    
    if (!/[a-z]/.test(password)) {
        errors.push('Password must contain at least one lowercase letter (a-z)');
    }
    
    if (!/[A-Z]/.test(password)) {
        errors.push('Password must contain at least one uppercase letter (A-Z)');
    }
    
    if (!/[0-9]/.test(password)) {
        errors.push('Password must contain at least one number (0-9)');
    }
    
    if (password !== passwordConfirm) {
        errors.push('Passwords do not match');
    }
    
    if (!company) {
        errors.push('Please select a company');
    }
    
    // Show client-side errors if any
    if (errors.length > 0) {
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li);
        });
        errorAlert.style.display = 'block';
        return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    submitBtn.style.display = 'none';
    cancelBtn.disabled = true;
    loadingIndicator.style.display = 'flex';
    
    // Submit via AJAX with timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        
        // Check if response is ok
        if (!response.ok) {
            // Try to parse error response
            return response.text().then(text => {
                try {
                    return Promise.reject(JSON.parse(text));
                } catch (e) {
                    return Promise.reject({
                        success: false,
                        message: `Server error: ${response.status} ${response.statusText}`
                    });
                }
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Success - show success message and close modal
            const pageAlert = document.createElement('div');
            pageAlert.style.cssText = 'position:fixed; top:20px; right:20px; background:#dff0d8; border:1px solid #d4edda; color:#155724; padding:16px 24px; border-radius:8px; z-index:10000; box-shadow: 0 2px 4px rgba(0,0,0,0.1);';
            pageAlert.textContent = 'Coordinator account created successfully!';
            document.body.appendChild(pageAlert);
            
            // Close modal and reset
            setTimeout(() => {
                closeModal('addUserModal');
                // Reset form
                document.getElementById('addUserForm').reset();
                // Reload page to show new coordinator
                setTimeout(() => location.reload(), 500);
            }, 1500);
        } else {
            throw data; // Treat as error
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Form submission error:', error);
        
        const serverErrors = [];
        
        // Handle different error types
        if (error.name === 'AbortError') {
            serverErrors.push('Request timeout. Please check your connection and try again.');
        } else if (error.errors) {
            // Laravel validation errors
            Object.keys(error.errors).forEach(field => {
                const fieldErrors = error.errors[field];
                if (Array.isArray(fieldErrors)) {
                    serverErrors.push(...fieldErrors);
                } else {
                    serverErrors.push(fieldErrors);
                }
            });
        } else if (error.message) {
            serverErrors.push(error.message);
        } else if (typeof error === 'string') {
            serverErrors.push(error);
        } else {
            serverErrors.push('An error occurred. Please try again.');
        }
        
        // Display errors
        serverErrors.forEach(errMsg => {
            const li = document.createElement('li');
            li.textContent = errMsg;
            errorList.appendChild(li);
        });
        
        errorAlert.style.display = 'block';
        submitBtn.style.display = '';
        cancelBtn.disabled = false;
        loadingIndicator.style.display = 'none';
    });
    
    return false;
}

function validateEditUserForm() {
    return true;
}
</script>
@endpush
