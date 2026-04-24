@extends('layouts.app')
@section('title', 'Feedback Submissions')

@section('content')

@php
    $authUser    = auth()->user();
    $hasFilters  = request()->hasAny(['search','type','priority','assigned_to','date_from','date_to','status']);
    $typeColors  = ['complaint'=>'danger','compliment'=>'success','suggestion'=>'info','enquiry'=>'secondary'];
    $typeIcons   = ['complaint'=>'bi-exclamation-octagon','compliment'=>'bi-hand-thumbs-up','suggestion'=>'bi-lightbulb','enquiry'=>'bi-question-circle'];
@endphp

{{-- ── Page Title ─────────────────────────────────────────────────────── --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h4 class="mb-0">Feedback Submissions</h4>
                <p class="text-muted mb-0 small mt-1">
                    Manage, filter, and respond to all submitted feedback
                </p>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('feedback.manual.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Add Manual Entry
                </a>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Feedback</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ── Status Summary Cards ────────────────────────────────────────────── --}}
<div class="row g-3 mb-3">
    @php
        $statusCards = [
            ['label'=>'All',         'key'=>'',             'icon'=>'bi-collection',      'color'=>'primary',   'count'=>$counts['total']],
            ['label'=>'New',         'key'=>'new',          'icon'=>'bi-inbox',            'color'=>'danger',    'count'=>$counts['new']],
            ['label'=>'Under Review','key'=>'under_review', 'icon'=>'bi-hourglass-split',  'color'=>'warning',   'count'=>$counts['under_review']],
            ['label'=>'Responded',   'key'=>'responded',    'icon'=>'bi-check2-circle',    'color'=>'success',   'count'=>$counts['responded']],
            ['label'=>'Closed',      'key'=>'closed',       'icon'=>'bi-archive',          'color'=>'secondary', 'count'=>$counts['closed']],
        ];
        $activeStatus = request('status', '');
    @endphp
    @foreach($statusCards as $card)
    @php $isActive = $activeStatus === $card['key']; @endphp
    <div class="col-6 col-md">
        <a href="{{ route('feedback.admin.index') }}{{ $card['key'] ? '?status='.$card['key'] : '' }}"
           class="card text-decoration-none h-100 {{ $isActive ? 'border-'.$card['color'] : '' }}"
           style="{{ $isActive ? 'box-shadow:0 0 0 2px var(--bs-'.$card['color'].'-rgb,0 0 0)/.15);' : '' }}">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                                {{ $isActive ? 'bg-'.$card['color'] : 'bg-'.$card['color'].'-subtle' }}"
                         style="width:40px;height:40px;font-size:17px;">
                        <i class="bi {{ $card['icon'] }} {{ $isActive ? 'text-white' : 'text-'.$card['color'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 lh-1 {{ $isActive ? 'text-'.$card['color'] : 'text-dark' }}">
                            {{ $card['count'] }}
                        </div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

{{-- ── Filters Card ────────────────────────────────────────────────────── --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('feedback.admin.index') }}" id="filterForm">
            {{-- preserve status from stat card clicks --}}
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="row g-2 align-items-end">
                {{-- Search --}}
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               value="{{ request('search') }}"
                               placeholder="Ref #, name, email, phone…">
                    </div>
                </div>

                {{-- Type --}}
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="complaint"  {{ request('type')=='complaint'  ? 'selected':'' }}>Complaint</option>
                        <option value="compliment" {{ request('type')=='compliment' ? 'selected':'' }}>Compliment</option>
                        <option value="suggestion" {{ request('type')=='suggestion' ? 'selected':'' }}>Suggestion</option>
                        <option value="enquiry"    {{ request('type')=='enquiry'    ? 'selected':'' }}>Enquiry</option>
                    </select>
                </div>

                {{-- Priority --}}
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Priority</label>
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1" {{ request('priority') ? 'selected':'' }}>Priority Only</option>
                    </select>
                </div>

                {{-- Assigned To --}}
                <div class="col-md-3 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Assigned To</label>
                    <select name="assigned_to" class="form-select form-select-sm">
                        <option value="">Anyone</option>
                        <option value="unassigned" {{ request('assigned_to')=='unassigned' ? 'selected':'' }}>— Unassigned —</option>
                        @foreach($assignableUsers as $u)
                            <option value="{{ $u->id }}" {{ request('assigned_to')==$u->id ? 'selected':'' }}>
                                {{ $u->getFullName() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date range --}}
                <div class="col-6 col-md-2 col-lg-1">
                    <label class="form-label small fw-semibold mb-1">From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-2 col-lg-1">
                    <label class="form-label small fw-semibold mb-1">To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}">
                </div>

                {{-- Buttons --}}
                <div class="col-md-auto">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        @if($hasFilters)
                        <a href="{{ route('feedback.admin.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Submissions Table ───────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-2">
        <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0 small fw-bold text-uppercase ls-1" style="letter-spacing:.05em;">
                @if(request('status'))
                    {{ ucfirst(str_replace('_',' ',request('status'))) }}
                @elseif($hasFilters)
                    Filtered Results
                @else
                    All Submissions
                @endif
            </h5>
            <span class="badge bg-secondary rounded-pill">{{ $feedbacks->total() }}</span>
            @if($hasFilters)
                <span class="badge bg-primary-subtle text-primary" style="font-size:10px;">
                    <i class="bi bi-funnel me-1"></i>Filters active
                </span>
            @endif
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="text-muted small">
                Showing {{ $feedbacks->firstItem() }}–{{ $feedbacks->lastItem() }} of {{ $feedbacks->total() }}
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="ps-3 py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;width:160px;">Reference</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Patient</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Type</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Service Area</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Assigned</th>
                        <th class="py-3 fw-semibold text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;">Submitted</th>
                        <th class="py-3 text-end pe-3" style="width:80px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedbacks as $fb)
                    @php
                        $fbColor = $typeColors[$fb->feedback_type] ?? 'secondary';
                        $fbIcon  = $typeIcons[$fb->feedback_type]  ?? 'bi-chat';
                    @endphp
                    <tr class="{{ $fb->is_priority ? 'table-danger bg-opacity-10' : '' }}">

                        {{-- Reference --}}
                        <td class="ps-3 py-3">
                            <div class="d-flex align-items-center gap-2">
                                @if($fb->is_priority)
                                <span class="text-danger flex-shrink-0" title="High Priority">
                                    <i class="bi bi-exclamation-circle-fill" style="font-size:14px;"></i>
                                </span>
                                @endif
                                <div>
                                    <a href="{{ route('feedback.admin.show', $fb) }}"
                                       class="fw-semibold font-monospace text-decoration-none"
                                       style="color:#065321;font-size:12px;">
                                        {{ $fb->reference_number }}
                                    </a>
                                    @if($fb->is_priority)
                                    <div><span class="badge bg-danger" style="font-size:9px;">PRIORITY</span></div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Patient --}}
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:30px;height:30px;font-size:11px;background:#0b6b2c;">
                                    {{ strtoupper(substr($fb->patient_name ?: 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">{{ $fb->patient_name ?: 'Anonymous' }}</div>
                                    @if($fb->patient_email)
                                    <div class="text-muted" style="font-size:11px;">{{ $fb->patient_email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Type --}}
                        <td class="py-3">
                            <span class="badge bg-{{ $fbColor }}-subtle text-{{ $fbColor }} d-inline-flex align-items-center gap-1"
                                  style="font-size:11px;">
                                <i class="bi {{ $fbIcon }}"></i>
                                {{ ucfirst($fb->feedback_type) }}
                            </span>
                            @if($fb->service_rating)
                            <div class="text-muted mt-1" style="font-size:10px;">
                                ★ {{ $fb->getServiceRatingLabel() }}
                            </div>
                            @endif
                        </td>

                        {{-- Service Area --}}
                        <td class="py-3">
                            <div class="text-dark small">{{ $fb->getServiceCategoryLabel() }}</div>
                            @if($fb->service_units_summary)
                            <div class="text-muted" style="font-size:10px;">{{ \Illuminate\Support\Str::limit($fb->service_units_summary, 40) }}</div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="py-3">{!! $fb->getStatusBadge() !!}</td>

                        {{-- Assigned --}}
                        <td class="py-3">
                            @if($fb->assignedTo)
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:26px;height:26px;font-size:10px;background:#065321;">
                                    {{ strtoupper(substr($fb->assignedTo->fname ?? $fb->assignedTo->name, 0, 1)) }}
                                </div>
                                <span class="small text-dark">{{ $fb->assignedTo->getFullName() }}</span>
                            </div>
                            @else
                            <span class="text-muted small fst-italic">Unassigned</span>
                            @endif
                        </td>

                        {{-- Date --}}
                        <td class="py-3">
                            <div class="text-dark small">{{ $fb->created_at->format('d M Y') }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $fb->created_at->diffForHumans() }}</div>
                        </td>

                        {{-- Action --}}
                        <td class="text-end pe-3 py-3">
                            <a href="{{ route('feedback.admin.show', $fb) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox d-block fs-1 mb-3 opacity-25"></i>
                                @if($hasFilters)
                                    <p class="fw-semibold mb-1">No submissions match your filters</p>
                                    <p class="small mb-3">Try adjusting your search or clearing filters.</p>
                                    <a href="{{ route('feedback.admin.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-x-lg me-1"></i>Clear All Filters
                                    </a>
                                @else
                                    <p class="fw-semibold mb-1">No feedback submissions yet</p>
                                    <p class="small mb-3">Submissions from the public portal will appear here.</p>
                                    <a href="{{ route('feedback.manual.create') }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-lg me-1"></i>Add Manual Entry
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($feedbacks->hasPages())
    <div class="card-footer d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
        <span class="text-muted small">
            Page {{ $feedbacks->currentPage() }} of {{ $feedbacks->lastPage() }}
            &nbsp;&middot;&nbsp; {{ $feedbacks->total() }} total records
        </span>
        {{ $feedbacks->links() }}
    </div>
    @endif
</div>

@endsection
