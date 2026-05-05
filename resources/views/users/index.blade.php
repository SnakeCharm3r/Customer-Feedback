@extends('layouts.app')
@section('title', 'User Management')

@section('content')

<style>
    .users-toolbar {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }

    .users-summary-card {
        border: 1px solid rgba(11, 107, 44, 0.08);
        border-radius: 1.1rem;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    }

    .users-summary-card__icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .users-pending-card {
        border: 1px solid rgba(245, 158, 11, 0.24);
        border-left: 4px solid #f59e0b;
        border-radius: 1.1rem;
        box-shadow: 0 12px 28px rgba(245, 158, 11, 0.08);
    }

    .users-filter-card,
    .users-table-card {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1.1rem;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.04);
    }

    .users-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        background: rgba(11, 107, 44, 0.08);
        color: #0b6b2c;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .users-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
    }

    .users-table tbody tr {
        transition: background-color 0.18s ease, transform 0.18s ease;
    }

    .users-table tbody tr:hover {
        background: rgba(11, 107, 44, 0.03);
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #065321, #0b6b2c);
        box-shadow: 0 8px 18px rgba(6, 83, 33, 0.18);
        flex-shrink: 0;
    }

    .users-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        justify-content: flex-end;
    }

    @media (max-width: 575.98px) {
        .users-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>

@php
    $authUser   = auth()->user();
    $roleColors = ['admin'=>'primary','qa_hod'=>'info','qa_officer'=>'success','call_center'=>'warning','coo'=>'danger','line_manager'=>'dark'];
    $hasFilters = request()->hasAny(['search','role','status']);
    $activeFilters = array_filter([
        request('search') ? 'Search: ' . request('search') : null,
        request('role') ? 'Role: ' . (
            \App\Models\User::getRoleLabels()[request('role')] ?? request('role')
        ) : null,
        request('status') ? 'Status: ' . ucfirst(request('status')) : null,
    ]);
@endphp

<div class="row mb-3">
    <div class="col-12">
        <div class="users-toolbar p-3 p-lg-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="text-uppercase fw-semibold text-muted mb-1" style="font-size:11px;letter-spacing:.08em;">Administration</div>
                    <h4 class="mb-1">User Management</h4>
                    <p class="text-muted mb-0 small">Manage system staff accounts, approvals, roles, and access in one place.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @if($pendingCount > 0)
                    <a href="{{ route('users.pending') }}" class="btn btn-warning btn-sm fw-semibold">
                        <i class="bi bi-person-check me-1"></i>Pending Approvals
                        <span class="badge bg-dark ms-1">{{ $pendingCount }}</span>
                    </a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i>Dashboard
                    </a>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
                <ol class="breadcrumb m-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
                <span class="text-muted">•</span>
                <span class="small text-muted">{{ $stats['total'] }} total users</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Flash ───────────────────────────────────────────────────────────── --}}
@foreach(['status'=>'success','error'=>'danger'] as $key => $type)
@if(session($key))
<div class="alert alert-{{ $type }} alert-dismissible fade show d-flex align-items-center gap-2">
    <i class="bi bi-{{ $type === 'success' ? 'check-circle' : 'exclamation-circle' }}-fill"></i>
    <span>{{ session($key) }}</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@endforeach

{{-- ── Stats Cards ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-3">
    @php
        $statCards = [
            ['label'=>'Total Users',   'val'=>$stats['total'],   'icon'=>'bi-people',       'color'=>'primary'],
            ['label'=>'Active',        'val'=>$stats['active'],  'icon'=>'bi-person-check',  'color'=>'success'],
            ['label'=>'Pending',       'val'=>$stats['pending'], 'icon'=>'bi-hourglass-split','color'=>'warning'],
            ['label'=>'Admins / HODs', 'val'=>$stats['admins'],  'icon'=>'bi-shield-check',  'color'=>'info'],
        ];
    @endphp
    @foreach($statCards as $card)
    <div class="col-6 col-md-3">
        <div class="card users-summary-card h-100" style="border-left:3px solid var(--bs-{{ $card['color'] }}) !important;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="users-summary-card__icon bg-{{ $card['color'] }}-subtle text-{{ $card['color'] }}">
                        <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1 text-dark">{{ $card['val'] }}</div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                        <div class="text-muted" style="font-size:11px;">
                            @if($card['label'] === 'Pending')
                                Needs review before access
                            @elseif($card['label'] === 'Active')
                                Currently allowed to sign in
                            @elseif($card['label'] === 'Admins / HODs')
                                Elevated access roles
                            @else
                                Registered system accounts
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Pending Banner ──────────────────────────────────────────────────── --}}
@if($pendingCount > 0 && $authUser->canManageUsers())
<div class="card users-pending-card mb-3">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-person-clock fs-4 text-warning"></i>
                <div>
                    <div class="fw-semibold">{{ $pendingCount }} user{{ $pendingCount > 1 ? 's' : '' }} awaiting approval</div>
                    <div class="text-muted small">These accounts are registered but not yet granted access.</div>
                </div>
            </div>
            <a href="{{ route('users.pending') }}" class="btn btn-warning btn-sm flex-shrink-0">
                <i class="bi bi-person-check me-1"></i>Review Now
            </a>
        </div>

        @if($pendingUsers->count())
        <div class="row g-2 mt-2">
            @foreach($pendingUsers as $pu)
            <div class="col-md-4 col-lg-3">
                <div class="d-flex align-items-center gap-2 rounded px-3 py-2 bg-warning bg-opacity-10">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                         style="width:32px;height:32px;font-size:12px;background:#b45309;">
                        {{ strtoupper(substr($pu->fname ?? $pu->name, 0,1)) }}{{ strtoupper(substr($pu->lname ?? '',0,1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold small text-truncate">{{ $pu->getFullName() }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $pu->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif

{{-- ── Filters ─────────────────────────────────────────────────────────── --}}
<div class="card users-filter-card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-5 col-lg-4">
                    <label class="form-label small fw-semibold mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               value="{{ request('search') }}" placeholder="Name or email…">
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        @foreach(\App\Models\User::getRoleLabels() as $val => $lbl)
                        <option value="{{ $val }}" {{ request('role')===$val ? 'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="active"  {{ request('status')==='active'  ? 'selected':'' }}>Active</option>
                        <option value="pending" {{ request('status')==='pending' ? 'selected':'' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        @if($hasFilters)
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            @if($hasFilters)
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
                <span class="small fw-semibold text-muted">Active filters:</span>
                @foreach($activeFilters as $filter)
                <span class="users-filter-chip">{{ $filter }}</span>
                @endforeach
            </div>
            @endif
        </form>
    </div>
</div>

{{-- ── Users Table ─────────────────────────────────────────────────────── --}}
<div class="card users-table-card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-2">
        <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0 small fw-bold text-uppercase" style="letter-spacing:.05em;">
                {{ $hasFilters ? 'Filtered Results' : 'All System Users' }}
            </h5>
            <span class="badge bg-secondary rounded-pill">{{ $users->total() }}</span>
            @if($hasFilters)
            <span class="badge bg-primary-subtle text-primary" style="font-size:10px;">
                <i class="bi bi-funnel me-1"></i>Filters active
            </span>
            @endif
        </div>
        <span class="text-muted small">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table users-table table-hover align-middle mb-0" style="font-size:13px;">
                <thead>
                    <tr>
                        <th class="ps-3 py-3">User</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Approved By</th>
                        <th class="py-3">Joined</th>
                        <th class="py-3 text-end pe-3" style="width:130px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php $rc = $roleColors[$user->role] ?? 'secondary'; @endphp
                    <tr>
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->fname ?? $user->name,0,1)) }}{{ strtoupper(substr($user->lname ?? '',0,1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">
                                        {{ $user->getFullName() }}
                                        @if($user->is_first_user)
                                        <span class="badge ms-1" style="background:#ede9fe;color:#6d28d9;font-size:10px;">System Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-muted" style="font-size:11px;">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-{{ $rc }}-subtle text-{{ $rc }}" style="font-size:11px;">
                                {{ $user->getRoleLabel() }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($user->is_active)
                            <span class="d-inline-flex align-items-center gap-1 badge bg-success-subtle text-success">
                                <i class="bi bi-circle-fill" style="font-size:7px;"></i>Active
                            </span>
                            @else
                            <span class="d-inline-flex align-items-center gap-1 badge bg-warning-subtle text-warning">
                                <i class="bi bi-circle-fill" style="font-size:7px;"></i>Pending
                            </span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($user->approvedBy)
                            <div class="small text-dark">{{ $user->approvedBy->getFullName() }}</div>
                            @if($user->approved_at)
                            <div class="text-muted" style="font-size:11px;">{{ $user->approved_at->format('d M Y') }}</div>
                            @endif
                            @elseif($user->is_first_user)
                            <span class="text-muted small fst-italic">System</span>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            <div class="small text-dark">{{ $user->created_at->format('d M Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-end pe-3 py-3">
                            <div class="users-actions">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                                @if(!$user->is_first_user && $user->id !== $authUser->id && $authUser->canManageUsers())
                                    @if($user->is_active)
                                    <form method="POST" action="{{ route('users.deactivate', $user) }}" class="d-inline">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Deactivate {{ addslashes($user->getFullName()) }}?')"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    </form>
                                    @else
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-success" title="Review pending account">
                                        <i class="bi bi-person-check"></i>
                                    </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people d-block fs-1 mb-3 opacity-25"></i>
                            @if($hasFilters)
                            <p class="fw-semibold mb-1">No users match your filters</p>
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                                <i class="bi bi-x-lg me-1"></i>Clear Filters
                            </a>
                            @else
                            <p class="fw-semibold mb-1">No users found</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
        <span class="text-muted small">
            Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
            &nbsp;·&nbsp; {{ $users->total() }} total users
        </span>
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
