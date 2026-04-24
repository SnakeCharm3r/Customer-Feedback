@extends('layouts.app')
@section('title', 'Reports')

@section('content')
@php
    $months = [
        1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
        7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December',
    ];
    $activeMonth = (int) ($filters['month'] ?? 0);
    $activeYear  = (int) ($filters['year']  ?? 0);
    $currentYear = (int) now()->format('Y');
    $summaryCards = [
        ['label' => 'Total Feedback',  'value' => $summary['total'],          'icon' => 'bi-bar-chart-line', 'class' => 'primary'],
        ['label' => 'Portal',          'value' => $summary['portal'],         'icon' => 'bi-globe2',         'class' => 'success'],
        ['label' => 'Manual / Paper',  'value' => $summary['manual'],         'icon' => 'bi-file-earmark-text','class'=> 'warning'],
        ['label' => 'Other Sources',   'value' => $summary['other'],          'icon' => 'bi-diagram-3',      'class' => 'info'],
        ['label' => 'Reviewed',        'value' => $summary['reviewed'],       'icon' => 'bi-person-check',   'class' => 'secondary'],
        ['label' => 'Pending Review',  'value' => $summary['pending_review'], 'icon' => 'bi-hourglass-split','class' => 'danger'],
    ];
@endphp

{{-- Page Header --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Reports</h4>
                <p class="text-muted mb-0 small mt-1">Feedback reports and weekly general submission sheets.</p>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    @foreach($summaryCards as $card)
        <div class="col-6 col-xl-2 col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-{{ $card['class'] }} bg-{{ $card['class'] }}-subtle"
                         style="width:48px;height:48px;font-size:20px;flex-shrink:0;">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $card['value'] }}</div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Report Tabs --}}
<ul class="nav nav-tabs mb-0" id="reportTabs" role="tablist">
    @if($canViewFeedbackReport)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $canViewFeedbackReport ? 'active' : '' }}" id="feedback-tab"
                data-bs-toggle="tab" data-bs-target="#feedbackReport" type="button" role="tab">
            <i class="bi bi-clipboard-data me-1"></i>Feedback Report
        </button>
    </li>
    @endif
    @if($canViewWeeklyReport)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ !$canViewFeedbackReport ? 'active' : '' }}" id="weekly-tab"
                data-bs-toggle="tab" data-bs-target="#weeklyReport" type="button" role="tab">
            <i class="bi bi-calendar-week me-1"></i>Weekly Report
        </button>
    </li>
    @endif
</ul>

<div class="tab-content border border-top-0 rounded-bottom bg-white p-0" id="reportTabContent">

    {{-- ===================== TAB 1: FEEDBACK REPORT ===================== --}}
    @if($canViewFeedbackReport)
    <div class="tab-pane fade show active p-3" id="feedbackReport" role="tabpanel">

        {{-- Filters --}}
        <form method="GET" action="{{ route('reports.feedback.index') }}" id="feedbackFilterForm">
            <input type="hidden" name="_tab" value="feedback">

            {{-- Filters --}}
            <div class="row g-2 align-items-end">
                <div class="col-md-1 col-lg-1">
                    <label class="form-label small fw-semibold mb-1">Month</label>
                    <select name="month" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $activeMonth === $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 col-lg-1">
                    <label class="form-label small fw-semibold mb-1">Year</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $activeYear === (int)$yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-3">
                    <label class="form-label small fw-semibold mb-1">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control form-control-sm"
                           placeholder="Reference, patient, report text…">
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Source</label>
                    <select name="source" class="form-select form-select-sm">
                        <option value="">All Sources</option>
                        @foreach(\App\Models\Feedback::SOURCES as $val => $lbl)
                            <option value="{{ $val }}" {{ ($filters['source'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        @foreach(\App\Models\Feedback::STATUSES as $val => $lbl)
                            <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Feedback Type</label>
                    <select name="feedback_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach(\App\Models\Feedback::FEEDBACK_TYPES as $val => $lbl)
                            <option value="{{ $val }}" {{ ($filters['feedback_type'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-lg-2">
                    <label class="form-label small fw-semibold mb-1">Reviewer</label>
                    <select name="reviewed_by" class="form-select form-select-sm">
                        <option value="">All Reviewers</option>
                        @foreach($reviewers as $rv)
                            <option value="{{ $rv->id }}" {{ (string)($filters['reviewed_by'] ?? '') === (string)$rv->id ? 'selected' : '' }}>{{ $rv->getFullName() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 col-lg-1">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                        <a href="{{ route('reports.feedback.index', ['_tab' => 'feedback']) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Export buttons --}}
        <div class="d-flex gap-2 my-3 justify-content-end">
            <a href="{{ route('reports.feedback.export.csv', request()->except('_tab')) }}"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route('reports.feedback.export.excel', request()->except('_tab')) }}"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('reports.feedback.export.pdf', request()->except('_tab')) }}"
               target="_blank" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>

        {{-- Table --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small">
                Showing <strong>{{ $reports->firstItem() ?? 0 }}–{{ $reports->lastItem() ?? 0 }}</strong>
                of <strong>{{ $reports->total() }}</strong> records
                @if($activeMonth) — <strong>{{ $months[$activeMonth] }}</strong> @endif
                @if($activeYear) <strong>{{ $activeYear }}</strong> @endif
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-2">Ref #</th>
                        <th>Source</th>
                        <th>Type</th>
                        <th>Report</th>
                        <th>Theme</th>
                        <th>Sentiment</th>
                        <th>Wing</th>
                        <th>Department</th>
                        <th>Reviewer</th>
                        <th>Date Reviewed</th>
                        <th>Assigned To</th>
                        <th>Submitted</th>
                        <th class="text-end pe-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $r)
                    <tr>
                        <td class="ps-2">
                            <div class="fw-semibold text-primary font-monospace" style="font-size:12px;">{{ $r->reference_no }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $r->getStatusLabel() }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $r->getSourceLabel() }}</span></td>
                        <td><span class="badge bg-secondary-subtle text-secondary border">{{ $r->getFeedbackTypeLabel() }}</span></td>
                        <td style="min-width:220px;">
                            <div class="fw-semibold small">{{ $r->patient_name ?: 'Anonymous' }}</div>
                            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($r->report_excerpt, 120) ?: '—' }}</div>
                        </td>
                        <td class="small">{{ $r->getThemeLabel() }}</td>
                        <td>
                            @if($r->sentiment)
                                <span class="badge {{ ['positive'=>'bg-success','negative'=>'bg-danger','neutral'=>'bg-secondary'][$r->sentiment] ?? 'bg-secondary' }}">
                                    {{ $r->getSentimentLabel() }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $r->getWingLabel() }}</td>
                        <td class="small text-muted">{{ $r->department?->name ?? '—' }}</td>
                        <td>
                            <div class="small fw-semibold">{{ $r->reviewedBy?->getFullName() ?? '—' }}</div>
                        </td>
                        <td class="text-muted small text-nowrap">{{ $r->reviewed_at?->format('d M Y') ?? '—' }}</td>
                        <td class="small">{{ $r->assignedTo?->getFullName() ?? '—' }}</td>
                        <td class="text-muted small text-nowrap">{{ $r->created_at?->format('d M Y') ?? '—' }}</td>
                        <td class="text-end pe-2">
                            <a href="{{ route('feedback.admin.show', $r) }}" class="btn btn-xs btn-outline-primary" style="font-size:11px;padding:2px 8px;">Open</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            No records found. Try adjusting your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
            <div class="pt-3">{{ $reports->appends(request()->except('_tab'))->links() }}</div>
        @endif
    </div>
    @endif

    {{-- ===================== TAB 2: WEEKLY REPORT ===================== --}}
    @if($canViewWeeklyReport)
    <div class="tab-pane fade {{ !$canViewFeedbackReport ? 'show active' : '' }} p-3" id="weeklyReport" role="tabpanel">

        <div class="mb-3">
            <p class="text-muted small mb-2">General Submission Sheet — matches the standard weekly QA report format.</p>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('reports.feedback.index') }}" id="weeklyFilterForm">
            <input type="hidden" name="_tab" value="weekly">

            <div class="row g-2 align-items-end mb-3">
                <div class="col-md-1 col-lg-1">
                    <label class="form-label small fw-semibold mb-1">Month</label>
                    <select name="month" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $activeMonth === $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 col-lg-1">
                    <label class="form-label small fw-semibold mb-1">Year</label>
                    <select name="year" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $activeYear === (int)$yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Source</label>
                    <select name="source" class="form-select form-select-sm">
                        <option value="">All Sources</option>
                        @foreach(\App\Models\Feedback::SOURCES as $val => $lbl)
                            <option value="{{ $val }}" {{ ($filters['source'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1">Feedback Type</label>
                    <select name="feedback_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach(\App\Models\Feedback::FEEDBACK_TYPES as $val => $lbl)
                            <option value="{{ $val }}" {{ ($filters['feedback_type'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                        <a href="{{ route('reports.feedback.index', ['_tab' => 'weekly']) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i></a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Export buttons --}}
        <div class="d-flex gap-2 mb-3 justify-content-end">
            <a href="{{ route('reports.weekly.export.csv', request()->except('_tab')) }}"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-filetype-csv me-1"></i>Export CSV
            </a>
            <a href="{{ route('reports.weekly.export.excel', request()->except('_tab')) }}"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('reports.weekly.export.pdf', request()->except('_tab')) }}"
               target="_blank" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>

        {{-- Record count --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small">
                Showing <strong>{{ $weekly->firstItem() ?? 0 }}–{{ $weekly->lastItem() ?? 0 }}</strong>
                of <strong>{{ $weekly->total() }}</strong> records
                @if($activeMonth) — <strong>{{ $months[$activeMonth] }}</strong> @endif
                @if($activeYear) <strong>{{ $activeYear }}</strong> @endif
            </span>
        </div>

        {{-- Weekly table matching the sheet format --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-2">Collection Means</th>
                        <th>Date</th>
                        <th>Month</th>
                        <th>Tel # of Person</th>
                        <th style="min-width:260px;">Comment / Suggestion (Kiswahili)</th>
                        <th>Theme</th>
                        <th>Feedback Type</th>
                        <th>Sentiment</th>
                        <th>Wing</th>
                        <th>Unit</th>
                        <th>Platform</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($weekly as $w)
                    <tr>
                        <td class="ps-2 fw-semibold">{{ $w->getSourceLabel() }}</td>
                        <td class="text-center">{{ $w->created_at?->format('d') }}</td>
                        <td class="text-center">{{ $w->created_at?->format('F') }}</td>
                        <td class="font-monospace small">{{ $w->phone ?? '' }}</td>
                        <td style="min-width:260px;">{{ $w->message ?? $w->overall_experience ?? '' }}</td>
                        <td>{{ $w->getThemeLabel() }}</td>
                        <td>
                            @if($w->feedback_type)
                                <span class="badge {{ ['complaint'=>'bg-danger','compliment'=>'bg-success','suggestion'=>'bg-info text-dark'][$w->feedback_type] ?? 'bg-secondary' }}">
                                    {{ $w->getFeedbackTypeLabel() }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($w->sentiment)
                                <span class="badge {{ ['positive'=>'bg-success','negative'=>'bg-danger','neutral'=>'bg-secondary'][$w->sentiment] ?? 'bg-secondary' }}">
                                    {{ $w->getSentimentLabel() }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $w->getWingLabel() }}</td>
                        <td>{{ $w->department?->name ?? (is_array($w->service_units) && count($w->service_units) ? implode(', ', $w->service_units) : '') }}</td>
                        <td>{{ $w->getServiceCategoryLabel() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                            No records found. Try adjusting the month/year filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($weekly->hasPages())
            <div class="pt-3">{{ $weekly->appends(request()->except('_tab'))->links() }}</div>
        @endif
    </div>
    @endif

</div>

{{-- Preserve active tab on page load --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tab = new URLSearchParams(window.location.search).get('_tab');
    if (tab === 'weekly') {
        var el = document.getElementById('weekly-tab');
        if (el) { new bootstrap.Tab(el).show(); }
    }
});
</script>
@endsection
