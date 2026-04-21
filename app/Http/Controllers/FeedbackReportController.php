<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
        abort_unless(Auth::user()?->canViewReports(), 403);

        $reports = $this->buildQuery($request)->paginate(20);
        $reports->appends($request->query());

        $summary = $this->buildSummary();
        $reviewers = $this->reviewUsers();
        $assignableUsers = $this->assignableUsers();

        return view('reports.feedback', [
            'reports' => $reports,
            'summary' => $summary,
            'reviewers' => $reviewers,
            'assignableUsers' => $assignableUsers,
            'filters' => $request->only(['status', 'source', 'reviewed_by', 'assigned_to', 'search']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        abort_unless(Auth::user()?->canViewReports(), 403);

        $feedbacks = $this->buildQuery($request)->get();
        $summary = $this->buildSummary();
        $filename = 'feedback-reports-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($feedbacks, $summary): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['CCBRT Feedback Reports']);
            fputcsv($handle, ['Generated At', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, ['Total Feedback', $summary['total']]);
            fputcsv($handle, ['Portal Feedback', $summary['portal']]);
            fputcsv($handle, ['Manual / Paper Feedback', $summary['manual']]);
            fputcsv($handle, ['Other Sources', $summary['other']]);
            fputcsv($handle, ['Reviewed Records', $summary['reviewed']]);
            fputcsv($handle, ['Pending Review', $summary['pending_review']]);
            fputcsv($handle, []);
            fputcsv($handle, [
                'Reference No',
                'Source',
                'Submitter Role',
                'Status',
                'Feedback Type',
                'Service Category',
                'Report',
                'Reviewer',
                'Date Reviewed',
                'Reviewer Response',
                'Assigned User',
                'Created At',
                'Reviewed At',
            ]);

            foreach ($feedbacks as $feedback) {
                fputcsv($handle, [
                    $feedback->reference_no,
                    $feedback->getSourceLabel(),
                    $feedback->getSubmitterRoleLabel(),
                    $feedback->getStatusLabel(),
                    $feedback->getFeedbackTypeLabel(),
                    $feedback->getServiceCategoryLabel(),
                    $feedback->report_excerpt,
                    $feedback->reviewedBy?->getFullName() ?? '',
                    $feedback->reviewed_at?->format('Y-m-d H:i:s') ?? '',
                    $feedback->latest_response?->content ?? '',
                    $feedback->assignedTo?->getFullName() ?? '',
                    $feedback->created_at?->format('Y-m-d H:i:s') ?? '',
                    $feedback->reviewed_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function buildQuery(Request $request): Builder
    {
        return Feedback::query()
            ->with(['assignedTo', 'reviewedBy', 'createdBy', 'patientResponses.sender'])
            ->when($request->filled('status'), function (Builder $query) use ($request): void {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->filled('source'), function (Builder $query) use ($request): void {
                $query->where('source', $request->string('source')->toString());
            })
            ->when($request->filled('reviewed_by'), function (Builder $query) use ($request): void {
                $query->where('reviewed_by', $request->integer('reviewed_by'));
            })
            ->when($request->filled('assigned_to'), function (Builder $query) use ($request): void {
                $query->where('assigned_to', $request->integer('assigned_to'));
            })
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = trim($request->string('search')->toString());

                $query->where(function (Builder $searchQuery) use ($search): void {
                    $searchQuery
                        ->where('reference_no', 'like', '%' . $search . '%')
                        ->orWhere('patient_name', 'like', '%' . $search . '%')
                        ->orWhere('overall_experience', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('created_at');
    }

    private function buildSummary(): array
    {
        return [
            'total' => Feedback::count(),
            'portal' => Feedback::where('source', 'portal')->count(),
            'manual' => Feedback::where('source', 'manual')->count(),
            'other' => Feedback::where('source', 'other')->count(),
            'reviewed' => Feedback::whereNotNull('reviewed_at')->count(),
            'pending_review' => Feedback::whereNull('reviewed_at')->count(),
        ];
    }

    private function reviewUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->whereIn('role', User::FEEDBACK_MANAGEMENT_ROLES)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();
    }

    private function assignableUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->whereIn('role', User::FEEDBACK_MANAGEMENT_ROLES)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();
    }
}
