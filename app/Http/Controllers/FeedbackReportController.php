<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeedbackReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user?->canViewReports() || $user?->canViewWeeklyReport(), 403);

        $canViewFeedbackReport = $user->canViewReports();
        $canViewWeeklyReport   = $user->canViewWeeklyReport();

        $reports = $canViewFeedbackReport
            ? $this->buildQuery($request)->paginate(20)->appends($request->query())
            : null;

        $weekly = $canViewWeeklyReport
            ? $this->buildWeeklyQuery($request)->paginate(50)->appends($request->query())
            : null;

        $collectionMeans = $canViewWeeklyReport
            ? $this->buildCollectionMeans($request)
            : [];

        $summary  = $this->buildSummary();
        $reviewers = $this->reviewUsers();
        $assignableUsers = $this->assignableUsers();

        $availableYears = Feedback::selectRaw('YEAR(created_at) as yr')
            ->groupBy('yr')->orderByDesc('yr')->pluck('yr');

        return view('reports.feedback', [
            'reports'              => $reports,
            'weekly'               => $weekly,
            'collectionMeans'      => $collectionMeans,
            'summary'              => $summary,
            'reviewers'            => $reviewers,
            'assignableUsers'      => $assignableUsers,
            'availableYears'       => $availableYears,
            'canViewFeedbackReport'=> $canViewFeedbackReport,
            'canViewWeeklyReport'  => $canViewWeeklyReport,
            'filters'              => $request->only([
                'status', 'source', 'reviewed_by', 'assigned_to',
                'search', 'month', 'year', 'feedback_type',
            ]),
        ]);
    }

    public function analytics(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user?->canViewReports() || $user?->canViewWeeklyReport(), 403);

        $filters = $request->only(['month', 'year', 'source', 'department_id', 'location']);

        // ── Base scoped query helper ──
        $base = function () use ($filters): Builder {
            return Feedback::query()
                ->when(!empty($filters['month']),        fn(Builder $q) => $q->whereMonth('created_at', (int)$filters['month']))
                ->when(!empty($filters['year']),         fn(Builder $q) => $q->whereYear('created_at',  (int)$filters['year']))
                ->when(!empty($filters['source']),       fn(Builder $q) => $q->where('source', $filters['source']))
                ->when(!empty($filters['department_id']),fn(Builder $q) => $q->where('department_id', (int)$filters['department_id']))
                ->when(!empty($filters['location']),     fn(Builder $q) => $q->where('location', $filters['location']));
        };

        // ── 1. Sentiment (Feedback Type) ──
        $sentimentRaw = $base()->selectRaw('sentiment, COUNT(*) as cnt')->groupBy('sentiment')->get();
        $sentimentTotal = $sentimentRaw->sum('cnt');
        $sentiment = $sentimentRaw->mapWithKeys(fn($r) => [
            ($r->sentiment ?: 'unknown') => ['count' => $r->cnt, 'pct' => $sentimentTotal > 0 ? round($r->cnt / $sentimentTotal * 100, 1) : 0]
        ])->toArray();

        // ── 2. Collection Means (source) ──
        $sourceRaw = $base()->selectRaw('source, COUNT(*) as cnt')->groupBy('source')->orderByDesc('cnt')->get();
        $sourceTotal = $sourceRaw->sum('cnt');
        $collectionMeans = $sourceRaw->map(fn($r) => [
            'key'   => $r->source,
            'label' => Feedback::SOURCES[$r->source] ?? ucfirst((string)$r->source),
            'count' => $r->cnt,
            'pct'   => $sourceTotal > 0 ? round($r->cnt / $sourceTotal * 100, 1) : 0,
        ])->values()->toArray();

        // ── 3. Theme breakdown by service_category + sentiment ──
        $themeRaw = $base()
            ->selectRaw('service_category, sentiment, theme, COUNT(*) as cnt')
            ->groupBy('service_category', 'sentiment', 'theme')
            ->orderByDesc('cnt')
            ->get();

        $categories = ['opd' => 'OPD', 'ipd' => 'IPD', 'theatre' => 'OTD / Theatre', 'other' => 'Other'];
        $themesByCat = [];
        foreach ($categories as $catKey => $catLabel) {
            foreach (['positive', 'negative'] as $sent) {
                $rows = $themeRaw->filter(fn($r) => $r->service_category === $catKey && $r->sentiment === $sent);
                $total = $rows->sum('cnt');
                $themesByCat[$catKey][$sent] = [
                    'total'  => $total,
                    'themes' => $rows->map(fn($r) => [
                        'key'   => $r->theme,
                        'label' => Feedback::THEMES[$r->theme] ?? ucfirst((string)$r->theme),
                        'count' => $r->cnt,
                        'pct'   => $total > 0 ? round($r->cnt / $total * 100, 1) : 0,
                    ])->values()->toArray(),
                ];
            }
        }

        // ── 4. General feedback theme (all categories) ──
        $generalRaw = $base()->selectRaw('theme, COUNT(*) as cnt')->groupBy('theme')->orderByDesc('cnt')->get();
        $generalTotal = $generalRaw->sum('cnt');
        $generalThemes = $generalRaw->map(fn($r) => [
            'key'   => $r->theme,
            'label' => Feedback::THEMES[$r->theme] ?? ucfirst((string)$r->theme),
            'count' => $r->cnt,
            'pct'   => $generalTotal > 0 ? round($r->cnt / $generalTotal * 100, 1) : 0,
        ])->values()->toArray();

        // ── 5. Monthly trend (current year or filtered year) ──
        $trendYear = !empty($filters['year']) ? (int)$filters['year'] : now()->year;
        $trendRaw = Feedback::query()
            ->whereYear('created_at', $trendYear)
            ->when(!empty($filters['source']),       fn(Builder $q) => $q->where('source', $filters['source']))
            ->when(!empty($filters['department_id']),fn(Builder $q) => $q->where('department_id', (int)$filters['department_id']))
            ->selectRaw('MONTH(created_at) as mo, sentiment, COUNT(*) as cnt')
            ->groupBy('mo', 'sentiment')
            ->orderBy('mo')
            ->get();

        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $trend = ['positive' => array_fill(0, 12, 0), 'negative' => array_fill(0, 12, 0), 'neutral' => array_fill(0, 12, 0)];
        foreach ($trendRaw as $r) {
            $idx = $r->mo - 1;
            if (isset($trend[$r->sentiment])) $trend[$r->sentiment][$idx] = $r->cnt;
        }

        // ── 6. Summary counts ──
        $totalAll     = $base()->count();
        $totalPositive = $base()->where('sentiment', 'positive')->count();
        $totalNegative = $base()->where('sentiment', 'negative')->count();
        $totalNeutral  = $base()->where('sentiment', 'neutral')->count();

        // ── 7. Weekly summary rows ──
        $weeklyRows = Feedback::query()
            ->with(['department'])
            ->when(!empty($filters['month']),        fn(Builder $q) => $q->whereMonth('created_at', (int)$filters['month']))
            ->when(!empty($filters['year']),         fn(Builder $q) => $q->whereYear('created_at',  (int)$filters['year']))
            ->when(!empty($filters['source']),       fn(Builder $q) => $q->where('source', $filters['source']))
            ->when(!empty($filters['department_id']),fn(Builder $q) => $q->where('department_id', (int)$filters['department_id']))
            ->orderBy('created_at')
            ->get();

        // ── 8. Mabinti Centre metrics ──
        $mabintiBase = function () use ($filters): Builder {
            return Feedback::query()
                ->where('location', 'mabinti')
                ->when(!empty($filters['month']),         fn(Builder $q) => $q->whereMonth('created_at', (int)$filters['month']))
                ->when(!empty($filters['year']),          fn(Builder $q) => $q->whereYear('created_at',  (int)$filters['year']))
                ->when(!empty($filters['source']),        fn(Builder $q) => $q->where('source', $filters['source']))
                ->when(!empty($filters['department_id']), fn(Builder $q) => $q->where('department_id', (int)$filters['department_id']));
        };

        $mabintiTotal       = $mabintiBase()->count();
        $mabintiSatisfied   = $mabintiBase()->whereNotNull('product_satisfied')->where('product_satisfied', 1)->count();
        $mabintiUnsatisfied = $mabintiBase()->whereNotNull('product_satisfied')->where('product_satisfied', 0)->count();
        $mabintiSurveyTotal = $mabintiSatisfied + $mabintiUnsatisfied;
        $mabintiSatisfiedPct = $mabintiSurveyTotal > 0
            ? round($mabintiSatisfied / $mabintiSurveyTotal * 100, 1) : null;

        // Product satisfaction by feedback type
        $mabintiByTypeRaw = $mabintiBase()
            ->selectRaw('feedback_type, product_satisfied, COUNT(*) as cnt')
            ->whereNotNull('product_satisfied')
            ->groupBy('feedback_type', 'product_satisfied')
            ->get();
        $mabintiByType = [];
        foreach ($mabintiByTypeRaw as $r) {
            $mabintiByType[$r->feedback_type][$r->product_satisfied ? 'satisfied' : 'unsatisfied'] = $r->cnt;
        }

        // Monthly Mabinti satisfaction trend
        $mabintiTrendRaw = Feedback::query()
            ->where('location', 'mabinti')
            ->whereNotNull('product_satisfied')
            ->whereYear('created_at', $trendYear)
            ->when(!empty($filters['source']),        fn(Builder $q) => $q->where('source', $filters['source']))
            ->selectRaw('MONTH(created_at) as mo, product_satisfied, COUNT(*) as cnt')
            ->groupBy('mo', 'product_satisfied')
            ->orderBy('mo')
            ->get();
        $mabintiTrendSat   = array_fill(0, 12, 0);
        $mabintiTrendUnsat = array_fill(0, 12, 0);
        foreach ($mabintiTrendRaw as $r) {
            $idx = $r->mo - 1;
            if ($r->product_satisfied) $mabintiTrendSat[$idx]   = $r->cnt;
            else                       $mabintiTrendUnsat[$idx] = $r->cnt;
        }

        // Mabinti feedback type breakdown
        $mabintiFeedbackTypeRaw = $mabintiBase()
            ->selectRaw('feedback_type, COUNT(*) as cnt')
            ->groupBy('feedback_type')->orderByDesc('cnt')->get();
        $mabintiFeedbackTypes = $mabintiFeedbackTypeRaw->map(fn($r) => [
            'key'   => $r->feedback_type,
            'label' => __('portal.options.feedback_types.' . $r->feedback_type),
            'count' => $r->cnt,
            'pct'   => $mabintiTotal > 0 ? round($r->cnt / $mabintiTotal * 100, 1) : 0,
        ])->values()->toArray();

        // Top products/services selected by Mabinti customers
        $mabintiProductsRaw = $mabintiBase()->whereNotNull('service_units')->get()
            ->flatMap(fn($f) => (array)($f->service_units ?? []))
            ->countBy()
            ->sortDesc()
            ->take(10);
        $customLabels = \App\Models\LocationServiceItem::active()->pluck('label', 'key')->all();
        $mabintiProducts = $mabintiProductsRaw->map(fn($cnt, $key) => [
            'key'   => $key,
            'label' => $customLabels[$key] ?? __('portal.options.service_units.' . $key),
            'count' => $cnt,
        ])->values()->toArray();

        $departments    = Department::orderBy('name')->get();
        $availableYears = Feedback::selectRaw('YEAR(created_at) as yr')->groupBy('yr')->orderByDesc('yr')->pluck('yr');
        $allLocations   = Feedback::getLocations(false);

        return view('reports.analytics', compact(
            'filters', 'sentiment', 'collectionMeans', 'themesByCat',
            'generalThemes', 'generalTotal', 'trend', 'months', 'trendYear',
            'totalAll', 'totalPositive', 'totalNegative', 'totalNeutral',
            'categories', 'departments', 'availableYears', 'weeklyRows',
            'allLocations',
            'mabintiTotal', 'mabintiSatisfied', 'mabintiUnsatisfied',
            'mabintiSurveyTotal', 'mabintiSatisfiedPct',
            'mabintiByType', 'mabintiFeedbackTypes', 'mabintiProducts',
            'mabintiTrendSat', 'mabintiTrendUnsat'
        ));
    }

    public function exportAnalyticsExcel(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless(Auth::user()?->canViewReports() || Auth::user()?->canViewWeeklyReport(), 403);

        $filters = $request->only(['month', 'year', 'source', 'department_id', 'location']);
        $base = function () use ($filters): Builder {
            return Feedback::query()
                ->when(!empty($filters['month']),         fn(Builder $q) => $q->whereMonth('created_at', (int)$filters['month']))
                ->when(!empty($filters['year']),          fn(Builder $q) => $q->whereYear('created_at',  (int)$filters['year']))
                ->when(!empty($filters['source']),        fn(Builder $q) => $q->where('source', $filters['source']))
                ->when(!empty($filters['department_id']), fn(Builder $q) => $q->where('department_id', (int)$filters['department_id']))
                ->when(!empty($filters['location']),      fn(Builder $q) => $q->where('location', $filters['location']));
        };

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setTitle('CCBRT Consolidated Analytics Report');

        // ── Style constants ──
        $hdrFill   = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065321']];
        $subFill   = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0b6b2c']];
        $posFill   = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e6f9ee']];
        $negFill   = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'fde8e8']];
        $altFill   = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f6fbf8']];
        $whtFill   = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']];
        $thinBdr   = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e2e8f0']];

        $monthNames = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
                       7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
        $filterLabel = 'Generated: ' . now()->format('d M Y, H:i');
        if (!empty($filters['month'])) $filterLabel .= '  |  Month: ' . ($monthNames[(int)$filters['month']] ?? $filters['month']);
        if (!empty($filters['year']))  $filterLabel .= '  |  Year: '  . $filters['year'];
        if (!empty($filters['source'])) $filterLabel .= '  |  Source: ' . (Feedback::SOURCES[$filters['source']] ?? $filters['source']);

        // ── Reusable: brand a sheet's top 2 rows ──
        $brandSheet = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $title, int $cols = 3) use ($hdrFill, $filterLabel) {
            $last = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cols);
            $ws->setCellValue('A1', 'CCBRT — ' . $title);
            $ws->setCellValue('A2', $filterLabel);
            $ws->mergeCells('A1:' . $last . '1');
            $ws->mergeCells('A2:' . $last . '2');
            $ws->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']], 'fill' => $hdrFill, 'alignment' => ['indent' => 1]]);
            $ws->getStyle('A2')->applyFromArray(['font' => ['size' => 9, 'color' => ['rgb' => '3d6b4f']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eef7e8']], 'alignment' => ['indent' => 1]]);
            $ws->getRowDimension(1)->setRowHeight(24);
            $ws->getRowDimension(2)->setRowHeight(14);
            $ws->getColumnDimension('A')->setWidth(40);
            $ws->getColumnDimension('B')->setWidth(10);
            $ws->getColumnDimension('C')->setWidth(10);
        };

        // ── Reusable: write a labelled count/% table block ──
        $writeBlock = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, int &$row, string $title, array $data, string $sent = '') use ($posFill, $negFill, $subFill, $altFill, $whtFill, $thinBdr) {
            $titleFill  = $sent === 'positive' ? $posFill : ($sent === 'negative' ? $negFill : $subFill);
            $titleColor = $sent === 'positive' ? '065f46'  : ($sent === 'negative' ? '991b1b'  : 'FFFFFF');
            $ws->setCellValue('A' . $row, $title);
            $ws->mergeCells('A' . $row . ':C' . $row);
            $ws->getStyle('A' . $row)->applyFromArray(['font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $titleColor]], 'fill' => $titleFill, 'alignment' => ['indent' => 1]]);
            $ws->getRowDimension($row)->setRowHeight(18);
            $row++;

            $ws->fromArray(['THEME / LABEL', 'COUNTS', '%'], null, 'A' . $row);
            $ws->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a6b3a']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $ws->getStyle('A' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1]]);
            $ws->getRowDimension($row)->setRowHeight(16);
            $row++;

            if (empty($data)) {
                $ws->setCellValue('A' . $row, 'No data for this period');
                $ws->mergeCells('A' . $row . ':C' . $row);
                $ws->getStyle('A' . $row)->applyFromArray(['font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '94a3b8']]]);
                $ws->getRowDimension($row)->setRowHeight(14);
                $row += 2;
                return;
            }

            foreach ($data as $i => $item) {
                $fill = ($i % 2 === 0) ? $altFill : $whtFill;
                $ws->fromArray([$item['label'], $item['count'], $item['pct'] . '%'], null, 'A' . $row);
                $ws->getStyle('A' . $row . ':C' . $row)->applyFromArray(['fill' => $fill, 'borders' => ['bottom' => $thinBdr], 'font' => ['size' => 9]]);
                $ws->getStyle('B' . $row . ':C' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER], 'font' => ['bold' => true, 'size' => 9]]);
                $ws->getRowDimension($row)->setRowHeight(15);
                $row++;
            }
            $total = array_sum(array_column($data, 'count'));
            $ws->fromArray(['GRAND TOTAL', $total, '100%'], null, 'A' . $row);
            $ws->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                'font'      => ['bold' => true, 'size' => 9],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eef7e8']],
                'borders'   => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '0b6b2c']]],
            ]);
            $ws->getStyle('B' . $row . ':C' . $row)->applyFromArray(['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
            $ws->getRowDimension($row)->setRowHeight(16);
            $row += 2;
        };

        // ── Helper: build theme data for a category+sentiment ──
        $themeData = function (string $cat, string $sent) use ($base): array {
            $rows = $base()->selectRaw('theme, COUNT(*) as cnt')
                ->where('service_category', $cat)->where('sentiment', $sent)
                ->groupBy('theme')->orderByDesc('cnt')->get();
            $tot = $rows->sum('cnt');
            return $rows->map(fn($r) => [
                'label' => Feedback::THEMES[$r->theme] ?? ucfirst((string)$r->theme),
                'count' => $r->cnt,
                'pct'   => $tot > 0 ? round($r->cnt / $tot * 100, 1) : 0,
            ])->values()->toArray();
        };

        // ════════════════════════════════════
        // SHEET 1: Overview — Feedback Type + Collection Means
        // ════════════════════════════════════
        $sh1 = $spreadsheet->getActiveSheet()->setTitle('Overview');
        $brandSheet($sh1, 'Analytics Overview');
        $row = 4;

        $sentRaw  = $base()->selectRaw('sentiment, COUNT(*) as cnt')->groupBy('sentiment')->get();
        $sentTot  = $sentRaw->sum('cnt');
        $sentData = $sentRaw->map(fn($r) => ['label' => ucfirst($r->sentiment ?: 'Unknown'), 'count' => $r->cnt, 'pct' => $sentTot > 0 ? round($r->cnt / $sentTot * 100, 1) : 0])->values()->toArray();
        $writeBlock($sh1, $row, 'FEEDBACK TYPE (SENTIMENT)', $sentData);

        $srcRaw  = $base()->selectRaw('source, COUNT(*) as cnt')->groupBy('source')->orderByDesc('cnt')->get();
        $srcTot  = $srcRaw->sum('cnt');
        $srcData = $srcRaw->map(fn($r) => ['label' => Feedback::SOURCES[$r->source] ?? ucfirst((string)$r->source), 'count' => $r->cnt, 'pct' => $srcTot > 0 ? round($r->cnt / $srcTot * 100, 1) : 0])->values()->toArray();
        $writeBlock($sh1, $row, 'COLLECTION MEANS', $srcData);

        // ════════════════════════════════════
        // SHEET 2: General Customer Feedback
        // ════════════════════════════════════
        $sh2 = $spreadsheet->createSheet()->setTitle('General Feedback');
        $brandSheet($sh2, 'General Customer Feedback');
        $row = 4;
        $genRaw  = $base()->selectRaw('theme, COUNT(*) as cnt')->groupBy('theme')->orderByDesc('cnt')->get();
        $genTot  = $genRaw->sum('cnt');
        $genData = $genRaw->map(fn($r) => ['label' => Feedback::THEMES[$r->theme] ?? ucfirst((string)$r->theme), 'count' => $r->cnt, 'pct' => $genTot > 0 ? round($r->cnt / $genTot * 100, 1) : 0])->values()->toArray();
        $writeBlock($sh2, $row, 'GENERAL FEEDBACK — ALL THEMES', $genData);

        // ════════════════════════════════════
        // SHEET 3: OPD
        // ════════════════════════════════════
        $sh3 = $spreadsheet->createSheet()->setTitle('OPD Feedback');
        $brandSheet($sh3, 'OPD Feedback');
        $row = 4;
        $writeBlock($sh3, $row, 'OPD POSITIVE FEEDBACK', $themeData('opd', 'positive'), 'positive');
        $writeBlock($sh3, $row, 'OPD NEGATIVE FEEDBACK', $themeData('opd', 'negative'), 'negative');

        // ════════════════════════════════════
        // SHEET 4: IPD
        // ════════════════════════════════════
        $sh4 = $spreadsheet->createSheet()->setTitle('IPD Feedback');
        $brandSheet($sh4, 'IPD Feedback');
        $row = 4;
        $writeBlock($sh4, $row, 'IPD POSITIVE FEEDBACK', $themeData('ipd', 'positive'), 'positive');
        $writeBlock($sh4, $row, 'IPD NEGATIVE FEEDBACK', $themeData('ipd', 'negative'), 'negative');

        // ════════════════════════════════════
        // SHEET 5: OTD / Theatre
        // ════════════════════════════════════
        $sh5 = $spreadsheet->createSheet()->setTitle('OTD Feedback');
        $brandSheet($sh5, 'OTD / Theatre Feedback');
        $row = 4;
        $writeBlock($sh5, $row, 'OTD POSITIVE FEEDBACK', $themeData('theatre', 'positive'), 'positive');
        $writeBlock($sh5, $row, 'OTD NEGATIVE FEEDBACK', $themeData('theatre', 'negative'), 'negative');

        // ════════════════════════════════════
        // SHEET 6: Weekly Summary (raw rows)
        // ════════════════════════════════════
        $sh6 = $spreadsheet->createSheet()->setTitle('Weekly Summary');
        $weeklyHeaders = ['Collection Means','Date','Month','Tel # of Person','Comment / Suggestion','Theme','Feedback Type','Sentiment','Wing','Unit','Platform'];
        $lastWCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($weeklyHeaders));
        $brandSheet($sh6, 'General Submission Sheet — Weekly Summary', count($weeklyHeaders));
        $sh6->fromArray($weeklyHeaders, null, 'A4');
        $sh6->getStyle('A4:' . $lastWCol . '4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0b6b2c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '065321']]],
        ]);
        $sh6->getRowDimension(4)->setRowHeight(18);
        // widen columns for weekly sheet
        foreach ([14, 8, 12, 16, 50, 16, 14, 12, 12, 20, 14] as $i => $w) {
            $sh6->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1))->setWidth($w);
        }

        $weeklyFeedbacks = Feedback::query()->with(['department'])
            ->when(!empty($filters['month']),         fn(Builder $q) => $q->whereMonth('created_at', (int)$filters['month']))
            ->when(!empty($filters['year']),          fn(Builder $q) => $q->whereYear('created_at',  (int)$filters['year']))
            ->when(!empty($filters['source']),        fn(Builder $q) => $q->where('source', $filters['source']))
            ->when(!empty($filters['department_id']), fn(Builder $q) => $q->where('department_id', (int)$filters['department_id']))
            ->orderBy('created_at')->get();

        $wRow = 5;
        foreach ($weeklyFeedbacks as $f) {
            $sh6->fromArray([
                $f->getSourceLabel(),
                $f->created_at?->format('d') ?? '',
                $f->created_at?->format('F') ?? '',
                $f->phone ?? '',
                $f->message ?? $f->overall_experience ?? '',
                $f->getThemeLabel(),
                $f->getFeedbackTypeLabel(),
                $f->getSentimentLabel(),
                $f->getWingLabel(),
                $f->department?->name ?? (is_array($f->service_units) ? implode(', ', $f->service_units) : ($f->service_units ?? '')),
                $f->getServiceCategoryLabel(),
            ], null, 'A' . $wRow);
            $sh6->getStyle('A' . $wRow . ':' . $lastWCol . $wRow)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => ($wRow % 2 === 0) ? 'f6fbf8' : 'FFFFFF']],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e2e8f0']]],
                'font'    => ['size' => 9],
            ]);
            $sh6->getRowDimension($wRow)->setRowHeight(15);
            $wRow++;
        }
        $sh6->getStyle('E5:E' . max(5, $wRow - 1))->getAlignment()->setWrapText(true);
        $sh6->freezePane('A5');
        $sh6->setAutoFilter('A4:' . $lastWCol . '4');

        // ════════════════════════════════════
        // SHEET 7: Mabinti Centre Analytics
        // ════════════════════════════════════
        $sh7 = $spreadsheet->createSheet()->setTitle('Mabinti Centre');
        $mabintiHeaders = ['Date','Feedback Type','Product \ Service','Other Product','Service Rating','Satisfied?','Satisfaction Comment','Overall Experience'];
        $lastMCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($mabintiHeaders));
        $brandSheet($sh7, 'Mabinti Centre Feedback', count($mabintiHeaders));
        $sh7->fromArray($mabintiHeaders, null, 'A4');
        $sh7->getStyle('A4:' . $lastMCol . '4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '14532d']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '065321']]],
        ]);
        $sh7->getRowDimension(4)->setRowHeight(18);
        foreach ([16, 14, 30, 20, 14, 12, 40, 50] as $i => $w) {
            $sh7->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1))->setWidth($w);
        }
        $mabintiFeedbacks = Feedback::query()
            ->where('location', 'mabinti')
            ->when(!empty($filters['month']),         fn(Builder $q) => $q->whereMonth('created_at', (int)$filters['month']))
            ->when(!empty($filters['year']),          fn(Builder $q) => $q->whereYear('created_at',  (int)$filters['year']))
            ->orderBy('created_at')->get();
        $customLbls = \App\Models\LocationServiceItem::active()->pluck('label', 'key')->all();
        $mRow = 5;
        foreach ($mabintiFeedbacks as $f) {
            $units = collect((array)($f->service_units ?? []))->map(function ($key) use ($customLbls) {
                $t = __('portal.options.service_units.' . $key);
                return ($t === 'portal.options.service_units.' . $key) ? ($customLbls[$key] ?? $key) : $t;
            })->implode(', ');
            $satisfied = is_null($f->product_satisfied) ? '—' : ($f->product_satisfied ? 'Yes' : 'No');
            $sh7->fromArray([
                $f->created_at?->format('d M Y') ?? '',
                $f->getFeedbackTypeLabel(),
                $units,
                $f->service_unit_other_text ?? '',
                $f->service_rating ? $f->getServiceRatingLabel() : '',
                $satisfied,
                $f->product_satisfaction_comment ?? '',
                $f->overall_experience ?? $f->message ?? '',
            ], null, 'A' . $mRow);
            $sh7->getStyle('A' . $mRow . ':' . $lastMCol . $mRow)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => ($mRow % 2 === 0) ? 'f0fdf4' : 'FFFFFF']],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'bbf7d0']]],
                'font'    => ['size' => 9],
            ]);
            $sh7->getRowDimension($mRow)->setRowHeight(15);
            $mRow++;
        }
        $sh7->getStyle('G5:H' . max(5, $mRow - 1))->getAlignment()->setWrapText(true);
        $sh7->freezePane('A5');
        $sh7->setAutoFilter('A4:' . $lastMCol . '4');

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'CCBRT-Analytics-Report-' . now()->format('Ymd-His') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()?->canViewReports(), 403);
        $feedbacks = $this->buildQuery($request)->get();
        return $this->streamCsvFeedbackReport($feedbacks, $request);
    }

    public function exportExcel(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless(Auth::user()?->canViewReports(), 403);
        $feedbacks = $this->buildQuery($request)->get();
        return $this->streamExcelFeedbackReport($feedbacks);
    }

    public function exportPdf(Request $request): Response
    {
        abort_unless(Auth::user()?->canViewReports(), 403);
        $feedbacks = $this->buildQuery($request)->get();
        $summary   = $this->buildSummary();
        $filters   = $request->only(['month', 'year', 'feedback_type', 'status', 'source']);
        $html = view('reports.pdf.feedback_report', compact('feedbacks', 'summary', 'filters'))->render();
        $filename  = 'CCBRT-Feedback-Report-' . now()->format('Ymd-His') . '.html';
        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportWeeklyCsv(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()?->canViewWeeklyReport(), 403);
        $feedbacks = $this->buildWeeklyQuery($request)->get();
        return $this->streamCsvWeeklyReport($feedbacks);
    }

    public function exportWeeklyExcel(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless(Auth::user()?->canViewWeeklyReport(), 403);
        $feedbacks = $this->buildWeeklyQuery($request)->get();
        return $this->streamExcelWeeklyReport($feedbacks);
    }

    public function exportWeeklyPdf(Request $request): Response
    {
        abort_unless(Auth::user()?->canViewWeeklyReport(), 403);
        $feedbacks = $this->buildWeeklyQuery($request)->get();
        $filters   = $request->only(['month', 'year', 'feedback_type', 'source']);
        $html = view('reports.pdf.weekly_report', compact('feedbacks', 'filters'))->render();
        $filename  = 'CCBRT-Weekly-Report-' . now()->format('Ymd-His') . '.html';
        return response($html, 200, [
            'Content-Type'        => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function buildQuery(Request $request): Builder
    {
        return Feedback::query()
            ->with(['assignedTo', 'reviewedBy', 'createdBy', 'patientResponses.sender', 'department'])
            ->when($request->filled('status'), fn(Builder $q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('source'), fn(Builder $q) => $q->where('source', $request->string('source')->toString()))
            ->when($request->filled('reviewed_by'), fn(Builder $q) => $q->where('reviewed_by', $request->integer('reviewed_by')))
            ->when($request->filled('assigned_to'), fn(Builder $q) => $q->where('assigned_to', $request->integer('assigned_to')))
            ->when($request->filled('feedback_type'), fn(Builder $q) => $q->where('feedback_type', $request->string('feedback_type')->toString()))
            ->when($request->filled('month'), fn(Builder $q) => $q->whereMonth('created_at', $request->integer('month')))
            ->when($request->filled('year'), fn(Builder $q) => $q->whereYear('created_at', $request->integer('year')))
            ->when($request->filled('search'), function (Builder $q) use ($request): void {
                $s = trim($request->string('search')->toString());
                $q->where(fn(Builder $sq) => $sq
                    ->where('reference_no', 'like', "%{$s}%")
                    ->orWhere('patient_name', 'like', "%{$s}%")
                    ->orWhere('overall_experience', 'like', "%{$s}%")
                    ->orWhere('message', 'like', "%{$s}%"));
            })
            ->orderByDesc('created_at');
    }

    private function buildCollectionMeans(Request $request): array
    {
        $rows = Feedback::query()
            ->when($request->filled('month'), fn(Builder $q) => $q->whereMonth('created_at', $request->integer('month')))
            ->when($request->filled('year'),  fn(Builder $q) => $q->whereYear('created_at',  $request->integer('year')))
            ->selectRaw('source, COUNT(*) as cnt')
            ->groupBy('source')
            ->orderByDesc('cnt')
            ->get();

        $total = $rows->sum('cnt');
        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'source' => $row->source,
                'label'  => Feedback::SOURCES[$row->source] ?? ucfirst((string) $row->source),
                'count'  => $row->cnt,
                'pct'    => $total > 0 ? round(($row->cnt / $total) * 100, 1) : 0,
            ];
        }
        return ['rows' => $result, 'total' => $total];
    }

    private function buildWeeklyQuery(Request $request): Builder
    {
        return Feedback::query()
            ->with(['department'])
            ->when($request->filled('source'), fn(Builder $q) => $q->where('source', $request->string('source')->toString()))
            ->when($request->filled('feedback_type'), fn(Builder $q) => $q->where('feedback_type', $request->string('feedback_type')->toString()))
            ->when($request->filled('month'), fn(Builder $q) => $q->whereMonth('created_at', $request->integer('month')))
            ->when($request->filled('year'), fn(Builder $q) => $q->whereYear('created_at', $request->integer('year')))
            ->orderBy('created_at');
    }

    private function buildSummary(): array
    {
        return [
            'total'          => Feedback::count(),
            'portal'         => Feedback::where('source', 'portal')->count(),
            'manual'         => Feedback::where('source', 'manual')->count(),
            'other'          => Feedback::where('source', 'other')->count(),
            'reviewed'       => Feedback::whereNotNull('reviewed_at')->count(),
            'pending_review' => Feedback::whereNull('reviewed_at')->count(),
        ];
    }

    private function streamCsvFeedbackReport($feedbacks, Request $request): StreamedResponse
    {
        $filename = 'feedback-report-' . now()->format('Ymd-His') . '.csv';
        return response()->streamDownload(function () use ($feedbacks): void {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Ref #','Source','Feedback Type','Service Category','Report Excerpt','Theme','Sentiment','Wing','Department','Reviewer','Date Reviewed','Assigned To','Submitted At']);
            foreach ($feedbacks as $f) {
                fputcsv($h, [
                    $f->reference_no,
                    $f->getSourceLabel(),
                    $f->getFeedbackTypeLabel(),
                    $f->getServiceCategoryLabel(),
                    $f->report_excerpt,
                    $f->getThemeLabel(),
                    $f->getSentimentLabel(),
                    $f->getWingLabel(),
                    $f->department?->name ?? '—',
                    $f->reviewedBy?->getFullName() ?? '',
                    $f->reviewed_at?->format('Y-m-d H:i') ?? '',
                    $f->assignedTo?->getFullName() ?? '',
                    $f->created_at?->format('Y-m-d H:i') ?? '',
                ]);
            }
            fclose($h);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function streamExcelFeedbackReport($feedbacks): \Symfony\Component\HttpFoundation\Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Feedback Report');

        // ── Meta rows ──
        $sheet->setCellValue('A1', 'CCBRT Feedback Report');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y, H:i') . '   |   Total Records: ' . count($feedbacks));
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065321']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['rgb' => '3d6b4f']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eef7e8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(16);

        // ── Header row ──
        $headers = ['Ref #','Source','Feedback Type','Service Category','Report Excerpt','Theme','Sentiment','Wing','Department','Reviewer','Reviewer Role','Date Reviewed','Assigned To','Submitted At'];
        $sheet->fromArray($headers, null, 'A4');
        $headerStyle = [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0b6b2c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '065321']]],
        ];
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A4:' . $lastCol . '4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // ── Data rows ──
        $row = 5;
        foreach ($feedbacks as $f) {
            $sheet->fromArray([
                $f->reference_no,
                $f->getSourceLabel(),
                $f->getFeedbackTypeLabel(),
                $f->getServiceCategoryLabel(),
                $f->report_excerpt,
                $f->getThemeLabel(),
                $f->getSentimentLabel(),
                $f->getWingLabel(),
                $f->department?->name ?? '',
                $f->reviewedBy?->getFullName() ?? '',
                $f->reviewedBy?->getRoleLabel() ?? '',
                $f->reviewed_at?->format('d M Y H:i') ?? '',
                $f->assignedTo?->getFullName() ?? '',
                $f->created_at?->format('d M Y H:i') ?? '',
            ], null, 'A' . $row);

            // Alternating row fill
            $fillColor = ($row % 2 === 0) ? 'f6fbf8' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e2e8f0']]],
                'font'    => ['size' => 9],
            ]);

            // Colour-code feedback type cell (col C)
            $typeColors = ['Complaint' => 'fee2e2', 'Compliment' => 'd1fae5', 'Suggestion' => 'dbeafe', 'Enquiry' => 'f3e8ff'];
            $typeLabel = $f->getFeedbackTypeLabel();
            if (isset($typeColors[$typeLabel])) {
                $sheet->getStyle('C' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $typeColors[$typeLabel]]],
                    'font' => ['bold' => true, 'size' => 9],
                ]);
            }

            // Colour-code sentiment cell (col G)
            $sentColors = ['Positive' => 'd1fae5', 'Negative' => 'fee2e2', 'Neutral' => 'e5e7eb'];
            $sentLabel = $f->getSentimentLabel();
            if (isset($sentColors[$sentLabel])) {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $sentColors[$sentLabel]]],
                    'font' => ['bold' => true, 'size' => 9],
                ]);
            }

            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;
        }

        // ── Column widths ──
        $colWidths = [18, 12, 14, 20, 50, 16, 12, 10, 18, 22, 20, 18, 22, 18];
        foreach ($colWidths as $i => $width) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Wrap text on Report Excerpt column (E)
        $sheet->getStyle('E5:E' . ($row - 1))->getAlignment()->setWrapText(true);

        // Freeze header
        $sheet->freezePane('A5');

        // Auto-filter
        $sheet->setAutoFilter('A4:' . $lastCol . '4');

        $filename = 'CCBRT-Feedback-Report-' . now()->format('Ymd-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function streamCsvWeeklyReport($feedbacks): StreamedResponse
    {
        $filename = 'weekly-report-' . now()->format('Ymd-His') . '.csv';
        return response()->streamDownload(function () use ($feedbacks): void {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Collection Means','Date','Month','Tel # of Person','Comment / Suggestion (Kiswahili)','Theme','Feedback Type','Sentiment','Wing','Unit','Platform']);
            foreach ($feedbacks as $f) {
                fputcsv($h, [
                    $f->getSourceLabel(),
                    $f->created_at?->format('d') ?? '',
                    $f->created_at?->format('F') ?? '',
                    $f->phone ?? '',
                    $f->message ?? $f->overall_experience ?? '',
                    $f->getThemeLabel(),
                    $f->getFeedbackTypeLabel(),
                    $f->getSentimentLabel(),
                    $f->getWingLabel(),
                    $f->department?->name ?? (is_array($f->service_units) ? implode(', ', $f->service_units) : ($f->service_units ?? '')),
                    $f->getServiceCategoryLabel(),
                ]);
            }
            fclose($h);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function streamExcelWeeklyReport($feedbacks): \Symfony\Component\HttpFoundation\Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Weekly Submissions');

        // ── Meta rows ──
        $sheet->setCellValue('A1', 'CCBRT General Submission Sheet — Weekly Report');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y, H:i') . '   |   Total Records: ' . count($feedbacks));
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065321']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 9, 'color' => ['rgb' => '3d6b4f']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'eef7e8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(16);

        // ── Header row ──
        $headers = ['Collection Means','Date','Month','Tel # of Person','Comment / Suggestion','Theme','Feedback Type','Sentiment','Wing','Unit','Platform'];
        $sheet->fromArray($headers, null, 'A4');
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A4:' . $lastCol . '4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0b6b2c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '065321']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // ── Data rows ──
        $row = 5;
        foreach ($feedbacks as $f) {
            $sheet->fromArray([
                $f->getSourceLabel(),
                $f->created_at?->format('d') ?? '',
                $f->created_at?->format('F') ?? '',
                $f->phone ?? '',
                $f->message ?? $f->overall_experience ?? '',
                $f->getThemeLabel(),
                $f->getFeedbackTypeLabel(),
                $f->getSentimentLabel(),
                $f->getWingLabel(),
                $f->department?->name ?? (is_array($f->service_units) ? implode(', ', $f->service_units) : ($f->service_units ?? '')),
                $f->getServiceCategoryLabel(),
            ], null, 'A' . $row);

            $fillColor = ($row % 2 === 0) ? 'f6fbf8' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'e2e8f0']]],
                'font'    => ['size' => 9],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(16);
            $row++;
        }

        // ── Column widths ──
        foreach ([14, 8, 12, 16, 50, 16, 14, 12, 12, 20, 14] as $i => $width) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->getStyle('E5:E' . ($row - 1))->getAlignment()->setWrapText(true);
        $sheet->freezePane('A5');
        $sheet->setAutoFilter('A4:' . $lastCol . '4');

        $filename = 'CCBRT-Weekly-Report-' . now()->format('Ymd-His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function reviewUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->whereIn('role', User::FEEDBACK_MANAGEMENT_ROLES)
            ->orderBy('fname')->orderBy('lname')->get();
    }

    private function assignableUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->whereIn('role', User::FEEDBACK_MANAGEMENT_ROLES)
            ->orderBy('fname')->orderBy('lname')->get();
    }
}
