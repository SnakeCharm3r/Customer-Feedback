@extends('layouts.app')
@section('title', 'Dashboard')

@php
    $statusNew = (int) $statusCounts->get('new', 0);
    $statusUnderReview = (int) $statusCounts->get('under_review', 0);
    $statusResponded = (int) $statusCounts->get('responded', 0);
    $statusClosed = (int) $statusCounts->get('closed', 0);

    $typeComplaints = (int) $typeCounts->get('complaint', 0);
    $typeCompliments = (int) $typeCounts->get('compliment', 0);
    $typeSuggestions = (int) $typeCounts->get('suggestion', 0);
    $typeEnquiries = (int) $typeCounts->get('enquiry', 0);
@endphp

@section('content')
<style>
    .dashboard-page {
        --dash-green-950: #043d19;
        --dash-green-900: #065321;
        --dash-green-800: #0b6b2c;
        --dash-green-700: #15803d;
        --dash-lime: #94c83d;
        --dash-soft: #eef7e8;
        --dash-ink: #163223;
        --dash-muted: #667a6e;
        --dash-border: rgba(11, 107, 44, 0.12);
        display: grid;
        gap: 1rem;
    }

    .dashboard-hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
        min-height: 154px;
        padding: 1.7rem 2rem;
        border-radius: 14px;
        background: linear-gradient(125deg, var(--dash-green-950), var(--dash-green-800) 62%, var(--dash-green-700));
        color: #fff;
        box-shadow: 0 14px 34px rgba(6, 83, 33, 0.17);
    }
    .dashboard-hero::after {
        position: absolute;
        right: -70px;
        top: -105px;
        width: 300px;
        height: 300px;
        border: 44px solid rgba(148, 200, 61, 0.14);
        border-radius: 50%;
        content: '';
    }
    .dashboard-hero__content, .dashboard-hero__actions { position: relative; z-index: 1; }
    .dashboard-hero__eyebrow { margin: 0 0 0.4rem; color: #cbe5b7; font-size: 0.72rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; }
    .dashboard-hero h1 { margin: 0; color: #fff; font-size: clamp(1.45rem, 2.4vw, 2rem); font-weight: 700; letter-spacing: -0.025em; }
    .dashboard-hero__meta { display: flex; flex-wrap: wrap; gap: 0.65rem 1rem; margin-top: 0.65rem; color: rgba(255,255,255,.75); font-size: 0.8rem; }
    .dashboard-hero__meta span { display: inline-flex; align-items: center; gap: 0.4rem; }
    .dashboard-hero__actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 0.65rem; }
    .dashboard-hero__actions .btn { border-radius: 8px; padding: 0.62rem 0.95rem; font-weight: 600; }
    .dashboard-hero__actions .btn-light { color: var(--dash-green-900); }
    .dashboard-hero__actions .btn-outline-light { border-color: rgba(255,255,255,.5); color: #fff; }

    .dashboard-alerts { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; }
    .dashboard-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-height: 70px;
        padding: 0.8rem 0.9rem;
        border: 1px solid var(--dash-border);
        border-radius: 10px;
        background: #fff;
        color: var(--dash-ink);
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .dashboard-alert:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(22,50,35,.08); }
    .dashboard-alert__icon { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; flex-shrink: 0; border-radius: 9px; font-size: 1rem; }
    .dashboard-alert__copy { min-width: 0; flex: 1; }
    .dashboard-alert__copy strong, .dashboard-alert__copy small { display: block; }
    .dashboard-alert__copy strong { font-size: 0.8rem; }
    .dashboard-alert__copy small { margin-top: 0.15rem; color: var(--dash-muted); font-size: 0.68rem; }
    .dashboard-alert__arrow { color: #94a39a; }
    .dashboard-alert--warning .dashboard-alert__icon { background: #fff5d9; color: #b77900; }
    .dashboard-alert--danger .dashboard-alert__icon { background: #fee8e8; color: #dc3545; }
    .dashboard-alert--info .dashboard-alert__icon { background: var(--dash-soft); color: var(--dash-green-700); }

    .dashboard-settings {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        border: 1px solid var(--dash-border);
        border-left: 4px solid var(--dash-lime);
        border-radius: 10px;
        background: linear-gradient(90deg, var(--dash-soft), #fff 55%);
    }
    .dashboard-settings__main { display: flex; align-items: center; gap: 0.85rem; min-width: 0; }
    .dashboard-settings__icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; flex-shrink: 0; border-radius: 10px; background: #fff; color: var(--dash-green-800); box-shadow: 0 5px 14px rgba(6,83,33,.08); }
    .dashboard-settings h2 { margin: 0; color: var(--dash-green-900); font-size: 0.92rem; font-weight: 700; }
    .dashboard-settings p { margin: 0.22rem 0 0; color: var(--dash-muted); font-size: 0.72rem; }

    .dashboard-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.9rem; }
    .dashboard-metric {
        --metric-color: var(--dash-green-800);
        --metric-soft: var(--dash-soft);
        display: block;
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--dash-border);
        border-top: 3px solid var(--metric-color);
        border-radius: 11px;
        background: #fff;
        color: var(--dash-ink);
        box-shadow: 0 7px 20px rgba(6,83,33,.045);
        transition: transform .18s ease, box-shadow .18s ease;
    }
    a.dashboard-metric:hover { transform: translateY(-3px); box-shadow: 0 12px 26px rgba(6,83,33,.1); }
    .dashboard-metric--red { --metric-color: #dc3545; --metric-soft: #fee8e8; }
    .dashboard-metric--amber { --metric-color: #e6a400; --metric-soft: #fff4d6; }
    .dashboard-metric--lime { --metric-color: #58a931; --metric-soft: #eaf7df; }
    .dashboard-metric__body { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem; min-height: 148px; padding: 1.15rem; }
    .dashboard-metric__content { display: flex; flex-direction: column; align-items: flex-start; min-width: 0; }
    .dashboard-metric__label { color: var(--dash-muted); font-size: 0.68rem; font-weight: 700; letter-spacing: 0.075em; text-transform: uppercase; }
    .dashboard-metric__value { margin-top: 0.5rem; color: var(--metric-color); font-size: 1.85rem; line-height: 1; }
    .dashboard-metric__icon { display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; background: var(--metric-soft); color: var(--metric-color); font-size: 1.15rem; }
    .dashboard-metric__badge { margin-top: 0.65rem; padding: 0.25rem 0.48rem; border-radius: 6px; background: var(--metric-soft); color: var(--metric-color); font-size: 0.65rem; font-weight: 700; }
    .dashboard-metric__meta { margin-top: 0.45rem; color: var(--dash-muted); font-size: 0.68rem; }
    .dashboard-metric__progress { width: 115px; height: 5px; margin-top: 0.7rem; overflow: hidden; border-radius: 99px; background: #e9efeb; }
    .dashboard-metric__progress span { display: block; height: 100%; border-radius: inherit; background: var(--metric-color); }

    .dashboard-grid { display: grid; gap: 1rem; }
    .dashboard-grid--primary { grid-template-columns: minmax(0, 1.55fr) minmax(320px, .85fr); }
    .dashboard-grid--insights { grid-template-columns: 1.15fr .8fr 1fr; }
    .dashboard-panel { display: flex; flex-direction: column; min-width: 0; overflow: hidden; border: 1px solid var(--dash-border); border-radius: 11px; background: #fff; box-shadow: 0 7px 22px rgba(6,83,33,.045); }
    .dashboard-panel__header { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; min-height: 58px; padding: 0.8rem 1rem; border-bottom: 1px solid var(--dash-border); }
    .dashboard-panel__heading { display: flex; align-items: center; gap: 0.65rem; min-width: 0; }
    .dashboard-panel__icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; flex-shrink: 0; border-radius: 8px; background: var(--dash-soft); color: var(--dash-green-800); }
    .dashboard-panel__heading h2 { margin: 0; color: var(--dash-green-900); font-size: 0.9rem; font-weight: 700; }
    .dashboard-panel__actions { flex-shrink: 0; }
    .dashboard-panel__body { flex: 1; padding: 1rem; }
    .dashboard-panel__body--flush { padding: 0; }
    .dashboard-panel__footer { padding: 0.65rem 1rem; border-top: 1px solid var(--dash-border); background: #fbfdfb; }

    .dashboard-assignment { display: flex; align-items: center; gap: 0.7rem; padding: 0.82rem 1rem; border-bottom: 1px solid var(--dash-border); color: var(--dash-ink); text-decoration: none; }
    .dashboard-assignment:last-child { border-bottom: 0; }
    .dashboard-assignment:hover { background: #f8fbf8; }
    .dashboard-assignment__icon { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; flex-shrink: 0; border-radius: 10px; background: var(--dash-soft); color: var(--dash-green-800); }
    .dashboard-assignment__copy { min-width: 0; flex: 1; }
    .dashboard-assignment__copy strong, .dashboard-assignment__copy small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dashboard-assignment__copy strong { color: var(--dash-green-800); font-family: monospace; font-size: 0.73rem; }
    .dashboard-assignment__copy small { margin-top: 0.18rem; color: var(--dash-muted); font-size: 0.66rem; }

    .dashboard-breakdown { display: grid; gap: 0.9rem; }
    .dashboard-breakdown__row { display: grid; gap: 0.38rem; }
    .dashboard-breakdown__top { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; font-size: 0.73rem; }
    .dashboard-breakdown__label { display: flex; align-items: center; gap: 0.48rem; min-width: 0; color: var(--dash-ink); font-weight: 600; }
    .dashboard-breakdown__label i { color: var(--row-color); }
    .dashboard-breakdown__numbers { color: var(--dash-muted); white-space: nowrap; }
    .dashboard-breakdown__numbers strong { color: var(--dash-ink); }
    .dashboard-breakdown__bar { height: 5px; overflow: hidden; border-radius: 99px; background: #edf1ee; }
    .dashboard-breakdown__bar span { display: block; height: 100%; border-radius: inherit; background: var(--row-color); }

    .dashboard-pipeline__item { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.86rem 1rem; border-bottom: 1px solid var(--dash-border); color: var(--dash-ink); text-decoration: none; }
    .dashboard-pipeline__item:hover { background: #f8fbf8; }
    .dashboard-pipeline__label { display: flex; align-items: center; gap: 0.6rem; font-size: 0.73rem; font-weight: 600; }
    .dashboard-pipeline__dot { width: 8px; height: 8px; border-radius: 50%; background: var(--status-color); }
    .dashboard-pipeline__value { display: flex; align-items: center; gap: 0.45rem; color: var(--dash-muted); font-size: 0.66rem; }
    .dashboard-pipeline__value strong { min-width: 28px; padding: 0.18rem 0.42rem; border-radius: 99px; background: var(--dash-soft); color: var(--status-color); text-align: center; }

    .dashboard-service { padding: 0.68rem 1rem; border-bottom: 1px solid var(--dash-border); }
    .dashboard-service:last-child { border-bottom: 0; }
    .dashboard-service__top { display: flex; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.38rem; color: var(--dash-ink); font-size: 0.7rem; }
    .dashboard-service__top strong { font-weight: 600; }
    .dashboard-service__top span { color: var(--dash-muted); }

    .dashboard-empty { padding: 2.5rem 1rem; color: var(--dash-muted); text-align: center; }
    .dashboard-empty i { display: block; margin-bottom: 0.5rem; font-size: 1.6rem; opacity: .45; }
    .dashboard-empty p { margin: 0; font-size: 0.72rem; }

    .dashboard-table { margin: 0; }
    .dashboard-table thead th { padding: 0.7rem 0.85rem !important; border-bottom: 1px solid var(--dash-border); background: #f7faf7; color: var(--dash-muted); font-size: 0.62rem; letter-spacing: .06em; text-transform: uppercase; white-space: nowrap; }
    .dashboard-table tbody td { padding: 0.72rem 0.85rem !important; border-bottom: 1px solid var(--dash-border); font-size: 0.7rem; vertical-align: middle; }
    .dashboard-table tbody tr:last-child td { border-bottom: 0; }
    .dashboard-table__patient strong, .dashboard-table__patient small { display: block; }
    .dashboard-table__patient small { color: var(--dash-muted); font-size: .62rem; }

    [data-bs-theme="dark"] .dashboard-page {
        --dash-ink: #e7eee9;
        --dash-muted: #aebcb4;
        --dash-border: rgba(174, 188, 180, 0.18);
        --dash-soft: rgba(148, 200, 61, 0.13);
    }
    [data-bs-theme="dark"] .dashboard-hero {
        background: linear-gradient(125deg, #043d19, #075a29 60%, #0b6b2c);
        border: 1px solid rgba(148, 200, 61, 0.24);
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.25);
    }
    [data-bs-theme="dark"] .dashboard-hero h1,
    [data-bs-theme="dark"] .dashboard-hero__eyebrow,
    [data-bs-theme="dark"] .dashboard-hero__meta,
    [data-bs-theme="dark"] .dashboard-hero__meta span { color: #ffffff !important; }
    [data-bs-theme="dark"] .dashboard-hero__eyebrow { color: #cfe8b9 !important; }
    [data-bs-theme="dark"] .dashboard-hero__meta,
    [data-bs-theme="dark"] .dashboard-hero__meta span { color: #d9e6dd !important; }
    [data-bs-theme="dark"] .dashboard-hero__actions .btn-light {
        background: #ffffff !important;
        border-color: #ffffff !important;
        color: #065321 !important;
    }
    [data-bs-theme="dark"] .dashboard-hero__actions .btn-outline-light {
        background: transparent !important;
        border-color: rgba(255,255,255,.72) !important;
        color: #ffffff !important;
    }
    [data-bs-theme="dark"] .dashboard-hero__actions .btn-outline-light:hover {
        background: rgba(255,255,255,.12) !important;
    }
    [data-bs-theme="dark"] .dashboard-alert,
    [data-bs-theme="dark"] .dashboard-settings,
    [data-bs-theme="dark"] .dashboard-metric,
    [data-bs-theme="dark"] .dashboard-panel {
        background: var(--dm-bg-raised);
        border-color: var(--dash-border);
        box-shadow: 0 8px 24px rgba(0,0,0,.17);
    }
    [data-bs-theme="dark"] .dashboard-alert { color: var(--dash-ink) !important; }
    [data-bs-theme="dark"] .dashboard-alert:hover { background: var(--dm-bg-surface); box-shadow: 0 9px 22px rgba(0,0,0,.22); }
    [data-bs-theme="dark"] .dashboard-alert__copy strong { color: var(--dash-ink) !important; }
    [data-bs-theme="dark"] .dashboard-alert__copy small { color: var(--dash-muted) !important; }
    [data-bs-theme="dark"] .dashboard-alert__arrow { color: #91a198 !important; }
    [data-bs-theme="dark"] .dashboard-alert--warning .dashboard-alert__icon { background: rgba(251,193,75,.15); color: #fbc14b; }
    [data-bs-theme="dark"] .dashboard-alert--danger .dashboard-alert__icon { background: rgba(255,107,120,.14); color: #ff6b78; }
    [data-bs-theme="dark"] .dashboard-alert--info .dashboard-alert__icon { background: rgba(111,201,137,.14); color: #6fc989; }
    [data-bs-theme="dark"] .dashboard-settings { background: linear-gradient(90deg, rgba(148,200,61,.09), var(--dm-bg-raised) 60%); }
    [data-bs-theme="dark"] .dashboard-settings__icon,
    [data-bs-theme="dark"] .dashboard-metric__icon { background: var(--dm-bg-surface); }
    [data-bs-theme="dark"] .dashboard-panel__heading h2,
    [data-bs-theme="dark"] .dashboard-settings h2 { color: var(--dm-text); }
    [data-bs-theme="dark"] .dashboard-settings p { color: var(--dash-muted) !important; }
    [data-bs-theme="dark"] .dashboard-settings__icon,
    [data-bs-theme="dark"] .dashboard-panel__icon { color: #8fd067; }
    [data-bs-theme="dark"] .dashboard-metric { --metric-color: #78ca91; --metric-soft: rgba(120,202,145,.13); }
    [data-bs-theme="dark"] .dashboard-metric--red { --metric-color: #ff7582; --metric-soft: rgba(255,117,130,.13); }
    [data-bs-theme="dark"] .dashboard-metric--amber { --metric-color: #fbc14b; --metric-soft: rgba(251,193,75,.13); }
    [data-bs-theme="dark"] .dashboard-metric--lime { --metric-color: #9bd66e; --metric-soft: rgba(155,214,110,.13); }
    [data-bs-theme="dark"] .dashboard-metric__label,
    [data-bs-theme="dark"] .dashboard-metric__meta { color: var(--dash-muted) !important; }
    [data-bs-theme="dark"] .dashboard-metric__value,
    [data-bs-theme="dark"] .dashboard-metric__badge { color: var(--metric-color) !important; }
    [data-bs-theme="dark"] .dashboard-metric__badge,
    [data-bs-theme="dark"] .dashboard-metric__icon { background: var(--metric-soft); }
    [data-bs-theme="dark"] .dashboard-panel__footer,
    [data-bs-theme="dark"] .dashboard-table thead th { background: var(--dm-bg-surface); }
    [data-bs-theme="dark"] .dashboard-assignment:hover,
    [data-bs-theme="dark"] .dashboard-pipeline__item:hover { background: var(--dm-bg-surface); }
    [data-bs-theme="dark"] .dashboard-breakdown__bar,
    [data-bs-theme="dark"] .dashboard-metric__progress { background: var(--dm-bg-surface); }
    [data-bs-theme="dark"] .dashboard-panel__heading h2,
    [data-bs-theme="dark"] .dashboard-breakdown__label,
    [data-bs-theme="dark"] .dashboard-breakdown__numbers strong,
    [data-bs-theme="dark"] .dashboard-pipeline__item,
    [data-bs-theme="dark"] .dashboard-pipeline__label,
    [data-bs-theme="dark"] .dashboard-service__top,
    [data-bs-theme="dark"] .dashboard-table tbody td,
    [data-bs-theme="dark"] .dashboard-table__patient strong { color: var(--dash-ink) !important; }
    [data-bs-theme="dark"] .dashboard-breakdown__numbers,
    [data-bs-theme="dark"] .dashboard-pipeline__value,
    [data-bs-theme="dark"] .dashboard-service__top span,
    [data-bs-theme="dark"] .dashboard-empty,
    [data-bs-theme="dark"] .dashboard-table thead th,
    [data-bs-theme="dark"] .dashboard-table__patient small { color: var(--dash-muted) !important; }
    [data-bs-theme="dark"] .dashboard-assignment { color: var(--dash-ink) !important; }
    [data-bs-theme="dark"] .dashboard-assignment__copy strong,
    [data-bs-theme="dark"] .table-ref-link { color: #78ca91 !important; }
    [data-bs-theme="dark"] .dashboard-assignment__copy small { color: var(--dash-muted) !important; }
    [data-bs-theme="dark"] .dashboard-assignment__icon { background: rgba(120,202,145,.12); color: #78ca91; }
    [data-bs-theme="dark"] .dashboard-pipeline__value strong { background: rgba(255,255,255,.07); color: var(--status-color) !important; }
    [data-bs-theme="dark"] .dashboard-table tbody tr:hover > * { background: var(--dm-bg-surface) !important; color: var(--dash-ink) !important; }
    [data-bs-theme="dark"] .dashboard-panel .badge.bg-primary-subtle { background: rgba(148,200,61,.14) !important; color: #a8dc7e !important; }
    [data-bs-theme="dark"] .dashboard-panel .badge.bg-secondary { background: #55635b !important; color: #ffffff !important; }

    @media (max-width: 1199.98px) {
        .dashboard-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dashboard-grid--insights { grid-template-columns: 1fr 1fr; }
        .dashboard-grid--insights > :first-child { grid-column: 1 / -1; }
    }
    @media (max-width: 991.98px) {
        .dashboard-hero { align-items: flex-start; flex-direction: column; }
        .dashboard-hero__actions { justify-content: flex-start; }
        .dashboard-alerts, .dashboard-grid--primary { grid-template-columns: 1fr; }
    }
    @media (max-width: 767.98px) {
        .dashboard-page { gap: .8rem; }
        .dashboard-hero { min-height: 0; padding: 1.35rem; }
        .dashboard-settings { align-items: flex-start; flex-direction: column; }
        .dashboard-metrics, .dashboard-grid--insights { grid-template-columns: 1fr; }
        .dashboard-grid--insights > :first-child { grid-column: auto; }
        .dashboard-metric__body { min-height: 126px; }
        .dashboard-table .dashboard-col-optional { display: none; }
    }
    @media (max-width: 479.98px) {
        .dashboard-hero__actions, .dashboard-hero__actions .btn { width: 100%; }
        .dashboard-hero__actions .btn { justify-content: center; }
    }
</style>

<div class="dashboard-page">
    <section class="dashboard-hero" aria-labelledby="dashboard-welcome-title">
        <div class="dashboard-hero__content">
            <p class="dashboard-hero__eyebrow">Customer feedback workspace</p>
            <h1 id="dashboard-welcome-title">Welcome, {{ $authUser->fname ?? $authUser->name }}</h1>
            <div class="dashboard-hero__meta">
                <span><i class="bi bi-calendar3"></i>{{ now()->format('l, d F Y') }}</span>
                <span><i class="bi bi-person-badge"></i>{{ $authUser->getRoleLabel() }}</span>
                <span><i class="bi bi-activity"></i>{{ $weekCount }} submission{{ $weekCount === 1 ? '' : 's' }} this week</span>
            </div>
        </div>
        @if($authUser->canManageComplaints())
            <div class="dashboard-hero__actions">
                <a href="{{ route('feedback.manual.create') }}" class="btn btn-light d-inline-flex align-items-center">
                    <i class="bi bi-plus-circle me-2"></i>Add Feedback
                </a>
                <a href="{{ route('feedback.admin.index') }}" class="btn btn-outline-light d-inline-flex align-items-center">
                    <i class="bi bi-list-ul me-2"></i>All Submissions
                </a>
            </div>
        @endif
    </section>

    @if(($authUser->canManageUsers() && $pendingUsers > 0) || $urgentOpen > 0 || $pendingEscalations > 0)
        <div class="dashboard-alerts" aria-label="Items requiring attention">
            @if($authUser->canManageUsers() && $pendingUsers > 0)
                <a href="{{ route('users.pending') }}" class="dashboard-alert dashboard-alert--warning">
                    <span class="dashboard-alert__icon"><i class="bi bi-person-check"></i></span>
                    <span class="dashboard-alert__copy"><strong>{{ $pendingUsers }} pending user{{ $pendingUsers === 1 ? '' : 's' }}</strong><small>Waiting for access review</small></span>
                    <i class="bi bi-chevron-right dashboard-alert__arrow"></i>
                </a>
            @endif
            @if($urgentOpen > 0)
                <a href="{{ route('feedback.admin.index', ['priority' => 1]) }}" class="dashboard-alert dashboard-alert--danger">
                    <span class="dashboard-alert__icon"><i class="bi bi-exclamation-triangle"></i></span>
                    <span class="dashboard-alert__copy"><strong>{{ $urgentOpen }} urgent submission{{ $urgentOpen === 1 ? '' : 's' }}</strong><small>Immediate attention required</small></span>
                    <i class="bi bi-chevron-right dashboard-alert__arrow"></i>
                </a>
            @endif
            @if($pendingEscalations > 0)
                <a href="{{ route('escalations.index', ['status' => 'pending']) }}" class="dashboard-alert dashboard-alert--info">
                    <span class="dashboard-alert__icon"><i class="bi bi-arrow-up-right-circle"></i></span>
                    <span class="dashboard-alert__copy"><strong>{{ $pendingEscalations }} pending escalation{{ $pendingEscalations === 1 ? '' : 's' }}</strong><small>Awaiting HOD response</small></span>
                    <i class="bi bi-chevron-right dashboard-alert__arrow"></i>
                </a>
            @endif
        </div>
    @endif

    @if($authUser->isAdmin())
        <section class="dashboard-settings">
            <div class="dashboard-settings__main">
                <span class="dashboard-settings__icon"><i class="bi bi-sliders"></i></span>
                <div>
                    <h2>System configuration</h2>
                    <p>Manage branding, contact details, outgoing email identity, and login security.</p>
                </div>
            </div>
            <a href="{{ route('settings.edit') }}" class="btn btn-sm btn-outline-primary flex-shrink-0">Open Settings <i class="bi bi-arrow-right ms-1"></i></a>
        </section>
    @endif

    <section class="dashboard-metrics" aria-label="Feedback overview">
        <x-dashboard.metric-card
            label="Total submissions"
            :value="$totalFeedback"
            icon="bi-chat-left-text"
            :badge="$todayCount > 0 ? '+'.$todayCount.' today' : 'None today'"
            :meta="$weekCount.' this week'"
        />
        <x-dashboard.metric-card
            label="Awaiting review"
            :value="$statusNew"
            icon="bi-inbox"
            tone="red"
            :href="route('feedback.admin.index', ['status' => 'new'])"
            :badge="$statusNew > 0 ? 'Needs attention' : 'Queue is clear'"
            :meta="$statusUnderReview.' under review'"
        />
        <x-dashboard.metric-card
            label="Resolution rate"
            :value="$responseRate.'%'"
            icon="bi-check2-circle"
            tone="lime"
            :progress="$responseRate"
            :meta="$resolvedCount.' of '.$totalFeedback.' resolved'"
        />
        <x-dashboard.metric-card
            label="Priority open"
            :value="$urgentOpen"
            icon="bi-exclamation-triangle"
            tone="amber"
            :href="route('feedback.admin.index', ['priority' => 1])"
            :badge="$urgentOpen > 0 ? 'Requires attention' : 'No urgent items'"
            :meta="$pendingEscalations.' escalation'.($pendingEscalations === 1 ? '' : 's').' pending'"
        />
    </section>

    <div class="dashboard-grid dashboard-grid--primary">
        <x-dashboard.panel title="Submissions — Last 7 Days" icon="bi-graph-up-arrow">
            <x-slot:actions><span class="badge bg-primary-subtle text-primary">{{ array_sum($chartCounts) }} total</span></x-slot:actions>
            <div id="submissionsTrendChart" style="min-height:235px;"></div>
        </x-dashboard.panel>

        <x-dashboard.panel title="My Assignments" icon="bi-person-check" :flush="true">
            <x-slot:actions><span class="badge bg-secondary rounded-pill">{{ $myAssignments->count() }}</span></x-slot:actions>
            @forelse($myAssignments as $item)
                <a href="{{ route('feedback.admin.show', $item) }}" class="dashboard-assignment">
                    <span class="dashboard-assignment__icon"><i class="bi {{ ['complaint' => 'bi-exclamation-octagon', 'compliment' => 'bi-hand-thumbs-up', 'suggestion' => 'bi-lightbulb', 'enquiry' => 'bi-question-circle'][$item->feedback_type] ?? 'bi-chat' }}"></i></span>
                    <span class="dashboard-assignment__copy">
                        <strong>{{ $item->reference_number }}</strong>
                        <small>{{ $item->patient_name ?: 'Anonymous' }} &middot; {{ $item->created_at->diffForHumans() }}</small>
                    </span>
                    @if($item->is_priority)<span class="badge bg-danger-subtle text-danger">Priority</span>@else{!! $item->getStatusBadge() !!}@endif
                </a>
            @empty
                <div class="dashboard-empty"><i class="bi bi-check2-all"></i><p>No open assignments for you.</p></div>
            @endforelse
            @if($myAssignments->isNotEmpty())
                <x-slot:footer><div class="text-center"><a href="{{ route('feedback.admin.index') }}" class="small text-decoration-none">View all submissions <i class="bi bi-arrow-right ms-1"></i></a></div></x-slot:footer>
            @endif
        </x-dashboard.panel>
    </div>

    <div class="dashboard-grid dashboard-grid--insights">
        <x-dashboard.panel title="Feedback by Type" icon="bi-pie-chart">
            <x-slot:actions><span class="badge bg-primary-subtle text-primary">{{ $totalFeedback }} total</span></x-slot:actions>
            @php
                $feedbackTypes = [
                    ['label' => 'Complaints', 'count' => $typeComplaints, 'color' => '#dc3545', 'icon' => 'bi-exclamation-octagon', 'type' => 'complaint'],
                    ['label' => 'Compliments', 'count' => $typeCompliments, 'color' => '#15803d', 'icon' => 'bi-hand-thumbs-up', 'type' => 'compliment'],
                    ['label' => 'Suggestions', 'count' => $typeSuggestions, 'color' => '#198fb8', 'icon' => 'bi-lightbulb', 'type' => 'suggestion'],
                    ['label' => 'Enquiries', 'count' => $typeEnquiries, 'color' => '#6c757d', 'icon' => 'bi-question-circle', 'type' => 'enquiry'],
                ];
            @endphp
            <div class="dashboard-breakdown">
                @foreach($feedbackTypes as $type)
                    @php
                        $percentage = $totalFeedback > 0 ? (int) round(($type['count'] / $totalFeedback) * 100) : 0;
                    @endphp
                    <div class="dashboard-breakdown__row" style="--row-color:{{ $type['color'] }};">
                        <div class="dashboard-breakdown__top">
                            <a href="{{ route('feedback.admin.index', ['type' => $type['type']]) }}" class="dashboard-breakdown__label text-decoration-none"><i class="bi {{ $type['icon'] }}"></i>{{ $type['label'] }}</a>
                            <span class="dashboard-breakdown__numbers"><strong>{{ $type['count'] }}</strong> &nbsp;{{ $percentage }}%</span>
                        </div>
                        <div class="dashboard-breakdown__bar"><span style="width:{{ $percentage }}%"></span></div>
                    </div>
                @endforeach
            </div>
        </x-dashboard.panel>

        <x-dashboard.panel title="Status Pipeline" icon="bi-kanban" :flush="true">
            @php
                $pipeline = [
                    ['label' => 'New', 'count' => $statusNew, 'color' => '#dc3545', 'status' => 'new'],
                    ['label' => 'Under Review', 'count' => $statusUnderReview, 'color' => '#e6a400', 'status' => 'under_review'],
                    ['label' => 'Responded', 'count' => $statusResponded, 'color' => '#15803d', 'status' => 'responded'],
                    ['label' => 'Closed', 'count' => $statusClosed, 'color' => '#6c757d', 'status' => 'closed'],
                ];
            @endphp
            @foreach($pipeline as $status)
                @php
                    $percentage = $totalFeedback > 0 ? (int) round(($status['count'] / $totalFeedback) * 100) : 0;
                @endphp
                <a href="{{ route('feedback.admin.index', ['status' => $status['status']]) }}" class="dashboard-pipeline__item" style="--status-color:{{ $status['color'] }};">
                    <span class="dashboard-pipeline__label"><span class="dashboard-pipeline__dot"></span>{{ $status['label'] }}</span>
                    <span class="dashboard-pipeline__value"><strong>{{ $status['count'] }}</strong>{{ $percentage }}%</span>
                </a>
            @endforeach
        </x-dashboard.panel>

        <x-dashboard.panel title="Top Service Areas" icon="bi-hospital" :flush="true">
            @php
                $categoryLabels = \App\Models\Feedback::SERVICE_CATEGORIES;
            @endphp
            @forelse($byCategory as $index => $category)
                @php
                    $percentage = $totalFeedback > 0 ? (int) round(($category->total / $totalFeedback) * 100) : 0;
                    $label = $categoryLabels[$category->service_category] ?? ucfirst(str_replace('_', ' ', $category->service_category ?? 'Other'));
                    $colors = ['#0b6b2c', '#198fb8', '#58a931', '#e6a400', '#dc3545', '#6c757d'];
                    $color = $colors[$index % count($colors)];
                @endphp
                <div class="dashboard-service">
                    <div class="dashboard-service__top"><strong>{{ $label }}</strong><span>{{ $category->total }} &middot; {{ $percentage }}%</span></div>
                    <div class="dashboard-breakdown__bar"><span style="width:{{ $percentage }}%;background:{{ $color }};"></span></div>
                </div>
            @empty
                <div class="dashboard-empty"><i class="bi bi-inbox"></i><p>No service data yet.</p></div>
            @endforelse
        </x-dashboard.panel>
    </div>

    <x-dashboard.panel title="Recent Submissions" icon="bi-clock-history" :flush="true">
        <x-slot:actions>
            @if($authUser->canManageComplaints())
                <a href="{{ route('feedback.admin.index') }}" class="btn btn-sm btn-outline-primary">View All <i class="bi bi-arrow-right ms-1"></i></a>
            @endif
        </x-slot:actions>
        <div class="table-responsive">
            <table class="table table-hover align-middle dashboard-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Patient</th>
                        <th>Type</th>
                        <th class="dashboard-col-optional">Service Area</th>
                        <th>Status</th>
                        <th class="dashboard-col-optional">Assigned To</th>
                        <th class="dashboard-col-optional">Submitted</th>
                        @if($authUser->canManageComplaints())<th></th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentFeedback as $item)
                        <tr>
                            <td><span class="table-ref-link">{{ $item->reference_number }}</span>@if($item->is_priority)<span class="badge bg-danger ms-1">Priority</span>@endif</td>
                            <td class="dashboard-table__patient"><strong>{{ $item->patient_name ?: 'Anonymous' }}</strong><small>{{ $item->created_at->format('d M Y') }}</small></td>
                            <td><span class="badge {{ ['complaint' => 'bg-danger', 'compliment' => 'bg-success', 'suggestion' => 'bg-info', 'enquiry' => 'bg-secondary'][$item->feedback_type] ?? 'bg-secondary' }}">{{ ucfirst($item->feedback_type) }}</span></td>
                            <td class="dashboard-col-optional">{{ \App\Models\Feedback::SERVICE_CATEGORIES[$item->service_category] ?? ucfirst(str_replace('_', ' ', $item->service_category ?? 'Not set')) }}</td>
                            <td>{!! $item->getStatusBadge() !!}</td>
                            <td class="dashboard-col-optional">{{ $item->assignedTo?->getFullName() ?? 'Unassigned' }}</td>
                            <td class="dashboard-col-optional text-muted">{{ $item->created_at->diffForHumans() }}</td>
                            @if($authUser->canManageComplaints())<td class="text-end"><a href="{{ route('feedback.admin.show', $item) }}" class="btn btn-sm btn-outline-primary" aria-label="View {{ $item->reference_number }}"><i class="bi bi-arrow-right"></i></a></td>@endif
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="dashboard-empty"><i class="bi bi-inbox"></i><p>No feedback submissions yet.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-dashboard.panel>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartElement = document.querySelector('#submissionsTrendChart');
    if (!chartElement || typeof ApexCharts === 'undefined') return;

    const isDark = function () { return document.documentElement.getAttribute('data-bs-theme') === 'dark'; };
    const chart = new ApexCharts(chartElement, {
        series: [{ name: 'Submissions', data: @json($chartCounts) }],
        chart: { type: 'area', height: 235, toolbar: { show: false }, fontFamily: 'Poppins, sans-serif', background: 'transparent' },
        colors: ['#0b6b2c'],
        stroke: { curve: 'smooth', width: 3 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, gradientToColors: ['#94c83d'], opacityFrom: .38, opacityTo: .04, stops: [0, 95] } },
        dataLabels: { enabled: false },
        markers: { size: 4, colors: ['#ffffff'], strokeColors: '#0b6b2c', strokeWidth: 2, hover: { size: 6 } },
        xaxis: { categories: @json($chartDays), labels: { style: { fontSize: '11px', colors: isDark() ? '#94a3b8' : '#667a6e' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { min: 0, tickAmount: 4, forceNiceScale: true, labels: { formatter: function (value) { return Math.floor(value); }, style: { fontSize: '11px', colors: [isDark() ? '#94a3b8' : '#667a6e'] } } },
        grid: { borderColor: isDark() ? 'rgba(148,163,184,.16)' : 'rgba(11,107,44,.10)', strokeDashArray: 4, padding: { left: 6, right: 8 } },
        tooltip: { theme: isDark() ? 'dark' : 'light', y: { formatter: function (value) { return value + ' submission' + (value === 1 ? '' : 's'); } } }
    });
    chart.render();

    new MutationObserver(function () {
        chart.updateOptions({
            xaxis: { labels: { style: { colors: isDark() ? '#94a3b8' : '#667a6e' } } },
            yaxis: { labels: { style: { colors: [isDark() ? '#94a3b8' : '#667a6e'] } } },
            grid: { borderColor: isDark() ? 'rgba(148,163,184,.16)' : 'rgba(11,107,44,.10)' },
            tooltip: { theme: isDark() ? 'dark' : 'light' }
        });
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
});
</script>
@endpush
@endsection
