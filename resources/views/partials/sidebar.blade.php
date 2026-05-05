@php $sidebarUser = auth()->user(); @endphp
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('dashboard') }}" class="logo logo-dark admin-brand-link">
            <span class="admin-brand-shell">
                <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}" alt="{{ $systemSettings?->organization_name ?? 'CCBRT' }} Logo" class="admin-brand-logo admin-brand-logo-lg">
                <span class="admin-brand-text sidebar-brand-copy" style="color:#065321;">
                    <span class="admin-brand-title">{{ $systemSettings?->organization_name ?? 'CCBRT' }}</span>
                    <span class="admin-brand-subtitle">{{ $systemSettings?->portal_name ?? 'Feedback System' }}</span>
                </span>
            </span>
         </a>
        <a href="{{ route('dashboard') }}" class="logo logo-light admin-brand-link">
            <span class="admin-brand-shell">
                <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}" alt="{{ $systemSettings?->organization_name ?? 'CCBRT' }} Logo" class="admin-brand-logo admin-brand-logo-lg">
                <span class="admin-brand-text text-white sidebar-brand-copy">
                    <span class="admin-brand-title">{{ $systemSettings?->organization_name ?? 'CCBRT' }}</span>
                    <span class="admin-brand-subtitle">{{ $systemSettings?->portal_name ?? 'Feedback System' }}</span>
                </span>
            </span>
         </a>
        <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">

                <li class="menu-title"><span>Main</span></li>

                <!-- Dashboard — visible to all -->
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if($sidebarUser)

                {{-- FEEDBACK MANAGEMENT — Quality Assurance Officer, Call Center, Quality Assurance HOD, Admin, COO --}}
                @if($sidebarUser->canManageComplaints())
                <li class="menu-title"><span>Feedback</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('feedback.admin.*') ? 'active' : '' }}"
                        href="{{ route('feedback.admin.index') }}">
                        <i class="bi bi-chat-left-text"></i>
                        <span>All Submissions</span>
                        @php $newCount = \App\Models\Feedback::where('status','new')->count(); @endphp
                        @if($newCount > 0)
                            <span class="badge bg-danger ms-auto">{{ $newCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('feedback.admin.index') && request('status') == 'under_review' ? 'active' : '' }}"
                        href="{{ route('feedback.admin.index') }}?status=under_review">
                        <i class="bi bi-hourglass-split"></i>
                        <span>Under Review</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('feedback.admin.index') && request('status') == 'responded' ? 'active' : '' }}"
                        href="{{ route('feedback.admin.index') }}?status=responded">
                        <i class="bi bi-check2-circle"></i>
                        <span>Responded</span>
                    </a>
                </li>
                @endif

                {{-- REPORTS — all feedback management roles see Weekly; Admin/COO/Line Manager also see Feedback Report --}}
                @if($sidebarUser->canViewWeeklyReport())
                <li class="menu-title"><span>Reports</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                        href="{{ route('reports.feedback.index') }}">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Reports</span>
                    </a>
                </li>
                @endif

                {{-- USER MANAGEMENT — Admin, Quality Assurance HOD --}}
                @if($sidebarUser->canManageUsers())
                <li class="menu-title"><span>Administration</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <i class="bi bi-people"></i>
                        <span>Manage Users</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('users.pending') ? 'active' : '' }}"
                        href="{{ route('users.pending') }}">
                        <i class="bi bi-person-check"></i>
                        <span>Pending Approvals</span>
                        @php $pending = \App\Models\User::where('is_active', false)->where('is_first_user', false)->count(); @endphp
                        @if($pending > 0)
                            <span class="badge bg-warning text-dark ms-auto">{{ $pending }}</span>
                        @endif
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('hods.*') ? 'active' : '' }}"
                        href="{{ route('hods.index') }}">
                        <i class="bi bi-diagram-3"></i>
                        <span>HOD Officers</span>
                    </a>
                </li>

                @if($sidebarUser->isAdmin())
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('departments.*') ? 'active' : '' }}"
                        href="{{ route('departments.index') }}">
                        <i class="bi bi-buildings"></i>
                        <span>Departments</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                        href="{{ route('settings.edit') }}">
                        <i class="bi bi-gear"></i>
                        <span>System Settings</span>
                    </a>
                </li>
                @endif
                @endif

                {{-- ESCALATION MATRIX — all feedback managers --}}
                @if($sidebarUser->canManageComplaints())
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('escalations.index') ? 'active' : '' }}"
                        href="{{ route('escalations.index') }}">
                        <i class="bi bi-arrow-up-right-circle"></i>
                        <span>Escalation Matrix</span>
                        @php $pendingEsc = \App\Models\Escalation::where('status','pending')->count(); @endphp
                        @if($pendingEsc > 0)
                            <span class="badge ms-auto" style="background:#f59e0b; color:#fff;">{{ $pendingEsc }}</span>
                        @endif
                    </a>
                </li>
                @endif

                @endif

                <!-- Divider -->
                <li class="menu-title mt-2"><span>Account</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
                        href="{{ route('profile.edit') }}">
                        <i class="bi bi-person-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link menu-link w-100 text-start border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right text-danger"></i>
                            <span class="text-danger">Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
