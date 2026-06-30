<head>

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ $systemSettings?->appDisplayName() ?? 'CCBRT Feedback System' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="{{ $systemSettings?->appDisplayName() ?? 'CCBRT Hospital Customer Feedback Management System' }}" name="description">
    <meta content="{{ $systemSettings?->organization_name ?? 'CCBRT' }}" name="author">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ $systemSettings?->faviconUrl() ?? asset('assets/images/favicon.ico') }}">

    <!-- Fonts css load -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link id="fontsLink" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">

    <!-- Apply saved theme immediately to prevent flash -->
    <script>(function(){var t=localStorage.getItem('ccbrt_theme');if(t)document.documentElement.setAttribute('data-bs-theme',t);})();</script>

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css">
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css">

    <style>
        :root {
            --ccbrt-brand-900: #065321;
            --ccbrt-brand-800: #0b6b2c;
            --ccbrt-brand-700: #15803d;
            --ccbrt-brand-500: #94c83d;
            --ccbrt-brand-400: #add95a;
            --ccbrt-brand-100: #eef7e8;
            --ccbrt-brand-text: #163223;
            --bs-primary: #0b6b2c;
            --bs-primary-rgb: 11, 107, 44;
            --bs-link-color: #0b6b2c;
            --bs-link-hover-color: #065321;
        }

        #page-topbar,
        #page-topbar .navbar-header {
            background: #ffffff !important;
            border-bottom: 3px solid var(--ccbrt-brand-500);
            box-shadow: 0 6px 20px rgba(6, 83, 33, 0.08);
        }

        #page-topbar .btn-topbar,
        #page-topbar .topbar-user > .btn,
        #page-topbar .vertical-menu-btn {
            color: var(--ccbrt-brand-800) !important;
        }

        #page-topbar .btn-topbar:hover,
        #page-topbar .topbar-user > .btn:hover,
        #page-topbar .vertical-menu-btn:hover {
            background-color: var(--ccbrt-brand-100) !important;
        }

        #page-topbar .logo-dark .logo-sm,
        #page-topbar .logo-dark .logo-lg {
            color: var(--ccbrt-brand-800) !important;
        }

        .app-menu.navbar-menu,
        .navbar-menu {
            background: linear-gradient(180deg, var(--ccbrt-brand-900) 0%, var(--ccbrt-brand-800) 100%) !important;
            border-right: 1px solid rgba(148, 200, 61, 0.18);
        }

        .navbar-brand-box {
            background: transparent !important;
            border-bottom: 1px solid rgba(148, 200, 61, 0.18);
            height: var(--tb-header-height, 70px);
            display: flex;
            align-items: center;
            padding: 0 1rem;
            box-sizing: border-box;
        }

        .admin-brand-link {
            text-decoration: none;
            flex: 1;
            min-width: 0;
        }

        /* Push the sm-hover toggle to the far right of the brand box */
        .btn-vertical-sm-hover {
            margin-left: auto;
            flex-shrink: 0;
        }

        .admin-brand-shell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .admin-brand-icon-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 2px solid rgba(148, 200, 61, 0.45);
            flex-shrink: 0;
            overflow: hidden;
        }

        .admin-brand-logo {
            width: 36px;
            height: 36px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .admin-brand-logo-lg {
            width: 38px;
            height: 38px;
        }

        .admin-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .sidebar-brand-copy {
            max-width: 160px;
            overflow: hidden;
            opacity: 1;
            white-space: nowrap;
            transition: max-width 0.2s ease, opacity 0.2s ease, transform 0.2s ease;
        }

        .navbar-menu .navbar-brand-box {
            overflow: hidden;
        }

        .navbar-menu.sidebar-brand-collapsed .admin-brand-shell {
            width: 100%;
            justify-content: center;
            gap: 0;
        }

        .navbar-menu.sidebar-brand-collapsed .sidebar-brand-copy {
            max-width: 0;
            opacity: 0;
            transform: translateX(-8px);
            pointer-events: none;
        }

        [data-layout="vertical"][data-sidebar-size="sm-hover"] .navbar-menu.sidebar-brand-collapsed:hover .admin-brand-shell,
        [data-layout="vertical"][data-sidebar-size="sm-hover-active"] .navbar-menu .admin-brand-shell {
            justify-content: flex-start;
            gap: 0.75rem;
        }

        [data-layout="vertical"][data-sidebar-size="sm-hover"] .navbar-menu.sidebar-brand-collapsed:hover .sidebar-brand-copy,
        [data-layout="vertical"][data-sidebar-size="sm-hover-active"] .navbar-menu .sidebar-brand-copy {
            max-width: 160px;
            opacity: 1;
            transform: translateX(0);
            pointer-events: auto;
        }

        .admin-brand-title {
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .admin-brand-subtitle {
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            opacity: 0.82;
        }

        .sidebar-background {
            background: transparent !important;
        }

        /* ── dm-text-adapt: dark text in light mode, readable in dark mode ──── */
        .dm-text-adapt { color: #1a1e2a; }
        [data-bs-theme="dark"] .dm-text-adapt { color: var(--dm-text, #e2e8f0) !important; }

        /* ── Table reference number links ───────────────────────────────────── */
        .table-ref-link {
            color: #1a7a3c;
            font-weight: 600;
            font-size: 12px;
            font-family: monospace;
            text-decoration: none;
        }

        [data-bs-theme="dark"] .table-ref-link {
            color: #5bbf7a !important;
        }

        /* ── Priority table row highlight ────────────────────────────────────── */
        tr.priority-row > td,
        tr.priority-row > th {
            background-color: #fdf3e3 !important;
            border-top: 1px solid #e8c97a !important;
            border-bottom: 1px solid #e8c97a !important;
        }

        tr.priority-row > td:first-child {
            border-left: 3px solid #d4920a !important;
        }

        [data-bs-theme="dark"] tr.priority-row > td,
        [data-bs-theme="dark"] tr.priority-row > th {
            background-color: #38332a !important;
            border-top: 1px solid #6b5520 !important;
            border-bottom: 1px solid #6b5520 !important;
        }

        [data-bs-theme="dark"] tr.priority-row > td:first-child {
            border-left: 3px solid #d4920a !important;
        }

        /* ── Escalation row entry (Actions card & modal) ────────────────────── */
        .esc-row-entry {
            background: #f6fbf4;
            border: 1px solid #ddeedd;
        }

        [data-bs-theme="dark"] .esc-row-entry {
            background: #263030 !important;
            border: 1px solid rgba(148,200,61,0.18) !important;
        }

        [data-bs-theme="dark"] .esc-row-entry,
        [data-bs-theme="dark"] .esc-row-entry span:not(.badge) {
            color: var(--dm-text) !important;
        }

        .esc-response-box {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            border-left: 4px solid #0b6b2c;
            background: #f6fbf4;
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.6;
        }

        [data-bs-theme="dark"] .esc-response-box {
            background: #263030 !important;
            border-left-color: var(--ccbrt-brand-500) !important;
            color: var(--dm-text) !important;
        }

        /* ── Last Reviewed box (Actions card) ───────────────────────────────── */
        .last-reviewed-box {
            background: #eef7e8;
            border-left: 3px solid #0b6b2c;
        }

        [data-bs-theme="dark"] .last-reviewed-box {
            background: #2a3a2e !important;
            border-left: 3px solid var(--ccbrt-brand-500) !important;
        }

        [data-bs-theme="dark"] .last-reviewed-box .text-muted {
            color: var(--dm-text-muted) !important;
        }

        [data-bs-theme="dark"] .last-reviewed-box .fw-semibold {
            color: var(--dm-text) !important;
        }

        /* ── Feedback detail field boxes ────────────────────────────────────── */
        .feedback-field-box {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            background: #f0f1f3;
            color: #1a1e2a;
        }

        .feedback-field-box p {
            color: #1a1e2a !important;
        }

        .feedback-field-primary { border-left: 4px solid #065321; }
        .feedback-field-success  { border-left: 4px solid #198754; }
        .feedback-field-warning  { border-left: 4px solid #ffc107; background: #f9f5e3; }

        [data-bs-theme="dark"] .feedback-field-box {
            background: #3a404f !important;
            color: #e4e8f0 !important;
        }

        [data-bs-theme="dark"] .feedback-field-box p {
            color: #e4e8f0 !important;
        }

        [data-bs-theme="dark"] .feedback-field-warning {
            background: #3d3a2e !important;
        }

        .navbar-nav .menu-title span {
            color: rgba(255, 255, 255, 0.72) !important;
            letter-spacing: 0.08em;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.92) !important;
        }

        .navbar-nav .nav-link i {
            color: var(--ccbrt-brand-400) !important;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active,
        .navbar-nav .nav-link[aria-expanded='true'] {
            background: rgba(148, 200, 61, 0.16) !important;
            color: #ffffff !important;
        }

        .navbar-nav .nav-link:hover i,
        .navbar-nav .nav-link.active i,
        .navbar-nav .nav-link[aria-expanded='true'] i {
            color: var(--ccbrt-brand-500) !important;
        }

        .header-profile-user.bg-primary,
        .bg-primary,
        .badge.bg-primary,
        .btn-primary {
            background-color: var(--ccbrt-brand-800) !important;
            border-color: var(--ccbrt-brand-800) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--ccbrt-brand-900) !important;
            border-color: var(--ccbrt-brand-900) !important;
        }

        .btn-outline-primary {
            color: var(--ccbrt-brand-800) !important;
            border-color: var(--ccbrt-brand-800) !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            background-color: var(--ccbrt-brand-800) !important;
            border-color: var(--ccbrt-brand-800) !important;
            color: #ffffff !important;
        }

        .text-primary,
        .page-title-box h4,
        .card-title,
        .breadcrumb-item a,
        a:not(.btn):not(.nav-link):not(.dropdown-item) {
            color: var(--ccbrt-brand-800);
        }

        .text-primary {
            color: var(--ccbrt-brand-800) !important;
        }

        .bg-primary-subtle {
            background-color: rgba(148, 200, 61, 0.18) !important;
        }

        .text-info,
        .text-info-emphasis {
            color: var(--ccbrt-brand-700) !important;
        }

        .bg-info-subtle {
            background-color: rgba(21, 128, 61, 0.14) !important;
        }

        .card {
            border: 1px solid rgba(11, 107, 44, 0.08);
            box-shadow: 0 8px 24px rgba(6, 83, 33, 0.05);
        }

        .card-header {
            border-bottom-color: rgba(11, 107, 44, 0.08);
        }

        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            border-color: var(--ccbrt-brand-500);
            box-shadow: 0 0 0 0.2rem rgba(148, 200, 61, 0.18);
        }

        .form-check-input:checked {
            background-color: var(--ccbrt-brand-800);
            border-color: var(--ccbrt-brand-800);
        }

        .page-link {
            color: var(--ccbrt-brand-800);
        }

        .page-item.active .page-link {
            background-color: var(--ccbrt-brand-800);
            border-color: var(--ccbrt-brand-800);
        }

        .topbar-badge.bg-warning {
            background-color: var(--ccbrt-brand-500) !important;
            color: var(--ccbrt-brand-text) !important;
        }

        /* ── Dark mode overrides ────────────────────────────────────────────── */
        /*
         * Palette — softer slate tones (not near-black) for comfortable reading:
         *   --dm-bg-base   : page / body background
         *   --dm-bg-raised : cards, modals, dropdowns (one step lighter)
         *   --dm-bg-surface: card headers, table headers, inputs (slightly lighter still)
         *   --dm-border    : visible but subtle borders
         *   --dm-text      : primary text
         *   --dm-text-muted: secondary / muted text
         */
        [data-bs-theme="dark"] {
            --dm-bg-base:    #1f2433;
            --dm-bg-raised:  #2a3042;
            --dm-bg-surface: #313848;
            --dm-border:     rgba(255,255,255,0.13);
            --dm-border-strong: rgba(255,255,255,0.22);
            --dm-text:       #e4e8f0;
            --dm-text-muted: #8e97ad;
        }

        /* Topbar */
        [data-bs-theme="dark"] #page-topbar,
        [data-bs-theme="dark"] #page-topbar .navbar-header {
            background: var(--dm-bg-raised) !important;
            border-bottom: 3px solid var(--ccbrt-brand-500) !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.25) !important;
        }

        [data-bs-theme="dark"] #page-topbar .btn-topbar,
        [data-bs-theme="dark"] #page-topbar .topbar-user > .btn,
        [data-bs-theme="dark"] #page-topbar .vertical-menu-btn {
            color: var(--dm-text) !important;
        }

        [data-bs-theme="dark"] #page-topbar .btn-topbar:hover,
        [data-bs-theme="dark"] #page-topbar .topbar-user > .btn:hover,
        [data-bs-theme="dark"] #page-topbar .vertical-menu-btn:hover {
            background-color: rgba(148, 200, 61, 0.12) !important;
        }

        [data-bs-theme="dark"] .user-name-text { color: var(--dm-text) !important; }
        [data-bs-theme="dark"] .user-name-sub-text { color: var(--dm-text-muted) !important; }

        /* Topbar dropdowns */
        [data-bs-theme="dark"] #page-topbar .topbar-head-dropdown .dropdown-menu,
        [data-bs-theme="dark"] #page-topbar .topbar-user .dropdown-menu,
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: var(--dm-bg-raised) !important;
            border: 1px solid var(--dm-border-strong) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.35) !important;
        }

        [data-bs-theme="dark"] #page-topbar .topbar-head-dropdown .dropdown-item,
        [data-bs-theme="dark"] #page-topbar .topbar-user .dropdown-item,
        [data-bs-theme="dark"] #page-topbar .dropdown-head,
        [data-bs-theme="dark"] .dropdown-item,
        [data-bs-theme="dark"] .py-2.bg-white,
        [data-bs-theme="dark"] .p-2.border-top.bg-white,
        [data-bs-theme="dark"] .dropdown-head.rounded-top.bg-white,
        [data-bs-theme="dark"] .py-2.bg-white,
        [data-bs-theme="dark"] .p-2.border-top.bg-white {
            background-color: var(--dm-bg-raised) !important;
            color: var(--dm-text) !important;
        }

        [data-bs-theme="dark"] .dropdown-item:hover,
        [data-bs-theme="dark"] .dropdown-item:focus {
            background-color: rgba(148, 200, 61, 0.12) !important;
            color: #fff !important;
        }

        [data-bs-theme="dark"] .dropdown-divider {
            border-color: var(--dm-border) !important;
        }

        [data-bs-theme="dark"] .dropdown-head {
            background-color: var(--dm-bg-surface) !important;
        }

        [data-bs-theme="dark"] h6.mb-0, [data-bs-theme="dark"] h6.dropdown-header {
            color: var(--dm-text) !important;
        }

        /* Main content background */
        [data-bs-theme="dark"] body,
        [data-bs-theme="dark"] .main-content,
        [data-bs-theme="dark"] .page-content {
            background-color: var(--dm-bg-base) !important;
        }

        /* Cards */
        [data-bs-theme="dark"] .card {
            background-color: var(--dm-bg-raised) !important;
            border: 1px solid var(--dm-border) !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18) !important;
        }

        [data-bs-theme="dark"] .card-header {
            background-color: var(--dm-bg-surface) !important;
            border-bottom: 1px solid var(--dm-border) !important;
            color: var(--dm-text) !important;
        }

        [data-bs-theme="dark"] .card-footer {
            background-color: var(--dm-bg-surface) !important;
            border-top: 1px solid var(--dm-border) !important;
        }

        [data-bs-theme="dark"] .card-title { color: var(--dm-text) !important; }
        [data-bs-theme="dark"] .card-body  { color: var(--dm-text) !important; }

        /* Tables — stronger borders and readable header */
        [data-bs-theme="dark"] .table {
            color: var(--dm-text) !important;
            border-color: var(--dm-border-strong) !important;
        }

        [data-bs-theme="dark"] .table-light,
        [data-bs-theme="dark"] thead.table-light,
        [data-bs-theme="dark"] .table > thead {
            background-color: var(--dm-bg-surface) !important;
            color: var(--dm-text) !important;
            border-bottom: 2px solid var(--dm-border-strong) !important;
        }

        [data-bs-theme="dark"] .table th {
            background-color: var(--dm-bg-surface) !important;
            color: var(--dm-text) !important;
            border-color: var(--dm-border-strong) !important;
        }

        [data-bs-theme="dark"] .table td {
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }

        [data-bs-theme="dark"] .table-hover > tbody > tr:hover > * {
            background-color: rgba(148, 200, 61, 0.07) !important;
            color: var(--dm-text) !important;
        }

        [data-bs-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > * {
            background-color: rgba(255,255,255,0.03) !important;
        }

        /* Forms */
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text {
            background-color: var(--dm-bg-surface) !important;
            border-color: var(--dm-border-strong) !important;
            color: var(--dm-text) !important;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: var(--dm-bg-surface) !important;
            border-color: var(--ccbrt-brand-500) !important;
            color: var(--dm-text) !important;
            box-shadow: 0 0 0 0.2rem rgba(148,200,61,0.2) !important;
        }

        [data-bs-theme="dark"] .form-control::placeholder { color: var(--dm-text-muted) !important; }
        [data-bs-theme="dark"] .form-control:disabled,
        [data-bs-theme="dark"] .form-control[readonly] {
            background-color: rgba(255,255,255,0.05) !important;
            color: var(--dm-text-muted) !important;
        }

        [data-bs-theme="dark"] .form-label,
        [data-bs-theme="dark"] label { color: var(--dm-text-muted) !important; }

        /* Textarea readability */
        [data-bs-theme="dark"] textarea.form-control {
            background-color: var(--dm-bg-surface) !important;
            color: var(--dm-text) !important;
        }

        /* Breadcrumb & page title */
        [data-bs-theme="dark"] .page-title-box h4 { color: var(--dm-text) !important; }
        [data-bs-theme="dark"] .breadcrumb-item a { color: var(--ccbrt-brand-400) !important; }
        [data-bs-theme="dark"] .breadcrumb-item.active,
        [data-bs-theme="dark"] .breadcrumb-item + .breadcrumb-item::before {
            color: var(--dm-text-muted) !important;
        }

        /* Text utilities */
        [data-bs-theme="dark"] .text-muted { color: var(--dm-text-muted) !important; }
        [data-bs-theme="dark"] .text-dark   { color: var(--dm-text) !important; }
        [data-bs-theme="dark"] p, [data-bs-theme="dark"] span:not(.badge):not(.avatar-title) {
            color: inherit;
        }

        /* Badges — bg-light variant */
        [data-bs-theme="dark"] .badge.bg-light {
            background-color: var(--dm-bg-surface) !important;
            color: var(--dm-text) !important;
        }

        /* Alerts */
        [data-bs-theme="dark"] .alert {
            border-color: var(--dm-border-strong) !important;
        }

        /* Greeting card (dashboard welcome banner) */
        [data-bs-theme="dark"] .dm-welcome-card {
            background: var(--dm-bg-raised) !important;
            border: 1px solid var(--dm-border) !important;
            border-left: 4px solid var(--ccbrt-brand-500) !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.2) !important;
        }

        [data-bs-theme="dark"] .dm-welcome-card h4 { color: var(--dm-text) !important; }
        [data-bs-theme="dark"] .dm-welcome-card p   { color: var(--dm-text-muted) !important; }

        /* Modal */
        [data-bs-theme="dark"] .modal-content {
            background-color: var(--dm-bg-raised) !important;
            border: 1px solid var(--dm-border-strong) !important;
        }

        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: var(--dm-border) !important;
            background-color: var(--dm-bg-surface) !important;
        }

        [data-bs-theme="dark"] .modal-title { color: var(--dm-text) !important; }
        [data-bs-theme="dark"] .modal-body  { color: var(--dm-text) !important; }

        /* Footer */
        [data-bs-theme="dark"] .footer {
            background-color: var(--dm-bg-raised) !important;
            color: var(--dm-text-muted) !important;
            border-top: 1px solid var(--dm-border) !important;
        }

        /* Page title box */
        [data-bs-theme="dark"] .page-title-box { background-color: transparent !important; }

        /* Vertical overlay */
        [data-bs-theme="dark"] .vertical-overlay { background-color: rgba(0,0,0,0.55) !important; }

        /* Pagination */
        [data-bs-theme="dark"] .page-link {
            background-color: var(--dm-bg-raised) !important;
            border-color: var(--dm-border-strong) !important;
            color: var(--ccbrt-brand-400) !important;
        }

        [data-bs-theme="dark"] .page-item.disabled .page-link {
            background-color: var(--dm-bg-surface) !important;
            color: var(--dm-text-muted) !important;
        }

        /* List-group */
        [data-bs-theme="dark"] .list-group-item {
            background-color: var(--dm-bg-raised) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }

        /* Scrollbar (Simplebar) */
        [data-bs-theme="dark"] .simplebar-scrollbar::before {
            background: rgba(148,200,61,0.35) !important;
        }

        /* ── Medium-large screens: 1280px–1599px (standard external monitors,
           18"–24" 1080p, typical HP/Dell office screens on Ubuntu at 100% scale)
           Gentle proportional nudge — no font-size change on html to avoid
           triggering rem cascade on fixed-pixel Bootstrap components.
        ─────────────────────────────────────────────────────────────────────── */
        @media (min-width: 1280px) and (max-width: 1599px) {
            /* Keep sidebar at theme default width — override variable to match */
            :root { --tb-vertical-menu-width: 250px; }

            /* Topbar stays at standard 70px — no height change */
            #page-topbar .btn-topbar,
            #page-topbar .topbar-user > .btn,
            #page-topbar .vertical-menu-btn { font-size: 1rem; }

            /* Sidebar — keep default width, just tighten nav-link sizing */
            .navbar-nav .nav-link { font-size: 0.9rem !important; padding: 0.55rem 1rem !important; }
            .navbar-nav .menu-title span { font-size: 0.68rem !important; }

            /* Page content — standard padding, no extra top offset */
            .page-content { padding: calc(70px + 1.5rem) 1.5rem 60px 1.5rem; }

            /* Cards — subtle rounding, no size changes */
            .card-header { padding: 0.75rem 1rem; }
            .card-body { padding: 1rem; }

            /* Tables — keep default font-size, just clean up cell padding */
            .table th, .table td { padding: 0.65rem 0.75rem !important; }

            /* Constrain max content width so lines don't stretch too wide */
            .container-fluid { max-width: 1560px; }
        }

        /* ── Large screens: ≥ 1600px (27"+ monitors, 1440p, 4K at 150% scale)
           Moderate scale-up — raises base font-size slightly so rem-based
           components breathe on genuinely large viewports.
        ─────────────────────────────────────────────────────────────────────── */
        @media (min-width: 1600px) {
            /* Sync CSS variable so footer + topbar left tracks sidebar width */
            :root { --tb-vertical-menu-width: 270px; }

            /* Modest root scale-up — less aggressive than before */
            html { font-size: 16.5px; }

            body { font-size: 1rem; }

            /* Topbar */
            #page-topbar { min-height: 70px; }
            #page-topbar .navbar-header { height: 70px; }
            #page-topbar .btn-topbar,
            #page-topbar .topbar-user > .btn,
            #page-topbar .vertical-menu-btn { font-size: 1.05rem; }

            .navbar-brand-box { height: 70px; }

            /* Sidebar */
            .app-menu.navbar-menu { width: 270px !important; }
            .navbar-nav .nav-link { font-size: 0.92rem !important; padding: 0.6rem 1.05rem !important; }
            .navbar-nav .menu-title span { font-size: 0.7rem !important; }
            .navbar-nav .nav-link i { font-size: 1.05rem !important; }
            .main-content { margin-left: 270px !important; }

            /* Admin brand */
            .admin-brand-title { font-size: 1.1rem; }
            .admin-brand-icon-circle { width: 50px; height: 50px; }
            .admin-brand-logo-lg { width: 40px; height: 40px; }

            /* Page content */
            .page-content { padding: calc(70px + 1.75rem) 1.75rem 80px 1.75rem; }

            /* Page title */
            .page-title-box h4 { font-size: 1.2rem; }
            .breadcrumb-item, .breadcrumb-item a { font-size: 0.83rem; }

            /* Cards */
            .card { border-radius: 0.5rem; }
            .card-header { padding: 0.85rem 1.2rem; font-size: 0.97rem; }
            .card-body { padding: 1.2rem; }
            .card-title { font-size: 1rem; }

            /* Tables */
            .table { font-size: 0.92rem !important; }
            .table th, .table td { padding: 0.7rem 0.8rem !important; }
            .table thead th { font-size: 0.78rem !important; }

            /* Badges */
            .badge { font-size: 0.76rem !important; padding: 0.35em 0.65em !important; }

            /* Buttons */
            .btn-sm { font-size: 0.8rem !important; padding: 0.35rem 0.75rem !important; }
            .btn { font-size: 0.9rem; }

            /* Forms */
            .form-control, .form-select { font-size: 0.92rem; padding: 0.5rem 0.8rem; }
            .form-control-sm, .form-select-sm { font-size: 0.83rem !important; }
            .form-label { font-size: 0.86rem; }
            .input-group-text { font-size: 0.88rem; }

            /* Pagination */
            .page-link { font-size: 0.88rem; padding: 0.45rem 0.8rem; }

            /* Alert */
            .alert { font-size: 0.9rem; }

            /* Containers */
            .container-fluid { max-width: 1760px; }

            /* Modals */
            .modal-dialog { font-size: 0.92rem; }
            .modal-header .modal-title { font-size: 1.05rem; }
        }

        /* ── TV / Very large screens: ≥ 1920px (4K, 32"+, actual TV displays)
           Stronger scale-up for genuine large-format viewing.
        ─────────────────────────────────────────────────────────────────────── */
        @media (min-width: 1920px) {
            /* Sync CSS variable so footer + topbar left tracks sidebar width */
            :root { --tb-vertical-menu-width: 290px; }

            html { font-size: 18px; }
            .app-menu.navbar-menu { width: 290px !important; }
            .main-content { margin-left: 290px !important; }
            .container-fluid { max-width: 2000px; }
            #page-topbar { min-height: 72px; }
            #page-topbar .navbar-header { height: 72px; }
            .navbar-brand-box { height: 72px; }
            .page-content { padding: calc(72px + 1.75rem) 1.75rem 80px 1.75rem; }
            .admin-brand-title { font-size: 1.15rem; }
            .admin-brand-icon-circle { width: 56px; height: 56px; }
            .admin-brand-logo-lg { width: 46px; height: 46px; }
        }

        /* ── Responsive overrides ── */

        /* Topbar: prevent notification dropdown from overflowing viewport on mobile */
        @media (max-width: 575.98px) {
            .dropdown-menu-lg {
                width: calc(100vw - 1.5rem) !important;
                max-width: 340px;
            }

            /* Page title bar: stack heading and right-side controls vertically */
            .page-title-box {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }

            /* Notification dropdown stays within screen */
            #notificationDropdown .dropdown-menu {
                right: -4rem;
                left: auto;
            }

            /* Card body padding reduction on mobile */
            .card-body {
                padding: 1rem;
            }

            /* Sidebar status pipeline links: ensure text wraps */
            .hover-bg-light {
                flex-wrap: wrap;
                gap: 0.25rem;
            }
        }

        @media (max-width: 767.98px) {
            /* Table cells: reduce horizontal padding so tables breathe */
            .table th,
            .table td {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            /* Dashboard stat cards: reduce icon margin */
            .avatar-sm {
                width: 2.5rem;
                height: 2.5rem;
            }

            /* Alert actions: give the button full width on mobile when alert wraps */
            .alert .btn.flex-shrink-0 {
                align-self: flex-start;
            }
        }

        @media (max-width: 991.98px) {
            /* Actions panel in feedback detail: ensure it doesn't overflow */
            .feedback-detail-sticky-card {
                position: static !important;
            }
        }
    </style>

    <!-- jsvectormap css -->
    <link href="{{ asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">

    <!--Swiper slider css-->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    
</head>