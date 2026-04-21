@extends('layouts.app')
@section('title', 'User: ' . $user->getFullName())

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">User Profile</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">{{ $user->getFullName() }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-body text-center p-4">
                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center text-white fw-bold mb-3"
                     style="width:72px;height:72px;font-size:24px;">
                    {{ strtoupper(substr($user->fname ?? $user->name, 0, 1)) }}{{ strtoupper(substr($user->lname ?? '', 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $user->getFullName() }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                    @php
                        $rc = ['admin'=>'bg-primary','qa_hod'=>'bg-info','qa_officer'=>'bg-success','call_center'=>'bg-warning text-dark','coo'=>'bg-danger','line_manager'=>'bg-dark'];
                    @endphp
                    <span class="badge {{ $rc[$user->role] ?? 'bg-secondary' }}">{{ $user->getRoleLabel() }}</span>
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $user->is_active ? 'Active' : 'Pending Approval' }}
                    </span>
                    @if($user->is_first_user)
                        <span class="badge" style="background:#ede9fe;color:#6d28d9;">System Admin</span>
                    @endif
                </div>
            </div>
            <div class="card-body border-top">
                <div class="row g-3">
                    <div class="col-6">
                        <p class="text-muted small text-uppercase mb-1">First Name</p>
                        <p class="fw-medium mb-0">{{ $user->fname ?? '—' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small text-uppercase mb-1">Middle Name</p>
                        <p class="fw-medium mb-0">{{ $user->mname ?: '—' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small text-uppercase mb-1">Last Name</p>
                        <p class="fw-medium mb-0">{{ $user->lname ?? '—' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small text-uppercase mb-1">Date of Birth</p>
                        <p class="fw-medium mb-0">{{ $user->dob?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small text-uppercase mb-1">Registered</p>
                        <p class="fw-medium mb-0">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="col-6">
                        <p class="text-muted small text-uppercase mb-1">Approved At</p>
                        <p class="fw-medium mb-0">{{ $user->approved_at?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div class="col-12">
                        <p class="text-muted small text-uppercase mb-1">Approved By</p>
                        <p class="fw-medium mb-0">{{ $user->approvedBy?->getFullName() ?? ($user->is_first_user ? 'System (First User)' : '—') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">

        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(!$user->is_first_user && auth()->user()->canManageUsers())
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-shield-lock me-2"></i>Admin Actions</h5>
            </div>
            <div class="card-body">

                {{-- Approve & assign role if pending --}}
                @if(!$user->is_active)
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">Approve &amp; Assign Role</h6>
                    <form method="POST" action="{{ route('users.approve', $user) }}" class="d-flex gap-2">
                        @csrf
                        <select name="role" class="form-select form-select-sm" style="max-width:240px;" required>
                            <option value="">-- Assign Role --</option>
                            <option value="qa_officer"  {{ $user->role=='qa_officer'  ?'selected':'' }}>QA Officer</option>
                            <option value="call_center" {{ $user->role=='call_center' ?'selected':'' }}>Call Center</option>
                            <option value="qa_hod"      {{ $user->role=='qa_hod'      ?'selected':'' }}>QA Head of Department</option>
                            <option value="coo"         {{ $user->role=='coo'         ?'selected':'' }}>Chief Operating Officer</option>
                            <option value="line_manager" {{ $user->role=='line_manager' ?'selected':'' }}>Line Manager</option>
                        </select>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-lg me-1"></i>Approve User
                        </button>
                    </form>
                </div>
                @endif

                {{-- Change role if already active and current user is admin --}}
                @if($user->is_active && auth()->user()->isAdmin())
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2">Change Role</h6>
                    <form method="POST" action="{{ route('users.role', $user) }}" class="d-flex gap-2">
                        @csrf
                        <select name="role" class="form-select form-select-sm" style="max-width:240px;" required>
                            <option value="qa_officer"  {{ $user->role=='qa_officer'  ?'selected':'' }}>QA Officer</option>
                            <option value="call_center" {{ $user->role=='call_center' ?'selected':'' }}>Call Center</option>
                            <option value="qa_hod"      {{ $user->role=='qa_hod'      ?'selected':'' }}>QA Head of Department</option>
                            <option value="coo"         {{ $user->role=='coo'         ?'selected':'' }}>Chief Operating Officer</option>
                            <option value="line_manager" {{ $user->role=='line_manager' ?'selected':'' }}>Line Manager</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-repeat me-1"></i>Update Role
                        </button>
                    </form>
                </div>
                @endif

                {{-- Activate / Deactivate --}}
                <div class="pt-3 border-top">
                    <h6 class="fw-semibold mb-2">Account Status</h6>
                    @if($user->is_active)
                    <form method="POST" action="{{ route('users.deactivate', $user) }}" class="d-inline">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Deactivate {{ addslashes($user->getFullName()) }}? They will no longer be able to login.')"
                            class="btn btn-danger btn-sm">
                            <i class="bi bi-person-x me-1"></i>Deactivate User
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('users.activate', $user) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-person-check me-1"></i>Activate User
                        </button>
                    </form>
                    @endif
                </div>

            </div>
        </div>
        @endif

        <a href="{{ route('users.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Back to all users
        </a>
    </div>
</div>
@endsection
