@extends('layouts.app')
@section('title', 'User: ' . $user->getFullName())

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="mb-0">User profile</h4>
                <p class="text-muted small mb-0 mt-1">Review identity details, role access, and account status.</p>
            </div>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">{{ $user->getFullName() }}</li>
            </ol>
        </div>
    </div>
</div>

@if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="user-profile-grid">
    <x-admin.user-identity :user="$user" :department-name="$user->department?->name">
        <dl class="user-profile-detail-grid mb-0">
            <div class="user-profile-detail">
                <dt>First name</dt>
                <dd>{{ $user->fname ?: 'None' }}</dd>
            </div>
            <div class="user-profile-detail">
                <dt>Middle name</dt>
                <dd>{{ $user->mname ?: 'None' }}</dd>
            </div>
            <div class="user-profile-detail">
                <dt>Last name</dt>
                <dd>{{ $user->lname ?: 'None' }}</dd>
            </div>
            <div class="user-profile-detail">
                <dt>Date of birth</dt>
                <dd>{{ $user->dob?->format('d M Y') ?? 'None' }}</dd>
            </div>
            <div class="user-profile-detail">
                <dt>Approved at</dt>
                <dd>{{ $user->approved_at?->format('d M Y') ?? 'Not approved' }}</dd>
            </div>
            <div class="user-profile-detail">
                <dt>Approved by</dt>
                <dd>{{ $user->approvedBy?->getFullName() ?? ($user->is_first_user ? 'System' : 'None') }}</dd>
            </div>
        </dl>
    </x-admin.user-identity>

    <section class="card user-settings-card h-100">
        <header class="user-settings-card__header">
            <div>
                <h2 class="user-settings-card__title"><i class="bi bi-shield-check me-2" aria-hidden="true"></i>Account administration</h2>
                <p class="user-settings-card__description">Manage this user’s access level and availability.</p>
            </div>
            @if(!$user->is_first_user && auth()->user()->canManageUsers())
                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil me-1" aria-hidden="true"></i>Edit profile
                </a>
            @endif
        </header>

        @if(!$user->is_first_user && auth()->user()->canManageUsers())
            @if(!$user->is_active)
                <div class="user-action-section">
                    <h3 class="user-action-section__heading">Approve and assign a role</h3>
                    <p class="user-action-section__copy">Activate the account and choose the permissions this user should receive.</p>
                    <form method="POST" action="{{ route('users.approve', $user) }}" class="user-action-section__control d-flex flex-wrap gap-2">
                        @csrf
                        <select name="role" class="form-select form-select-sm" style="max-width:320px;" required aria-label="Role to assign">
                            <option value="">Select a role</option>
                            @foreach(\App\Models\User::APPROVABLE_ROLES as $role)
                                <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                    {{ \App\Models\User::getRoleLabels()[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success btn-sm px-3">
                            <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Approve user
                        </button>
                    </form>
                </div>
            @elseif(auth()->user()->isAdmin())
                <div class="user-action-section">
                    <h3 class="user-action-section__heading">Role and permissions</h3>
                    <p class="user-action-section__copy">Changing the role updates the areas and actions available to this user.</p>
                    <form method="POST" action="{{ route('users.role', $user) }}" class="user-action-section__control d-flex flex-wrap gap-2">
                        @csrf
                        <select name="role" class="form-select form-select-sm" style="max-width:320px;" required aria-label="User role">
                            @foreach(\App\Models\User::APPROVABLE_ROLES as $role)
                                <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                    {{ \App\Models\User::getRoleLabels()[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Update role
                        </button>
                    </form>
                </div>
            @endif

            <div class="user-action-section">
                <h3 class="user-action-section__heading">Account status</h3>
                <p class="user-action-section__copy">
                    {{ $user->is_active ? 'Deactivation immediately prevents this user from signing in.' : 'Activate this account to restore access to the system.' }}
                </p>
                <div class="user-action-section__control">
                    @if($user->is_active)
                        <form method="POST" action="{{ route('users.deactivate', $user) }}" class="d-inline">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Deactivate {{ addslashes($user->getFullName()) }}? They will no longer be able to login.')"
                                    class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-person-x me-1" aria-hidden="true"></i>Deactivate user
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('users.activate', $user) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-person-check me-1" aria-hidden="true"></i>Activate user
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <div class="user-action-section">
                <h3 class="user-action-section__heading">Protected account</h3>
                <p class="user-action-section__copy">
                    {{ $user->is_first_user ? 'This is the primary system administrator account and its access settings are protected.' : 'You can review this account, but you do not have permission to change its access settings.' }}
                </p>
            </div>
        @endif

        <footer class="user-settings-card__footer mt-auto">
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Back to users
            </a>
        </footer>
    </section>
</div>
@endsection
