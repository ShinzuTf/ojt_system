@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card register-card">
        {{-- Logo & Title --}}
        <div class="auth-logo">
            <img src="{{ asset('images/philcst_logo.png') }}" alt="PHILCST Logo">
            <h1>Create Your Account</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom:16px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <ul style="list-style:none; margin:0; padding:0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Registration Form --}}
        <form class="auth-form" method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Name Row 1: First + Middle --}}
            <div class="form-grid" style="margin-bottom: 14px;">
                <div class="form-group">
                    <label class="form-label" for="reg-fname">First Name <span class="required">*</span></label>
                    <input type="text" id="reg-fname" name="fname" class="form-input {{ $errors->has('fname') ? 'input-error' : '' }}" placeholder="Juan" value="{{ old('fname') }}" required autocomplete="given-name">
                    @error('fname') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="reg-mname">Middle Name <span class="text-muted text-small">(optional)</span></label>
                    <input type="text" id="reg-mname" name="mname" class="form-input" placeholder="Dela" value="{{ old('mname') }}" autocomplete="additional-name">
                    @error('mname') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Name Row 2: Last + Suffix --}}
            <div class="form-grid" style="margin-bottom: 14px;">
                <div class="form-group">
                    <label class="form-label" for="reg-lname">Last Name <span class="required">*</span></label>
                    <input type="text" id="reg-lname" name="lname" class="form-input {{ $errors->has('lname') ? 'input-error' : '' }}" placeholder="Cruz" value="{{ old('lname') }}" required autocomplete="family-name">
                    @error('lname') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="reg-suffix">Suffix <span class="text-muted text-small">(optional)</span></label>
                    <select id="reg-suffix" name="suffix" class="form-select">
                        <option value="">None</option>
                        <option value="Jr." {{ old('suffix') == 'Jr.' ? 'selected' : '' }}>Jr.</option>
                        <option value="Sr." {{ old('suffix') == 'Sr.' ? 'selected' : '' }}>Sr.</option>
                        <option value="II"  {{ old('suffix') == 'II'  ? 'selected' : '' }}>II</option>
                        <option value="III" {{ old('suffix') == 'III' ? 'selected' : '' }}>III</option>
                        <option value="IV"  {{ old('suffix') == 'IV'  ? 'selected' : '' }}>IV</option>
                    </select>
                </div>
            </div>

            {{-- Email --}}
            <div class="form-group" style="margin-bottom: 14px;">
                <label class="form-label" for="reg-email">Gmail Address <span class="required">*</span></label>
                <input type="email" id="reg-email" name="email" class="form-input {{ $errors->has('email') ? 'input-error' : '' }}" placeholder="Enter your email address" value="{{ old('email') }}" required autocomplete="email">
                @error('email') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            {{-- Password --}}
            <div class="form-grid" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label" for="reg-password">Password <span class="required">*</span></label>
                    <input type="password" id="reg-password" name="password" class="form-input {{ $errors->has('password') ? 'input-error' : '' }}" placeholder="Minimum 8 characters" required autocomplete="new-password">
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="reg-password-confirm">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="reg-password-confirm" name="password_confirmation" class="form-input" placeholder="Re-type password" required autocomplete="new-password">
                </div>
            </div>

            <div class="form-actions" style="justify-content: center;">
                <button type="submit" class="btn btn-primary btn-lg" id="btn-register" style="width:100%;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Create Account
                </button>
            </div>
        </form>

        <div class="auth-divider">or</div>

        <div class="auth-link">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameRegex = /^[a-zA-Z\s\-']*$/;
    const suffixRegex = /^[a-zA-Z.]*$/;

    const fnameInput = document.getElementById('reg-fname');
    const mnameInput = document.getElementById('reg-mname');
    const lnameInput = document.getElementById('reg-lname');
    const suffixSelect = document.getElementById('reg-suffix');

    // Create error elements for each field
    function initializeErrorElement(input, fieldName) {
        const container = input.closest('.form-group');
        let errorEl = container.querySelector('.form-error');
        
        if (!errorEl) {
            errorEl = document.createElement('span');
            errorEl.className = 'form-error';
            errorEl.textContent = fieldName + ' can only contain letters, spaces, hyphens, and apostrophes.';
            errorEl.style.display = 'none';
            container.appendChild(errorEl);
        }
        return errorEl;
    }

    const fnameError = initializeErrorElement(fnameInput, 'First name');
    const mnameError = initializeErrorElement(mnameInput, 'Middle name');
    const lnameError = initializeErrorElement(lnameInput, 'Last name');

    function validateNameField(input, errorEl, regex) {
        if (input.value && !regex.test(input.value)) {
            errorEl.style.display = 'block';
            input.classList.add('input-error');
        } else {
            errorEl.style.display = 'none';
            input.classList.remove('input-error');
        }
    }

    fnameInput.addEventListener('input', function() {
        validateNameField(this, fnameError, nameRegex);
    });

    mnameInput.addEventListener('input', function() {
        validateNameField(this, mnameError, nameRegex);
    });

    lnameInput.addEventListener('input', function() {
        validateNameField(this, lnameError, nameRegex);
    });

    suffixSelect.addEventListener('change', function() {
        const container = this.closest('.form-group');
        let errorEl = container.querySelector('.form-error');
        
        if (!errorEl) {
            errorEl = document.createElement('span');
            errorEl.className = 'form-error';
            errorEl.textContent = 'Suffix can only contain letters and periods.';
            errorEl.style.display = 'none';
            container.appendChild(errorEl);
        }
        
        if (this.value && !suffixRegex.test(this.value)) {
            errorEl.textContent = 'Suffix can only contain letters and periods.';
            errorEl.style.display = 'block';
            this.classList.add('input-error');
        } else {
            errorEl.style.display = 'none';
            this.classList.remove('input-error');
        }
    });
});
</script>
@endsection
