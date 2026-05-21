<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') | {{ $systemSettings?->appDisplayName() ?? 'CCBRT Feedback System' }}</title>
    <link rel="shortcut icon" href="{{ $systemSettings?->faviconUrl() ?? asset('assets/images/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .auth-page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f7f5;
            padding: 2rem;
        }
        .auth-card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .auth-card-wide { max-width: 680px; }
        .auth-header {
            background: linear-gradient(135deg, #065321, #0b6b2c);
            padding: 2rem;
            text-align: center;
            color: #fff;
        }
        .auth-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }
        .auth-brand-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            padding: 0.25rem;
            flex-shrink: 0;
        }
        .auth-brand-copy {
            text-align: left;
        }
        .auth-header .logo-text {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .auth-header .logo-sub {
            font-size: 0.78rem;
            opacity: 0.8;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .auth-body { padding: 2rem; }
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.6rem 0.85rem;
            font-size: 0.9rem;
            border: 1px solid #d1d5db;
        }
        .form-control:focus, .form-select:focus {
            border-color: #94c83d;
            box-shadow: 0 0 0 3px rgba(148,200,61,0.18);
        }
        .btn-auth {
            background: linear-gradient(135deg, #15803d, #0b6b2c);
            color: #fff;
            border: none;
            padding: 0.65rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            transition: opacity 0.2s;
        }
        .btn-auth:hover { opacity: 0.9; color: #fff; }
        .section-divider {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.4rem;
            margin-bottom: 1rem;
            margin-top: 1.25rem;
        }
        .invalid-feedback { display: block; }

        /* ── Login split-panel ── */
        .auth-login-card {
            max-width: 980px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid rgba(15,23,42,0.08);
            box-shadow: 0 22px 70px rgba(15,23,42,0.16);
            overflow: hidden;
        }
        .login-shell {
            display: grid;
            grid-template-columns: minmax(300px, 0.9fr) minmax(390px, 1.1fr);
            min-height: 540px;
        }
        .login-brand-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 2rem;
            background: #075a29;
            color: #fff;
            padding: 2.75rem 2.5rem;
        }
        .login-brand-top {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .login-brand-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
            border: 1px solid rgba(255,255,255,0.42);
            padding: 0.35rem;
            flex-shrink: 0;
        }
        .login-brand-name {
            color: #fff;
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .login-brand-subtitle {
            color: rgba(255,255,255,0.78);
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            margin-top: 0.35rem;
            text-transform: uppercase;
        }
        .login-brand-message h2 {
            color: #fff;
            font-size: 1.65rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        .login-brand-message p {
            color: rgba(255,255,255,0.86);
            line-height: 1.6;
            margin: 0;
        }
        .login-brand-list {
            display: grid;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        .login-brand-list span {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
        }
        .login-brand-list i { color: #b8d986; }
        .login-security-note {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            width: fit-content;
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            color: #fff;
            padding: 0.55rem 0.85rem;
            font-size: 0.82rem;
            font-weight: 600;
        }
        .login-form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3.4rem;
            background: #fff;
        }
        .login-eyebrow {
            color: #0b6b2c;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.55rem;
        }
        .login-title {
            color: #065321;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.45rem;
        }
        .login-copy {
            color: #64748b;
            font-size: 0.92rem;
            margin-bottom: 1.5rem;
            max-width: 380px;
        }
        .login-input-wrap { position: relative; }
        .login-input-icon {
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
            z-index: 2;
        }
        .login-input-wrap .form-control {
            min-height: 46px;
            padding-left: 2.65rem;
            padding-right: 2.65rem;
            border-radius: 8px;
        }
        .login-password-toggle {
            position: absolute;
            right: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            border: 0;
            background: transparent;
            padding: 0;
            z-index: 3;
        }
        .btn-auth:disabled,
        .btn-auth[aria-busy="true"] { opacity: 0.9; }
        .btn-auth:disabled,
        .btn-auth[aria-busy="true"],
        .btn-auth .login-loading-state { color: #fff; }
        @media (max-width: 575.98px) {
            .auth-brand { flex-direction: column; gap: 0.75rem; }
            .auth-brand-copy { text-align: center; }
            .auth-brand-logo { width: 64px; height: 64px; }
            .auth-header .logo-text { font-size: 1.8rem; }
        }
        @media (max-width: 767.98px) {
            .auth-login-card { max-width: 480px; }
            .auth-page-wrapper { padding: 1rem; }
            .login-shell { grid-template-columns: 1fr; min-height: auto; }
            .login-brand-panel { padding: 1.25rem 1.5rem; }
            .login-brand-message { display: none; }
            .login-security-note { display: none; }
            .login-form-panel { padding: 1.65rem 1.5rem 1.85rem; }
            .login-brand-top { align-items: center; }
            .login-brand-logo { width: 52px; height: 52px; }
            .login-brand-name { font-size: 1.25rem; }
            .login-brand-subtitle { font-size: 0.68rem; letter-spacing: 0.05em; }
            .login-eyebrow { margin-bottom: 0.4rem; }
            .login-title { font-size: 1.55rem; }
            .login-copy { margin-bottom: 1.1rem; }
        }

        @media (min-width: 1600px) {
            html { font-size: 17px; }
            .auth-card { max-width: 560px; }
            .auth-card-wide { max-width: 780px; }
            .auth-login-card { max-width: 1080px; }
            .auth-brand-logo { width: 88px; height: 88px; }
            .auth-header .logo-text { font-size: 2.4rem; }
            .auth-header .logo-sub { font-size: 0.9rem; letter-spacing: 3px; }
            .auth-header { padding: 2.5rem; }
            .auth-body { padding: 2.5rem; }
            .login-brand-logo { width: 88px; height: 88px; }
            .login-brand-name { font-size: 1.9rem; }
            .login-form-panel { padding: 3.5rem 4rem; }
            .login-title { font-size: 2.1rem; }
            .form-control, .form-select { font-size: 1rem; padding: 0.75rem 1rem; }
            .btn-auth { font-size: 1rem; padding: 0.8rem 1.75rem; }
            .section-divider { font-size: 0.8rem; }
        }

        @media (min-width: 1920px) {
            html { font-size: 18.5px; }
            .auth-card { max-width: 620px; }
            .auth-card-wide { max-width: 860px; }
            .auth-login-card { max-width: 1180px; }
        }
    </style>
</head>
<body>
    <div class="auth-page-wrapper">
        {{ $slot }}
    </div>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
