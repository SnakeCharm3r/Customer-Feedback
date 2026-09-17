@extends('layouts.app')
@section('title', 'Analytics Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
@endpush

@section('content')
<style>
    .analytics-stat-card { border-radius: 10px; padding: 16px 20px; border: 1px solid transparent; }
    .analytics-stat-card .stat-val { font-size: 28px; font-weight: 800; line-height: 1; }
    .analytics-stat-card .stat-lbl { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; opacity: .7; margin-top: 4px; }

    .analytics-filter-bar { background: var(--app-surface, #fff); border: 1px solid var(--app-border, #e9ebec); border-radius: 8px; padding: 14px 18px; }

    .chart-card { overflow: hidden; border-radius: 10px; }
    .chart-card .chart-card-header { padding: 14px 18px 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .chart-card .chart-card-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--tb-text-muted, #878a99); margin: 0; }
    .chart-card .chart-card-total { font-size: 22px; font-weight: 800; color: var(--tb-heading-color, #1a1e2a); line-height: 1; }
    .analytics-chart { position: relative; width: 100%; min-width: 0; }
    .analytics-chart--donut { min-height: 290px; }
    .analytics-chart--trend { min-height: 320px; }
    .analytics-chart-empty { display: flex; min-height: inherit; align-items: center; justify-content: center; flex-direction: column; gap: 8px; padding: 24px; color: var(--tb-text-muted,#878a99); text-align: center; }
    .analytics-chart-empty i { font-size: 28px; opacity: .55; }
    .analytics-chart-empty strong { color: var(--tb-heading-color,#1a1e2a); font-size: 13px; }
    .analytics-chart-empty span { max-width: 280px; font-size: 11px; line-height: 1.5; }
    .analytics-chart .apexcharts-canvas,
    .analytics-chart .apexcharts-svg { max-width: 100% !important; }
    .analytics-chart .apexcharts-legend-text { font-family: Poppins, Arial, sans-serif !important; }
    .analytics-chart .apexcharts-tooltip,
    .analytics-chart .apexcharts-tooltip.apexcharts-theme-light {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: 0 8px 24px rgba(15,23,42,.18) !important;
        color: #172033 !important;
        opacity: 1 !important;
    }
    .analytics-chart .apexcharts-tooltip-series-group {
        background: #ffffff !important;
        color: #172033 !important;
        padding: 9px 12px !important;
    }
    .analytics-chart .apexcharts-tooltip-text,
    .analytics-chart .apexcharts-tooltip-text-y-label,
    .analytics-chart .apexcharts-tooltip-text-y-value,
    .analytics-chart .apexcharts-tooltip-text-z-label,
    .analytics-chart .apexcharts-tooltip-text-z-value {
        color: #172033 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }
    .analytics-chart .apexcharts-tooltip-title {
        background: #f1f5f9 !important;
        border-bottom: 1px solid #cbd5e1 !important;
        color: #172033 !important;
        font-weight: 700 !important;
        padding: 8px 12px !important;
    }

    .tab-pill { display: inline-flex; gap: 4px; background: var(--tb-light, #f3f6f9); border-radius: 8px; padding: 4px; }
    .tab-pill .tab-btn { border: none; background: transparent; border-radius: 6px; padding: 6px 14px; font-size: 12px; font-weight: 600; color: var(--tb-text-muted, #878a99); cursor: pointer; transition: all .18s; }
    .tab-pill .tab-btn.active { background: #fff; color: #065321; box-shadow: 0 1px 4px rgba(0,0,0,.10); }

    [data-bs-theme="dark"] .tab-pill { background: rgba(255,255,255,0.06); }
    [data-bs-theme="dark"] .tab-pill .tab-btn.active { background: rgba(255,255,255,0.10); color: #5bbf7a; box-shadow: none; }
    [data-bs-theme="dark"] .analytics-filter-bar {
        border-color: #344239;
        background: #202923;
        box-shadow: 0 1px 2px rgba(0,0,0,.16);
    }
    [data-bs-theme="dark"] .analytics-filter-bar .form-label { color: #bdc9c0; }
    [data-bs-theme="dark"] .analytics-filter-bar .form-control,
    [data-bs-theme="dark"] .analytics-filter-bar .form-select,
    [data-bs-theme="dark"] .analytics-filter-bar .input-group-text {
        border-color: #435148 !important;
        background-color: #2b352f !important;
        color: #e4ece6 !important;
        box-shadow: none !important;
    }
    [data-bs-theme="dark"] .analytics-filter-bar .form-control::placeholder { color: #96a39a; opacity: 1; }
    [data-bs-theme="dark"] .analytics-filter-bar .form-select option { background: #202923; color: #e4ece6; }
    [data-bs-theme="dark"] .analytics-filter-bar .input-group-text { color: #aebbb2 !important; }
    [data-bs-theme="dark"] .analytics-filter-bar .form-control:focus,
    [data-bs-theme="dark"] .analytics-filter-bar .form-select:focus {
        border-color: #6e9c58 !important;
        background-color: #303c34 !important;
        box-shadow: 0 0 0 2px rgba(148,200,61,.12) !important;
    }
    [data-bs-theme="dark"] .analytics-filter-bar .btn-outline-secondary {
        border-color: #526158;
        background: #2b352f;
        color: #cbd6ce;
    }
    [data-bs-theme="dark"] .analytics-filter-bar .btn-outline-secondary:hover {
        border-color: #6e7e73;
        background: #344139;
        color: #fff;
    }
    .monthly-report-modal .modal-content { border: 1px solid var(--app-border, #e2e8f0); }
    .monthly-report-modal .modal-header,
    .monthly-report-modal .modal-footer { border-color: var(--app-border, #e2e8f0); }
    [data-bs-theme="dark"] .monthly-report-modal .modal-content { border-color: #435148; background: #202923; color: #e4ece6; }
    [data-bs-theme="dark"] .monthly-report-modal .modal-header,
    [data-bs-theme="dark"] .monthly-report-modal .modal-footer { border-color: #344239; }
    [data-bs-theme="dark"] .monthly-report-modal .form-label { color: #bdc9c0; }
    [data-bs-theme="dark"] .monthly-report-modal .form-select { border-color: #435148; background-color: #2b352f; color: #e4ece6; }
    [data-bs-theme="dark"] .monthly-report-modal .form-select option { background: #202923; color: #e4ece6; }
    [data-bs-theme="dark"] .chart-card .chart-card-title { color: var(--dm-text-muted, #94a3b8); }
    [data-bs-theme="dark"] .chart-card .chart-card-total { color: var(--dm-text, #e2e8f0); }
    [data-bs-theme="dark"] .analytics-chart-empty { color: var(--dm-text-muted,#94a3b8); }
    [data-bs-theme="dark"] .analytics-chart-empty strong { color: var(--dm-text,#e2e8f0); }
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip,
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip.apexcharts-theme-dark {
        background: #172033 !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
        box-shadow: 0 8px 28px rgba(0,0,0,.45) !important;
    }
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip-series-group {
        background: #172033 !important;
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip-text,
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip-text-y-label,
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip-text-y-value,
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip-text-z-label,
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip-text-z-value {
        color: #f8fafc !important;
    }
    [data-bs-theme="dark"] .analytics-chart .apexcharts-tooltip-title {
        background: #0f172a !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }

    .themes-card-header { border-bottom: 1px solid var(--tb-border-color,#e9ebec); padding-bottom: 14px !important; }
    .themes-card-subtitle { margin-top: 5px; font-size: 12px; color: var(--tb-text-muted,#878a99); }
    .cat-tab-content, .sentiment-tab-content { display: none; }
    .cat-tab-content.active, .sentiment-tab-content.active { display: block; }
    .sentiment-tab-bar { display: flex; flex-wrap: wrap; gap: 8px; padding: 16px 18px; background: var(--tb-light,#f8fafb); border-bottom: 1px solid var(--tb-border-color,#e9ebec); }
    .sentiment-tab-btn { display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--tb-border-color,#dfe3e8); background: var(--tb-card-bg,#fff); color: var(--tb-body-color,#495057); border-radius: 8px; padding: 8px 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: border-color .18s, background .18s, color .18s, box-shadow .18s; }
    .sentiment-tab-btn .sentiment-count { min-width: 24px; padding: 2px 7px; border-radius: 999px; background: var(--tb-light,#eef1f4); color: var(--tb-text-muted,#64748b); font-size: 10px; text-align: center; }
    .sentiment-tab-btn[data-sentiment="positive"].active { border-color: #6ee7b7; background: #ecfdf5; color: #065f46; box-shadow: 0 1px 4px rgba(6,95,70,.10); }
    .sentiment-tab-btn[data-sentiment="negative"].active { border-color: #fca5a5; background: #fef2f2; color: #991b1b; box-shadow: 0 1px 4px rgba(153,27,27,.10); }
    .sentiment-tab-btn[data-sentiment="neutral"].active { border-color: #cbd5e1; background: #f1f5f9; color: #334155; box-shadow: 0 1px 4px rgba(51,65,85,.10); }
    .theme-table-wrap { overflow-x: auto; }

    .theme-table { width: 100%; min-width: 620px; font-size: 13px; border-collapse: collapse; }
    .theme-table th { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: var(--tb-text-muted,#878a99); padding: 11px 18px; border-bottom: 1px solid var(--tb-border-color,#e9ebec); background: var(--tb-card-bg,#fff); }
    .theme-table td { padding: 12px 18px; border-bottom: 1px solid var(--tb-border-color,#e9ebec); vertical-align: middle; }
    .theme-table tr:last-child td { border-bottom: none; }
    .theme-table tbody tr:hover { background: rgba(6,83,33,.025); }
    .theme-table .theme-rank { width: 58px; color: var(--tb-text-muted,#878a99); }
    .theme-table .theme-count { width: 110px; font-weight: 700; }
    .theme-table .theme-share { width: 42%; min-width: 240px; }
    .pct-bar-wrap { background: var(--tb-light,#f3f6f9); border-radius: 999px; height: 8px; min-width: 100px; }
    .pct-bar { height: 8px; border-radius: 999px; }
    .theme-empty { padding: 42px 20px; text-align: center; color: var(--tb-text-muted,#878a99); }
    .theme-empty i { display: block; margin-bottom: 8px; font-size: 28px; opacity: .4; }
    [data-bs-theme="dark"] .pct-bar-wrap { background: rgba(255,255,255,0.08); }
    [data-bs-theme="dark"] .theme-table th { color: var(--dm-text-muted,#94a3b8); border-color: rgba(255,255,255,0.08); }
    [data-bs-theme="dark"] .theme-table td { border-color: rgba(255,255,255,0.06); }
    [data-bs-theme="dark"] .themes-card-header, [data-bs-theme="dark"] .sentiment-tab-bar { border-color: rgba(255,255,255,0.08); }
    [data-bs-theme="dark"] .sentiment-tab-bar { background: rgba(255,255,255,0.025); }
    [data-bs-theme="dark"] .sentiment-tab-btn { border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04); color: var(--dm-text,#e2e8f0); }
    [data-bs-theme="dark"] .sentiment-tab-btn[data-sentiment="positive"].active { background: rgba(6,95,70,.28); color: #6ee7b7; }
    [data-bs-theme="dark"] .sentiment-tab-btn[data-sentiment="negative"].active { background: rgba(153,27,27,.25); color: #fca5a5; }
    [data-bs-theme="dark"] .sentiment-tab-btn[data-sentiment="neutral"].active { background: rgba(100,116,139,.24); color: #cbd5e1; }

    .weekly-summary-table th { white-space: nowrap; font-size: 10px; text-transform: uppercase; letter-spacing: .04em; color: var(--tb-text-muted,#878a99); }
    .weekly-summary-table th i { font-size: 14px; vertical-align: -2px; }
    .weekly-feedback-row { cursor: pointer; transition: background-color .15s; }
    .weekly-feedback-row:focus { outline: 2px solid rgba(11,138,56,.45); outline-offset: -2px; }
    .weekly-feedback-link { color: var(--tb-heading-color,#1a1e2a); text-decoration: none; }
    .weekly-feedback-link:hover { color: #0b8a38; text-decoration: underline; }
    .feedback-sentiment { display: block; margin-top: 5px; font-size: 10px; font-weight: 600; }
    .feedback-sentiment i { font-size: 7px; vertical-align: 1px; }
    [data-bs-theme="dark"] .weekly-feedback-link { color: var(--dm-text,#e2e8f0); }
    .analytics-date-range { cursor: pointer; background-color: var(--app-surface, #fff) !important; }
    .flatpickr-calendar {
        overflow: hidden;
        border: 1px solid var(--app-border,#dfe7e2);
        border-radius: 9px;
        background: var(--app-surface,#fff);
        box-shadow: 0 12px 30px rgba(6,83,33,.14);
        color: var(--app-text,#17211b);
        font-family: var(--app-font-sans, Poppins, sans-serif);
    }
    .flatpickr-calendar .flatpickr-months,
    .flatpickr-calendar .flatpickr-month,
    .flatpickr-calendar .flatpickr-current-month,
    .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-calendar .flatpickr-current-month input.cur-year {
        background: #0b6b2c !important;
        color: #fff !important;
        fill: #fff !important;
    }
    .flatpickr-calendar .flatpickr-months { padding: 2px 0; }
    .flatpickr-calendar .flatpickr-current-month {
        font-size: 0.9rem;
        font-weight: 400;
    }
    .flatpickr-calendar .flatpickr-current-month .cur-month,
    .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months,
    .flatpickr-calendar .flatpickr-current-month input.cur-year {
        font-weight: 400 !important;
    }
    .flatpickr-calendar .flatpickr-current-month .flatpickr-monthDropdown-months option {
        background: var(--app-surface,#fff);
        color: var(--app-text,#17211b);
    }
    .flatpickr-calendar .flatpickr-prev-month,
    .flatpickr-calendar .flatpickr-next-month { color: #fff !important; fill: #fff !important; }
    .flatpickr-calendar .flatpickr-prev-month svg,
    .flatpickr-calendar .flatpickr-next-month svg { fill: #fff !important; }
    .flatpickr-calendar .flatpickr-prev-month:hover svg,
    .flatpickr-calendar .flatpickr-next-month:hover svg { fill: #c9ed86 !important; }
    .flatpickr-calendar .flatpickr-weekdays,
    .flatpickr-calendar span.flatpickr-weekday {
        background: #f3f8f4;
        color: #526158;
        font-weight: 500;
    }
    .flatpickr-calendar .flatpickr-day { color: #344139; font-weight: 400; }
    .flatpickr-calendar .flatpickr-day:hover,
    .flatpickr-calendar .flatpickr-day:focus { border-color: #bbdca4; background: #eef7e8; }
    .flatpickr-calendar .flatpickr-day.today { border-color: #0b6b2c; color: #0b6b2c; }
    .flatpickr-calendar .flatpickr-day.today:hover { border-color: #0b6b2c; background: #eef7e8; color: #065321; }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange,
    .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover { background: #0b6b2c; border-color: #0b6b2c; color: #fff; }
    .flatpickr-day.inRange { border-color: #dcfce7; background: #dcfce7; box-shadow: -5px 0 0 #dcfce7, 5px 0 0 #dcfce7; }
    [data-bs-theme="dark"] .flatpickr-calendar { border-color: var(--app-border,#303b34); background: var(--app-surface,#171d19); color: var(--app-text,#edf4ef); box-shadow: 0 14px 36px rgba(0,0,0,.36); }
    [data-bs-theme="dark"] .flatpickr-weekdays,
    [data-bs-theme="dark"] span.flatpickr-weekday { background: var(--app-surface-muted,#1c241f); color: #b7c5bb; }
    [data-bs-theme="dark"] .flatpickr-day { color: var(--app-text,#edf4ef); }
    [data-bs-theme="dark"] .flatpickr-day.prevMonthDay,
    [data-bs-theme="dark"] .flatpickr-day.nextMonthDay { color: var(--app-text-muted,#91a096); }
    [data-bs-theme="dark"] .flatpickr-day:hover,
    [data-bs-theme="dark"] .flatpickr-day:focus { border-color: #3e6d4d; background: #26362b; color: #fff; }
    [data-bs-theme="dark"] .flatpickr-day.today { border-color: #94c83d; color: #c9ed86; }
    [data-bs-theme="dark"] .flatpickr-day.inRange { border-color: #234a30; background: #234a30; box-shadow: -5px 0 0 #234a30, 5px 0 0 #234a30; color: #eaf5e3; }
    [data-bs-theme="dark"] .flatpickr-day.selected,
    [data-bs-theme="dark"] .flatpickr-day.startRange,
    [data-bs-theme="dark"] .flatpickr-day.endRange { border-color: #5a9e45; background: #0b6b2c; color: #fff; }
    [data-bs-theme="dark"] .flatpickr-current-month .flatpickr-monthDropdown-months option { background: #171d19; color: #edf4ef; }

    @media (max-width: 767.98px) {
        .chart-card .chart-card-header { align-items: flex-start; padding: 14px 14px 0; }
        .chart-card .card-body { padding-inline: 10px; }
        .chart-card .chart-card-total { font-size: 18px; }
        .analytics-chart--donut { min-height: 310px; }
        .analytics-chart--trend { min-height: 300px; }
        .tab-pill { width: 100%; overflow-x: auto; scrollbar-width: thin; }
        .tab-pill .tab-btn { flex: 0 0 auto; }
        .themes-card-header { align-items: stretch !important; }
        .sentiment-tab-bar { padding: 12px 14px; }
        .sentiment-tab-btn { flex: 1 1 auto; justify-content: center; }
    }

    @media (max-width: 479.98px) {
        .analytics-chart--donut { min-height: 330px; }
        .analytics-chart--trend { min-height: 285px; }
        .sentiment-tab-btn { padding-inline: 10px; }
    }
</style>

{{-- ── Page Header ── --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="mb-0">Analytics Dashboard</h4>
        </div>
    </div>
</div>

{{-- ── Filters ── --}}
<div class="analytics-filter-bar mb-4">
    <form method="GET" action="{{ route('reports.analytics') }}" id="analyticsForm">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-6 col-xl-3">
                <label for="analyticsDateRange" class="form-label small fw-semibold mb-1">Dates Filter</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-calendar-range" aria-hidden="true"></i></span>
                    <input type="text" id="analyticsDateRange"
                           class="form-control analytics-date-range"
                           value="{{ !empty($filters['date_from']) ? $filters['date_from'] . (!empty($filters['date_to']) ? ' to ' . $filters['date_to'] : '') : '' }}"
                           placeholder="Select start and end dates"
                           autocomplete="off" readonly>
                </div>
                <input type="hidden" name="date_from" id="analyticsDateFrom" value="{{ $filters['date_from'] ?? '' }}">
                <input type="hidden" name="date_to" id="analyticsDateTo" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-6 col-md-3 col-xl">
                <label class="form-label small fw-semibold mb-1">Month</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @foreach([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                        <option value="{{ $n }}" {{ ($filters['month'] ?? '') == $n ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl">
                <label class="form-label small fw-semibold mb-1">Year</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">All Years</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ ($filters['year'] ?? '') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl">
                <label class="form-label small fw-semibold mb-1">Source</label>
                <select name="source" class="form-select form-select-sm">
                    <option value="">All Sources</option>
                    @foreach(\App\Models\Feedback::SOURCES as $val => $lbl)
                        <option value="{{ $val }}" {{ ($filters['source'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl">
                <label class="form-label small fw-semibold mb-1">Location</label>
                <select name="location" class="form-select form-select-sm">
                    <option value="">All Locations</option>
                    @foreach($allLocations as $locKey => $locLabel)
                        <option value="{{ $locKey }}" {{ ($filters['location'] ?? '') === $locKey ? 'selected' : '' }}>{{ $locLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl">
                <label class="form-label small fw-semibold mb-1">Department</label>
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ ($filters['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
            <button type="submit" class="btn btn-sm btn-primary px-3">
                <i class="bi bi-funnel me-1"></i>Apply
            </button>
            @if(array_filter($filters ?? []))
            <a href="{{ route('reports.analytics') }}" class="btn btn-sm btn-outline-secondary" title="Clear filters" aria-label="Clear filters">
                <i class="bi bi-x-lg"></i>
            </a>
            @endif
            <button type="button"
                    class="btn btn-sm btn-outline-success"
                    data-bs-toggle="modal"
                    data-bs-target="#monthlyReportModal">
                <i class="bi bi-calendar-month me-1"></i>Monthly Report
            </button>
            <button type="button"
                    class="btn btn-sm btn-outline-success"
                    data-bs-toggle="modal"
                    data-bs-target="#quarterlyReportModal">
                <i class="bi bi-calendar3-range me-1"></i>Quarterly Report
            </button>
            <button type="submit"
                    class="btn btn-sm btn-success"
                    formaction="{{ route('reports.analytics.export.excel') }}"
                    formmethod="GET">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Consolidated Excel
            </button>
        </div>
    </form>
</div>

@php
    $monthlyReportYears = $availableYears->isNotEmpty() ? $availableYears : collect([now()->year]);
    $monthlyDefaultMonth = (int) ($filters['month'] ?? now()->month);
    $monthlyDefaultYear = (int) ($filters['year'] ?? $monthlyReportYears->first() ?? now()->year);
    $quarterlyDefaultQuarter = (int) ceil($monthlyDefaultMonth / 3);
@endphp

<div class="modal fade monthly-report-modal" id="monthlyReportModal" tabindex="-1" aria-labelledby="monthlyReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form method="GET" action="{{ route('reports.analytics.export.monthly') }}">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fs-6" id="monthlyReportModalLabel">Export Monthly Report</h5>
                        <p class="text-muted small mb-0 mt-1">Choose the submission month to include.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="monthlyReportMonth" class="form-label small fw-semibold">Month</label>
                        <select name="month" id="monthlyReportMonth" class="form-select form-select-sm" required>
                            @foreach([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                                <option value="{{ $n }}" {{ $monthlyDefaultMonth === $n ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="monthlyReportYear" class="form-label small fw-semibold">Year</label>
                        <select name="year" id="monthlyReportYear" class="form-select form-select-sm" required>
                            @foreach($monthlyReportYears as $year)
                                <option value="{{ $year }}" {{ $monthlyDefaultYear === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export Monthly Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade monthly-report-modal" id="quarterlyReportModal" tabindex="-1" aria-labelledby="quarterlyReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form method="GET" action="{{ route('reports.analytics.export.quarterly') }}">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fs-6" id="quarterlyReportModalLabel">Export Quarterly Report</h5>
                        <p class="text-muted small mb-0 mt-1">Choose the quarter and reporting year.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quarterlyReportQuarter" class="form-label small fw-semibold">Quarter</label>
                        <select name="quarter" id="quarterlyReportQuarter" class="form-select form-select-sm" required>
                            <option value="1" {{ $quarterlyDefaultQuarter === 1 ? 'selected' : '' }}>Q1 — January to March</option>
                            <option value="2" {{ $quarterlyDefaultQuarter === 2 ? 'selected' : '' }}>Q2 — April to June</option>
                            <option value="3" {{ $quarterlyDefaultQuarter === 3 ? 'selected' : '' }}>Q3 — July to September</option>
                            <option value="4" {{ $quarterlyDefaultQuarter === 4 ? 'selected' : '' }}>Q4 — October to December</option>
                        </select>
                    </div>
                    <div>
                        <label for="quarterlyReportYear" class="form-label small fw-semibold">Year</label>
                        <select name="year" id="quarterlyReportYear" class="form-select form-select-sm" required>
                            @foreach($monthlyReportYears as $year)
                                <option value="{{ $year }}" {{ $monthlyDefaultYear === (int) $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export Quarterly Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── KPI Strip ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="analytics-stat-card card h-100 border-0" style="background:linear-gradient(135deg,#065321,#0b8a38);">
            <div class="stat-val text-white">{{ number_format($totalAll) }}</div>
            <div class="stat-lbl text-white">Total Submissions</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="analytics-stat-card card h-100" style="background:#d1fae5;border-color:#6ee7b7;">
            <div class="stat-val" style="color:#065f46;">{{ number_format($totalPositive) }}</div>
            <div class="stat-lbl" style="color:#065f46;">
                Positive
                @if($totalAll > 0)<span class="ms-1 fw-normal opacity-75">{{ round($totalPositive / $totalAll * 100, 1) }}%</span>@endif
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="analytics-stat-card card h-100" style="background:#fee2e2;border-color:#fca5a5;">
            <div class="stat-val" style="color:#991b1b;">{{ number_format($totalNegative) }}</div>
            <div class="stat-lbl" style="color:#991b1b;">
                Negative
                @if($totalAll > 0)<span class="ms-1 fw-normal opacity-75">{{ round($totalNegative / $totalAll * 100, 1) }}%</span>@endif
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="analytics-stat-card card h-100" style="background:#e5e7eb;border-color:#d1d5db;">
            <div class="stat-val" style="color:#374151;">{{ number_format($totalNeutral) }}</div>
            <div class="stat-lbl" style="color:#374151;">
                Neutral
                @if($totalAll > 0)<span class="ms-1 fw-normal opacity-75">{{ round($totalNeutral / $totalAll * 100, 1) }}%</span>@endif
            </div>
        </div>
    </div>
</div>

{{-- ── Row 1: Sentiment Pie + Collection Means Pie ── --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-md-6">
        <div class="card chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <p class="chart-card-title">Feedback Type</p>
                    <div class="chart-card-total">{{ number_format($totalAll) }}</div>
                </div>
                <span class="badge bg-success-subtle text-success" style="font-size:10px;">Sentiment split</span>
            </div>
            <div class="card-body pb-2">
                <div id="chart-sentiment" class="analytics-chart analytics-chart--donut" aria-label="Feedback sentiment chart"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <p class="chart-card-title">Collection Means</p>
                    <div class="chart-card-total">{{ array_sum(array_column($collectionMeans, 'count')) }}</div>
                </div>
                <span class="badge bg-info-subtle text-info" style="font-size:10px;">By source</span>
            </div>
            <div class="card-body pb-2">
                <div id="chart-collection" class="analytics-chart analytics-chart--donut" aria-label="Feedback collection means chart"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Row 2: Monthly Trend ── --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card chart-card">
            <div class="chart-card-header">
                <div>
                    <p class="chart-card-title">Monthly Trend — {{ $trendYear }}</p>
                    <div class="chart-card-total" style="font-size:14px;font-weight:600;">Positive / Negative / Neutral over the year</div>
                </div>
            </div>
            <div class="card-body pb-2">
                <div id="chart-trend" class="analytics-chart analytics-chart--trend" aria-label="Monthly feedback trend chart"></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Themes by Service Category ── --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card chart-card" id="themesServiceCard">
            <div class="chart-card-header themes-card-header">
                <div>
                    <p class="chart-card-title">Themes by Service Category</p>
                    <p class="themes-card-subtitle mb-0">Choose a service and feedback sentiment to view its complete theme breakdown.</p>
                </div>
                <div class="tab-pill" id="catTabPill" role="tablist" aria-label="Service categories">
                    @foreach($categories as $catKey => $catLabel)
                        <button type="button"
                                class="tab-btn {{ $loop->first ? 'active' : '' }}"
                                id="cat-tab-{{ $catKey }}"
                                data-cat="{{ $catKey }}"
                                role="tab"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                aria-controls="cat-{{ $catKey }}">{{ $catLabel }}</button>
                    @endforeach
                </div>
            </div>
            @foreach($categories as $catKey => $catLabel)
            <div class="cat-tab-content {{ $loop->first ? 'active' : '' }}"
                 id="cat-{{ $catKey }}"
                 role="tabpanel"
                 aria-labelledby="cat-tab-{{ $catKey }}">
                <div class="sentiment-tab-bar" role="tablist" aria-label="{{ $catLabel }} feedback sentiment">
                    @foreach(['positive' => 'Positive', 'negative' => 'Negative', 'neutral' => 'Neutral'] as $sentKey => $sentLabel)
                        <button type="button"
                                class="sentiment-tab-btn {{ $loop->first ? 'active' : '' }}"
                                data-sentiment="{{ $sentKey }}"
                                role="tab"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                aria-controls="themes-{{ $catKey }}-{{ $sentKey }}">
                            {{ $sentLabel }}
                            <span class="sentiment-count">{{ $themesByCat[$catKey][$sentKey]['total'] ?? 0 }}</span>
                        </button>
                    @endforeach
                </div>

                @foreach(['positive' => ['Positive', '#0b8a38'], 'negative' => ['Negative', '#dc3545'], 'neutral' => ['Neutral', '#64748b']] as $sentKey => [$sentLabel, $sentColor])
                @php
                    $themeGroup = $themesByCat[$catKey][$sentKey] ?? ['total' => 0, 'themes' => []];
                @endphp
                <div class="sentiment-tab-content {{ $loop->first ? 'active' : '' }}"
                     id="themes-{{ $catKey }}-{{ $sentKey }}"
                     role="tabpanel">
                    @if(!empty($themeGroup['themes']))
                    <div class="theme-table-wrap">
                        <x-admin.table class="theme-table">
                            <thead>
                                <tr>
                                    <th class="theme-rank">#</th>
                                    <th>Theme</th>
                                    <th class="theme-count">Entries</th>
                                    <th class="theme-share">% of {{ $sentLabel }} Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($themeGroup['themes'] as $theme)
                                <tr>
                                    <td class="theme-rank">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $theme['label'] }}</td>
                                    <td class="theme-count">{{ number_format($theme['count']) }}</td>
                                    <td class="theme-share">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="pct-bar-wrap flex-grow-1" aria-hidden="true">
                                                <div class="pct-bar" style="width:{{ $theme['pct'] }}%;background:{{ $sentColor }};"></div>
                                            </div>
                                            <span class="fw-semibold" style="color:{{ $sentColor }};min-width:48px;text-align:right;">{{ $theme['pct'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </x-admin.table>
                    </div>
                    @else
                    <div class="theme-empty">
                        <i class="bi bi-inbox" aria-hidden="true"></i>
                        <div class="fw-semibold">No {{ strtolower($sentLabel) }} themes for {{ $catLabel }}</div>
                        <div class="small mt-1">Try another sentiment, service category, or report filter.</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── General Summary Sub-View ── --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card chart-card">
            <div class="chart-card-header mb-0 pb-3" style="border-bottom:1px solid var(--tb-border-color,#e9ebec);">
                <div>
                    <h2 class="chart-card-total mb-0" style="font-size:16px;">General Summary</h2>
                </div>
                <span class="badge bg-info-subtle text-info" style="font-size:10px;">{{ count($weeklyRows) }} records</span>
            </div>
            <div class="card-body p-0">
                <x-admin.table class="table-hover weekly-summary-table" id="weeklySummaryTable" style="font-size:12px;" max-height="420px">
                        <thead class="sticky-top" style="background:var(--tb-card-bg,#fff);z-index:1;">
                            <tr>
                                <th class="ps-3"><i class="bi bi-inboxes me-1" aria-hidden="true"></i>Collection Means</th>
                                <th><i class="bi bi-calendar3 me-1" aria-hidden="true"></i>Posting Date</th>
                                <th><i class="bi bi-geo-alt me-1" aria-hidden="true"></i>Location</th>
                                <th class="text-center" title="Telephone number">
                                    <i class="bi bi-telephone" aria-hidden="true"></i><span class="visually-hidden">Telephone number</span>
                                </th>
                                <th><i class="bi bi-tags me-1" aria-hidden="true"></i>Theme</th>
                                <th class="pe-3"><i class="bi bi-chat-square-text me-1" aria-hidden="true"></i>Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($weeklyRows as $f)
                            <tr class="weekly-feedback-row"
                                data-feedback-url="{{ route('feedback.admin.show', $f) }}"
                                tabindex="0"
                                role="link"
                                aria-label="View feedback {{ $f->reference_no }}">
                                <td class="ps-3 fw-semibold">
                                    <a href="{{ route('feedback.admin.show', $f) }}" class="weekly-feedback-link">
                                        {{ $f->getSourceLabel() }}
                                        <i class="bi bi-arrow-up-right ms-1 small" aria-hidden="true"></i>
                                    </a>
                                    <span class="d-block text-muted fw-normal" style="font-size:10px;">{{ $f->reference_no }}</span>
                                </td>
                                <td class="text-nowrap">{{ $f->created_at?->format('d M Y') ?? '—' }}</td>
                                @php $locLabel = \App\Models\Feedback::getLocations(false)[$f->location] ?? null; @endphp
                                <td>
                                    @if($f->isMabinti())
                                        <span class="badge" style="background:#dcfce7;color:#14532d;"><i class="bi bi-shop me-1"></i>Mabinti</span>
                                    @elseif($locLabel)
                                        <span class="badge bg-light text-secondary">{{ $locLabel }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted text-center text-nowrap">{{ $f->phone ?: '—' }}</td>
                                @php $themeLabel = $f->getThemeLabel(); @endphp
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        {{ $themeLabel === '—' ? 'None' : $themeLabel }}
                                    </span>
                                </td>
                                <td class="pe-3">
                                    @php
                                        $ft = $f->getFeedbackTypeLabel();
                                        $sent = $f->getSentimentLabel();
                                        $sentColor = $sent === 'Positive' ? '#0b8a38' : ($sent === 'Negative' ? '#dc3545' : '#64748b');
                                    @endphp
                                    <span class="badge" style="
                                        background:{{ $ft==='Complaint'?'#fee2e2':($ft==='Compliment'?'#d1fae5':($ft==='Suggestion'?'#dbeafe':'#f3e8ff')) }};
                                        color:{{ $ft==='Complaint'?'#991b1b':($ft==='Compliment'?'#065f46':($ft==='Suggestion'?'#1e40af':'#6b21a8')) }};
                                    ">{{ $ft }}</span>
                                    <span class="feedback-sentiment" style="color:{{ $sentColor }};">
                                        <i class="bi bi-circle-fill me-1" aria-hidden="true"></i>{{ $sent }} sentiment
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox d-block fs-2 mb-2 opacity-25"></i>No records for selected filters.
                            </td></tr>
                            @endforelse
                        </tbody>
                </x-admin.table>
            </div>
        </div>
    </div>
</div>

{{-- ── ApexCharts Data (JSON) ── --}}
@php
    $sentLabels = array_map(fn($k) => ucfirst($k), array_keys($sentiment));
    $sentCounts = array_column($sentiment, 'count');
    $sentColors = array_map(fn($k) => match($k) { 'positive' => '#0b8a38', 'negative' => '#dc2626', 'neutral' => '#94a3b8', default => '#64748b' }, array_keys($sentiment));

    $colLabels = array_column($collectionMeans, 'label');
    $colCounts = array_column($collectionMeans, 'count');
    $colColors = ['#0b8a38','#f59e0b','#3b82f6','#8b5cf6','#64748b','#ef4444'];
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateRangeInput = document.getElementById('analyticsDateRange');
    const dateFromInput = document.getElementById('analyticsDateFrom');
    const dateToInput = document.getElementById('analyticsDateTo');

    if (dateRangeInput && dateFromInput && dateToInput && typeof window.flatpickr === 'function') {
        const initialDates = [dateFromInput.value, dateToInput.value].filter(Boolean);

        window.flatpickr(dateRangeInput, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            conjunction: ' to ',
            defaultDate: initialDates,
            showMonths: window.innerWidth >= 992 ? 2 : 1,
            disableMobile: true,
            allowInput: false,
            onChange: function (selectedDates, dateString, instance) {
                dateFromInput.value = selectedDates[0] ? instance.formatDate(selectedDates[0], 'Y-m-d') : '';
                dateToInput.value = selectedDates[1] ? instance.formatDate(selectedDates[1], 'Y-m-d') : '';
            },
        });
    }

    // Service and sentiment tabs remain usable even if the chart library fails to load.
    document.getElementById('themesServiceCard')?.addEventListener('click', function (event) {
        const categoryButton = event.target.closest('#catTabPill .tab-btn');
        if (categoryButton) {
            document.querySelectorAll('#catTabPill .tab-btn').forEach(function (button) {
                button.classList.remove('active');
                button.setAttribute('aria-selected', 'false');
            });
            categoryButton.classList.add('active');
            categoryButton.setAttribute('aria-selected', 'true');

            document.querySelectorAll('#themesServiceCard .cat-tab-content').forEach(function (panel) {
                panel.classList.remove('active');
            });
            document.getElementById('cat-' + categoryButton.dataset.cat)?.classList.add('active');
            return;
        }

        const sentimentButton = event.target.closest('.sentiment-tab-btn');
        if (!sentimentButton) return;

        const categoryPanel = sentimentButton.closest('.cat-tab-content');
        categoryPanel.querySelectorAll('.sentiment-tab-btn').forEach(function (button) {
            button.classList.remove('active');
            button.setAttribute('aria-selected', 'false');
        });
        sentimentButton.classList.add('active');
        sentimentButton.setAttribute('aria-selected', 'true');

        categoryPanel.querySelectorAll('.sentiment-tab-content').forEach(function (panel) {
            panel.classList.remove('active');
        });
        document.getElementById(sentimentButton.getAttribute('aria-controls'))?.classList.add('active');
    });

    const openFeedbackRow = function (row) {
        if (row?.dataset.feedbackUrl) window.location.href = row.dataset.feedbackUrl;
    };
    document.getElementById('weeklySummaryTable')?.addEventListener('click', function (event) {
        if (event.target.closest('a')) return;
        const row = event.target.closest('.weekly-feedback-row');
        if (row) openFeedbackRow(row);
    });
    document.getElementById('weeklySummaryTable')?.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const row = event.target.closest('.weekly-feedback-row');
        if (!row) return;
        event.preventDefault();
        openFeedbackRow(row);
    });

    if (typeof ApexCharts === 'undefined') return;

    const sentimentCounts = @json(array_values($sentCounts));
    const sentimentLabels = @json(array_values($sentLabels));
    const collectionCounts = @json(array_values($colCounts));
    const collectionLabels = @json(array_values($colLabels));
    const trendSeries = [
        { name: 'Positive', data: @json(array_values($trend['positive'])) },
        { name: 'Negative', data: @json(array_values($trend['negative'])) },
        { name: 'Neutral', data: @json(array_values($trend['neutral'])) },
    ];
    const monthLabels = @json(array_values($months));
    const chartRegistry = [];

    const isDark = function () {
        return document.documentElement.getAttribute('data-bs-theme') === 'dark';
    };
    const palette = function () {
        return {
            text: isDark() ? '#aebcb4' : '#64748b',
            heading: isDark() ? '#e7eee9' : '#163223',
            grid: isDark() ? 'rgba(174,188,180,.14)' : 'rgba(11,107,44,.10)',
            tooltip: isDark() ? 'dark' : 'light',
        };
    };
    const hasValues = function (series) {
        return series.flatMap(function (item) { return Array.isArray(item) ? item : (item.data || []); })
            .some(function (value) { return Number(value) > 0; });
    };
    const showEmptyState = function (element, message) {
        element.innerHTML = '<div class="analytics-chart-empty" role="status">'
            + '<i class="bi bi-bar-chart" aria-hidden="true"></i>'
            + '<strong>No chart data available</strong>'
            + '<span>' + message + '</span>'
            + '</div>';
    };
    const registerChart = function (selector, hasData, emptyMessage, optionsFactory) {
        const element = document.querySelector(selector);
        if (!element) return;
        element.setAttribute('role', 'img');

        if (!hasData) {
            showEmptyState(element, emptyMessage);
            return;
        }

        const chart = new ApexCharts(element, optionsFactory());
        chart.render();
        chartRegistry.push({ chart: chart, optionsFactory: optionsFactory });
    };
    const baseChart = function (type, height) {
        const colors = palette();
        return {
            chart: {
                type: type,
                height: height,
                width: '100%',
                background: 'transparent',
                fontFamily: 'Poppins, Arial, sans-serif',
                foreColor: colors.text,
                animations: { enabled: true, easing: 'easeinout', speed: 450 },
                redrawOnParentResize: true,
                redrawOnWindowResize: true,
                toolbar: { show: false },
                zoom: { enabled: false },
            },
            theme: { mode: isDark() ? 'dark' : 'light' },
            grid: { borderColor: colors.grid, strokeDashArray: 4 },
            tooltip: {
                enabled: true,
                theme: colors.tooltip,
                fillSeriesColor: false,
                marker: { show: true },
                style: { fontSize: '12px', fontFamily: 'Poppins, Arial, sans-serif' },
            },
            noData: { text: 'No data for the selected filters', style: { color: colors.text } },
        };
    };
    const donutOptions = function (series, labels, colors) {
        const options = baseChart('donut', 290);
        const theme = palette();
        return Object.assign(options, {
            series: series,
            labels: labels,
            colors: colors,
            stroke: { width: 3, colors: [isDark() ? '#2a3042' : '#ffffff'] },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '11px',
                fontWeight: 500,
                labels: { colors: theme.text },
                markers: { width: 9, height: 9, radius: 9 },
                itemMargin: { horizontal: 9, vertical: 4 },
            },
            dataLabels: {
                enabled: true,
                formatter: function (value) { return value >= 4 ? value.toFixed(1) + '%' : ''; },
                style: { fontSize: '10px', fontWeight: 600, colors: ['#ffffff'] },
                dropShadow: { enabled: false },
            },
            plotOptions: {
                pie: {
                    expandOnClick: true,
                    donut: {
                        size: '62%',
                        labels: {
                            show: true,
                            name: { color: theme.text, fontSize: '11px' },
                            value: { color: theme.heading, fontSize: '20px', fontWeight: 700 },
                            total: {
                                show: true,
                                label: 'Total',
                                color: theme.text,
                                fontSize: '11px',
                                formatter: function (context) {
                                    return context.globals.seriesTotals.reduce(function (sum, value) { return sum + value; }, 0);
                                },
                            },
                        },
                    },
                },
            },
            tooltip: {
                enabled: true,
                theme: theme.tooltip,
                fillSeriesColor: false,
                marker: { show: true },
                style: { fontSize: '12px', fontFamily: 'Poppins, Arial, sans-serif' },
                y: { formatter: function (value) { return value + ' submission' + (value === 1 ? '' : 's'); } },
            },
            responsive: [
                { breakpoint: 768, options: { chart: { height: 310 }, legend: { fontSize: '10px', itemMargin: { horizontal: 6, vertical: 4 } }, plotOptions: { pie: { donut: { size: '66%' } } } } },
                { breakpoint: 480, options: { chart: { height: 330 }, dataLabels: { enabled: false }, legend: { position: 'bottom', horizontalAlign: 'left' }, plotOptions: { pie: { donut: { size: '68%' } } } } },
            ],
        });
    };

    registerChart(
        '#chart-sentiment',
        hasValues([sentimentCounts]),
        'Feedback sentiment will appear after submissions match the selected filters.',
        function () { return donutOptions(sentimentCounts, sentimentLabels, @json(array_values($sentColors))); }
    );

    registerChart(
        '#chart-collection',
        hasValues([collectionCounts]),
        'Collection means will appear after submissions match the selected filters.',
        function () {
            const sourcePalette = ['#0b6b2c', '#e6a400', '#198fb8', '#7c5cc4', '#6c757d', '#dc3545', '#58a931', '#d97706'];
            return donutOptions(collectionCounts, collectionLabels, collectionLabels.map(function (_, index) { return sourcePalette[index % sourcePalette.length]; }));
        }
    );

    registerChart(
        '#chart-trend',
        hasValues(trendSeries),
        'The monthly trend will appear after feedback is recorded for this period.',
        function () {
            const options = baseChart('area', 320);
            const theme = palette();
            return Object.assign(options, {
                series: trendSeries,
                colors: ['#15803d', '#dc3545', '#8b98a5'],
                stroke: { curve: 'smooth', width: [3, 3, 2], lineCap: 'round' },
                markers: { size: 3, strokeWidth: 2, hover: { size: 6 } },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: .24, opacityTo: .025, stops: [0, 94] } },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: monthLabels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { trim: true, style: { colors: monthLabels.map(function () { return theme.text; }), fontSize: '10px' } },
                },
                yaxis: { min: 0, forceNiceScale: true, decimalsInFloat: 0, labels: { formatter: function (value) { return Math.floor(value); }, style: { colors: [theme.text], fontSize: '10px' } } },
                legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px', labels: { colors: theme.text }, markers: { width: 9, height: 9, radius: 9 } },
                tooltip: {
                    enabled: true,
                    shared: true,
                    intersect: false,
                    theme: theme.tooltip,
                    fillSeriesColor: false,
                    marker: { show: true },
                    style: { fontSize: '12px', fontFamily: 'Poppins, Arial, sans-serif' },
                    y: { formatter: function (value) { return value + ' submission' + (value === 1 ? '' : 's'); } },
                },
                responsive: [
                    { breakpoint: 768, options: { chart: { height: 300 }, legend: { position: 'bottom', horizontalAlign: 'center' }, stroke: { width: [2, 2, 2] }, markers: { size: 2 } } },
                    { breakpoint: 480, options: { chart: { height: 285 }, xaxis: { labels: { rotate: -45, rotateAlways: true, hideOverlappingLabels: true } }, grid: { padding: { left: 2, right: 5 } } } },
                ],
            });
        }
    );

    new MutationObserver(function (mutations) {
        const themeChanged = mutations.some(function (mutation) { return mutation.attributeName === 'data-bs-theme'; });
        if (!themeChanged) return;
        chartRegistry.forEach(function (entry) {
            entry.chart.updateOptions(entry.optionsFactory(), false, true);
        });
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
});
</script>
@endsection
