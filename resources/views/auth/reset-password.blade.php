<x-guest-layout>
<div class="auth-login-card">
    <div class="login-shell">

        {{-- ── Left brand panel ── --}}
        <aside class="login-brand-panel">
            <div class="login-brand-top">
                <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}"
                     alt="{{ $systemSettings?->organization_name ?? 'CCBRT' }} Logo"
                     class="login-brand-logo">
                <div>
                    <div class="login-brand-name">{{ $systemSettings?->organization_name ?? 'CCBRT Hospital' }}</div>
                    <div class="login-brand-subtitle">{{ $systemSettings?->portal_name ?? 'Customer Feedback Portal' }}</div>
                </div>
            </div>

            <div class="login-brand-message">
                <h2>Set Your Password</h2>
                <div class="login-brand-list">
                    <span><i class="bi bi-shield-lock"></i>Secure your account</span>
                    <span><i class="bi bi-key"></i>Create a strong password</span>
                    <span><i class="bi bi-check-circle"></i>Get started quickly</span>
                </div>
            </div>

            <div class="login-security-note">
                <i class="bi bi-shield-check"></i>
                Protected by CCBRT Security
            </div>
        </aside>

        {{-- ── Right form panel ── --}}
        <div class="login-form-panel">
            <div class="login-eyebrow">Password Reset</div>
            <h1 class="login-title">Welcome, {{ $user?->getFullName() ?? 'User' }}</h1>
            <p class="login-copy">Create a new secure password to access your account and get started.</p>

            {{-- Session status --}}
            @if (session('status'))
                <div class="alert alert-info alert-dismissible fade show py-2 px-3 small mb-3" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Auth errors --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 small mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form id="resetPasswordForm" method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Email Address</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-envelope login-input-icon"></i>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required autofocus autocomplete="username"
                               placeholder="you@example.com">
                    </div>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">New Password</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-lock login-input-icon"></i>
                        <input id="password" type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="new-password"
                               placeholder="Enter new password">
                        <button type="button"
                                id="togglePassword"
                                class="login-password-toggle"
                                aria-label="Show password"
                                aria-pressed="false">
                            <i class="bi bi-eye fs-5"></i>
                        </button>
                    </div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label small fw-semibold">Confirm Password</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-lock login-input-icon"></i>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               required autocomplete="new-password"
                               placeholder="Confirm new password">
                    </div>
                    @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" id="resetPasswordSubmitButton" class="btn btn-auth">
                    <span class="login-idle-state"><i class="bi bi-arrow-clockwise me-2"></i>Reset Password</span>
                    <span class="login-loading-state d-none"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Resetting...</span>
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="small text-muted text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Back to login
                </a>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const resetPasswordForm = document.getElementById('resetPasswordForm');
        const submitButton = document.getElementById('resetPasswordSubmitButton');

        if (passwordInput && togglePassword) {
            togglePassword.addEventListener('click', function () {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                togglePassword.setAttribute('aria-label',   isHidden ? 'Hide password' : 'Show password');
                togglePassword.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
                togglePassword.querySelector('i')?.classList.toggle('bi-eye',       !isHidden);
                togglePassword.querySelector('i')?.classList.toggle('bi-eye-slash',  isHidden);
            });
        }

        if (resetPasswordForm && submitButton) {
            resetPasswordForm.addEventListener('submit', function () {
                if (!resetPasswordForm.checkValidity()) return;
                submitButton.disabled = true;
                submitButton.setAttribute('aria-busy', 'true');
                submitButton.querySelector('.login-idle-state')?.classList.add('d-none');
                submitButton.querySelector('.login-loading-state')?.classList.remove('d-none');
            });
        }
    });
</script>
@endpush
</x-guest-layout>
