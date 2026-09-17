@extends('layouts.app')
@section('title', 'Feedback Reports')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const range = document.getElementById('reportDateRange');
    const from = document.getElementById('reportDateFrom');
    const to = document.getElementById('reportDateTo');

    if (!range || !from || !to || typeof window.flatpickr !== 'function') return;

    window.flatpickr(range, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        conjunction: ' to ',
        defaultDate: [from.value, to.value].filter(Boolean),
        showMonths: window.innerWidth >= 992 ? 2 : 1,
        disableMobile: true,
        allowInput: false,
        onChange: function (dates, value, instance) {
            from.value = dates[0] ? instance.formatDate(dates[0], 'Y-m-d') : '';
            to.value = dates[1] ? instance.formatDate(dates[1], 'Y-m-d') : '';
        },
    });
});
</script>
@endpush

@section('content')
<style>
    /* Keep this information-heavy view compact without shrinking controls
       below a comfortable reading and interaction size. */
    .feedback-reports-page {
        --report-text: 0.75rem;
        --report-meta: 0.66rem;
        --report-label: 0.7rem;
    }

    .feedback-reports-page .page-title-box h4 {
        font-size: 1rem;
    }

    .feedback-reports-page .page-title-box p,
    .feedback-reports-page .breadcrumb {
        font-size: var(--report-meta);
    }

    .feedback-summary-grid {
        margin-bottom: 1rem;
    }

    .feedback-summary-card .card-body {
        min-height: 72px;
        padding: 0.75rem 0.85rem;
    }

    .feedback-summary-icon {
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        font-size: 0.95rem;
    }

    .feedback-summary-value {
        font-size: 0.95rem;
        line-height: 1.1;
    }

    .feedback-summary-label {
        margin-top: 0.18rem;
        font-size: var(--report-label);
        line-height: 1.25;
    }

    .feedback-report-filters .card-body {
        padding: 0.8rem 0.9rem;
    }

    .feedback-report-filters .form-label {
        font-size: var(--report-label);
        line-height: 1.2;
    }

    .feedback-report-filters .form-control,
    .feedback-report-filters .form-select,
    .feedback-report-filters .input-group-text,
    .feedback-reports-page .btn-sm {
        font-size: var(--report-text);
    }

    .feedback-report-filters .form-control,
    .feedback-report-filters .form-select,
    .feedback-report-filters .input-group-text {
        min-height: 32px;
    }

    .feedback-results-card .card-header {
        min-height: 48px;
        padding: 0.55rem 0.9rem;
    }

    .feedback-results-card .card-title,
    .feedback-results-card .badge {
        font-size: var(--report-meta);
    }

    .reports-table {
        table-layout: auto;
        font-size: var(--report-text);
        line-height: 1.4;
    }

    .reports-table thead th {
        padding: 0.65rem 0.7rem;
        font-size: var(--report-label);
        font-weight: 700;
        line-height: 1.25;
        vertical-align: middle;
    }

    .reports-table tbody td {
        padding: 0.68rem 0.7rem;
        vertical-align: middle;
    }

    .reports-table .small {
        font-size: var(--report-text) !important;
        line-height: 1.4;
    }

    .reports-table .report-meta,
    .reports-table .badge {
        font-size: var(--report-meta) !important;
        line-height: 1.25;
    }

    .reports-table .badge {
        padding: 0.28rem 0.45rem;
        font-weight: 600;
        white-space: normal;
    }

    .reports-table .source-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        max-width: 150px;
        padding: 0.3rem 0.62rem;
        border: 1px solid transparent;
        border-radius: 999px;
        color: #475569;
        background: #f8fafc;
        font-size: var(--report-meta) !important;
        font-weight: 650;
        line-height: 1.2;
        text-align: center;
        white-space: nowrap;
    }

    .source-pill--portal {
        color: #166534;
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .source-pill--manual,
    .source-pill--paper-form {
        color: #92400e;
        border-color: #fde68a;
        background: #fffbeb;
    }

    .source-pill--calls {
        color: #1d4ed8;
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .source-pill--walk-in {
        color: #0f766e;
        border-color: #99f6e4;
        background: #f0fdfa;
    }

    .source-pill--ward-visit {
        color: #6d28d9;
        border-color: #ddd6fe;
        background: #f5f3ff;
    }

    .source-pill--social-media {
        color: #7e22ce;
        border-color: #e9d5ff;
        background: #faf5ff;
    }

    .source-pill--sms {
        color: #0369a1;
        border-color: #bae6fd;
        background: #f0f9ff;
    }

    .source-pill--other {
        color: #475569;
        border-color: #e2e8f0;
        background: #f8fafc;
    }

    .report-open-action {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(11, 107, 44, 0.28);
        border-radius: 7px;
        color: var(--ccbrt-brand-800, #065321);
        background: rgba(240, 253, 244, 0.72);
        font-size: 0.72rem;
        line-height: 1;
        text-decoration: none;
        transition: background-color 150ms ease, border-color 150ms ease, color 150ms ease, transform 150ms ease;
    }

    .report-open-action:hover {
        transform: translateY(-1px);
        border-color: var(--ccbrt-brand-700, #0b6b2c);
        color: #ffffff;
        background: var(--ccbrt-brand-700, #0b6b2c);
    }

    .report-open-action:focus-visible {
        outline: 2px solid var(--ccbrt-brand-500, #94c83d);
        outline-offset: 2px;
    }

    .reports-table .report-column {
        min-width: 235px;
        max-width: 330px;
    }

    .reports-table .source-column {
        min-width: 145px;
    }

    .reports-table .response-column {
        min-width: 180px;
        max-width: 260px;
    }

    .report-breakdown-grid { margin-bottom: 1rem; }
    .report-breakdown-card .card-header { padding: 0.65rem 0.8rem; }
    .report-breakdown-card .card-body { max-height: 155px; overflow-y: auto; padding: 0.45rem 0.8rem; }
    .report-breakdown-row { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.42rem 0; border-bottom: 1px solid var(--app-border-soft); }
    .report-breakdown-row:last-child { border-bottom: 0; }
    .report-breakdown-row span { color: var(--app-text-muted); font-size: var(--report-meta); }
    .report-breakdown-row strong { min-width: 28px; color: var(--app-text); font-size: var(--report-text); text-align: right; }

    .report-delay-badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.26rem 0.45rem; border: 1px solid transparent; border-radius: 999px; font-size: var(--report-meta); font-weight: 600; white-space: nowrap; }
    .report-delay-badge--late { color: #991b1b; border-color: #fecaca; background: #fef2f2; }
    .report-delay-badge--ok { color: #047857; border-color: #a7f3d0; background: #ecfdf5; }
    .report-delay-owner { margin-top: 0.25rem; color: var(--app-text-muted); font-size: var(--report-meta); }

    .feedback-report-filters .report-date-range { cursor: pointer; }
    .flatpickr-calendar .flatpickr-months,
    .flatpickr-calendar .flatpickr-month,
    .flatpickr-calendar .flatpickr-current-month,
    .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-calendar .flatpickr-current-month input.cur-year { background: #0b6b2c !important; color: #fff !important; fill: #fff !important; }
    .flatpickr-calendar .flatpickr-current-month,
    .flatpickr-calendar .flatpickr-current-month .cur-month,
    .flatpickr-calendar .flatpickr-current-month input.cur-year { font-weight: 400 !important; }
    .flatpickr-calendar .flatpickr-prev-month svg,
    .flatpickr-calendar .flatpickr-next-month svg { fill: #fff !important; }
    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange { border-color: #0b6b2c; background: #0b6b2c; color: #fff; }
    .flatpickr-day.inRange { border-color: #dcfce7; background: #dcfce7; box-shadow: -5px 0 0 #dcfce7, 5px 0 0 #dcfce7; }

    .feedback-reports-page .table-ref-link {
        font-size: 0.69rem;
    }

    /* The shared navigation is reduced only while this dense report is open. */
    .ccbrt-sidebar .navbar-nav .nav-link {
        min-height: 40px;
        gap: 0.55rem;
        font-size: 0.78rem !important;
    }

    .ccbrt-sidebar .sidebar-nav-icon {
        width: 30px;
        height: 30px;
        flex-basis: 30px;
    }

    .ccbrt-sidebar .navbar-nav .nav-link i,
    .ccbrt-sidebar .sidebar-nav-icon i {
        font-size: 0.9rem !important;
    }

    #page-topbar .user-name-text {
        font-size: 0.78rem;
    }

    #page-topbar .user-name-sub-text {
        font-size: 0.68rem !important;
    }

    @media (max-width: 767.98px) {
        .feedback-summary-card .card-body {
            min-height: 66px;
            padding: 0.65rem;
        }

        .feedback-summary-icon {
            width: 32px;
            height: 32px;
            flex-basis: 32px;
            font-size: 0.85rem;
        }

        .feedback-results-card .card-header {
            align-items: flex-start !important;
        }
    }

    /* ── Reports dark mode overrides ── */
    [data-bs-theme="dark"] .reports-table thead th {
        background: var(--dm-bg-raised, #323a4e) !important;
        border-bottom-color: rgba(255,255,255,0.08) !important;
        color: var(--dm-text-muted, #94a3b8) !important;
    }

    [data-bs-theme="dark"] .reports-table .badge.bg-light {
        background: rgba(255,255,255,0.08) !important;
        border-color: rgba(255,255,255,0.12) !important;
        color: var(--dm-text, #e2e8f0) !important;
    }

    [data-bs-theme="dark"] .reports-table .source-pill {
        color: #cbd5e1;
        border-color: rgba(203, 213, 225, 0.2);
        background: rgba(148, 163, 184, 0.1);
    }

    [data-bs-theme="dark"] .reports-table .source-pill--portal {
        color: #86efac;
        border-color: rgba(134, 239, 172, 0.24);
        background: rgba(22, 101, 52, 0.22);
    }

    [data-bs-theme="dark"] .reports-table :is(.source-pill--manual, .source-pill--paper-form) {
        color: #fde68a;
        border-color: rgba(253, 230, 138, 0.22);
        background: rgba(146, 64, 14, 0.2);
    }

    [data-bs-theme="dark"] .reports-table .source-pill--calls {
        color: #93c5fd;
        border-color: rgba(147, 197, 253, 0.22);
        background: rgba(29, 78, 216, 0.18);
    }

    [data-bs-theme="dark"] .reports-table .source-pill--walk-in {
        color: #99f6e4;
        border-color: rgba(153, 246, 228, 0.22);
        background: rgba(15, 118, 110, 0.18);
    }

    [data-bs-theme="dark"] .reports-table .source-pill--ward-visit {
        color: #c4b5fd;
        border-color: rgba(196, 181, 253, 0.22);
        background: rgba(109, 40, 217, 0.18);
    }

    [data-bs-theme="dark"] .reports-table .source-pill--social-media {
        color: #d8b4fe;
        border-color: rgba(216, 180, 254, 0.22);
        background: rgba(126, 34, 206, 0.18);
    }

    [data-bs-theme="dark"] .reports-table .source-pill--sms {
        color: #7dd3fc;
        border-color: rgba(125, 211, 252, 0.22);
        background: rgba(3, 105, 161, 0.18);
    }

    [data-bs-theme="dark"] .report-open-action {
        border-color: rgba(148, 200, 61, 0.3);
        color: #bef264;
        background: rgba(148, 200, 61, 0.08);
    }

    [data-bs-theme="dark"] .report-open-action:hover {
        border-color: #94c83d;
        color: #102414;
        background: #bef264;
    }

    [data-bs-theme="dark"] .feedback-report-filters {
        border-color: #344239 !important;
        background: #202923;
    }
    [data-bs-theme="dark"] .feedback-report-filters .form-label { color: #bdc9c0; }
    [data-bs-theme="dark"] .feedback-report-filters :is(.form-control, .form-select, .input-group-text) {
        border-color: #435148 !important;
        background-color: #2b352f !important;
        color: #e4ece6 !important;
        box-shadow: none !important;
    }
    [data-bs-theme="dark"] .feedback-report-filters .form-control::placeholder { color: #96a39a; }
    [data-bs-theme="dark"] .report-delay-badge--late { color: #fca5a5; border-color: rgba(248,113,113,.35); background: rgba(153,27,27,.2); }
    [data-bs-theme="dark"] .report-delay-badge--ok { color: #6ee7b7; border-color: rgba(16,185,129,.35); background: rgba(6,95,70,.22); }
    [data-bs-theme="dark"] .flatpickr-calendar { border-color: #344239; background: #171d19; color: #edf4ef; }
    [data-bs-theme="dark"] .flatpickr-weekdays,
    [data-bs-theme="dark"] span.flatpickr-weekday { background: #1c241f; color: #b7c5bb; }
    [data-bs-theme="dark"] .flatpickr-day { color: #edf4ef; }
    [data-bs-theme="dark"] .flatpickr-day.inRange { border-color: #234a30; background: #234a30; box-shadow: -5px 0 0 #234a30, 5px 0 0 #234a30; }

    [data-bs-theme="dark"] .reports-table .fw-semibold.small,
    [data-bs-theme="dark"] .reports-table td .fw-semibold {
        color: var(--dm-text, #e2e8f0) !important;
    }

    [data-bs-theme="dark"] .reports-table td.text-muted .fw-semibold {
        color: var(--dm-text-muted, #94a3b8) !important;
    }
</style>

@php
    $summaryCards = [
        ['label' => 'Total Feedback', 'value' => $summary['total'], 'icon' => 'bi-bar-chart-line', 'class' => 'primary'],
        ['label' => 'Portal', 'value' => $summary['portal'], 'icon' => 'bi-globe2', 'class' => 'success'],
        ['label' => 'Manual Entry', 'value' => $summary['manual'], 'icon' => 'bi-file-earmark-text', 'class' => 'warning'],
        ['label' => 'Other Sources', 'value' => $summary['other'], 'icon' => 'bi-diagram-3', 'class' => 'info'],
        ['label' => 'Reviewed', 'value' => $summary['reviewed'], 'icon' => 'bi-person-check', 'class' => 'secondary'],
        ['label' => 'Pending Review', 'value' => $summary['pending_review'], 'icon' => 'bi-hourglass-split', 'class' => 'danger'],
    ];
@endphp

<div class="feedback-reports-page">
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Feedback Reports</h4>
                <p class="text-muted mb-0 small mt-1">Review all submitted feedback, response ownership, reviewer activity, and source breakdowns.</p>
            </div>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Feedback Reports</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 feedback-summary-grid">
    @foreach($summaryCards as $card)
        <div class="col-6 col-xl-2 col-md-4">
            <div class="card h-100 feedback-summary-card">
                <div class="card-body d-flex align-items-center gap-2">
                    <div class="feedback-summary-icon rounded-circle d-flex align-items-center justify-content-center text-{{ $card['class'] }} bg-{{ $card['class'] }}-subtle">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="feedback-summary-value fw-bold">{{ $card['value'] }}</div>
                        <div class="feedback-summary-label text-muted">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($canViewFeedbackReport)
<div class="card mb-3 feedback-report-filters">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.feedback.index') }}" id="feedbackReportFilters">
            <div class="row g-2 align-items-end">

                {{-- Search --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label small fw-semibold mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Reference, patient, report text…">
                    </div>
                </div>

                {{-- Date range --}}
                <div class="col-12 col-md-6 col-xl-3">
                    <label for="reportDateRange" class="form-label small fw-semibold mb-1">Dates Filter</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-calendar-range" aria-hidden="true"></i></span>
                        <input type="text" id="reportDateRange" class="form-control report-date-range"
                               value="{{ !empty($filters['date_from']) ? $filters['date_from'] . (!empty($filters['date_to']) ? ' to ' . $filters['date_to'] : '') : '' }}"
                               placeholder="Select start and end dates" autocomplete="off" readonly>
                    </div>
                    <input type="hidden" name="date_from" id="reportDateFrom" value="{{ $filters['date_from'] ?? '' }}">
                    <input type="hidden" name="date_to" id="reportDateTo" value="{{ $filters['date_to'] ?? '' }}">
                </div>

                {{-- Source --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Source</label>
                    <select name="source" class="form-select form-select-sm">
                        <option value="">All Sources</option>
                        @foreach(\App\Models\Feedback::SOURCES as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['source'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Department --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Department</label>
                    <select name="department_id" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ (string)($filters['department_id'] ?? '') === (string)$department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Location --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Location</label>
                    <select name="location" class="form-select form-select-sm">
                        <option value="">All Locations</option>
                        @foreach($locations as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['location'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Ward / Wing --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Ward / Wing</label>
                    <select name="wing" class="form-select form-select-sm">
                        <option value="">All Wards / Wings</option>
                        @foreach(\App\Models\Feedback::WINGS as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['wing'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Theme --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Theme</label>
                    <select name="theme" class="form-select form-select-sm">
                        <option value="">All Themes</option>
                        @foreach(\App\Models\Feedback::THEMES as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['theme'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        @foreach(\App\Models\Feedback::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @if($value === 'new')
                                <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Responsible user --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Responsible</label>
                    <select name="assigned_to" class="form-select form-select-sm">
                        <option value="">All Responsible Users</option>
                        <option value="unassigned" {{ ($filters['assigned_to'] ?? '') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                        @foreach($assignableUsers as $assignedUser)
                            <option value="{{ $assignedUser->id }}" {{ (string)($filters['assigned_to'] ?? '') === (string)$assignedUser->id ? 'selected' : '' }}>{{ $assignedUser->getFullName() }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Review delay --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Review Timing</label>
                    <select name="delay" class="form-select form-select-sm">
                        <option value="">All Timings</option>
                        <option value="delayed" {{ ($filters['delay'] ?? '') === 'delayed' ? 'selected' : '' }}>Delayed / Overdue</option>
                        <option value="on_time" {{ ($filters['delay'] ?? '') === 'on_time' ? 'selected' : '' }}>On Time</option>
                    </select>
                </div>

                {{-- Feedback Type --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Type</label>
                    <select name="feedback_type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        @foreach(\App\Models\Feedback::FEEDBACK_TYPES as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['feedback_type'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Reviewer --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="form-label small fw-semibold mb-1">Reviewer</label>
                    <select name="reviewed_by" class="form-select form-select-sm">
                        <option value="">All Reviewers</option>
                        @foreach($reviewers as $reviewer)
                            <option value="{{ $reviewer->id }}" {{ (string)($filters['reviewed_by'] ?? '') === (string)$reviewer->id ? 'selected' : '' }}>{{ $reviewer->getFullName() }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="col-12 col-xl-auto ms-xl-auto">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        @if(array_filter($filters ?? []))
                        <a href="{{ route('reports.feedback.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-lg"></i>
                        </a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="row g-2 report-breakdown-grid">
    @foreach([
        ['key' => 'wards', 'title' => 'By Ward / Wing', 'icon' => 'bi-hospital'],
        ['key' => 'themes', 'title' => 'By Theme', 'icon' => 'bi-tags'],
        ['key' => 'locations', 'title' => 'By Location', 'icon' => 'bi-geo-alt'],
    ] as $breakdown)
        <div class="col-12 col-md-6 col-xl-3">
            <section class="card h-100 report-breakdown-card">
                <header class="card-header d-flex align-items-center justify-content-between">
                    <h2 class="card-title mb-0 small fw-semibold">
                        <i class="bi {{ $breakdown['icon'] }} me-1" aria-hidden="true"></i>{{ $breakdown['title'] }}
                    </h2>
                    <span class="badge bg-secondary-subtle text-secondary">{{ collect($breakdowns[$breakdown['key']] ?? [])->sum('count') }}</span>
                </header>
                <div class="card-body">
                    @forelse($breakdowns[$breakdown['key']] ?? [] as $item)
                        <div class="report-breakdown-row">
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ number_format($item['count']) }}</strong>
                        </div>
                    @empty
                        <p class="text-muted small mb-0 py-2">No matching feedback.</p>
                    @endforelse
                </div>
            </section>
        </div>
    @endforeach

    <div class="col-12 col-md-6 col-xl-3">
        <section class="card h-100 report-breakdown-card">
            <header class="card-header">
                <h2 class="card-title mb-0 small fw-semibold">
                    <i class="bi bi-stopwatch me-1" aria-hidden="true"></i>Review Delays
                </h2>
            </header>
            <div class="card-body">
                <div class="report-breakdown-row">
                    <span>Delayed beyond {{ \App\Models\Feedback::REVIEW_SLA_HOURS }} hours</span>
                    <strong>{{ number_format($delaySummary['total'] ?? 0) }}</strong>
                </div>
                <div class="report-breakdown-row">
                    <span>Delayed and unassigned</span>
                    <strong>{{ number_format($delaySummary['unassigned'] ?? 0) }}</strong>
                </div>
                <p class="text-muted mb-0 mt-2" style="font-size:var(--report-meta);">
                    Delay is measured from submission to first review.
                </p>
            </div>
        </section>
    </div>
</div>

<x-admin.table-panel
    class="feedback-results-card"
    title="Results"
    :count="$reports->total()"
    :filtered="(bool) array_filter($filters ?? [])">
    <x-slot:actions>
            <button type="submit" form="feedbackReportFilters" formaction="{{ route('reports.feedback.export.csv') }}" formmethod="GET" class="btn btn-outline-success btn-sm">
                <i class="bi bi-filetype-csv me-1"></i>CSV
            </button>
            <button type="submit" form="feedbackReportFilters" formaction="{{ route('reports.feedback.export.excel') }}" formmethod="GET" class="btn btn-outline-success btn-sm">
                <i class="bi bi-file-earmark-excel me-1"></i>Excel
            </button>
            <button type="submit" form="feedbackReportFilters" formaction="{{ route('reports.feedback.export.pdf') }}" formmethod="GET" formtarget="_blank" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-filetype-pdf me-1"></i>PDF
            </button>
    </x-slot:actions>

            <x-admin.table class="table-hover reports-table">
                <thead>
                    <tr>
                        <th class="ps-3 text-nowrap">Ref #</th>
                        <th class="source-column">Source</th>
                        <th class="report-column">Report</th>
                        <th class="d-none d-xl-table-cell">Ward / Theme / Location</th>
                        <th class="d-none d-lg-table-cell">Reviewer</th>
                        <th class="d-none d-xl-table-cell response-column">Reviewer Response</th>
                        <th class="d-none d-lg-table-cell">Assigned User</th>
                        <th class="text-nowrap">Review Timing</th>
                        <th class="text-nowrap">Timeline</th>
                        <th class="text-end pe-3 text-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        @php
                            $latestResponse = $report->latest_response;
                        @endphp
                        <tr>
                            <td class="ps-3 text-nowrap">
                                <div class="table-ref-link">{{ $report->reference_no }}</div>
                                <div class="text-muted report-meta">{{ $report->getTableStatusLabel() }} • {{ $report->getFeedbackTypeLabel() }}</div>
                            </td>
                            <td class="source-column">
                                <span class="source-pill source-pill--{{ str_replace('_', '-', $report->source ?: 'other') }}">
                                    {{ $report->getSourceLabel() }}
                                </span>
                                <div class="text-muted mt-1 report-meta">{{ $report->getServiceCategoryLabel() }}</div>
                            </td>
                            <td class="report-column">
                                <div class="fw-semibold small">{{ $report->patient_name ?: 'Anonymous / Not Provided' }}</div>
                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($report->report_excerpt, 140) ?: 'No report text available.' }}</div>
                                <div class="mt-1 d-lg-none">
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $report->getSubmitterRoleLabel() }}</span>
                                </div>
                            </td>
                            <td class="d-none d-xl-table-cell">
                                <div class="small fw-semibold">{{ $report->getWingLabel() === '—' ? 'No ward specified' : $report->getWingLabel() }}</div>
                                <div class="text-muted report-meta">{{ $report->getThemeLabel() === '—' ? 'No theme' : $report->getThemeLabel() }}</div>
                                <div class="text-muted report-meta">{{ $locations[$report->location] ?? ($report->location ? ucfirst(str_replace('_', ' ', $report->location)) : 'No location') }}</div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <div class="small fw-semibold">{{ $report->reviewedBy?->getFullName() ?? 'Not yet reviewed' }}</div>
                                <div class="text-muted report-meta">{{ $report->reviewedBy?->getRoleLabel() ?? '—' }}</div>
                            </td>
                            <td class="d-none d-xl-table-cell response-column">
                                <div class="small text-muted">{{ \Illuminate\Support\Str::limit($latestResponse?->content ?? 'No reviewer response recorded.', 120) }}</div>
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <div class="small fw-semibold">{{ $report->assignedTo?->getFullName() ?? 'Unassigned' }}</div>
                                <div class="text-muted report-meta">{{ $report->assignedTo?->getRoleLabel() ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="report-delay-badge {{ $report->isReviewDelayed() ? 'report-delay-badge--late' : 'report-delay-badge--ok' }}">
                                    <i class="bi {{ $report->isReviewDelayed() ? 'bi-exclamation-circle' : 'bi-check-circle' }}" aria-hidden="true"></i>
                                    {{ $report->getReviewDelayLabel() }}
                                </span>
                                @if($report->isReviewDelayed())
                                    <div class="report-delay-owner">Owner: {{ $report->getReviewDelayOwnerLabel() }}</div>
                                @endif
                            </td>
                            <td class="text-muted small text-nowrap">
                                <div><span class="fw-semibold">Created:</span> {{ $report->created_at?->format('d M Y, H:i') ?? '—' }}</div>
                                <div><span class="fw-semibold">Reviewed:</span> {{ $report->reviewed_at?->format('d M Y, H:i') ?? '—' }}</div>
                            </td>
                            <td class="text-end pe-3 text-nowrap">
                                <a href="{{ route('feedback.admin.show', $report) }}"
                                   class="report-open-action"
                                   aria-label="Open feedback report {{ $report->reference_no }}"
                                   title="Open report details">
                                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                    <span class="visually-hidden">Open report</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                <p class="fw-medium mb-1">No feedback report records found</p>
                                <p class="small mb-0">Try adjusting your filters or export the current empty result set.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-admin.table>

    @if($reports->hasPages())
        <x-slot:footer>
            {{ $reports->links() }}
        </x-slot:footer>
    @endif
</x-admin.table-panel>
@else
<div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span>You do not have permission to view the detailed feedback report table.</span>
</div>
@endif
</div>
@endsection
