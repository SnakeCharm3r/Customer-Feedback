<head>

    <meta charset="utf-8">
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
        }

        .admin-brand-link {
            text-decoration: none;
        }

        .admin-brand-shell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .admin-brand-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .admin-brand-logo-lg {
            width: 52px;
            height: 52px;
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