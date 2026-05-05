@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
{{-- Page Title --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h4 class="mb-0">My Profile</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('status') === 'profile-updated')
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
    <i class="bi bi-check-circle-fill fs-5"></i>
    <span>Profile information updated successfully.</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('status') === 'password-updated')
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
    <i class="bi bi-shield-check fs-5"></i>
    <span>Password changed successfully.</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- LEFT COLUMN: Identity card + Account info --}}
    <div class="col-xl-4 col-lg-4">

        {{-- Identity Card --}}
        <div class="card text-center mb-4">
            <div class="card-body py-4">
                <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                     style="width:80px;height:80px;font-size:2rem;background:linear-gradient(135deg,#065321 0%,#0b6b2c 100%);">
                    {{ strtoupper(substr($user->fname ?? $user->name, 0, 1)) }}{{ strtoupper(substr($user->lname ?? '', 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $user->getFullName() }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <span class="badge"
                      style="background:linear-gradient(135deg,#065321 0%,#0b6b2c 100%);color:#fff;font-size:0.78rem;padding:0.4em 0.8em;">
                    {{ $user->getRoleLabel() }}
                </span>
                @if($user->is_active)
                    <span class="badge bg-success-subtle text-success ms-1" style="font-size:0.78rem;padding:0.4em 0.8em;">
                        <i class="bi bi-circle-fill me-1" style="font-size:7px;"></i>Active
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning ms-1" style="font-size:0.78rem;padding:0.4em 0.8em;">
                        <i class="bi bi-circle-fill me-1" style="font-size:7px;"></i>Pending
                    </span>
                @endif
            </div>
        </div>

        {{-- Account Details (read-only) --}}
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-info-circle me-2"></i>Account Details</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4">
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Role</div>
                            <div class="fw-semibold small mt-1">{{ $user->getRoleLabel() }}</div>
                        </div>
                        <i class="bi bi-person-badge text-muted"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4">
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Account Status</div>
                            <div class="mt-1">
                                @if($user->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">Pending Approval</span>
                                @endif
                            </div>
                        </div>
                        <i class="bi bi-toggle-on text-muted"></i>
                    </li>
                    @if($user->dob)
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4">
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Date of Birth</div>
                            <div class="fw-semibold small mt-1">{{ $user->dob->format('d M Y') }}</div>
                        </div>
                        <i class="bi bi-calendar2 text-muted"></i>
                    </li>
                    @endif
                    @if($user->approvedBy)
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4">
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Approved By</div>
                            <div class="fw-semibold small mt-1">{{ $user->approvedBy->getFullName() }}</div>
                            @if($user->approved_at)
                                <div class="text-muted" style="font-size:11px;">{{ $user->approved_at->format('d M Y') }}</div>
                            @endif
                        </div>
                        <i class="bi bi-person-check text-muted"></i>
                    </li>
                    @endif
                    @if($user->is_first_user)
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4">
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Account Type</div>
                            <div class="mt-1">
                                <span class="badge" style="background:#ede9fe;color:#6d28d9;">System Administrator</span>
                            </div>
                        </div>
                        <i class="bi bi-shield-lock text-muted"></i>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4">
                        <div>
                            <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Member Since</div>
                            <div class="fw-semibold small mt-1">{{ $user->created_at->format('d M Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                        <i class="bi bi-clock-history text-muted"></i>
                    </li>
                </ul>
            </div>
        </div>

    </div>{{-- /LEFT --}}

    {{-- RIGHT COLUMN: Edit forms --}}
    <div class="col-xl-8 col-lg-8">

        {{-- Personal Information --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-primary"></i>
                <h5 class="card-title mb-0">Personal Information</h5>
            </div>
            <div class="card-body">

                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label for="fname" class="form-label fw-semibold small">
                                First Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="fname" name="fname"
                                   class="form-control @error('fname') is-invalid @enderror"
                                   value="{{ old('fname', $user->fname) }}"
                                   placeholder="First name" required>
                            @error('fname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label for="mname" class="form-label fw-semibold small">Middle Name</label>
                            <input type="text" id="mname" name="mname"
                                   class="form-control @error('mname') is-invalid @enderror"
                                   value="{{ old('mname', $user->mname) }}"
                                   placeholder="Middle name (optional)">
                            @error('mname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-4">
                            <label for="lname" class="form-label fw-semibold small">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="lname" name="lname"
                                   class="form-control @error('lname') is-invalid @enderror"
                                   value="{{ old('lname', $user->lname) }}"
                                   placeholder="Last name" required>
                            @error('lname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-8">
                            <label for="email" class="form-label fw-semibold small">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="email@example.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2 small">
                                    <span class="text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Your email is not verified.
                                    </span>
                                    <button form="send-verification"
                                            class="btn btn-link btn-sm p-0 ms-1 text-primary">
                                        Re-send verification email
                                    </button>
                                </div>
                                @if (session('status') === 'verification-link-sent')
                                    <div class="text-success small mt-1">
                                        <i class="bi bi-check-circle me-1"></i>Verification link sent to your email.
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <label for="dob" class="form-label fw-semibold small">Date of Birth</label>
                            <input type="date" id="dob" name="dob"
                                   class="form-control @error('dob') is-invalid @enderror"
                                   value="{{ old('dob', $user->dob?->format('Y-m-d')) }}">
                            @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Role (read-only display) --}}
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold small">Role</label>
                            <div class="form-control bg-light text-muted" style="cursor:default;">
                                {{ $user->getRoleLabel() }}
                            </div>
                            <div class="form-text">Role is managed by your administrator.</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock text-warning"></i>
                <h5 class="card-title mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-4">
                    Use a strong password — at least 8 characters, mixing letters, numbers, and symbols.
                </p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="current_password" class="form-label fw-semibold small">
                                Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="current_password" name="current_password"
                                       class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                                       placeholder="Enter your current password" autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePw('current_password')">
                                    <i class="bi bi-eye" id="eye-current_password"></i>
                                </button>
                                @if($errors->updatePassword->has('current_password'))
                                    <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold small">
                                New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="password" name="password"
                                       class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                                       placeholder="New password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePw('password')">
                                    <i class="bi bi-eye" id="eye-password"></i>
                                </button>
                                @if($errors->updatePassword->has('password'))
                                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold small">
                                Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif"
                                       placeholder="Repeat new password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button"
                                        onclick="togglePw('password_confirmation')">
                                    <i class="bi bi-eye" id="eye-password_confirmation"></i>
                                </button>
                                @if($errors->updatePassword->has('password_confirmation'))
                                    <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-warning px-4">
                            <i class="bi bi-key me-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Danger Zone — hidden for first (system admin) user --}}
        @if(!$user->is_first_user)
        <div class="card border-danger border-opacity-25">
            <div class="card-header d-flex align-items-center gap-2" style="border-bottom-color:rgba(220,53,69,.15);">
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                <h5 class="card-title mb-0 text-danger">Danger Zone</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h6 class="fw-semibold mb-1">Delete Account</h6>
                        <p class="text-muted small mb-0">
                            Permanently delete your account and all associated data. This action cannot be undone.
                        </p>
                    </div>
                    <button type="button" class="btn btn-outline-danger flex-shrink-0"
                            data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash me-2"></i>Delete My Account
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>{{-- /RIGHT --}}
</div>

{{-- Delete Account Modal --}}
@if(!$user->is_first_user)
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <div class="text-center w-100 pt-2">
                    <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle"
                         style="width:64px;height:64px;">
                        <i class="bi bi-trash3 text-danger" style="font-size:1.75rem;"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-danger" id="deleteAccountModalLabel">Delete Account?</h5>
                    <p class="text-muted small mt-1 mb-0">
                        This will permanently delete your account and all associated data. There is no undo.
                    </p>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="delete_password" class="form-label fw-semibold small">
                            Confirm your password to continue <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="delete_password" name="password"
                               class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif"
                               placeholder="Enter your password">
                        @if($errors->userDeletion->has('password'))
                            <div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>
                        @endif
                    </div>
                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Yes, Delete My Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function togglePw(id) {
    const input = document.getElementById(id);
    const icon = document.getElementById('eye-' + id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

// Auto-open delete modal if there were validation errors on it
@if($errors->userDeletion->isNotEmpty())
    document.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
    });
@endif
</script>
@endpush

@endsection
