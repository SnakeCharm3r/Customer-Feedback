@extends('layouts.app')
@section('title', 'Analytics Dashboard')

@section('content')
<style>
    .analytics-stat-card { border-radius: 10px; padding: 16px 20px; border: 1px solid transparent; }
    .analytics-stat-card .stat-val { font-size: 28px; font-weight: 800; line-height: 1; }
    .analytics-stat-card .stat-lbl { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; opacity: .7; margin-top: 4px; }

    .analytics-filter-bar { background: var(--tb-card-bg, #fff); border: 1px solid var(--tb-border-color, #e9ebec); border-radius: 8px; padding: 14px 18px; }

    .chart-card { overflow: hidden; border-radius: 10px; }
    .chart-card .chart-card-header { padding: 14px 18px 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .chart-card .chart-card-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--tb-text-muted, #878a99); margin: 0; }
    .chart-card .chart-card-total { font-size: 22px; font-weight: 800; color: var(--tb-heading-color, #1a1e2a); line-height: 1; }
    .analytics-chart { position: relative; width: 100%; min-width: 0; }
    .analytics-chart--donut { min-height: 290px; }
    .analytics-chart--trend { min-height: 320px; }
    .analytics-chart--bar { min-height: 320px; }
    .analytics-chart-empty { display: flex; min-height: inherit; align-items: center; justify-content: center; flex-direction: column; gap: 8px; padding: 24px; color: var(--tb-text-muted,#878a99); text-align: center; }
    .analytics-chart-empty i { font-size: 28px; opacity: .55; }
    .analytics-chart-empty strong { color: var(--tb-heading-color,#1a1e2a); font-size: 13px; }
    .analytics-chart-empty span { max-width: 280px; font-size: 11px; line-height: 1.5; }
    .analytics-chart .apexcharts-canvas,
    .analytics-chart .apexcharts-svg { max-width: 100% !important; }
    .analytics-chart .apexcharts-legend-text { font-family: Poppins, Arial, sans-serif !important; }

    .tab-pill { display: inline-flex; gap: 4px; background: var(--tb-light, #f3f6f9); border-radius: 8px; padding: 4px; }
    .tab-pill .tab-btn { border: none; background: transparent; border-radius: 6px; padding: 6px 14px; font-size: 12px; font-weight: 600; color: var(--tb-text-muted, #878a99); cursor: pointer; transition: all .18s; }
    .tab-pill .tab-btn.active { background: #fff; color: #065321; box-shadow: 0 1px 4px rgba(0,0,0,.10); }

    [data-bs-theme="dark"] .tab-pill { background: rgba(255,255,255,0.06); }
    [data-bs-theme="dark"] .tab-pill .tab-btn.active { background: rgba(255,255,255,0.10); color: #5bbf7a; box-shadow: none; }
    [data-bs-theme="dark"] .analytics-filter-bar { border-color: rgba(255,255,255,0.08); }
    [data-bs-theme="dark"] .chart-card .chart-card-title { color: var(--dm-text-muted, #94a3b8); }
    [data-bs-theme="dark"] .chart-card .chart-card-total { color: var(--dm-text, #e2e8f0); }
    [data-bs-theme="dark"] .analytics-chart-empty { color: var(--dm-text-muted,#94a3b8); }
    [data-bs-theme="dark"] .analytics-chart-empty strong { color: var(--dm-text,#e2e8f0); }

    .cat-tab-content { display: none; }
    .cat-tab-content.active { display: block; }

    .theme-table { width: 100%; font-size: 12px; border-collapse: collapse; }
    .theme-table th { font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: var(--tb-text-muted,#878a99); padding: 6px 10px; border-bottom: 1px solid var(--tb-border-color,#e9ebec); }
    .theme-table td { padding: 7px 10px; border-bottom: 1px solid var(--tb-border-color,#e9ebec); vertical-align: middle; }
    .theme-table tr:last-child td { border-bottom: none; }
    .pct-bar-wrap { background: var(--tb-light,#f3f6f9); border-radius: 999px; height: 6px; min-width: 60px; }
    .pct-bar { height: 6px; border-radius: 999px; }
    [data-bs-theme="dark"] .pct-bar-wrap { background: rgba(255,255,255,0.08); }
    [data-bs-theme="dark"] .theme-table th { color: var(--dm-text-muted,#94a3b8); border-color: rgba(255,255,255,0.08); }
    [data-bs-theme="dark"] .theme-table td { border-color: rgba(255,255,255,0.06); }

    @media (max-width: 767.98px) {
        .chart-card .chart-card-header { align-items: flex-start; padding: 14px 14px 0; }
        .chart-card .card-body { padding-inline: 10px; }
        .chart-card .chart-card-total { font-size: 18px; }
        .analytics-chart--donut { min-height: 310px; }
        .analytics-chart--trend { min-height: 300px; }
        .analytics-chart--bar { min-height: 360px; }
        .tab-pill { width: 100%; overflow-x: auto; scrollbar-width: thin; }
        .tab-pill .tab-btn { flex: 0 0 auto; }
    }

    @media (max-width: 479.98px) {
        .analytics-chart--donut { min-height: 330px; }
        .analytics-chart--trend { min-height: 285px; }
        .analytics-chart--bar { min-height: 390px; }
    }
</style>

{{-- ── Page Header ── --}}
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Analytics Dashboard</h4>
                <p class="text-muted mb-0 small mt-1">Consolidated feedback analytics — sentiment, collection means, themes by category and monthly trends.</p>
            </div>
            <div class="page-title-right d-flex align-items-center gap-2">
                <a href="{{ route('reports.feedback.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-table me-1"></i>Feedback Table
                </a>
                <a href="{{ route('reports.analytics.export.excel', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Consolidated Excel
                </a>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Analytics</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- ── Filters ── --}}
<div class="analytics-filter-bar mb-4">
    <form method="GET" action="{{ route('reports.analytics') }}" id="analyticsForm">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label small fw-semibold mb-1">Month</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @foreach([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                        <option value="{{ $n }}" {{ ($filters['month'] ?? '') == $n ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label small fw-semibold mb-1">Year</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">All Years</option>
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ ($filters['year'] ?? '') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label small fw-semibold mb-1">Source</label>
                <select name="source" class="form-select form-select-sm">
                    <option value="">All Sources</option>
                    @foreach(\App\Models\Feedback::SOURCES as $val => $lbl)
                        <option value="{{ $val }}" {{ ($filters['source'] ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label small fw-semibold mb-1">Location</label>
                <select name="location" class="form-select form-select-sm">
                    <option value="">All Locations</option>
                    @foreach($allLocations as $locKey => $locLabel)
                        <option value="{{ $locKey }}" {{ ($filters['location'] ?? '') === $locKey ? 'selected' : '' }}>{{ $locLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label class="form-label small fw-semibold mb-1">Department</label>
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ ($filters['department_id'] ?? '') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-xl-auto ms-xl-auto">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-3">
                        <i class="bi bi-funnel me-1"></i>Apply
                    </button>
                    @if(array_filter($filters ?? []))
                    <a href="{{ route('reports.analytics') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    @endif
                    <a href="{{ route('reports.analytics.export.excel', request()->query()) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-excel me-1"></i>Export
                    </a>
                </div>
            </div>
        </div>
    </form>
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

{{-- ── Row 3: General Feedback Themes ── --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-xl-5">
        <div class="card chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <p class="chart-card-title">General Feedback</p>
                    <div class="chart-card-total">{{ count($generalThemes) }} themes</div>
                </div>
            </div>
            <div class="card-body pb-2">
                <div id="chart-general-themes" class="analytics-chart analytics-chart--bar" aria-label="General feedback themes chart"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-7">
        <div class="card chart-card h-100">
            <div class="chart-card-header mb-3">
                <div>
                    <p class="chart-card-title">Themes by Service Category</p>
                </div>
                <div class="tab-pill" id="catTabPill">
                    @foreach($categories as $catKey => $catLabel)
                        <button class="tab-btn {{ $loop->first ? 'active' : '' }}" data-cat="{{ $catKey }}">{{ $catLabel }}</button>
                    @endforeach
                </div>
            </div>
            <div class="card-body pt-0">
                @foreach($categories as $catKey => $catLabel)
                <div class="cat-tab-content {{ $loop->first ? 'active' : '' }}" id="cat-{{ $catKey }}">
                    <div class="row g-3">
                        {{-- Positive --}}
                        <div class="col-12 col-lg-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge" style="background:#d1fae5;color:#065f46;font-size:10px;">Positive</span>
                                <span class="text-muted small">{{ $themesByCat[$catKey]['positive']['total'] ?? 0 }} entries</span>
                            </div>
                            @if(!empty($themesByCat[$catKey]['positive']['themes']))
                                <table class="theme-table">
                                    <thead><tr><th>Theme</th><th style="width:50px;">Count</th><th style="width:80px;">%</th></tr></thead>
                                    <tbody>
                                    @foreach($themesByCat[$catKey]['positive']['themes'] as $t)
                                    <tr>
                                        <td>{{ $t['label'] }}</td>
                                        <td class="fw-semibold">{{ $t['count'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="pct-bar-wrap flex-grow-1">
                                                    <div class="pct-bar" style="width:{{ $t['pct'] }}%;background:#065f46;"></div>
                                                </div>
                                                <span style="font-size:10px;color:#065f46;min-width:30px;">{{ $t['pct'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted small fst-italic">No positive entries.</p>
                            @endif
                        </div>
                        {{-- Negative --}}
                        <div class="col-12 col-lg-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:10px;">Negative</span>
                                <span class="text-muted small">{{ $themesByCat[$catKey]['negative']['total'] ?? 0 }} entries</span>
                            </div>
                            @if(!empty($themesByCat[$catKey]['negative']['themes']))
                                <table class="theme-table">
                                    <thead><tr><th>Theme</th><th style="width:50px;">Count</th><th style="width:80px;">%</th></tr></thead>
                                    <tbody>
                                    @foreach($themesByCat[$catKey]['negative']['themes'] as $t)
                                    <tr>
                                        <td>{{ $t['label'] }}</td>
                                        <td class="fw-semibold">{{ $t['count'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="pct-bar-wrap flex-grow-1">
                                                    <div class="pct-bar" style="width:{{ $t['pct'] }}%;background:#991b1b;"></div>
                                                </div>
                                                <span style="font-size:10px;color:#991b1b;min-width:30px;">{{ $t['pct'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted small fst-italic">No negative entries.</p>
                            @endif
                        </div>
                        {{-- Neutral --}}
                        <div class="col-12 col-lg-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge" style="background:#e5e7eb;color:#374151;font-size:10px;">Neutral</span>
                                <span class="text-muted small">{{ $themesByCat[$catKey]['neutral']['total'] ?? 0 }} entries</span>
                            </div>
                            @if(!empty($themesByCat[$catKey]['neutral']['themes']))
                                <table class="theme-table">
                                    <thead><tr><th>Theme</th><th style="width:50px;">Count</th><th style="width:80px;">%</th></tr></thead>
                                    <tbody>
                                    @foreach($themesByCat[$catKey]['neutral']['themes'] as $t)
                                    <tr>
                                        <td>{{ $t['label'] }}</td>
                                        <td class="fw-semibold">{{ $t['count'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <div class="pct-bar-wrap flex-grow-1">
                                                    <div class="pct-bar" style="width:{{ $t['pct'] }}%;background:#64748b;"></div>
                                                </div>
                                                <span style="font-size:10px;color:#475569;min-width:30px;">{{ $t['pct'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted small fst-italic">No neutral entries.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── General Feedback Data Table ── --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card chart-card">
            <div class="chart-card-header mb-0 pb-3" style="border-bottom:1px solid var(--tb-border-color,#e9ebec);">
                <div>
                    <p class="chart-card-title">General Customer Feedback</p>
                    <div class="chart-card-total" style="font-size:16px;">Theme breakdown — all categories</div>
                </div>
                <span class="badge bg-secondary-subtle text-secondary" style="font-size:10px;">{{ $generalTotal }} total entries</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="theme-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="ps-3">Theme</th>
                                <th style="width:80px;">Count</th>
                                <th style="width:120px;">% Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($generalThemes as $t)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $t['label'] }}</td>
                                <td>{{ $t['count'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="pct-bar-wrap flex-grow-1">
                                            <div class="pct-bar" style="width:{{ $t['pct'] }}%;background:#065321;"></div>
                                        </div>
                                        <span style="font-size:10px;color:#065321;min-width:34px;">{{ $t['pct'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3 small fst-italic">No data for selected filters.</td></tr>
                            @endforelse
                            @if(!empty($generalThemes))
                            <tr style="background:var(--tb-light,#f3f6f9);">
                                <td class="ps-3 fw-bold small">GRAND TOTAL</td>
                                <td class="fw-bold">{{ $generalTotal }}</td>
                                <td class="fw-bold">100%</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Weekly Summary Sub-View ── --}}
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card chart-card">
            <div class="chart-card-header mb-0 pb-3" style="border-bottom:1px solid var(--tb-border-color,#e9ebec);">
                <div>
                    <p class="chart-card-title">Weekly Summary</p>
                    <div class="chart-card-total" style="font-size:16px;">General Submission Sheet</div>
                </div>
                <span class="badge bg-info-subtle text-info" style="font-size:10px;">{{ count($weeklyRows) }} records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:420px;overflow-y:auto;">
                    <table class="table table-hover align-middle mb-0" style="font-size:12px;">
                        <thead class="sticky-top" style="background:var(--tb-card-bg,#fff);z-index:1;">
                            <tr>
                                <th class="ps-3">Collection Means</th>
                                <th>Date</th>
                                <th>Month</th>
                                <th>Location</th>
                                <th>Tel #</th>
                                <th style="min-width:220px;">Comment / Suggestion</th>
                                <th>Theme</th>
                                <th>Feedback Type</th>
                                <th>Sentiment</th>
                                <th>Wing</th>
                                <th>Unit</th>
                                <th>Satisfied?</th>
                                <th class="pe-3">Platform</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($weeklyRows as $f)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $f->getSourceLabel() }}</td>
                                <td>{{ $f->created_at?->format('d') }}</td>
                                <td>{{ $f->created_at?->format('M') }}</td>
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
                                <td class="text-muted">{{ $f->phone ?: '—' }}</td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit($f->message ?? $f->overall_experience ?? '', 100) }}</td>
                                <td><span class="badge bg-secondary-subtle text-secondary">{{ $f->getThemeLabel() }}</span></td>
                                <td>
                                    @php $ft = $f->getFeedbackTypeLabel(); @endphp
                                    <span class="badge" style="
                                        background:{{ $ft==='Complaint'?'#fee2e2':($ft==='Compliment'?'#d1fae5':($ft==='Suggestion'?'#dbeafe':'#f3e8ff')) }};
                                        color:{{ $ft==='Complaint'?'#991b1b':($ft==='Compliment'?'#065f46':($ft==='Suggestion'?'#1e40af':'#6b21a8')) }};
                                    ">{{ $ft }}</span>
                                </td>
                                <td>
                                    @php $sent = $f->getSentimentLabel(); @endphp
                                    <span class="badge" style="
                                        background:{{ $sent==='Positive'?'#d1fae5':($sent==='Negative'?'#fee2e2':'#e5e7eb') }};
                                        color:{{ $sent==='Positive'?'#065f46':($sent==='Negative'?'#991b1b':'#374151') }};
                                    ">{{ $sent }}</span>
                                </td>
                                <td class="text-muted">{{ $f->getWingLabel() }}</td>
                                <td class="text-muted">{{ $f->department?->name ?? (is_array($f->service_units) ? implode(', ', $f->service_units) : ($f->service_units ?? '—')) }}</td>
                                <td>
                                    @if($f->isMabinti() && !is_null($f->product_satisfied))
                                        @if($f->product_satisfied)
                                            <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>Yes</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger"><i class="bi bi-x-circle me-1"></i>No</span>
                                        @endif
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="pe-3 text-muted">{{ $f->getServiceCategoryLabel() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="13" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox d-block fs-2 mb-2 opacity-25"></i>No records for selected filters.
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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

    $genLabels = array_column($generalThemes, 'label');
    $genCounts = array_column($generalThemes, 'count');
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
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
    const themeLabels = @json(array_values($genLabels));
    const themeCounts = @json(array_values($genCounts));
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
            tooltip: { theme: colors.tooltip },
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
            tooltip: { theme: theme.tooltip, y: { formatter: function (value) { return value + ' submission' + (value === 1 ? '' : 's'); } } },
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
                tooltip: { shared: true, intersect: false, theme: theme.tooltip, y: { formatter: function (value) { return value + ' submission' + (value === 1 ? '' : 's'); } } },
                responsive: [
                    { breakpoint: 768, options: { chart: { height: 300 }, legend: { position: 'bottom', horizontalAlign: 'center' }, stroke: { width: [2, 2, 2] }, markers: { size: 2 } } },
                    { breakpoint: 480, options: { chart: { height: 285 }, xaxis: { labels: { rotate: -45, rotateAlways: true, hideOverlappingLabels: true } }, grid: { padding: { left: 2, right: 5 } } } },
                ],
            });
        }
    );

    registerChart(
        '#chart-general-themes',
        hasValues([themeCounts]),
        'Theme distribution will appear after feedback is classified.',
        function () {
            const options = baseChart('bar', 320);
            const theme = palette();
            return Object.assign(options, {
                series: [{ name: 'Entries', data: themeCounts }],
                colors: ['#0b6b2c'],
                plotOptions: { bar: { borderRadius: 5, borderRadiusApplication: 'end', columnWidth: '48%', distributed: false } },
                dataLabels: { enabled: true, offsetY: -5, style: { fontSize: '10px', fontWeight: 600, colors: [theme.heading] }, background: { enabled: false } },
                xaxis: {
                    categories: themeLabels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        rotate: -35,
                        trim: true,
                        hideOverlappingLabels: true,
                        style: { colors: themeLabels.map(function () { return theme.text; }), fontSize: '9px' },
                        formatter: function (value) { return String(value).length > 18 ? String(value).slice(0, 17) + '…' : value; },
                    },
                },
                yaxis: { min: 0, forceNiceScale: true, decimalsInFloat: 0, labels: { formatter: function (value) { return Math.floor(value); }, style: { colors: [theme.text], fontSize: '10px' } } },
                tooltip: { theme: theme.tooltip, x: { formatter: function (_, context) { return themeLabels[context.dataPointIndex] || ''; } }, y: { formatter: function (value) { return value + ' entr' + (value === 1 ? 'y' : 'ies'); } } },
                responsive: [
                    { breakpoint: 768, options: { chart: { height: Math.max(360, themeLabels.length * 42) }, plotOptions: { bar: { horizontal: true, barHeight: '58%', borderRadius: 4 } }, dataLabels: { offsetX: 8, offsetY: 0 }, xaxis: { categories: themeLabels, labels: { rotate: 0, style: { fontSize: '9px' } } }, yaxis: { labels: { maxWidth: 135, style: { fontSize: '9px' } } }, grid: { padding: { left: 4, right: 14 } } } },
                    { breakpoint: 480, options: { chart: { height: Math.max(390, themeLabels.length * 46) }, yaxis: { labels: { maxWidth: 110, style: { fontSize: '9px' } } } } },
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

    // ── Category tab switching ──
    document.getElementById('catTabPill')?.addEventListener('click', function (e) {
        const btn = e.target.closest('.tab-btn');
        if (!btn) return;
        document.querySelectorAll('#catTabPill .tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.dataset.cat;
        document.querySelectorAll('.cat-tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById('cat-' + cat)?.classList.add('active');
    });
});
</script>
@endsection
