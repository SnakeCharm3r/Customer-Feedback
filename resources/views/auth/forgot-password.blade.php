<x-guest-layout>
<div class="auth-login-card">
    <div class="login-shell">

        {{-- Left brand panel --}}
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
                <h2>Recover Your Account</h2>
                <div class="login-brand-list">
                    <span><i class="bi bi-envelope-check"></i>Receive a secure reset link</span>
                    <span><i class="bi bi-key"></i>Choose a new password</span>
                    <span><i class="bi bi-check-circle"></i>Return to your workspace</span>
                </div>
            </div>

            <div class="login-security-note">
                <i class="bi bi-shield-lock"></i>
                Authorized staff access
            </div>
        </aside>

        {{-- Right form panel --}}
        <div class="login-form-panel">
            <div class="login-eyebrow">Password Assistance</div>
            <h1 class="login-title">Forgot Your Password?</h1>
            <p class="login-copy">Enter the email address linked to your account and we will send you a secure password reset link.</p>

            @if (session('status'))
                <div class="alert alert-info alert-dismissible fade show py-2 px-3 small mb-3" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 small mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label small fw-semibold">Email Address</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-envelope login-input-icon"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required autofocus autocomplete="email"
                               placeholder="you@example.com">
                    </div>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" id="forgotPasswordSubmitButton" class="btn btn-auth">
                    <span class="login-idle-state"><i class="bi bi-send me-2"></i>Send Reset Link</span>
                    <span class="login-loading-state d-none"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Sending...</span>
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
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const submitButton = document.getElementById('forgotPasswordSubmitButton');

        if (forgotPasswordForm && submitButton) {
            forgotPasswordForm.addEventListener('submit', function () {
                if (!forgotPasswordForm.checkValidity()) return;
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
