<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FeedbackReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request): View
    {
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

    public function exportCsv(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()?->canViewReports(), 403);
        $feedbacks = $this->buildQuery($request)->get();
        return $this->streamCsvFeedbackReport($feedbacks, $request);
    }

    public function exportExcel(Request $request): StreamedResponse
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
        return response($html)->header('Content-Type', 'text/html');
    }

    public function exportWeeklyCsv(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()?->canViewWeeklyReport(), 403);
        $feedbacks = $this->buildWeeklyQuery($request)->get();
        return $this->streamCsvWeeklyReport($feedbacks);
    }

    public function exportWeeklyExcel(Request $request): StreamedResponse
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
        return response($html)->header('Content-Type', 'text/html');
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
            fputcsv($h, ['Ref #','Source','Feedback Type','Service Category','Report','Theme','Sentiment','Wing','Department','Reviewer','Date Reviewed','Assigned To','Submitted At']);
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

    private function streamExcelFeedbackReport($feedbacks): StreamedResponse
    {
        $filename = 'feedback-report-' . now()->format('Ymd-His') . '.csv';
        return response()->streamDownload(function () use ($feedbacks): void {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Ref #','Source','Feedback Type','Service Category','Report','Theme','Sentiment','Wing','Department','Reviewer','Date Reviewed','Assigned To','Submitted At']);
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
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
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

    private function streamExcelWeeklyReport($feedbacks): StreamedResponse
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
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
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
