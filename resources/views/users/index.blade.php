@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">User Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">

        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(auth()->user()->canManageUsers() && $pendingCount > 0)
            <div class="card border-warning-subtle shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-warning text-dark">{{ $pendingCount }}</span>
                                <h5 class="mb-0">Pending user registrations</h5>
                            </div>
                            <p class="text-muted small mb-0">New staff accounts are waiting for review and approval.</p>
                        </div>
                        <a href="{{ route('users.pending') }}" class="btn btn-warning btn-sm flex-shrink-0">
                            <i class="bi bi-person-check me-1"></i>Review pending users
                        </a>
                    </div>
                    <div class="row g-3 mt-1">
                        @foreach($pendingUsers as $pendingUser)
                            <div class="col-md-6 col-xl-4">
                                <div class="border rounded-3 p-3 h-100 bg-light-subtle">
                                    <div class="fw-semibold text-dark">{{ $pendingUser->getFullName() }}</div>
                                    <div class="text-muted small">{{ $pendingUser->email }}</div>
                                    <div class="text-muted small mt-2">
                                        <span class="badge bg-secondary">{{ $pendingUser->getRoleLabel() }}</span>
                                        <span class="ms-2">{{ $pendingUser->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>All System Users
                    <span class="badge bg-secondary ms-2">{{ $users->total() }}</span>
                </h5>
                @if(auth()->user()->canManageUsers())
                <a href="{{ route('users.pending') }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-person-check me-1"></i> Pending Approvals
                    @if($pendingCount > 0)
                        <span class="badge bg-dark ms-1">{{ $pendingCount }}</span>
                    @endif
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-nowrap align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th>Joined</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-xs rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                             style="width:36px;height:36px;font-size:13px;">
                                            {{ strtoupper(substr($user->fname ?? $user->name, 0, 1)) }}{{ strtoupper(substr($user->lname ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">
                                                {{ $user->getFullName() }}
                                                @if($user->is_first_user)
                                                    <span class="badge bg-purple-subtle text-purple ms-1" style="background:#ede9fe;color:#6d28d9;">System Admin</span>
                                                @endif
                                            </div>
                                            @if($user->dob)
                                                <small class="text-muted">DOB: {{ $user->dob->format('d M Y') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $user->email }}</td>
                                <td>
                                     @php
                                         $roleClass = [
                                             'admin'       => 'bg-primary',
                                             'qa_hod'      => 'bg-info',
                                             'qa_officer'  => 'bg-success',
                                             'call_center' => 'bg-warning text-dark',
                                             'coo'         => 'bg-danger',
                                            'line_manager'=> 'bg-dark',
                                         ][$user->role] ?? 'bg-secondary';
                                     @endphp
                                     <span class="badge {{ $roleClass }}">{{ $user->getRoleLabel() }}</span>
                                 </td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                        <i class="bi bi-circle-fill me-1" style="font-size:7px;"></i>
                                        {{ $user->is_active ? 'Active' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $user->approvedBy?->getFullName() ?? ($user->is_first_user ? 'System' : '—') }}
                                </td>
                                <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('users.show', $user) }}"
                                       class="btn btn-sm btn-outline-primary me-1">View</a>
                                    @if(!$user->is_first_user && $user->id !== auth()->id() && auth()->user()->canManageUsers())
                                        @if($user->is_active)
                                            <form method="POST" action="{{ route('users.deactivate', $user) }}" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Deactivate {{ addslashes($user->getFullName()) }}?')"
                                                    class="btn btn-sm btn-outline-danger">Deactivate</button>
                                            </form>
                                        @else
                                            <a href="{{ route('users.show', $user) }}"
                                               class="btn btn-sm btn-outline-success">Approve</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                                    No users found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
