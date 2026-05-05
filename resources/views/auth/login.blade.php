<x-guest-layout>
 <div class="auth-card">
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
         <h5 class="fw-bold mb-1" style="color:#065321;">Welcome Back</h5>
         <p class="text-muted small mb-4">Sign in to your account to continue.</p>

        {{-- Session Status (e.g. pending approval or password reset link sent) --}}
        @if (session('status'))
            <div class="alert alert-info alert-dismissible fade show py-2 px-3 small" role="alert">
                <i class="bi bi-info-circle me-2"></i>{{ session('status') }}
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Auth errors --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small">
                <i class="bi bi-exclamation-triangle me-2"></i>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror"
                       required autofocus autocomplete="username"
                       placeholder="you@example.com">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold">Password</label>
                <div class="position-relative">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required autocomplete="current-password"
                           placeholder="Enter your password">
                    <button type="button"
                            class="btn btn-sm position-absolute top-50 translate-middle-y end-0 me-2 p-0 border-0 bg-transparent text-muted"
                            onclick="var p=document.getElementById('password'); p.type=p.type==='password'?'text':'password';">
                        <i class="bi bi-eye fs-5"></i>
                    </button>
                </div>
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

            <button type="submit" class="btn btn-auth">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <div class="text-center mt-4 small text-muted">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold" style="color:#0b6b2c;">Register here</a>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('home') }}" class="small text-muted text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>Back to public portal
            </a>
        </div>
    </div>
</div>
</x-guest-layout>
