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
    <div class="auth-body text-center">
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:72px;height:72px;background:#eef7e8;color:#0b6b2c;">
                <i class="bi bi-person-check fs-1"></i>
            </div>
            <h5 class="fw-bold mb-2" style="color:#065321;">Registration submitted</h5>
            <p class="text-muted small mb-0">
                {{ session('status', 'Your account request has been received and is awaiting administrator approval.') }}
            </p>
        </div>

        <div class="alert alert-warning text-start py-3 px-3 small mb-4">
            <i class="bi bi-clock-history me-2"></i>
            New staff accounts stay <strong>pending approval</strong> until an administrator reviews the registration and assigns access.
        </div>

        <div class="d-grid gap-2">
            <a href="{{ route('login') }}" class="btn btn-auth">
                <i class="bi bi-box-arrow-in-right me-2"></i>Go to Sign In
            </a>
            <a href="{{ route('home') }}" class="btn btn-light border">
                <i class="bi bi-house-door me-2"></i>Return to Home
            </a>
        </div>
    </div>
</div>
</x-guest-layout>
