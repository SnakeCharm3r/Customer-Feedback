@php
    $sidebarUser = auth()->user();
    $sidebarNewCount = $sidebarUser?->canManageComplaints()
        ? \App\Models\Feedback::where('status', 'new')->count()
        : 0;
    $sidebarPendingUsers = $sidebarUser?->canManageUsers()
        ? \App\Models\User::where('is_active', false)->where('is_first_user', false)->count()
        : 0;
    $sidebarPendingEscalations = $sidebarUser?->canManageComplaints()
        ? \App\Models\Escalation::where('status', 'pending')->count()
        : 0;
    $sidebarName = $sidebarUser?->getFullName() ?: $sidebarUser?->name ?: 'Account';
    $sidebarInitials = collect(preg_split('/\s+/', trim($sidebarName)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<div class="app-menu navbar-menu ccbrt-sidebar">
    <div class="navbar-brand-box ccbrt-sidebar-brand">
        <a href="{{ route('dashboard') }}" class="logo logo-dark admin-brand-link" aria-label="Open dashboard">
            <span class="admin-brand-shell">
                <span class="admin-brand-icon-circle">
                    <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}"
                        alt="{{ $systemSettings?->organization_name ?? 'CCBRT' }} Logo"
                        class="admin-brand-logo admin-brand-logo-lg">
                </span>
                <span class="admin-brand-text sidebar-brand-copy" style="color:#065321;">
                    <span class="admin-brand-title">{{ $systemSettings?->organization_name ?? 'CCBRT' }}</span>
                    <span class="admin-brand-subtitle">{{ $systemSettings?->portal_name ?? 'Feedback System' }}</span>
                </span>
            </span>
        </a>
        <a href="{{ route('dashboard') }}" class="logo logo-light admin-brand-link" aria-label="Open dashboard">
            <span class="admin-brand-shell">
                <span class="admin-brand-icon-circle">
                    <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}"
                        alt="{{ $systemSettings?->organization_name ?? 'CCBRT' }} Logo"
                        class="admin-brand-logo admin-brand-logo-lg">
                </span>
                <span class="admin-brand-text text-white sidebar-brand-copy">
                    <span class="admin-brand-title">{{ $systemSettings?->organization_name ?? 'CCBRT' }}</span>
                    <span class="admin-brand-subtitle">{{ $systemSettings?->portal_name ?? 'Feedback System' }}</span>
                </span>
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 header-item btn-vertical-sm-hover ccbrt-sidebar-collapse"
            id="vertical-hover" aria-label="Toggle compact sidebar" title="Toggle compact sidebar">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid ccbrt-sidebar-content">
            <div class="ccbrt-workspace-card sidebar-expand-copy" aria-label="Current workspace">
                <span class="ccbrt-workspace-icon"><i class="bi bi-shield-check"></i></span>
                <span class="ccbrt-workspace-copy">
                    <strong>Quality Workspace</strong>
                    <small>Feedback operations</small>
                </span>
                <span class="ccbrt-workspace-status" title="System available"></span>
            </div>

            <div id="two-column-menu"></div>
            <ul class="navbar-nav ccbrt-sidebar-nav" id="navbar-nav">
                <li class="menu-title"><span>Overview</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                        <span class="sidebar-nav-icon"><i class="bi bi-grid-1x2"></i></span>
                        <span class="sidebar-nav-label">Dashboard</span>
                    </a>
                </li>

                @if($sidebarUser)
                    @if($sidebarUser->canManageComplaints())
                        <li class="menu-title"><span>Feedback</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('feedback.admin.*') && !request('status') ? 'active' : '' }}"
                                href="{{ route('feedback.admin.index') }}">
                                <span class="sidebar-nav-icon"><i class="bi bi-inbox"></i></span>
                                <span class="sidebar-nav-label">All Submissions</span>
                                @if($sidebarNewCount > 0)
                                    <span class="sidebar-nav-count sidebar-nav-count-danger">{{ $sidebarNewCount }}</span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('feedback.admin.index') && request('status') === 'under_review' ? 'active' : '' }}"
                                href="{{ route('feedback.admin.index', ['status' => 'under_review']) }}">
                                <span class="sidebar-nav-icon"><i class="bi bi-hourglass-split"></i></span>
                                <span class="sidebar-nav-label">Under Review</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('feedback.admin.index') && request('status') === 'responded' ? 'active' : '' }}"
                                href="{{ route('feedback.admin.index', ['status' => 'responded']) }}">
                                <span class="sidebar-nav-icon"><i class="bi bi-check2-circle"></i></span>
                                <span class="sidebar-nav-label">Responded</span>
                            </a>
                        </li>
                    @endif

                    @if($sidebarUser->canViewWeeklyReport())
                        <li class="menu-title"><span>Insights</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('reports.analytics') ? 'active' : '' }}"
                                href="{{ route('reports.analytics') }}" @if(request()->routeIs('reports.analytics')) aria-current="page" @endif>
                                <span class="sidebar-nav-icon"><i class="bi bi-graph-up-arrow"></i></span>
                                <span class="sidebar-nav-label">Analytics</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('reports.feedback.index') ? 'active' : '' }}"
                                href="{{ route('reports.feedback.index') }}" @if(request()->routeIs('reports.feedback.index')) aria-current="page" @endif>
                                <span class="sidebar-nav-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
                                <span class="sidebar-nav-label">Feedback Report</span>
                            </a>
                        </li>
                    @endif

                    @if($sidebarUser->canManageUsers())
                        <li class="menu-title"><span>Administration</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                                href="{{ route('users.index') }}">
                                <span class="sidebar-nav-icon"><i class="bi bi-people"></i></span>
                                <span class="sidebar-nav-label">Manage Users</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('users.pending') ? 'active' : '' }}"
                                href="{{ route('users.pending') }}">
                                <span class="sidebar-nav-icon"><i class="bi bi-person-check"></i></span>
                                <span class="sidebar-nav-label">Pending Approvals</span>
                                @if($sidebarPendingUsers > 0)
                                    <span class="sidebar-nav-count sidebar-nav-count-warning">{{ $sidebarPendingUsers }}</span>
                                @endif
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('hods.*') ? 'active' : '' }}"
                                href="{{ route('hods.index') }}">
                                <span class="sidebar-nav-icon"><i class="bi bi-diagram-3"></i></span>
                                <span class="sidebar-nav-label">HOD Officers</span>
                            </a>
                        </li>

                        @if($sidebarUser->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}"
                                    href="{{ route('departments.index') }}">
                                    <span class="sidebar-nav-icon"><i class="bi bi-buildings"></i></span>
                                    <span class="sidebar-nav-label">Departments</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                                    href="{{ route('settings.edit') }}">
                                    <span class="sidebar-nav-icon"><i class="bi bi-sliders"></i></span>
                                    <span class="sidebar-nav-label">System Settings</span>
                                </a>
                            </li>
                        @endif
                    @endif

                    @if($sidebarUser->canManageComplaints())
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('escalations.index') ? 'active' : '' }}"
                                href="{{ route('escalations.index') }}">
                                <span class="sidebar-nav-icon"><i class="bi bi-arrow-up-right-circle"></i></span>
                                <span class="sidebar-nav-label">Escalation Matrix</span>
                                @if($sidebarPendingEscalations > 0)
                                    <span class="sidebar-nav-count sidebar-nav-count-warning">{{ $sidebarPendingEscalations }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endif

            </ul>

            @if($sidebarUser)
                <div class="ccbrt-sidebar-account">
                    <a href="{{ route('profile.edit') }}" class="ccbrt-account-summary" title="Open profile">
                        <span class="ccbrt-account-avatar">{{ $sidebarInitials ?: 'U' }}</span>
                        <span class="ccbrt-account-copy sidebar-expand-copy">
                            <strong>{{ $sidebarName }}</strong>
                            <small>{{ $sidebarUser->getRoleLabel() }}</small>
                        </span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="ccbrt-logout-form">
                        @csrf
                        <button type="submit" class="ccbrt-logout-button" title="Sign out">
                            <i class="bi bi-box-arrow-right"></i>
                            <span class="sidebar-expand-copy">Sign out</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
