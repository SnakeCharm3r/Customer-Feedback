<?php

namespace App\Http\Controllers;

use App\Models\Escalation;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(): View
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        $statusCounts = Feedback::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $typeCounts = Feedback::query()
            ->selectRaw('feedback_type, COUNT(*) as aggregate')
            ->groupBy('feedback_type')
            ->pluck('aggregate', 'feedback_type');

        $totalFeedback = $statusCounts->sum();
        $resolvedCount = (int) $statusCounts->get('responded', 0) + (int) $statusCounts->get('closed', 0);
        $responseRate = $totalFeedback > 0 ? (int) round(($resolvedCount / $totalFeedback) * 100) : 0;

        $today = today();
        $chartStart = $today->copy()->subDays(6);
        $dailyCounts = Feedback::query()
            ->selectRaw('DATE(created_at) as submission_date, COUNT(*) as aggregate')
            ->whereDate('created_at', '>=', $chartStart)
            ->groupBy('submission_date')
            ->pluck('aggregate', 'submission_date');

        $chartDays = [];
        $chartCounts = [];
        for ($offset = 0; $offset < 7; $offset++) {
            $date = $chartStart->copy()->addDays($offset);
            $chartDays[] = $date->format('D d');
            $chartCounts[] = (int) $dailyCounts->get($date->toDateString(), 0);
        }

        return view('dashboard', [
            'authUser' => $authUser,
            'totalFeedback' => $totalFeedback,
            'pendingUsers' => User::query()->where('is_active', false)->where('is_first_user', false)->count(),
            'urgentOpen' => Feedback::query()->where('is_urgent', true)->where('status', '!=', 'closed')->count(),
            'pendingEscalations' => Escalation::query()->where('status', 'pending')->count(),
            'statusCounts' => $statusCounts,
            'typeCounts' => $typeCounts,
            'resolvedCount' => $resolvedCount,
            'responseRate' => $responseRate,
            'todayCount' => Feedback::query()->whereDate('created_at', $today)->count(),
            'weekCount' => Feedback::query()->where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'byCategory' => Feedback::query()
                ->selectRaw('service_category, COUNT(*) as total')
                ->groupBy('service_category')
                ->orderByDesc('total')
                ->limit(6)
                ->get(),
            'chartDays' => $chartDays,
            'chartCounts' => $chartCounts,
            'myAssignments' => Feedback::query()
                ->where('assigned_to', $authUser->id)
                ->where('status', '!=', 'closed')
                ->latest()
                ->limit(6)
                ->get(),
            'recentFeedback' => Feedback::query()
                ->with('assignedTo')
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
