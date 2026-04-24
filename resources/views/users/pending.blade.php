@extends('layouts.app')
@section('title', 'Pending Approvals')

@section('content')

{{-- ── Page Title ──────────────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="mb-0">Pending Approvals</h4>
                <p class="text-muted mb-0 small mt-1">Review and approve staff registrations before granting access</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($pendingUsers->total() > 0)
                <span class="badge bg-warning text-dark px-3 py-2" style="font-size:13px;">
                    <i class="bi bi-hourglass-split me-1"></i>{{ $pendingUsers->total() }} pending
                </span>
                @endif
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="breadcrumb-item active">Pending</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ── Flash ───────────────────────────────────────────────────────────── --}}
@if(session('status'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i>
    <span>{{ session('status') }}</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Info Banner ────────────────────────────────────────────────────── --}}
@if($pendingUsers->total() > 0)
<div class="alert d-flex align-items-center gap-3 mb-4"
     style="background:#fff8e1;border:1px solid #ffc107;border-left:4px solid #ffc107;">
    <i class="bi bi-info-circle-fill text-warning fs-5 flex-shrink-0"></i>
    <span class="small">
        Users below have registered and are waiting for an administrator to review their account,
        assign a role, and activate access. They cannot log in until approved.
    </span>
</div>
@endif

{{-- ── Pending User Cards ──────────────────────────────────────────────── --}}
<div class="row g-3">
    @forelse($pendingUsers as $user)
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center g-3">

                    {{-- Avatar + Identity --}}
                    <div class="col-md-5 col-lg-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                 style="width:50px;height:50px;font-size:17px;background:linear-gradient(135deg,#b45309,#f59e0b);">
                                {{ strtoupper(substr($user->fname ?? $user->name,0,1)) }}{{ strtoupper(substr($user->lname ?? '',0,1)) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $user->getFullName() }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                                <div class="mt-1 d-flex flex-wrap gap-2" style="font-size:11px;">
                                    @if($user->dob)
                                    <span class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $user->dob->format('d M Y') }}
                                    </span>
                                    @endif
                                    <span class="text-muted">
                                        <i class="bi bi-clock me-1"></i>Registered {{ $user->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Requested role + metadata --}}
                    <div class="col-md-3 col-lg-3">
                        <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Requested Role</div>
                        <span class="badge bg-secondary mt-1">{{ $user->getRoleLabel() }}</span>
                        <div class="text-muted mt-2" style="font-size:11px;">
                            <i class="bi bi-calendar-plus me-1"></i>{{ $user->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    {{-- Approve form --}}
                    <div class="col-md-4 col-lg-5">
                        <form method="POST" action="{{ route('users.approve', $user) }}">
                            @csrf
                            <div class="d-flex flex-wrap gap-2 align-items-end">
                                <div class="flex-grow-1" style="min-width:180px;">
                                    <label class="form-label small fw-semibold mb-1">
                                        Assign Role <span class="text-danger">*</span>
                                    </label>
                                    <select name="role" class="form-select form-select-sm" required>
                                        <option value="">— Select role —</option>
                                        <option value="qa_officer"   {{ $user->role==='qa_officer'   ? 'selected':'' }}>QA Officer</option>
                                        <option value="call_center"  {{ $user->role==='call_center'  ? 'selected':'' }}>Call Center</option>
                                        <option value="qa_hod"       {{ $user->role==='qa_hod'       ? 'selected':'' }}>QA Head of Department</option>
                                        <option value="coo"          {{ $user->role==='coo'          ? 'selected':'' }}>Chief Operating Officer</option>
                                        <option value="line_manager" {{ $user->role==='line_manager' ? 'selected':'' }}>Line Manager</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm px-3 flex-shrink-0">
                                    <i class="bi bi-check-lg me-1"></i>Approve
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-6">
                <div class="py-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-success-subtle mb-3"
                         style="width:72px;height:72px;font-size:2rem;">
                        <i class="bi bi-check2-all text-success"></i>
                    </div>
                    <h5 class="fw-semibold">All caught up!</h5>
                    <p class="text-muted small mb-4">There are no pending user registrations to review right now.</p>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-people me-1"></i>View All Users
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($pendingUsers->hasPages())
<div class="mt-3">{{ $pendingUsers->links() }}</div>
@endif

<div class="mt-3">
    <a href="{{ route('users.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i>Back to all users
    </a>
</div>

@endsection
