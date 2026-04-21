@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

@php
    use App\Models\Feedback;
    use App\Models\User;

    // --- Totals ---
    $totalFeedback  = Feedback::count();
    $totalUsers     = User::where('is_active', true)->count();
    $pendingUsers   = User::where('is_active', false)->where('is_first_user', false)->count();
    $urgentOpen     = Feedback::where('is_urgent', true)->whereNotIn('status', ['closed'])->count();

    // --- By Status ---
    $statusNew          = Feedback::where('status', 'new')->count();
    $statusUnderReview  = Feedback::where('status', 'under_review')->count();
    $statusResponded    = Feedback::where('status', 'responded')->count();
    $statusClosed       = Feedback::where('status', 'closed')->count();

    // --- By Feedback Type ---
    $typeComplaints  = Feedback::where('feedback_type', 'complaint')->count();
    $typeCompliments = Feedback::where('feedback_type', 'compliment')->count();
    $typeSuggestions = Feedback::where('feedback_type', 'suggestion')->count();
    $typeEnquiries   = Feedback::where('feedback_type', 'enquiry')->count();

    // --- By Service Category ---
    $byCategory = Feedback::selectRaw('service_category, COUNT(*) as total')
        ->groupBy('service_category')
        ->orderByDesc('total')
        ->get();

    // --- Recent ---
    $recentFeedback = Feedback::with('assignedTo')->orderByDesc('created_at')->limit(8)->get();
@endphp

{{-- ═══ PAGE TITLE ═══ --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Dashboard</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ═══ ROW 1: TOP SUMMARY STATS ═══ --}}
<div class="row g-3">

    {{-- Card: Total Feedback --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted small mb-0">Total Submissions</p>
                        <h2 class="mt-3 mb-1 ff-secondary fw-bold">{{ $totalFeedback }}</h2>
                        <p class="mb-0 small text-muted">All feedback received</p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle rounded-circle fs-2xl">
                                <i class="bi bi-chat-left-text text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Total Active Users --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted small mb-0">Active Users</p>
                        <h2 class="mt-3 mb-1 ff-secondary fw-bold">{{ $totalUsers }}</h2>
                        @if($pendingUsers > 0)
                            <p class="mb-0 small">
                                <span class="badge bg-warning-subtle text-warning">{{ $pendingUsers }} pending approval</span>
                            </p>
                        @else
                            <p class="mb-0 small text-muted">All users active</p>
                        @endif
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle rounded-circle fs-2xl">
                                <i class="bi bi-people text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card: New (Unreviewed) --}}
    <div class="col-xl-3 col-sm-6">
        <a href="{{ route('feedback.admin.index') }}?status=new" class="text-decoration-none">
        <div class="card card-animate h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted small mb-0">Awaiting Review</p>
                        <h2 class="mt-3 mb-1 ff-secondary fw-bold text-danger">{{ $statusNew }}</h2>
                        <p class="mb-0 small"><span class="badge bg-danger-subtle text-danger">Needs attention</span></p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle rounded-circle fs-2xl">
                                <i class="bi bi-inbox text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </a>
    </div>

    {{-- Card: Urgent Open --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card card-animate h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted small mb-0">Urgent / Priority</p>
                        <h2 class="mt-3 mb-1 ff-secondary fw-bold {{ $urgentOpen > 0 ? 'text-danger' : '' }}">{{ $urgentOpen }}</h2>
                        <p class="mb-0 small text-muted">Open urgent submissions</p>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle rounded-circle fs-2xl">
                                <i class="bi bi-exclamation-triangle text-danger"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /ROW 1 --}}


{{-- ═══ ROW 2: FEEDBACK BY TYPE + STATUS PIPELINE ═══ --}}
<div class="row g-3 mt-1">

    {{-- Card: Feedback Type Breakdown (Complaint / Compliment / Suggestion / Enquiry) --}}
    <div class="col-xl-5 col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2 text-primary"></i>Feedback by Type
                </h5>
                <span class="badge bg-primary-subtle text-primary">{{ $totalFeedback }} total</span>
            </div>
            <div class="card-body">
                @php
                    $types = [
                        ['label' => 'Complaints',   'count' => $typeComplaints,  'color' => 'danger',   'icon' => 'bi-exclamation-octagon'],
                        ['label' => 'Compliments',  'count' => $typeCompliments, 'color' => 'success',  'icon' => 'bi-hand-thumbs-up'],
                        ['label' => 'Suggestions',  'count' => $typeSuggestions, 'color' => 'info',     'icon' => 'bi-lightbulb'],
                        ['label' => 'Enquiries',    'count' => $typeEnquiries,   'color' => 'secondary','icon' => 'bi-question-circle'],
                    ];
                @endphp

                <div class="vstack gap-3">
                    @foreach($types as $t)
                    @php $pct = $totalFeedback > 0 ? round(($t['count'] / $totalFeedback) * 100) : 0; @endphp
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs flex-shrink-0">
                                    <span class="avatar-title bg-{{ $t['color'] }}-subtle rounded-circle">
                                        <i class="bi {{ $t['icon'] }} text-{{ $t['color'] }}"></i>
                                    </span>
                                </div>
                                <span class="fw-medium text-dark small">{{ $t['label'] }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold fs-6">{{ $t['count'] }}</span>
                                <span class="text-muted small">({{ $pct }}%)</span>
                            </div>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-{{ $t['color'] }}"
                                 role="progressbar"
                                 style="width:{{ $pct }}%"
                                 aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Status Pipeline --}}
    <div class="col-xl-3 col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-kanban me-2 text-warning"></i>Status Pipeline
                </h5>
            </div>
            <div class="card-body p-0">
                @php
                    $statuses = [
                        ['label' => 'New',          'count' => $statusNew,         'color' => 'danger',    'status' => 'new'],
                        ['label' => 'Under Review',  'count' => $statusUnderReview, 'color' => 'warning',   'status' => 'under_review'],
                        ['label' => 'Responded',     'count' => $statusResponded,   'color' => 'success',   'status' => 'responded'],
                        ['label' => 'Closed',        'count' => $statusClosed,      'color' => 'secondary', 'status' => 'closed'],
                    ];
                @endphp
                @foreach($statuses as $s)
                <a href="{{ route('feedback.admin.index') }}?status={{ $s['status'] }}"
                   class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom text-decoration-none hover-bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-{{ $s['color'] }}-subtle d-inline-flex align-items-center justify-content-center"
                              style="width:10px;height:10px;flex-shrink:0;">
                        </span>
                        <span class="fw-medium small text-dark">{{ $s['label'] }}</span>
                    </div>
                    <span class="badge bg-{{ $s['color'] }} rounded-pill">{{ $s['count'] }}</span>
                </a>
                @endforeach
                <div class="px-4 py-3 d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Total</span>
                    <span class="fw-bold">{{ $totalFeedback }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Feedback by Service Category --}}
    <div class="col-xl-4 col-lg-12">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="bi bi-hospital me-2 text-info"></i>By Service Area
                </h5>
            </div>
            <div class="card-body p-0">
                @php
                    $categoryLabels = \App\Models\Feedback::SERVICE_CATEGORIES;
                @endphp
                @forelse($byCategory as $cat)
                @php
                    $pct = $totalFeedback > 0 ? round(($cat->total / $totalFeedback) * 100) : 0;
                    $label = $categoryLabels[$cat->service_category] ?? ucfirst(str_replace('_', ' ', $cat->service_category));
                @endphp
                <div class="px-4 py-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-medium text-dark">{{ $label }}</span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold small">{{ $cat->total }}</span>
                            <span class="text-muted" style="font-size:11px;">{{ $pct }}%</span>
                        </div>
                    </div>
                    <div class="progress" style="height:4px;">
                        <div class="progress-bar bg-info" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 small">
                    <i class="bi bi-inbox d-block fs-3 mb-2 opacity-25"></i>No submissions yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>{{-- /ROW 2 --}}


{{-- ═══ ROW 3: ALERTS (Pending Users / Urgent) ═══ --}}
@if((auth()->user()->canManageUsers() && $pendingUsers > 0) || $urgentOpen > 0)
<div class="row g-3 mt-1">
    @if(auth()->user()->canManageUsers() && $pendingUsers > 0)
    <div class="col-md-6">
        <div class="alert alert-warning d-flex align-items-center justify-content-between mb-0 py-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-check fs-4"></i>
                <div>
                    <div class="fw-semibold">{{ $pendingUsers }} Registration{{ $pendingUsers > 1 ? 's' : '' }} Pending Approval</div>
                    <div class="small">New users are waiting for admin review.</div>
                </div>
            </div>
            <a href="{{ route('users.pending') }}" class="btn btn-warning btn-sm flex-shrink-0 ms-3">Review Now</a>
        </div>
    </div>
    @endif
    @if($urgentOpen > 0)
    <div class="col-md-6">
        <div class="alert alert-danger d-flex align-items-center justify-content-between mb-0 py-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle fs-4"></i>
                <div>
                    <div class="fw-semibold">{{ $urgentOpen }} Urgent Submission{{ $urgentOpen > 1 ? 's' : '' }} Open</div>
                    <div class="small">High-priority feedback requires immediate attention.</div>
                </div>
            </div>
            <a href="{{ route('feedback.admin.index') }}" class="btn btn-danger btn-sm flex-shrink-0 ms-3">View All</a>
        </div>
    </div>
    @endif
</div>
@endif


{{-- ═══ ROW 4: RECENT SUBMISSIONS TABLE ═══ --}}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Recent Submissions
                </h5>
                @if(auth()->user()->canManageComplaints())
                <a href="{{ route('feedback.admin.index') }}" class="btn btn-sm btn-outline-primary">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless table-centered align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Reference</th>
                                <th>Patient</th>
                                <th>Type</th>
                                <th>Service Area</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                @if(auth()->user()->canManageComplaints())
                                <th class="text-end pe-3"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentFeedback as $item)
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-medium text-primary font-monospace small">{{ $item->reference_number }}</span>
                                    @if($item->is_urgent)
                                        <span class="badge bg-danger ms-1" style="font-size:9px;">URGENT</span>
                                    @endif
                                </td>
                                <td class="fw-semibold small">{{ $item->patient_name }}</td>
                                <td>
                                    @php
                                        $tc = ['complaint'=>'bg-danger','compliment'=>'bg-success','suggestion'=>'bg-info','enquiry'=>'bg-secondary'][$item->feedback_type] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $tc }}">{{ ucfirst($item->feedback_type) }}</span>
                                </td>
                                <td class="text-muted small">
                                    {{ \App\Models\Feedback::SERVICE_CATEGORIES[$item->service_category] ?? ucfirst(str_replace('_', ' ', $item->service_category)) }}
                                </td>
                                <td>{!! $item->getStatusBadge() !!}</td>
                                <td class="text-muted small">{{ $item->created_at->diffForHumans() }}</td>
                                @if(auth()->user()->canManageComplaints())
                                <td class="text-end pe-3">
                                    <a href="{{ route('feedback.admin.show', $item) }}"
                                       class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                    No feedback submissions yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>{{-- /ROW 4 --}}

@endsection
