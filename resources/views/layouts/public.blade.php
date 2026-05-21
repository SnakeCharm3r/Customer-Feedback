<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('portal.meta.description') }}">
    <title>@yield('title', __('portal.meta.default_title'))</title>
    <link rel="shortcut icon" href="{{ $systemSettings?->faviconUrl() ?? asset('assets/images/favicon.ico') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --ccbrt-navy: #065321;
            --ccbrt-navy-light: #0b6b2c;
            --ccbrt-teal: #15803d;
            --ccbrt-teal-light: #2b9348;
            --ccbrt-lime: #94c83d;
            --ccbrt-lime-light: #add95a;
            --ccbrt-white: #ffffff;
            --ccbrt-gray: #f4f8f1;
            --ccbrt-text: #1f2d1f;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: var(--ccbrt-gray);
            color: var(--ccbrt-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navbar Styles */
        .navbar-ccbrt {
            background: linear-gradient(135deg, var(--ccbrt-navy) 0%, var(--ccbrt-navy-light) 100%);
            padding: 1rem 0;
            box-shadow: 0 10px 24px rgba(6,83,33,0.16);
            border-top: 6px solid var(--ccbrt-lime);
        }
        
        .navbar-ccbrt .navbar-brand {
            color: var(--ccbrt-white);
            font-weight: 600;
            font-size: 1.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.85rem;
        }

        .public-brand-logo {
            width: 56px;
            height: 56px;
            object-fit: contain;
            flex-shrink: 0;
            background: rgba(255,255,255,0.12);
            border-radius: 50%;
            padding: 0.18rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
        }

        .public-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .public-brand-name {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.03em;
        }

        .public-brand-subtitle {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.86;
        }
        
        .navbar-ccbrt .navbar-brand:hover {
            color: var(--ccbrt-white);
        }
        
        .navbar-ccbrt .nav-link {
            color: rgba(255,255,255,0.9);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }
        
        .navbar-ccbrt .nav-link:hover,
        .navbar-ccbrt .nav-link.active {
            color: var(--ccbrt-white);
            background-color: rgba(148,200,61,0.18);
        }
        
        .language-switcher {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            margin-left: 1rem;
            padding: 0.35rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
        }

        .language-switcher-label {
            color: rgba(255,255,255,0.78);
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding-left: 0.35rem;
        }

        .language-switcher-options {
            display: inline-flex;
            align-items: center;
            background: rgba(255,255,255,0.08);
            border-radius: 999px;
            padding: 0.15rem;
        }

        .btn-language-toggle {
            border: none;
            border-radius: 999px;
            background: transparent;
            color: rgba(255,255,255,0.86);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            padding: 0.35rem 0.75rem;
            line-height: 1;
            transition: all 0.2s ease;
        }

        .btn-check:checked + .btn-language-toggle {
            background: var(--ccbrt-lime);
            color: #163223;
            box-shadow: 0 4px 12px rgba(148,200,61,0.28);
        }

        .btn-language-toggle:hover {
            color: var(--ccbrt-white);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--ccbrt-navy) 0%, var(--ccbrt-navy-light) 58%, #107531 100%);
            color: var(--ccbrt-white);
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
            border-top: 4px solid rgba(148,200,61,0.5);
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(148,200,61,0.28) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .hero-section::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 8px;
            background: linear-gradient(90deg, var(--ccbrt-lime-light) 0%, var(--ccbrt-lime) 50%, #6ba82d 100%);
            opacity: 0.95;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 300;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .btn-ccbrt-primary {
            background: linear-gradient(135deg, var(--ccbrt-teal) 0%, var(--ccbrt-navy-light) 100%);
            border-color: var(--ccbrt-teal);
            color: var(--ccbrt-white);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-ccbrt-primary:hover {
            background: linear-gradient(135deg, var(--ccbrt-navy-light) 0%, var(--ccbrt-teal) 100%);
            border-color: var(--ccbrt-navy-light);
            color: var(--ccbrt-white);
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(11,107,44,0.24);
        }
        
        .btn-ccbrt-outline {
            background-color: transparent;
            border: 2px solid var(--ccbrt-white);
            color: var(--ccbrt-white);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-ccbrt-outline:hover {
            background-color: var(--ccbrt-lime);
            border-color: var(--ccbrt-lime);
            color: var(--ccbrt-brand-text, #163223);
        }
        
        /* Card Styles */
        .card-ccbrt {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 12px 30px rgba(6,83,33,0.08);
            background: var(--ccbrt-white);
        }
        
        .card-ccbrt .card-header {
            background: linear-gradient(135deg, var(--ccbrt-navy) 0%, var(--ccbrt-navy-light) 100%);
            color: var(--ccbrt-white);
            border-radius: 1rem 1rem 0 0;
            padding: 1.5rem;
            border: none;
        }
        
        .card-ccbrt .card-body {
            padding: 2rem;
        }
        
        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: var(--ccbrt-navy);
            margin-bottom: 0.5rem;
        }
        
        .form-label .required::after {
            content: ' *';
            color: #dc3545;
        }
        
        .form-control-ccbrt {
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control-ccbrt:focus {
            border-color: var(--ccbrt-lime);
            box-shadow: 0 0 0 0.2rem rgba(148,200,61,0.18);
        }
        
        .form-check-input:checked {
            background-color: var(--ccbrt-teal);
            border-color: var(--ccbrt-teal);
        }
        
        .form-check-input:focus {
            border-color: var(--ccbrt-lime);
            box-shadow: 0 0 0 0.2rem rgba(148,200,61,0.18);
        }
        
        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #eef7e8 0%, #f9fcf5 100%);
            border-left: 4px solid var(--ccbrt-lime);
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        
        /* Steps */
        .process-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 3rem 0;
        }
        
        .process-steps::before {
            content: '';
            position: absolute;
            top: 30px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: rgba(148,200,61,0.35);
            z-index: 0;
        }
        
        .step {
            text-align: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--ccbrt-navy) 0%, var(--ccbrt-navy-light) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }
        
        .step-title {
            font-weight: 600;
            color: var(--ccbrt-navy);
            margin-bottom: 0.5rem;
        }
        
        .step-description {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(180deg, var(--ccbrt-navy-light) 0%, var(--ccbrt-navy) 100%);
            color: var(--ccbrt-white);
            padding: 3rem 0 2rem;
            margin-top: auto;
            border-top: 8px solid var(--ccbrt-lime);
        }
        
        footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        
        footer a:hover {
            color: var(--ccbrt-white);
        }
        
        /* Alert Styles */
        .alert-ccbrt {
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
        }
        
        .alert-ccbrt-success {
            background-color: #eef7e8;
            border-color: var(--ccbrt-lime);
            color: #184423;
        }
        
        /* Reference Number Display */
        .reference-box {
            background: linear-gradient(135deg, var(--ccbrt-navy) 0%, var(--ccbrt-navy-light) 70%, #107531 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            margin: 2rem 0;
            border-top: 6px solid var(--ccbrt-lime);
        }
        
        .reference-number {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 2px;
            margin-top: 0.5rem;
        }
        
        /* Status Timeline */
        .status-timeline {
            display: flex;
            justify-content: space-between;
            margin: 2rem 0;
            position: relative;
        }
        
        .status-timeline::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(148,200,61,0.35);
            z-index: 0;
        }
        
        .status-step {
            text-align: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        
        .status-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .status-step.active .status-dot {
            background: var(--ccbrt-lime);
            color: #163223;
        }
        
        .status-step.active .status-dot i {
            color: white;
        }
        
        .status-step.completed .status-dot {
            background: var(--ccbrt-navy);
            color: white;
        }
        
        .status-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #6c757d;
        }
        
        .status-step.active .status-label,
        .status-step.completed .status-label {
            color: var(--ccbrt-navy);
            font-weight: 600;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.75rem;
            }
            
            .process-steps {
                flex-direction: column;
            }
            
            .process-steps::before {
                display: none;
            }
            
            .step {
                margin-bottom: 2rem;
            }
            
            .status-timeline {
                flex-wrap: wrap;
            }
        }
        
        @media (max-width: 991.98px) {
            .navbar-ccbrt .navbar-brand {
                font-size: 1.2rem;
                gap: 0.65rem;
            }

            .language-switcher {
                width: fit-content;
                margin-top: 1rem;
                margin-left: auto;
            }

            .public-brand-logo {
                width: 48px;
                height: 48px;
            }

            .public-brand-name {
                font-size: 1.05rem;
            }

            .public-brand-subtitle {
                font-size: 0.62rem;
            }
        }
        
        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.9rem;
        }

        .footer-brand .public-brand-logo {
            width: 68px;
            height: 68px;
            background: rgba(255,255,255,0.14);
        }

        .footer-brand-title {
            margin-bottom: 0.3rem;
            font-weight: 700;
        }

        /* ── TV / Large-screen optimisation (≥ 1600 px) ──────────────────────
           Scales up the public portal for comfortable viewing on 65"+ displays
           at ~2 m viewing distance.
        ─────────────────────────────────────────────────────────────────────── */
        @media (min-width: 1600px) {
            html { font-size: 17px; }

            /* Wider container */
            .container { max-width: 1340px !important; }

            /* Navbar */
            .navbar-ccbrt { padding: 1.25rem 0; }
            .public-brand-logo { width: 68px; height: 68px; }
            .public-brand-name { font-size: 1.6rem; }
            .public-brand-subtitle { font-size: 0.82rem; }
            .navbar-ccbrt .nav-link { font-size: 1rem; padding: 0.6rem 1.1rem; }

            /* Hero */
            .hero-title { font-size: 3.2rem; }
            .hero-subtitle { font-size: 1.45rem; }
            .hero-section { padding: 5.5rem 0; }

            /* Cards */
            .card-ccbrt .card-header { padding: 1.75rem; font-size: 1.15rem; }
            .card-ccbrt .card-body { padding: 2.5rem; }

            /* Buttons */
            .btn-ccbrt-primary,
            .btn-ccbrt-outline { padding: 0.9rem 2.4rem; font-size: 1.05rem; }
            .btn { font-size: 0.95rem; }
            .btn-lg { font-size: 1.1rem !important; padding: 0.85rem 2rem !important; }

            /* Forms */
            .form-control-ccbrt { padding: 0.85rem 1.15rem; font-size: 1rem; }
            .form-label { font-size: 0.95rem; }
            .form-control, .form-select { font-size: 0.95rem; }

            /* Info box */
            .info-box { padding: 1.75rem; font-size: 0.95rem; }

            /* Process steps */
            .step-number { width: 72px; height: 72px; font-size: 1.75rem; }
            .step-title { font-size: 1.05rem; }
            .step-description { font-size: 1rem; }

            /* Reference box */
            .reference-number { font-size: 2.5rem; }

            /* Status timeline */
            .status-dot { width: 52px; height: 52px; font-size: 1.2rem; }
            .status-label { font-size: 0.95rem; }

            /* Footer */
            footer { padding: 3.5rem 0 2.5rem; font-size: 0.95rem; }
            .footer-brand .public-brand-logo { width: 80px; height: 80px; }
        }

        @media (min-width: 1920px) {
            html { font-size: 18.5px; }
            .container { max-width: 1560px !important; }
            .hero-title { font-size: 3.8rem; }
        }

        /* ── Tablet (≤ 991px) ──────────────────────────────────── */
        @media (max-width: 991.98px) {
            .card-ccbrt .card-body { padding: 1.5rem; }
            .card-ccbrt .card-header { padding: 1.1rem 1.5rem; }
        }

        /* ── Mobile (≤ 768px) ───────────────────────────────────── */
        @media (max-width: 768px) {
            .hero-section { padding: 2.5rem 0 !important; }
            .hero-title { font-size: 1.6rem; }
            .hero-subtitle { font-size: 1rem; margin-bottom: 1.25rem; }
            .process-steps { flex-direction: column; margin: 1.5rem 0; }
            .process-steps::before { display: none; }
            .step { display: flex; align-items: flex-start; gap: 1rem; text-align: left; margin-bottom: 1.25rem; }
            .step-number { width: 44px; height: 44px; font-size: 1.1rem; margin: 0; flex-shrink: 0; }
            .status-timeline::before { display: none; }
            .status-timeline { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin: 1.25rem 0; }
            .status-label { font-size: 0.75rem; }
            .status-dot { width: 34px; height: 34px; font-size: 0.85rem; }
            .reference-number { font-size: 1.5rem; letter-spacing: 1px; }
            .reference-box { padding: 1.25rem; margin: 1.25rem 0; }
            .info-box { padding: 1rem; }
            .card-ccbrt .card-body { padding: 1.1rem; }
            .card-ccbrt .card-header { padding: 1rem; }
            footer { padding: 2rem 0 1.25rem; }
            .footer-brand { flex-direction: row; }
            .footer-brand .public-brand-logo { width: 44px; height: 44px; }
        }

        /* ── Small phones (≤ 480px) ─────────────────────────────── */
        @media (max-width: 480px) {
            .hero-title { font-size: 1.3rem !important; }
            .hero-subtitle { font-size: 0.92rem; }
            .btn-ccbrt-primary,
            .btn-ccbrt-outline { padding: 0.6rem 1.1rem; font-size: 0.88rem; }
            .reference-number { font-size: 1.2rem; word-break: break-all; }
            .status-timeline { grid-template-columns: 1fr; gap: 0.4rem; }
            .status-step { display: flex; align-items: center; gap: 0.65rem; text-align: left; }
            .status-dot { margin: 0; flex-shrink: 0; }
            footer { padding: 1.5rem 0 1rem; }
            .card-ccbrt .card-body { padding: 0.9rem; }
        }

        /* ── Hero enhancements ──────────────────────────────── */
        .hero-trust-bar { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; }
        .hero-trust-chip {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.3rem 0.85rem; border-radius: 999px;
            background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22);
            font-size: 0.8rem; font-weight: 500; color: rgba(255,255,255,0.92);
            backdrop-filter: blur(4px);
        }
        .hero-trust-chip i { font-size: 0.85rem; color: var(--ccbrt-lime); }
        .hero-visual { display: flex; align-items: center; justify-content: center; height: 100%; }
        .hero-visual-scene { position: relative; width: 300px; height: 300px; display: flex; align-items: center; justify-content: center; }
        .pulse-ring { position: absolute; border-radius: 50%; border: 1.5px solid rgba(148,200,61,0.3); width: 110px; height: 110px; animation: pulse-expand 3.6s ease-out infinite; }
        .pulse-ring:nth-child(1) { animation-delay: 0s; }
        .pulse-ring:nth-child(2) { animation-delay: 1.2s; }
        .pulse-ring:nth-child(3) { animation-delay: 2.4s; }
        @keyframes pulse-expand { 0% { transform: scale(1); opacity: 0.65; } 100% { transform: scale(2.7); opacity: 0; } }
        .orbit-ring-outer { position: absolute; inset: 0; border-radius: 50%; border: 1.5px dashed rgba(148,200,61,0.32); animation: spin-cw 22s linear infinite; }
        .hero-center-icon { font-size: 6rem; color: var(--ccbrt-lime); position: relative; z-index: 3; animation: heartbeat 2.4s ease-in-out infinite; filter: drop-shadow(0 0 22px rgba(148,200,61,0.55)); }
        .med-icon { position: absolute; display: flex; align-items: center; justify-content: center; width: 52px; height: 52px; border-radius: 16px; background: rgba(255,255,255,0.13); border: 1.5px solid rgba(255,255,255,0.22); backdrop-filter: blur(6px); font-size: 1.4rem; color: rgba(255,255,255,0.88); z-index: 2; box-shadow: 0 6px 20px rgba(0,0,0,0.18); }
        .med-icon--tl { top: 8px; left: 20px; animation: float-a 4.0s ease-in-out infinite; background: rgba(239,68,68,0.2); border-color: rgba(239,68,68,0.35); color: #fca5a5; }
        .med-icon--tr { top: 8px; right: 20px; animation: float-b 4.8s ease-in-out infinite; background: rgba(59,130,246,0.2); border-color: rgba(59,130,246,0.35); color: #93c5fd; }
        .med-icon--ml { top: 50%; left: 0px; transform: translateY(-50%); animation: float-c 3.8s ease-in-out infinite; background: rgba(34,197,94,0.2); border-color: rgba(34,197,94,0.35); color: #86efac; }
        .med-icon--mr { top: 50%; right: 0px; transform: translateY(-50%); animation: float-d 4.4s ease-in-out infinite; background: rgba(168,85,247,0.2); border-color: rgba(168,85,247,0.35); color: #d8b4fe; }
        .med-icon--bl { bottom: 8px; left: 20px; animation: float-e 4.2s ease-in-out infinite; background: rgba(245,158,11,0.2); border-color: rgba(245,158,11,0.35); color: #fcd34d; }
        .med-icon--br { bottom: 8px; right: 20px; animation: float-f 3.6s ease-in-out infinite; background: rgba(20,184,166,0.2); border-color: rgba(20,184,166,0.35); color: #5eead4; }
        @keyframes float-a { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        @keyframes float-b { 0%,100%{transform:translateY(-6px)} 50%{transform:translateY(8px)} }
        @keyframes float-c { 0%,100%{transform:translateY(-50%) translateX(0)} 50%{transform:translateY(-50%) translateX(-8px)} }
        @keyframes float-d { 0%,100%{transform:translateY(-50%) translateX(0)} 50%{transform:translateY(-50%) translateX(8px)} }
        @keyframes float-e { 0%,100%{transform:translateY(0)} 50%{transform:translateY(9px)} }
        @keyframes float-f { 0%,100%{transform:translateY(6px)} 50%{transform:translateY(-8px)} }
        @keyframes heartbeat { 0%,100% { transform: scale(1); filter: drop-shadow(0 0 22px rgba(148,200,61,0.55)); } 14% { transform: scale(1.08); filter: drop-shadow(0 0 30px rgba(148,200,61,0.8)); } 28% { transform: scale(1); filter: drop-shadow(0 0 22px rgba(148,200,61,0.55)); } 42% { transform: scale(1.05); filter: drop-shadow(0 0 26px rgba(148,200,61,0.65)); } 70% { transform: scale(1); } }
        @keyframes spin-cw { to { transform: rotate(360deg); } }

        /* ── Hero CTA buttons ── */
        .hero-cta-group { margin-top: 0.5rem; }
        .hero-btn-primary { position: relative; overflow: hidden; display: inline-flex; align-items: center; gap: 0.6rem; padding: 0.85rem 1.8rem; border-radius: 999px; font-size: 1rem; font-weight: 700; color: #fff; background: var(--ccbrt-lime); border: none; animation: attention-pulse 2.2s ease-in-out infinite; transition: transform 0.2s, box-shadow 0.2s; text-decoration: none; white-space: nowrap; }
        .hero-btn-primary:hover { transform: translateY(-3px) scale(1.03); box-shadow: 0 12px 32px rgba(148,200,61,0.5); color: #fff; animation: none; }
        .hero-btn-shimmer { position: absolute; top: 0; left: -75%; width: 50%; height: 100%; background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,0.45) 50%, transparent 100%); transform: skewX(-20deg); animation: shimmer-sweep 2.8s ease-in-out infinite; }
        .hero-btn-arrow { font-size: 0.85rem; transition: transform 0.25s; }
        .hero-btn-primary:hover .hero-btn-arrow { transform: translateX(5px); }
        .hero-btn-outline { display: inline-flex; align-items: center; gap: 0.6rem; padding: 0.85rem 1.8rem; border-radius: 999px; font-size: 1rem; font-weight: 600; color: #fff; background: transparent; border: 2px solid rgba(255,255,255,0.55); transition: background 0.22s, border-color 0.22s, transform 0.2s; text-decoration: none; white-space: nowrap; }
        .hero-btn-outline:hover { background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.9); color: #fff; transform: translateY(-2px); }
        @keyframes attention-pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(148,200,61,0.55); } 50% { box-shadow: 0 0 0 12px rgba(148,200,61,0); } }
        @keyframes shimmer-sweep { 0% { left: -75%; } 60% { left: 130%; } 100% { left: 130%; } }
        @media (max-width: 480px) { .hero-btn-primary, .hero-btn-outline { width: 100%; justify-content: center; } }

        /* ── Step cards ── */
        .steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; position: relative; }
        .steps-grid::before { content: ''; position: absolute; top: 36px; left: calc(12.5% + 20px); right: calc(12.5% + 20px); height: 2px; background: linear-gradient(90deg, var(--ccbrt-lime) 0%, rgba(148,200,61,0.2) 100%); z-index: 0; }
        .step-card { background: #fff; border-radius: 16px; padding: 1.5rem 1.25rem; text-align: center; box-shadow: 0 4px 16px rgba(6,83,33,0.07); border: 1.5px solid #e5f0e8; position: relative; z-index: 1; transition: transform 0.25s, box-shadow 0.25s; }
        .step-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(6,83,33,0.13); }
        .step-card-number { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, var(--ccbrt-navy) 0%, var(--ccbrt-teal) 100%); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; margin: 0 auto 1rem; box-shadow: 0 6px 16px rgba(6,83,33,0.22); }
        .step-card-title { font-weight: 700; color: var(--ccbrt-navy); font-size: 0.95rem; margin-bottom: 0.4rem; }
        .step-card-desc { font-size: 0.83rem; color: #5e6b73; line-height: 1.5; margin: 0; }

        /* ── Service cards ── */
        .service-card { border-radius: 16px; padding: 1.5rem 1rem; text-align: center; border: 1.5px solid transparent; transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s; cursor: default; height: 100%; }
        .service-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.09); }
        .service-icon { width: 58px; height: 58px; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.85rem; }
        .service-card-title { font-size: 0.85rem; font-weight: 600; color: #1f2d1f; margin: 0; }
        .svc--outpatient { background: #eef7ff; border-color: #c7e2ff; } .svc--outpatient .service-icon { background: #dbeafe; color: #1d4ed8; }
        .svc--inpatient  { background: #fff5f5; border-color: #fecdd3; } .svc--inpatient  .service-icon { background: #fee2e2; color: #dc2626; }
        .svc--eye        { background: #f5f3ff; border-color: #ddd6fe; } .svc--eye        .service-icon { background: #ede9fe; color: #7c3aed; }
        .svc--rehab      { background: #fffbeb; border-color: #fde68a; } .svc--rehab      .service-icon { background: #fef3c7; color: #d97706; }
        .svc--pharmacy   { background: #f0fdf4; border-color: #bbf7d0; } .svc--pharmacy   .service-icon { background: #dcfce7; color: #15803d; }
        .svc--reception  { background: #fff7ed; border-color: #fed7aa; } .svc--reception  .service-icon { background: #ffedd5; color: #ea580c; }
        .svc--billing    { background: #f0fdfa; border-color: #99f6e4; } .svc--billing    .service-icon { background: #ccfbf1; color: #0d9488; }
        .svc--other      { background: #fafafa; border-color: #e5e7eb; } .svc--other      .service-icon { background: #f3f4f6; color: #4b5563; }

        /* ── CTA section ── */
        .cta-section { background: linear-gradient(135deg, var(--ccbrt-navy) 0%, var(--ccbrt-teal) 60%, #22c55e 100%); padding: 5rem 0; position: relative; overflow: hidden; }
        .cta-section::before { content: ''; position: absolute; top: -60%; right: -10%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(148,200,61,0.22) 0%, transparent 70%); border-radius: 50%; }
        .cta-section::after { content: ''; position: absolute; bottom: -40%; left: -5%; width: 350px; height: 350px; background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%); border-radius: 50%; }
        .cta-badge { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 1rem; border-radius: 999px; background: rgba(148,200,61,0.2); border: 1px solid rgba(148,200,61,0.4); color: var(--ccbrt-lime); font-size: 0.8rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 1.25rem; }
        .cta-actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem; }

        @media (max-width: 991px) { .steps-grid { grid-template-columns: repeat(2, 1fr); } .steps-grid::before { display: none; } .hero-section { padding: 3.5rem 0 3rem; } }
        @media (max-width: 768px) { .hero-trust-bar { gap: 0.6rem; } .cta-section { padding: 3.5rem 0; } .cta-actions .btn { min-width: 220px; } }
        @media (max-width: 576px) { .steps-grid { grid-template-columns: 1fr; } .step-card { text-align: left; display: flex; align-items: flex-start; gap: 1rem; } .step-card-number { margin: 0; flex-shrink: 0; width: 44px; height: 44px; font-size: 1rem; } .hero-section { padding: 2.5rem 0 2rem; } .cta-actions { flex-direction: column; align-items: center; } .cta-actions .btn { width: 100%; max-width: 320px; } .service-card { padding: 1.1rem 0.75rem; } }
        @media (max-width: 480px) { .col-6 { width: 100% !important; flex: 0 0 100%; max-width: 100%; } }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-ccbrt">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}" alt="{{ $systemSettings?->organization_name ?? __('portal.brand.hospital') }} Logo" class="public-brand-logo">
                <span class="public-brand-text">
                    <span class="public-brand-name">{{ $systemSettings?->organization_name ?? __('portal.brand.hospital') }}</span>
                    <span class="public-brand-subtitle">{{ $systemSettings?->portal_name ?? __('portal.brand.portal') }}</span>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">{{ __('portal.nav.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('feedback*') ? 'active' : '' }}" href="{{ route('feedback.create') }}">{{ __('portal.nav.submit_feedback') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('track*') ? 'active' : '' }}" href="{{ route('feedback.track') }}">{{ __('portal.nav.track_feedback') }}</a>
                    </li>
                </ul>
                <form method="POST" action="{{ route('locale.switch') }}" class="language-switcher">
                    @csrf
                    <span class="language-switcher-label">{{ __('portal.locale.label') }}</span>
                    <div class="language-switcher-options" role="radiogroup" aria-label="{{ __('portal.locale.label') }}">
                        <input type="radio" class="btn-check" name="locale" id="locale-en" value="en" autocomplete="off" onchange="this.form.submit()" {{ app()->getLocale() === 'en' ? 'checked' : '' }}>
                        <label class="btn btn-language-toggle" for="locale-en">{{ __('portal.locale.english') }}</label>
                        <input type="radio" class="btn-check" name="locale" id="locale-sw" value="sw" autocomplete="off" onchange="this.form.submit()" {{ app()->getLocale() === 'sw' ? 'checked' : '' }}>
                        <label class="btn btn-language-toggle" for="locale-sw">{{ __('portal.locale.swahili') }}</label>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="footer-brand mb-3">
                        <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}" alt="{{ $systemSettings?->organization_name ?? __('portal.brand.hospital') }} Logo" class="public-brand-logo">
                        <div>
                            <h5 class="footer-brand-title">{{ $systemSettings?->organization_name ?? __('portal.brand.hospital') }}</h5>
                            <p class="mb-0">{{ $systemSettings?->footerAboutText() ?? __('portal.brand.about') }}</p>
                        </div>
                    </div>
                    <p class="mb-0"><i class="bi bi-geo-alt me-2"></i>{{ $systemSettings?->footerLocationText() ?? __('portal.footer.location') }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="mb-3">{{ __('portal.footer.contact_us') }}</h5>
                    <p class="mb-2"><i class="bi bi-telephone me-2"></i>{{ $systemSettings?->contact_phone ?: '+255 22 277 5000' }}</p>
                    <p class="mb-2"><i class="bi bi-envelope me-2"></i>{{ $systemSettings?->contact_email ?: 'feedback@ccbrt.org' }}</p>
                    <p class="mb-0"><i class="bi bi-clock me-2"></i>{{ $systemSettings?->footerHoursText() ?? __('portal.footer.hours') }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="mb-3">{{ __('portal.footer.privacy_terms') }}</h5>
                    <p class="mb-2">{{ $systemSettings?->footerPrivacyText() ?? __('portal.footer.privacy_copy') }}</p>
                    <p class="mb-0">
                        <a href="{{ $systemSettings?->privacyPolicyUrl() ?: '#' }}">{{ __('portal.footer.privacy_policy') }}</a>
                        |
                        <a href="{{ $systemSettings?->termsOfUseUrl() ?: '#' }}">{{ __('portal.footer.terms_of_use') }}</a>
                    </p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ $systemSettings?->organization_name ?? __('portal.brand.hospital') }}. {{ __('portal.footer.rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
