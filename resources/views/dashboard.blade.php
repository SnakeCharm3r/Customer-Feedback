@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

@php
    use App\Models\Feedback;
    use App\Models\User;
    use App\Models\Escalation;
    use Illuminate\Support\Carbon;

    $authUser = auth()->user();

    // ── Totals ──────────────────────────────────────────────────────────────
    $totalFeedback       = Feedback::count();
    $totalUsers          = User::where('is_active', true)->count();
    $pendingUsers        = User::where('is_active', false)->where('is_first_user', false)->count();
    $urgentOpen          = Feedback::where('is_urgent', true)->whereNotIn('status', ['closed'])->count();

    // ── By Status ───────────────────────────────────────────────────────────
    $statusNew         = Feedback::where('status', 'new')->count();
    $statusUnderReview = Feedback::where('status', 'under_review')->count();
    $statusResponded   = Feedback::where('status', 'responded')->count();
    $statusClosed      = Feedback::where('status', 'closed')->count();

    // ── Rates ───────────────────────────────────────────────────────────────
    $resolvedCount   = $statusResponded + $statusClosed;
    $responseRate    = $totalFeedback > 0 ? round(($resolvedCount / $totalFeedback) * 100) : 0;
    $pendingEscalations = Escalation::where('status', 'pending')->count();

    // ── Trends (today / this week) ──────────────────────────────────────────
    $todayCount    = Feedback::whereDate('created_at', today())->count();
    $weekCount     = Feedback::where('created_at', '>=', now()->startOfWeek())->count();
    $yesterdayCount = Feedback::whereDate('created_at', today()->subDay())->count();
    $lastWeekCount  = Feedback::whereBetween('created_at', [now()->subWeeks(2)->startOfWeek(), now()->subWeeks(1)->endOfWeek()])->count();

    // ── By Feedback Type ────────────────────────────────────────────────────
    $typeComplaints  = Feedback::where('feedback_type', 'complaint')->count();
    $typeCompliments = Feedback::where('feedback_type', 'compliment')->count();
    $typeSuggestions = Feedback::where('feedback_type', 'suggestion')->count();
    $typeEnquiries   = Feedback::where('feedback_type', 'enquiry')->count();

    // ── By Service Category ─────────────────────────────────────────────────
    $byCategory = Feedback::selectRaw('service_category, COUNT(*) as total')
        ->groupBy('service_category')
        ->orderByDesc('total')
        ->limit(6)
        ->get();

    // ── Last 7 days chart data ───────────────────────────────────────────────
    $chartDays   = [];
    $chartCounts = [];
    for ($i = 6; $i >= 0; $i--) {
        $day = now()->subDays($i);
        $chartDays[]   = $day->format('D d');
        $chartCounts[] = Feedback::whereDate('created_at', $day->toDateString())->count();
    }

    // ── My Assignments ──────────────────────────────────────────────────────
    $myAssignments = Feedback::where('assigned_to', $authUser->id)
        ->whereNotIn('status', ['closed'])
        ->orderByDesc('created_at')
        ->limit(6)
        ->get();

    // ── Recent Submissions ──────────────────────────────────────────────────
    $recentFeedback = Feedback::with('assignedTo')->orderByDesc('created_at')->limit(8)->get();
@endphp

{{-- ══════════════════════════════════════════════════════════════════════
     WELCOME BANNER
══════════════════════════════════════════════════════════════════════ --}}
<div class="row mb-1">
    <div class="col-12">
        <div class="rounded-3 px-4 py-3 d-flex flex-wrap align-items-center justify-content-between gap-3"
             style="background:#fff;border:1px solid #e9ecef;border-left:4px solid #0b6b2c;box-shadow:0 2px 8px rgba(0,0,0,.04);">
            <div>
                <h4 class="mb-1 fw-bold text-dark">
                    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
                    {{ $authUser->fname ?? $authUser->name }} 👋
                </h4>
                <p class="mb-0 text-muted small">
                    {{ now()->format('l, d F Y') }}
                    &nbsp;·&nbsp;
                    {{ $authUser->getRoleLabel() }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($authUser->canManageComplaints())
                <a href="{{ route('feedback.manual.create') }}"
                   class="btn btn-sm btn-primary fw-semibold">
                    <i class="bi bi-plus-circle me-1"></i>Add Feedback
                </a>
                <a href="{{ route('feedback.admin.index') }}"
                   class="btn btn-sm btn-outline-secondary fw-semibold">
                    <i class="bi bi-list-ul me-1"></i>All Submissions
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     ALERTS — shown first so they're never missed
══════════════════════════════════════════════════════════════════════ --}}
@if(($authUser->canManageUsers() && $pendingUsers > 0) || $urgentOpen > 0 || $pendingEscalations > 0)
<div class="row g-2 mb-1">
    @if($authUser->canManageUsers() && $pendingUsers > 0)
    <div class="col-md-4">
        <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between gap-2 mb-0 py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-check fs-5"></i>
                <div>
                    <div class="fw-semibold small">{{ $pendingUsers }} pending user registration{{ $pendingUsers > 1 ? 's' : '' }}</div>
                    <div class="text-muted" style="font-size:11px;">Waiting for admin review</div>
                </div>
            </div>
            <a href="{{ route('users.pending') }}" class="btn btn-warning btn-sm flex-shrink-0">Review</a>
        </div>
    </div>
    @endif
    @if($urgentOpen > 0)
    <div class="col-md-4">
        <div class="alert alert-danger d-flex flex-wrap align-items-center justify-content-between gap-2 mb-0 py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle fs-5"></i>
                <div>
                    <div class="fw-semibold small">{{ $urgentOpen }} urgent submission{{ $urgentOpen > 1 ? 's' : '' }} open</div>
                    <div class="text-muted" style="font-size:11px;">High-priority, needs immediate action</div>
                </div>
            </div>
            <a href="{{ route('feedback.admin.index') }}?priority=1" class="btn btn-danger btn-sm flex-shrink-0">View</a>
        </div>
    </div>
    @endif
    @if($pendingEscalations > 0)
    <div class="col-md-4">
        <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between gap-2 mb-0 py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-arrow-up-right-circle fs-5"></i>
                <div>
                    <div class="fw-semibold small">{{ $pendingEscalations }} pending escalation{{ $pendingEscalations > 1 ? 's' : '' }}</div>
                    <div class="text-muted" style="font-size:11px;">Awaiting HOD response</div>
                </div>
            </div>
            <a href="{{ route('escalations.index', ['status' => 'pending']) }}" class="btn btn-info btn-sm flex-shrink-0 text-white">View</a>
        </div>
    </div>
    @endif
</div>
@endif

{{-- ══════════════════════════════════════════════════════════════════════
     ROW 1 — STAT CARDS
══════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mt-0">

    {{-- Total Submissions --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100 border-0 shadow-sm" style="border-left:4px solid #0b6b2c !important;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-uppercase fw-medium text-muted small mb-2 ls-1">Total Submissions</p>
                        <h2 class="fw-bold mb-1" style="color:#065321;">{{ $totalFeedback }}</h2>
                        <div class="d-flex align-items-center gap-2 small">
                            @php $todayDelta = $todayCount - $yesterdayCount; @endphp
                            @if($todayCount > 0)
                                <span class="badge bg-primary-subtle text-primary">+{{ $todayCount }} today</span>
                            @else
                                <span class="text-muted">None today</span>
                            @endif
                        </div>
                        <div class="text-muted mt-1" style="font-size:11px;">{{ $weekCount }} this week</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle flex-shrink-0"
                         style="width:48px;height:48px;font-size:20px;">
                        <i class="bi bi-chat-left-text" style="color:#0b6b2c;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Awaiting Review --}}
    <div class="col-xl-3 col-sm-6">
        <a href="{{ route('feedback.admin.index') }}?status=new" class="text-decoration-none">
        <div class="card h-100 border-0 shadow-sm" style="border-left:4px solid #dc3545 !important;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-uppercase fw-medium text-muted small mb-2 ls-1">Awaiting Review</p>
                        <h2 class="fw-bold mb-1 {{ $statusNew > 0 ? 'text-danger' : '' }}">{{ $statusNew }}</h2>
                        <div class="small">
                            @if($statusNew > 0)
                                <span class="badge bg-danger-subtle text-danger">Needs attention</span>
                            @else
                                <span class="text-muted">Queue is clear</span>
                            @endif
                        </div>
                        <div class="text-muted mt-1" style="font-size:11px;">{{ $statusUnderReview }} under review</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle flex-shrink-0"
                         style="width:48px;height:48px;font-size:20px;">
                        <i class="bi bi-inbox text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
        </a>
    </div>

    {{-- Response Rate --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100 border-0 shadow-sm" style="border-left:4px solid #198754 !important;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="flex-grow-1 me-3">
                        <p class="text-uppercase fw-medium text-muted small mb-2 ls-1">Resolution Rate</p>
                        <h2 class="fw-bold mb-1 text-success">{{ $responseRate }}%</h2>
                        <div class="progress mb-1" style="height:5px;">
                            <div class="progress-bar bg-success" style="width:{{ $responseRate }}%"></div>
                        </div>
                        <div class="text-muted" style="font-size:11px;">{{ $resolvedCount }} of {{ $totalFeedback }} resolved</div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle flex-shrink-0"
                         style="width:48px;height:48px;font-size:20px;">
                        <i class="bi bi-check2-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Urgent / Priority --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card h-100 border-0 shadow-sm"
             style="border-left:4px solid {{ $urgentOpen > 0 ? '#ffc107' : '#6c757d' }} !important;">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-uppercase fw-medium text-muted small mb-2 ls-1">Priority Open</p>
                        <h2 class="fw-bold mb-1 {{ $urgentOpen > 0 ? 'text-warning' : '' }}">{{ $urgentOpen }}</h2>
                        <div class="small">
                            @if($urgentOpen > 0)
                                <span class="badge bg-warning-subtle text-warning">Requires attention</span>
                            @else
                                <span class="text-muted">No urgent items</span>
                            @endif
                        </div>
                        <div class="text-muted mt-1" style="font-size:11px;">
                            {{ $pendingEscalations }} escalation{{ $pendingEscalations != 1 ? 's' : '' }} pending
                        </div>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:48px;height:48px;font-size:20px;background:{{ $urgentOpen > 0 ? 'rgba(255,193,7,.15)' : 'rgba(108,117,125,.1)' }}">
                        <i class="bi bi-exclamation-triangle {{ $urgentOpen > 0 ? 'text-warning' : 'text-muted' }}"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /ROW 1 --}}


{{-- ══════════════════════════════════════════════════════════════════════
     ROW 2 — 7-DAY CHART + MY ASSIGNMENTS
══════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mt-1">

    {{-- 7-Day Submissions Trend --}}
    <div class="col-xl-7 col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2" style="color:#0b6b2c;"></i>Submissions — Last 7 Days
                </h5>
                <span class="badge bg-primary-subtle text-primary">{{ array_sum($chartCounts) }} total</span>
            </div>
            <div class="card-body pb-2">
                <div id="submissionsTrendChart" style="min-height:220px;"></div>
            </div>
        </div>
    </div>

    {{-- My Assignments --}}
    <div class="col-xl-5 col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person-check me-2 text-warning"></i>My Assignments
                </h5>
                <span class="badge bg-secondary rounded-pill">{{ $myAssignments->count() }}</span>
            </div>
            <div class="card-body p-0" style="overflow-y:auto;max-height:280px;">
                @forelse($myAssignments as $item)
                <a href="{{ route('feedback.admin.show', $item) }}"
                   class="d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none hover-bg-light">
                    <div class="flex-shrink-0">
                        @php
                            $tc = ['complaint'=>['bg'=>'bg-danger-subtle','txt'=>'text-danger','icon'=>'bi-exclamation-octagon'],
                                   'compliment'=>['bg'=>'bg-success-subtle','txt'=>'text-success','icon'=>'bi-hand-thumbs-up'],
                                   'suggestion'=>['bg'=>'bg-info-subtle','txt'=>'text-info','icon'=>'bi-lightbulb'],
                                   'enquiry'=>['bg'=>'bg-secondary-subtle','txt'=>'text-secondary','icon'=>'bi-question-circle']][$item->feedback_type]
                                   ?? ['bg'=>'bg-secondary-subtle','txt'=>'text-secondary','icon'=>'bi-chat'];
                        @endphp
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $tc['bg'] }}"
                             style="width:38px;height:38px;font-size:15px;">
                            <i class="bi {{ $tc['icon'] }} {{ $tc['txt'] }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex align-items-center justify-content-between gap-1">
                            <span class="fw-semibold small text-dark text-truncate font-monospace">{{ $item->reference_number }}</span>
                            {!! $item->getStatusBadge() !!}
                        </div>
                        <div class="text-muted text-truncate" style="font-size:11px;">
                            {{ $item->patient_name }} &middot; {{ $item->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @if($item->is_priority)
                        <span class="badge bg-danger-subtle text-danger flex-shrink-0" style="font-size:9px;">PRIORITY</span>
                    @endif
                </a>
                @empty
                <div class="text-center text-muted py-5 px-3">
                    <i class="bi bi-check2-all d-block fs-2 mb-2 opacity-25"></i>
                    <p class="mb-0 small">No open assignments for you right now.</p>
                </div>
                @endforelse
            </div>
            @if($myAssignments->count() > 0)
            <div class="card-footer py-2 text-center">
                <a href="{{ route('feedback.admin.index') }}" class="small text-primary text-decoration-none">
                    View all submissions <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            @endif
        </div>
    </div>

</div>{{-- /ROW 2 --}}


{{-- ══════════════════════════════════════════════════════════════════════
     ROW 3 — FEEDBACK BY TYPE + STATUS PIPELINE + SERVICE AREA
══════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mt-1">

    {{-- Feedback Type Breakdown --}}
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
                        ['label' => 'Complaints',  'count' => $typeComplaints,  'color' => 'danger',   'icon' => 'bi-exclamation-octagon', 'status' => 'complaint'],
                        ['label' => 'Compliments', 'count' => $typeCompliments, 'color' => 'success',  'icon' => 'bi-hand-thumbs-up',      'status' => 'compliment'],
                        ['label' => 'Suggestions', 'count' => $typeSuggestions, 'color' => 'info',     'icon' => 'bi-lightbulb',           'status' => 'suggestion'],
                        ['label' => 'Enquiries',   'count' => $typeEnquiries,   'color' => 'secondary','icon' => 'bi-question-circle',     'status' => 'enquiry'],
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
                                <a href="{{ route('feedback.admin.index') }}?type={{ $t['status'] }}"
                                   class="fw-medium text-dark small text-decoration-none">{{ $t['label'] }}</a>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold">{{ $t['count'] }}</span>
                                <span class="text-muted small" style="min-width:36px;text-align:right;">{{ $pct }}%</span>
                            </div>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-{{ $t['color'] }}" role="progressbar"
                                 style="width:{{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Satisfaction indicator --}}
                @if($totalFeedback > 0)
                @php $satisfactionPct = round((($typeCompliments + $typeSuggestions) / $totalFeedback) * 100); @endphp
                <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                    <span class="text-muted small">Positive ratio</span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:6px;width:80px;">
                            <div class="progress-bar bg-success" style="width:{{ $satisfactionPct }}%"></div>
                        </div>
                        <span class="fw-semibold small text-success">{{ $satisfactionPct }}%</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Pipeline --}}
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
                    <div class="d-flex align-items-center gap-3">
                        <span class="rounded-circle d-inline-block bg-{{ $s['color'] }}"
                              style="width:8px;height:8px;flex-shrink:0;"></span>
                        <span class="fw-medium small text-dark">{{ $s['label'] }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-{{ $s['color'] }} rounded-pill">{{ $s['count'] }}</span>
                        @if($totalFeedback > 0)
                        <span class="text-muted" style="font-size:11px;min-width:30px;text-align:right;">
                            {{ round(($s['count'] / $totalFeedback) * 100) }}%
                        </span>
                        @endif
                    </div>
                </a>
                @endforeach
                <div class="px-4 py-3 d-flex justify-content-between align-items-center bg-light-subtle">
                    <span class="text-muted small fw-semibold">Total</span>
                    <span class="fw-bold">{{ $totalFeedback }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- By Service Area --}}
    <div class="col-xl-4 col-lg-12">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">
                    <i class="bi bi-hospital me-2 text-info"></i>Top Service Areas
                </h5>
                @if($byCategory->isNotEmpty())
                <span class="badge bg-info-subtle text-info">{{ $byCategory->count() }}</span>
                @endif
            </div>
            <div class="card-body p-0">
                @php $categoryLabels = \App\Models\Feedback::SERVICE_CATEGORIES; @endphp
                @forelse($byCategory as $i => $cat)
                @php
                    $pct   = $totalFeedback > 0 ? round(($cat->total / $totalFeedback) * 100) : 0;
                    $label = $categoryLabels[$cat->service_category] ?? ucfirst(str_replace('_', ' ', $cat->service_category));
                    $barColors = ['bg-primary','bg-info','bg-success','bg-warning','bg-danger','bg-secondary'];
                    $barColor  = $barColors[$i % count($barColors)];
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
                        <div class="progress-bar {{ $barColor }}" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5 small">
                    <i class="bi bi-inbox d-block fs-3 mb-2 opacity-25"></i>No submissions yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>{{-- /ROW 3 --}}


{{-- ══════════════════════════════════════════════════════════════════════
     ROW 4 — RECENT SUBMISSIONS
══════════════════════════════════════════════════════════════════════ --}}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Recent Submissions
                    <span class="badge bg-secondary ms-1 rounded-pill">{{ $recentFeedback->count() }}</span>
                </h5>
                @if($authUser->canManageComplaints())
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
                                <th>Assigned To</th>
                                <th>Submitted</th>
                                @if($authUser->canManageComplaints())
                                <th class="text-end pe-3"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentFeedback as $item)
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-medium text-primary font-monospace small">{{ $item->reference_number }}</span>
                                    @if($item->is_priority)
                                        <span class="badge bg-danger ms-1" style="font-size:9px;">PRIORITY</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold small">{{ $item->patient_name ?: 'Anonymous' }}</div>
                                </td>
                                <td>
                                    @php
                                        $tc = ['complaint'=>'bg-danger','compliment'=>'bg-success','suggestion'=>'bg-info','enquiry'=>'bg-secondary'][$item->feedback_type] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $tc }}">{{ ucfirst($item->feedback_type) }}</span>
                                </td>
                                <td class="text-muted small">
                                    {{ \App\Models\Feedback::SERVICE_CATEGORIES[$item->service_category] ?? ucfirst(str_replace('_', ' ', $item->service_category ?? '')) }}
                                </td>
                                <td>{!! $item->getStatusBadge() !!}</td>
                                <td class="text-muted small">
                                    @if($item->assignedTo)
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                                  style="width:22px;height:22px;font-size:9px;background:#065321;flex-shrink:0;">
                                                {{ strtoupper(substr($item->assignedTo->fname ?? $item->assignedTo->name, 0, 1)) }}
                                            </span>
                                            {{ $item->assignedTo->getFullName() }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">Unassigned</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $item->created_at->diffForHumans() }}</td>
                                @if($authUser->canManageComplaints())
                                <td class="text-end pe-3">
                                    <a href="{{ route('feedback.admin.show', $item) }}"
                                       class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var options = {
        series: [{
            name: 'Submissions',
            data: @json($chartCounts)
        }],
        chart: {
            type: 'bar',
            height: 220,
            toolbar: { show: false },
            fontFamily: 'Poppins, sans-serif',
            background: 'transparent',
            sparkline: { enabled: false }
        },
        plotOptions: {
            bar: {
                columnWidth: '50%',
                borderRadius: 4,
                distributed: false
            }
        },
        colors: ['#0b6b2c'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.4,
                gradientToColors: ['#94c83d'],
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 0.85,
                stops: [0, 100]
            }
        },
        dataLabels: {
            enabled: true,
            style: { fontSize: '11px', colors: ['#fff'] },
            formatter: function(val) { return val > 0 ? val : ''; }
        },
        xaxis: {
            categories: @json($chartDays),
            labels: {
                style: { fontSize: '11px', colors: '#6c757d' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#6c757d' },
                formatter: function(val) { return Math.floor(val); }
            },
            min: 0,
            tickAmount: 4
        },
        grid: {
            borderColor: '#f0f0f0',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } },
            xaxis: { lines: { show: false } }
        },
        tooltip: {
            y: {
                formatter: function(val) { return val + ' submission' + (val !== 1 ? 's' : ''); }
            }
        },
        states: {
            hover: { filter: { type: 'lighten', value: 0.1 } }
        }
    };

    var chart = new ApexCharts(document.querySelector("#submissionsTrendChart"), options);
    chart.render();
});
</script>
@endpush

@endsection
