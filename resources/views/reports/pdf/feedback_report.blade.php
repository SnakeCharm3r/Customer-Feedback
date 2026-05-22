<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CCBRT Feedback Report</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 11px;
        color: #1a1e2a;
        background: #eef2f7;
    }

    .page-wrap {
        max-width: 1440px;
        margin: 24px auto;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    }

    /* ── Letterhead ── */
    .report-header {
        background: linear-gradient(135deg, #065321 0%, #0b8a38 100%);
        color: #fff;
        padding: 24px 32px 20px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }
    .report-header .org-name {
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        opacity: 0.8;
        margin-bottom: 4px;
    }
    .report-header h1 {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.01em;
        margin-bottom: 4px;
    }
    .report-header .subtitle {
        font-size: 10px;
        opacity: 0.75;
    }
    .report-header .meta-right {
        text-align: right;
        font-size: 10px;
        opacity: 0.85;
        line-height: 1.8;
        flex-shrink: 0;
    }
    .report-header .meta-right strong { opacity: 1; font-size: 11px; }

    /* ── Active filters strip ── */
    .filters-strip {
        background: #f0faf4;
        border-bottom: 1px solid #c6e8d0;
        padding: 8px 32px;
        font-size: 10px;
        color: #3d6b4f;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .filters-strip .filter-label { font-weight: 700; color: #065321; margin-right: 4px; }
    .filter-chip {
        background: #d1fae5;
        color: #065f46;
        border-radius: 999px;
        padding: 2px 9px;
        font-weight: 600;
        font-size: 10px;
    }

    /* ── Summary bar ── */
    .summary-bar {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #065321;
    }
    .summary-card {
        flex: 1;
        padding: 14px 16px;
        border-right: 1px solid #e2e8f0;
        text-align: center;
    }
    .summary-card:last-child { border-right: none; }
    .summary-card .val {
        font-size: 26px;
        font-weight: 800;
        color: #065321;
        line-height: 1;
        margin-bottom: 3px;
    }
    .summary-card .lbl {
        font-size: 10px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 600;
    }
    .summary-card .sub {
        font-size: 9px;
        color: #94a3b8;
        margin-top: 1px;
    }

    /* ── Section title ── */
    .section-title {
        padding: 12px 32px 8px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    /* ── Table ── */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 11px;
    }

    col.col-ref      { width: 108px; }
    col.col-source   { width: 64px; }
    col.col-type     { width: 74px; }
    col.col-category { width: 108px; }
    col.col-report   { width: auto; }
    col.col-theme    { width: 88px; }
    col.col-sent     { width: 74px; }
    col.col-wing     { width: 64px; }
    col.col-dept     { width: 96px; }
    col.col-reviewer { width: 108px; }
    col.col-reviewed { width: 78px; }
    col.col-submitted{ width: 78px; }

    .data-table thead tr {
        background: #065321;
    }
    .data-table thead th {
        color: #fff;
        padding: 9px 10px;
        text-align: left;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border-right: 1px solid rgba(255,255,255,0.12);
        word-wrap: break-word;
        white-space: nowrap;
    }
    .data-table thead th:last-child { border-right: none; }

    /* Sub-header accent band */
    .data-table thead tr.sub-head th {
        background: #0b6b2c;
        font-size: 8.5px;
        padding: 4px 10px;
        color: rgba(255,255,255,0.7);
        font-style: italic;
        font-weight: 400;
        letter-spacing: 0;
        text-transform: none;
    }

    .data-table tbody td {
        padding: 7px 10px;
        border-bottom: 1px solid #e8edf2;
        border-right: 1px solid #f1f5f9;
        vertical-align: top;
        word-wrap: break-word;
        line-height: 1.45;
        color: #1a1e2a;
    }
    .data-table tbody td:last-child { border-right: none; }

    .data-table tbody tr:nth-child(even) td { background: #f6fbf8; }
    .data-table tbody tr:hover td { background: #eaf6ee; }

    /* Row number */
    .row-num {
        display: inline-block;
        width: 18px;
        height: 18px;
        background: #e2e8f0;
        border-radius: 50%;
        text-align: center;
        line-height: 18px;
        font-size: 9px;
        font-weight: 700;
        color: #475569;
        margin-right: 4px;
        flex-shrink: 0;
    }

    .ref-code {
        font-family: 'Courier New', monospace;
        font-size: 10px;
        font-weight: 700;
        color: #0b6b2c;
        display: block;
        white-space: nowrap;
    }
    .ref-sub {
        font-size: 9px;
        color: #94a3b8;
        margin-top: 1px;
        display: block;
    }

    .type-complaint  { color: #991b1b; font-weight: 600; }
    .type-compliment { color: #065f46; font-weight: 600; }
    .type-suggestion { color: #1d4ed8; font-weight: 600; }
    .type-enquiry    { color: #6b21a8; font-weight: 600; }

    .badge {
        display: inline-block;
        border-radius: 4px;
        padding: 2px 7px;
        font-size: 9.5px;
        font-weight: 700;
        white-space: nowrap;
    }
    .badge-pos { background:#d1fae5; color:#065f46; }
    .badge-neg { background:#fee2e2; color:#991b1b; }
    .badge-neu { background:#e5e7eb; color:#374151; }
    .badge-src { background:#dbeafe; color:#1d4ed8; }

    .reviewer-name { font-weight: 600; color: #1e293b; }
    .reviewer-role { font-size: 9px; color: #94a3b8; }

    .date-main { font-weight: 600; color: #1e293b; white-space: nowrap; }
    .date-sub  { font-size: 9px; color: #94a3b8; }

    /* ── Footer ── */
    .report-footer {
        background: #f8fafc;
        border-top: 2px solid #065321;
        padding: 12px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 9.5px;
        color: #64748b;
    }
    .report-footer .brand { font-weight: 700; color: #065321; }

    /* ── Empty state ── */
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #94a3b8;
    }
    .empty-state .icon { font-size: 32px; margin-bottom: 8px; }

    /* ── Print ── */
    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        body { background: #fff; }
        .page-wrap { margin: 0; border-radius: 0; box-shadow: none; }
        .data-table tbody tr:hover td { background: inherit; }
        .report-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .data-table thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .summary-bar { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .data-table tbody tr:nth-child(even) td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
</head>
<body>
<div class="page-wrap">

    {{-- ── Letterhead ── --}}
    <div class="report-header">
        <div>
            <div class="org-name">Comprehensive Community-Based Rehabilitation Tanzania</div>
            <h1>Feedback Report</h1>
            <div class="subtitle">Customer Feedback Management System &mdash; Official Export</div>
        </div>
        <div class="meta-right">
            <strong>Generated:</strong> {{ now()->format('d M Y, H:i') }}<br>
            @if(!empty($filters['year']))<strong>Year:</strong> {{ $filters['year'] }}<br>@endif
            @if(!empty($filters['month']))<strong>Month:</strong> {{ [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'][(int)$filters['month']] ?? $filters['month'] }}<br>@endif
            <strong>Total Records:</strong> {{ count($feedbacks) }}
        </div>
    </div>

    {{-- ── Active Filters ── --}}
    @if(!empty(array_filter($filters ?? [])))
    <div class="filters-strip">
        <span class="filter-label">Filters:</span>
        @if(!empty($filters['feedback_type']))<span class="filter-chip">Type: {{ ucfirst($filters['feedback_type']) }}</span>@endif
        @if(!empty($filters['status']))<span class="filter-chip">Status: {{ ucfirst($filters['status']) }}</span>@endif
        @if(!empty($filters['source']))<span class="filter-chip">Source: {{ ucfirst($filters['source']) }}</span>@endif
        @if(!empty($filters['search']))<span class="filter-chip">Search: {{ $filters['search'] }}</span>@endif
    </div>
    @endif

    {{-- ── Summary Bar ── --}}
    <div class="summary-bar">
        <div class="summary-card">
            <div class="val">{{ $summary['total'] }}</div>
            <div class="lbl">Total</div>
            <div class="sub">All submissions</div>
        </div>
        <div class="summary-card">
            <div class="val">{{ $summary['portal'] }}</div>
            <div class="lbl">Portal</div>
            <div class="sub">Online submissions</div>
        </div>
        <div class="summary-card">
            <div class="val">{{ $summary['manual'] }}</div>
            <div class="lbl">Manual</div>
            <div class="sub">Paper / staff entry</div>
        </div>
        <div class="summary-card">
            <div class="val">{{ $summary['reviewed'] }}</div>
            <div class="lbl">Reviewed</div>
            <div class="sub">QA assessed</div>
        </div>
        <div class="summary-card">
            <div class="val">{{ $summary['pending_review'] }}</div>
            <div class="lbl">Pending</div>
            <div class="sub">Awaiting review</div>
        </div>
    </div>

    {{-- ── Section Title ── --}}
    <div class="section-title">Feedback Records &mdash; {{ count($feedbacks) }} entries</div>

    {{-- ── Table ── --}}
    <div style="overflow-x:auto;">
    <table class="data-table">
        <colgroup>
            <col class="col-ref"><col class="col-source"><col class="col-type">
            <col class="col-category"><col class="col-report"><col class="col-theme">
            <col class="col-sent"><col class="col-wing"><col class="col-dept">
            <col class="col-reviewer"><col class="col-reviewed"><col class="col-submitted">
        </colgroup>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Source</th>
                <th>Type</th>
                <th>Service Category</th>
                <th>Report Excerpt</th>
                <th>Theme</th>
                <th>Sentiment</th>
                <th>Wing</th>
                <th>Department</th>
                <th>Reviewer</th>
                <th>Date Reviewed</th>
                <th>Submitted</th>
            </tr>
        </thead>
        <tbody>
            @forelse($feedbacks as $i => $f)
            <tr>
                <td>
                    <span class="ref-code">{{ $f->reference_no }}</span>
                    <span class="ref-sub">{{ $f->getStatusLabel() }}</span>
                </td>
                <td><span class="badge badge-src">{{ $f->getSourceLabel() }}</span></td>
                <td>
                    @php
                        $typeClass = match($f->feedback_type) {
                            'complaint'   => 'type-complaint',
                            'compliment'  => 'type-compliment',
                            'suggestion'  => 'type-suggestion',
                            default       => 'type-enquiry',
                        };
                    @endphp
                    <span class="{{ $typeClass }}">{{ $f->getFeedbackTypeLabel() }}</span>
                </td>
                <td>{{ $f->getServiceCategoryLabel() }}</td>
                <td>{{ \Illuminate\Support\Str::limit($f->report_excerpt, 110) ?: '—' }}</td>
                <td>{{ $f->getThemeLabel() ?: '—' }}</td>
                <td>
                    @if($f->sentiment === 'positive')     <span class="badge badge-pos">Positive</span>
                    @elseif($f->sentiment === 'negative') <span class="badge badge-neg">Negative</span>
                    @elseif($f->sentiment)                <span class="badge badge-neu">{{ $f->getSentimentLabel() }}</span>
                    @else —
                    @endif
                </td>
                <td>{{ $f->getWingLabel() ?: '—' }}</td>
                <td>{{ $f->department?->name ?? '—' }}</td>
                <td>
                    @if($f->reviewedBy)
                        <div class="reviewer-name">{{ $f->reviewedBy->getFullName() }}</div>
                        <div class="reviewer-role">{{ $f->reviewedBy->getRoleLabel() }}</div>
                    @else
                        <span style="color:#94a3b8;">Not reviewed</span>
                    @endif
                </td>
                <td>
                    @if($f->reviewed_at)
                        <div class="date-main">{{ $f->reviewed_at->format('d M Y') }}</div>
                        <div class="date-sub">{{ $f->reviewed_at->format('H:i') }}</div>
                    @else —
                    @endif
                </td>
                <td>
                    <div class="date-main">{{ $f->created_at?->format('d M Y') ?? '—' }}</div>
                    <div class="date-sub">{{ $f->created_at?->format('H:i') ?? '' }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12">
                    <div class="empty-state">
                        <div class="icon">📋</div>
                        No feedback records found for the selected filters.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- ── Footer ── --}}
    <div class="report-footer">
        <div><span class="brand">CCBRT</span> &mdash; Comprehensive Community-Based Rehabilitation Tanzania</div>
        <div>Generated {{ now()->format('d M Y \a\t H:i') }} &middot; {{ count($feedbacks) }} records</div>
    </div>

</div>
</body>
</html>
