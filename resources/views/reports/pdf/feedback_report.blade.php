<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Feedback Report</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 11px; color: #222; margin: 20px; }
    h2 { font-size: 16px; margin-bottom: 4px; }
    .meta { color: #666; font-size: 10px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #1a3353; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
    td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    tr:nth-child(even) td { background: #f8f9fa; }
    .badge-pos { background:#d1fae5; color:#065f46; border-radius:4px; padding:1px 6px; }
    .badge-neg { background:#fee2e2; color:#991b1b; border-radius:4px; padding:1px 6px; }
    .badge-neu { background:#e5e7eb; color:#374151; border-radius:4px; padding:1px 6px; }
    .summary { display:flex; gap:16px; flex-wrap:wrap; margin-bottom:16px; }
    .summary-item { background:#f1f5f9; border-radius:6px; padding:8px 14px; min-width:100px; }
    .summary-item .val { font-size:20px; font-weight:bold; }
    .summary-item .lbl { font-size:10px; color:#64748b; }
    @media print { @page { size: A4 landscape; margin: 15mm; } }
</style>
</head>
<body>
<h2>CCBRT Feedback Report</h2>
<div class="meta">
    Generated: {{ now()->format('d M Y, H:i') }}
    @if(!empty($filters['month'])) &nbsp;|&nbsp; Month: {{ [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'][(int)$filters['month']] ?? '' }} @endif
    @if(!empty($filters['year'])) &nbsp;|&nbsp; Year: {{ $filters['year'] }} @endif
    @if(!empty($filters['feedback_type'])) &nbsp;|&nbsp; Type: {{ ucfirst($filters['feedback_type']) }} @endif
    @if(!empty($filters['status'])) &nbsp;|&nbsp; Status: {{ ucfirst($filters['status']) }} @endif
    &nbsp;|&nbsp; Total records: {{ count($feedbacks) }}
</div>

<div class="summary">
    <div class="summary-item"><div class="val">{{ $summary['total'] }}</div><div class="lbl">Total</div></div>
    <div class="summary-item"><div class="val">{{ $summary['portal'] }}</div><div class="lbl">Portal</div></div>
    <div class="summary-item"><div class="val">{{ $summary['manual'] }}</div><div class="lbl">Manual</div></div>
    <div class="summary-item"><div class="val">{{ $summary['reviewed'] }}</div><div class="lbl">Reviewed</div></div>
    <div class="summary-item"><div class="val">{{ $summary['pending_review'] }}</div><div class="lbl">Pending</div></div>
</div>

<table>
    <thead>
        <tr>
            <th>Ref #</th>
            <th>Source</th>
            <th>Type</th>
            <th>Category</th>
            <th>Report</th>
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
        @forelse($feedbacks as $f)
        <tr>
            <td>{{ $f->reference_no }}</td>
            <td>{{ $f->getSourceLabel() }}</td>
            <td>{{ $f->getFeedbackTypeLabel() }}</td>
            <td>{{ $f->getServiceCategoryLabel() }}</td>
            <td>{{ \Illuminate\Support\Str::limit($f->report_excerpt, 100) }}</td>
            <td>{{ $f->getThemeLabel() }}</td>
            <td>
                @if($f->sentiment === 'positive') <span class="badge-pos">Positive</span>
                @elseif($f->sentiment === 'negative') <span class="badge-neg">Negative</span>
                @else <span class="badge-neu">{{ $f->getSentimentLabel() }}</span>
                @endif
            </td>
            <td>{{ $f->getWingLabel() }}</td>
            <td>{{ $f->department?->name ?? '—' }}</td>
            <td>{{ $f->reviewedBy?->getFullName() ?? '—' }}</td>
            <td>{{ $f->reviewed_at?->format('d M Y') ?? '—' }}</td>
            <td>{{ $f->created_at?->format('d M Y') ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="12" style="text-align:center;padding:20px;color:#888;">No records found.</td></tr>
        @endforelse
    </tbody>
</table>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
