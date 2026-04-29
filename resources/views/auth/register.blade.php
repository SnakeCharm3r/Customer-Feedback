<x-guest-layout>
 <div class="auth-card auth-card-wide">
     <div class="auth-header">
        <div class="auth-brand">
            <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}" alt="{{ $systemSettings?->organization_name ?? 'CCBRT' }} Logo" class="auth-brand-logo">
            <div class="auth-brand-copy">
                <div class="logo-text">{{ $systemSettings?->organization_name ?? 'CCBRT' }}</div>
                <div class="logo-sub">{{ $systemSettings?->portal_name ?? 'Feedback Management System' }}</div>
            </div>
        </div>
     </div>
     <div class="auth-body">

         @if($isFirstUser)
             <div class="alert py-2 px-3 small mb-4" style="background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd;">
                <i class="bi bi-shield-check me-2"></i>
                <strong>First-time setup:</strong> You are creating the system administrator account. This account will have full access.
            </div>
        @else
            <div class="alert alert-warning py-2 px-3 small mb-4">
                <i class="bi bi-clock-history me-2"></i>
                Your account will be <strong>pending approval</strong> until an administrator reviews and activates it.
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small mb-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- PERSONAL INFORMATION --}}
            <div class="section-divider">Personal Information</div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="fname" class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                    <input id="fname" type="text" name="fname" value="{{ old('fname') }}"
                           class="form-control @error('fname') is-invalid @enderror"
                           required autofocus autocomplete="given-name" placeholder="First name">
                    @error('fname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="mname" class="form-label small fw-semibold">Middle Name <span class="text-muted">(optional)</span></label>
                    <input id="mname" type="text" name="mname" value="{{ old('mname') }}"
                           class="form-control @error('mname') is-invalid @enderror"
                           autocomplete="additional-name" placeholder="Middle name">
                    @error('mname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="lname" class="form-label small fw-semibold">Last Name <span class="text-danger">*</span></label>
                    <input id="lname" type="text" name="lname" value="{{ old('lname') }}"
                           class="form-control @error('lname') is-invalid @enderror"
                           required autocomplete="family-name" placeholder="Last name">
                    @error('lname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="dob" class="form-label small fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                    <input id="dob" type="date" name="dob" value="{{ old('dob') }}"
                           class="form-control @error('dob') is-invalid @enderror" required>
                    @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- ACCOUNT INFORMATION --}}
            <div class="section-divider">Account Information</div>
            <div class="row g-3 mb-3">
                <div class="col-md-{{ $isFirstUser ? '12' : '6' }}">
                    <label for="email" class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           required autocomplete="username" placeholder="you@example.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if(!$isFirstUser)
                <div class="col-md-6">
                    <label for="role" class="form-label small fw-semibold">Requested Role <span class="text-danger">*</span></label>
                     <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                         <option value="">-- Select your role --</option>
                         <option value="qa_officer"  {{ old('role')=='qa_officer'  ?'selected':'' }}>Quality Assurance Officer</option>
                         <option value="call_center" {{ old('role')=='call_center' ?'selected':'' }}>Call Center</option>
                         <option value="qa_hod"      {{ old('role')=='qa_hod'      ?'selected':'' }}>Quality Assurance Head of Department</option>
                         <option value="coo"         {{ old('role')=='coo'         ?'selected':'' }}>Chief Operating Officer</option>
                        <option value="line_manager" {{ old('role')=='line_manager' ?'selected':'' }}>Line Manager</option>
                     </select>
                     @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                     <div class="form-text text-muted small">Role may be adjusted by admin during approval.</div>
                 </div>
                @endif
            </div>

            {{-- SECURITY --}}
            <div class="section-divider">Security</div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="password" class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input id="password" type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="new-password" placeholder="Min. 8 characters">
                        <button type="button"
                                class="btn btn-sm position-absolute top-50 translate-middle-y end-0 me-2 p-0 border-0 bg-transparent text-muted"
                                onclick="var p=document.getElementById('password'); p.type=p.type==='password'?'text':'password';">
                            <i class="bi bi-eye fs-5"></i>
                        </button>
                    </div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label small fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control" required autocomplete="new-password" placeholder="Repeat password">
                </div>
            </div>

            <button type="submit" class="btn btn-auth">
                @if($isFirstUser)
                    <i class="bi bi-shield-check me-2"></i>Create Administrator Account
                @else
                    <i class="bi bi-send me-2"></i>Submit Registration for Approval
                @endif
            </button>
        </form>

        <div class="text-center mt-4 small text-muted">
            Already have an account?
            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color:#0b6b2c;">Sign in here</a>
        </div>
    </div>
</div>
</x-guest-layout>
