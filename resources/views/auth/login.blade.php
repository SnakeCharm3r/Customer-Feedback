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
                <h2>Staff Feedback Workspace</h2>
                <div class="login-brand-list">
                    <span><i class="bi bi-check-circle"></i>Track feedback status</span>
                    <span><i class="bi bi-check-circle"></i>Manage team responses</span>
                    <span><i class="bi bi-check-circle"></i>Follow up on urgent issues</span>
                </div>
            </div>

            <div class="login-security-note">
                <i class="bi bi-shield-lock"></i>
                Authorized staff access
            </div>
        </aside>

        {{-- ── Right form panel ── --}}
        <div class="login-form-panel">
            <div class="login-eyebrow">Secure Sign In</div>
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-copy">Sign in to your account to manage and respond to feedback.</p>

            {{-- Session status (e.g. password reset link sent) --}}
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

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Email Address</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-envelope login-input-icon"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required autofocus autocomplete="username"
                               placeholder="you@example.com">
                    </div>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold">Password</label>
                    <div class="login-input-wrap">
                        <i class="bi bi-lock login-input-icon"></i>
                        <input id="password" type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="current-password"
                               placeholder="Enter your password">
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

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                        <label class="form-check-label small text-muted" for="remember_me">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="small text-decoration-none" style="color:#0b6b2c;">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <button type="submit" id="loginSubmitButton" class="btn btn-auth">
                    <span class="login-idle-state"><i class="bi bi-box-arrow-in-right me-2"></i>Sign In</span>
                    <span class="login-loading-state d-none"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>Signing In...</span>
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="small text-muted text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Back to public portal
                </a>
            </div>

            @if (!\App\Models\User::hasUsers())
                <div class="alert alert-info mt-4 py-2 px-3 small text-center">
                    <i class="bi bi-shield-check me-2"></i>
                    <strong>Initial Setup:</strong> <a href="{{ route('register') }}" class="alert-link">Create administrator account</a>
                </div>
            @else
                <div class="text-center mt-4 small text-muted">
                    <i class="bi bi-lock me-1"></i>
                    Registration is by invitation only. Contact your administrator for access.
                </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput  = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const loginForm      = document.getElementById('loginForm');
        const submitButton   = document.getElementById('loginSubmitButton');

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

        if (loginForm && submitButton) {
            loginForm.addEventListener('submit', function () {
                if (!loginForm.checkValidity()) return;
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
