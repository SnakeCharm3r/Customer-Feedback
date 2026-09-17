<?php

namespace App\Http\Controllers;

use App\Models\Escalation;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request): View
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        $periodOptions = [
            'today' => 'Today',
            'week' => 'This Week',
            'quarter' => 'This Quarter',
            'all' => 'All Time',
        ];
        $period = array_key_exists($request->query('period', 'week'), $periodOptions)
            ? $request->query('period', 'week')
            : 'week';
        $now = Carbon::now();
        $periodStart = match ($period) {
            'today' => $now->copy()->startOfDay(),
            'week' => $now->copy()->startOfWeek(),
            'quarter' => $now->copy()->firstOfQuarter()->startOfDay(),
            default => null,
        };
        $periodEnd = $now->copy()->endOfDay();
        $periodLabel = $periodOptions[$period];
        $periodDateLabel = $periodStart
            ? $periodStart->format('d M Y').' – '.$periodEnd->format('d M Y')
            : 'From the first submission to '.$periodEnd->format('d M Y');

        $feedbackQuery = static fn () => Feedback::query()
            ->when($periodStart, fn ($query) => $query->where('created_at', '>=', $periodStart))
            ->where('created_at', '<=', $periodEnd);

        $statusCounts = $feedbackQuery()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $typeCounts = $feedbackQuery()
            ->selectRaw('feedback_type, COUNT(*) as aggregate')
            ->groupBy('feedback_type')
            ->pluck('aggregate', 'feedback_type');

        $totalFeedback = $statusCounts->sum();
        $statusCounts->put('new', $feedbackQuery()->freshNew()->count());
        $statusCounts->put('open', $feedbackQuery()->agedOpen()->count());
        $resolvedCount = (int) $statusCounts->get('responded', 0) + (int) $statusCounts->get('closed', 0);
        $responseRate = $totalFeedback > 0 ? (int) round(($resolvedCount / $totalFeedback) * 100) : 0;

        $chartDays = [];
        $chartCounts = [];
        $chartDates = $feedbackQuery()->pluck('created_at')->map(fn ($date) => Carbon::parse($date));

        if (in_array($period, ['quarter', 'all'], true)) {
            $chartStart = $periodStart?->copy()->startOfMonth()
                ?? $chartDates->min()?->copy()->startOfMonth()
                ?? $now->copy()->startOfMonth();
            $monthlyCounts = $chartDates->countBy(fn (Carbon $date) => $date->format('Y-m'));

            for ($date = $chartStart->copy(); $date->lte($periodEnd); $date->addMonth()) {
                $chartDays[] = $date->format('M Y');
                $chartCounts[] = (int) $monthlyCounts->get($date->format('Y-m'), 0);
            }
        } else {
            $chartStart = ($periodStart ?? $now)->copy()->startOfDay();
            $chartEnd = $period === 'today' ? $periodEnd : $now->copy()->endOfWeek();
            $dailyCounts = $chartDates->countBy(fn (Carbon $date) => $date->toDateString());

            for ($date = $chartStart->copy(); $date->lte($chartEnd); $date->addDay()) {
                $chartDays[] = $date->format('D d');
                $chartCounts[] = (int) $dailyCounts->get($date->toDateString(), 0);
            }
        }

        $chartTitle = match ($period) {
            'today' => 'Submissions — Today',
            'week' => 'Submissions — This Week',
            'quarter' => 'Submissions — This Quarter',
            default => 'Submissions — All Time',
        };
        $feedbackListFilters = $periodStart
            ? ['date_from' => $periodStart->toDateString(), 'date_to' => $periodEnd->toDateString()]
            : [];

        return view('dashboard', [
            'authUser' => $authUser,
            'period' => $period,
            'periodOptions' => $periodOptions,
            'periodLabel' => $periodLabel,
            'periodDateLabel' => $periodDateLabel,
            'feedbackListFilters' => $feedbackListFilters,
            'totalFeedback' => $totalFeedback,
            'pendingUsers' => User::query()->where('is_active', false)->where('is_first_user', false)->count(),
            'urgentOpen' => Feedback::query()->where('is_urgent', true)->where('status', '!=', 'closed')->count(),
            'periodUrgentOpen' => $feedbackQuery()->where('is_urgent', true)->where('status', '!=', 'closed')->count(),
            'pendingEscalations' => Escalation::query()->where('status', 'pending')->count(),
            'statusCounts' => $statusCounts,
            'typeCounts' => $typeCounts,
            'resolvedCount' => $resolvedCount,
            'responseRate' => $responseRate,
            'weekCount' => Feedback::query()->where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'byCategory' => $feedbackQuery()
                ->selectRaw('service_category, COUNT(*) as total')
                ->groupBy('service_category')
                ->orderByDesc('total')
                ->limit(6)
                ->get(),
            'chartDays' => $chartDays,
            'chartCounts' => $chartCounts,
            'chartTitle' => $chartTitle,
            'myAssignments' => Feedback::query()
                ->where('assigned_to', $authUser->id)
                ->where('status', '!=', 'closed')
                ->latest()
                ->limit(6)
                ->get(),
            'recentFeedback' => $feedbackQuery()
                ->with('assignedTo')
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
