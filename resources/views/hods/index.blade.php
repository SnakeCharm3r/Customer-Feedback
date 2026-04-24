@extends('layouts.app')
@section('title', 'HOD Officers')

@section('content')

@php $hasFilters = request()->hasAny(['search','department','role','status']); @endphp

{{-- ── Page Title ──────────────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="mb-0">HOD &amp; Incharge Officers</h4>
                <p class="text-muted mb-0 small mt-1">Escalation matrix — department heads and line managers</p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('hods.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus me-1"></i>Add Officer
                </a>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">HOD Officers</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ── Flash ───────────────────────────────────────────────────────────── --}}
@if(session('toast'))
<div class="alert alert-{{ session('toast_type')==='success' ? 'success' : 'warning' }} alert-dismissible fade show d-flex align-items-center gap-2">
    <i class="bi bi-{{ session('toast_type')==='success' ? 'check-circle' : 'exclamation-triangle' }}-fill"></i>
    <span>{{ session('toast') }}</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Stats Cards ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-3">
    @php
        $statCards = [
            ['label'=>'Total Officers',   'val'=>$stats['total'],               'icon'=>'bi-people',              'color'=>'primary'],
            ['label'=>'Active',           'val'=>$stats['active'],              'icon'=>'bi-person-check',        'color'=>'success'],
            ['label'=>'Inactive',         'val'=>$stats['inactive'],            'icon'=>'bi-person-dash',         'color'=>'secondary'],
            ['label'=>'Pending Escals.',  'val'=>$stats['pending_escalations'], 'icon'=>'bi-arrow-up-right-circle','color'=>'warning'],
        ];
    @endphp
    @foreach($statCards as $card)
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:3px solid var(--bs-{{ $card['color'] }}) !important;">
            <div class="card-body py-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $card['color'] }}-subtle flex-shrink-0"
                         style="width:36px;height:36px;font-size:15px;">
                        <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 lh-1 text-dark">{{ $card['val'] }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Filters ──────────────────────────────────────────────────────────── --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('hods.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4 col-lg-4">
                    <label class="form-label small fw-semibold mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               value="{{ request('search') }}" placeholder="Name, email or department…">
                    </div>
                </div>
                <div class="col-md-3 col-lg-3">
                    <label class="form-label small fw-semibold mb-1">Department</label>
                    <select name="department" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department')===$dept ? 'selected':'' }}>
                            {{ $dept }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Role</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        <option value="Line Manager"    {{ request('role')==='Line Manager'    ? 'selected':'' }}>Line Manager</option>
                        <option value="In-Charge"       {{ request('role')==='In-Charge'       ? 'selected':'' }}>In-Charge Officer</option>
                        <option value="Acting"          {{ request('role')==='Acting'          ? 'selected':'' }}>Acting Line Manager</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 col-lg-1">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="active"   {{ request('status')==='active'   ? 'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')==='inactive' ? 'selected':'' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-auto">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        @if($hasFilters)
                        <a href="{{ route('hods.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── HOD Table ───────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="ps-3 py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">#</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Officer</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Department</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Role</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Contact</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Escalations</th>
                        <th class="py-3 text-end pe-3" style="width:100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hods as $i => $hod)
                    @php
                        $isLM      = str_contains($hod->notes ?? '', 'Line Manager');
                        $isIC      = str_contains($hod->notes ?? '', 'In-Charge');
                        $isActing  = str_contains($hod->notes ?? '', 'Acting');
                        $roleBadge = $isActing
                            ? ['label'=>'Acting LM',      'class'=>'bg-info-subtle text-info']
                            : ($isIC
                                ? ['label'=>'In-Charge',  'class'=>'bg-warning-subtle text-warning']
                                : ($isLM
                                    ? ['label'=>'Line Manager','class'=>'bg-primary-subtle text-primary']
                                    : ['label'=>$hod->notes ?? '—','class'=>'bg-secondary-subtle text-secondary']));
                        $escCount  = $hod->escalations_count ?? 0;
                        $pendCount = $hod->pending_escalations_count ?? 0;
                        $rowNum    = ($hods->currentPage() - 1) * $hods->perPage() + $i + 1;
                    @endphp
                    <tr>
                        {{-- Row number --}}
                        <td class="ps-3 py-2 text-muted" style="font-size:12px;width:40px;">{{ $rowNum }}</td>

                        {{-- Officer --}}
                        <td class="py-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:34px;height:34px;font-size:13px;background:{{ $hod->is_active ? 'linear-gradient(135deg,#065321,#0b6b2c)' : '#9ca3af' }};">
                                    {{ strtoupper(substr($hod->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size:13px;">{{ $hod->name }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Department --}}
                        <td class="py-2">
                            <span class="badge bg-light text-dark border" style="font-size:11px;font-weight:500;">
                                <i class="bi bi-building me-1 text-muted"></i>{{ $hod->department }}
                            </span>
                        </td>

                        {{-- Role --}}
                        <td class="py-2">
                            <span class="badge {{ $roleBadge['class'] }}" style="font-size:11px;">
                                {{ $roleBadge['label'] }}
                            </span>
                        </td>

                        {{-- Contact --}}
                        <td class="py-2">
                            <a href="mailto:{{ $hod->email }}" class="text-decoration-none text-dark small d-flex align-items-center gap-1">
                                <i class="bi bi-envelope text-muted" style="font-size:11px;"></i>
                                {{ $hod->email }}
                            </a>
                            @if($hod->phone)
                            <div class="text-muted d-flex align-items-center gap-1 mt-1" style="font-size:11px;">
                                <i class="bi bi-telephone" style="font-size:10px;"></i>{{ $hod->phone }}
                            </div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="py-2">
                            @if($hod->is_active)
                            <span class="badge bg-success-subtle text-success d-inline-flex align-items-center gap-1">
                                <i class="bi bi-circle-fill" style="font-size:6px;"></i>Active
                            </span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary d-inline-flex align-items-center gap-1">
                                <i class="bi bi-circle-fill" style="font-size:6px;"></i>Inactive
                            </span>
                            @endif
                        </td>

                        {{-- Escalations --}}
                        <td class="py-2">
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <span class="badge bg-light text-dark border" style="font-size:11px;">{{ $escCount }} total</span>
                                @if($pendCount > 0)
                                <span class="badge bg-warning-subtle text-warning" style="font-size:11px;">{{ $pendCount }} pending</span>
                                @endif
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="text-end pe-3 py-2">
                            <a href="{{ route('hods.edit', $hod) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('hods.destroy', $hod) }}" class="d-inline"
                                  onsubmit="return confirm('Delete {{ addslashes($hod->name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-people d-block fs-1 mb-3 opacity-25"></i>
                            @if($hasFilters)
                            <p class="fw-semibold mb-1">No officers match your filters</p>
                            <a href="{{ route('hods.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                                <i class="bi bi-x-lg me-1"></i>Clear Filters
                            </a>
                            @else
                            <p class="fw-semibold mb-1">No officers added yet</p>
                            <p class="small mb-3">Add HOD and incharge officers to enable the escalation feature.</p>
                            <a href="{{ route('hods.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-person-plus me-1"></i>Add First Officer
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
        <span class="text-muted small">
            @if($hods->total() > 0)
                Showing {{ $hods->firstItem() }}–{{ $hods->lastItem() }} of {{ $hods->total() }} officers
            @else
                No officers found
            @endif
        </span>
        @if($hods->hasPages())
        {{ $hods->links() }}
        @endif
    </div>
</div>

@endsection
