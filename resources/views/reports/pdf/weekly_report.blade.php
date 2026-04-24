<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Weekly Submission Report</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; margin: 20px; }
    h2 { font-size: 15px; margin-bottom: 4px; }
    .meta { color: #666; font-size: 10px; margin-bottom: 14px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #1a3353; color: #fff; padding: 6px 6px; text-align: left; font-size: 9px; text-transform: uppercase; }
    td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    tr:nth-child(even) td { background: #f8f9fa; }
    .pos { background:#d1fae5; color:#065f46; border-radius:4px; padding:1px 5px; }
    .neg { background:#fee2e2; color:#991b1b; border-radius:4px; padding:1px 5px; }
    .sug { background:#dbeafe; color:#1e40af; border-radius:4px; padding:1px 5px; }
    @media print { @page { size: A4 landscape; margin: 12mm; } }
</style>
</head>
<body>
<h2>CCBRT General Submission Sheet — Weekly Report</h2>
<div class="meta">
    Generated: {{ now()->format('d M Y, H:i') }}
    @if(!empty($filters['month'])) &nbsp;|&nbsp; Month: {{ [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'][(int)$filters['month']] ?? '' }} @endif
    @if(!empty($filters['year'])) &nbsp;|&nbsp; Year: {{ $filters['year'] }} @endif
    &nbsp;|&nbsp; Total records: {{ count($feedbacks) }}
</div>

<table>
    <thead>
        <tr>
            <th>Collection Means</th>
            <th>Date</th>
            <th>Month</th>
            <th>Tel # of Person</th>
            <th style="min-width:200px;">Comment / Suggestion (Kiswahili)</th>
            <th>Theme</th>
            <th>Feedback Type</th>
            <th>Sentiment</th>
            <th>Wing</th>
            <th>Unit</th>
            <th>Platform</th>
        </tr>
    </thead>
    <tbody>
        @forelse($feedbacks as $f)
        <tr>
            <td><strong>{{ $f->getSourceLabel() }}</strong></td>
            <td style="text-align:center;">{{ $f->created_at?->format('d') }}</td>
            <td style="text-align:center;">{{ $f->created_at?->format('F') }}</td>
            <td>{{ $f->phone ?? '' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($f->message ?? $f->overall_experience ?? '', 150) }}</td>
            <td>{{ $f->getThemeLabel() }}</td>
            <td>
                @if($f->feedback_type === 'compliment') <span class="pos">Positive</span>
                @elseif($f->feedback_type === 'complaint') <span class="neg">Negative</span>
                @else <span class="sug">{{ $f->getFeedbackTypeLabel() }}</span>
                @endif
            </td>
            <td>
                @if($f->sentiment === 'positive') <span class="pos">Positive</span>
                @elseif($f->sentiment === 'negative') <span class="neg">Negative</span>
                @elseif($f->sentiment) <span class="sug">{{ $f->getSentimentLabel() }}</span>
                @else —
                @endif
            </td>
            <td>{{ $f->getWingLabel() }}</td>
            <td>{{ $f->department?->name ?? (is_array($f->service_units) && count($f->service_units) ? implode(', ', $f->service_units) : '') }}</td>
            <td>{{ $f->getServiceCategoryLabel() }}</td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center;padding:20px;color:#888;">No records found.</td></tr>
        @endforelse
    </tbody>
</table>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
