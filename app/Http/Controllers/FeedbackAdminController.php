<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackResponseMail;
use App\Models\Feedback;
use App\Models\InternalNote;
use App\Models\PatientResponse;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class FeedbackAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List all feedback submissions with optional status filter.
     */
    public function index(Request $request): View
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $query = Feedback::with(['assignedTo', 'reviewedBy'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $feedbacks = $query->paginate(20)->withQueryString();

        $counts = [
            'new'          => Feedback::where('status', 'new')->count(),
            'under_review' => Feedback::where('status', 'under_review')->count(),
            'responded'    => Feedback::where('status', 'responded')->count(),
            'closed'       => Feedback::where('status', 'closed')->count(),
        ];

        return view('feedback.admin.index', compact('feedbacks', 'counts'));
    }

    /**
     * Show individual feedback submission detail.
     */
    public function show(Feedback $feedback): View
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $feedback->load(['assignedTo', 'reviewedBy', 'internalNotes.author', 'patientResponses.sender']);

        $assignableUsers = User::query()
            ->where('is_active', true)
            ->whereIn('role', User::FEEDBACK_MANAGEMENT_ROLES)
            ->orderBy('fname')
            ->orderBy('lname')
            ->get();

        return view('feedback.admin.show', compact('feedback', 'assignableUsers'));
    }

    public function updateStatus(Request $request, Feedback $feedback): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Feedback::STATUSES))],
        ]);

        $payload = [
            'status' => $validated['status'],
            'resolved_at' => $validated['status'] === 'closed' ? now() : null,
        ];

        if ($validated['status'] === 'new') {
            $payload['reviewed_by'] = null;
            $payload['reviewed_at'] = null;
        } else {
            $payload['reviewed_by'] = Auth::id();
            $payload['reviewed_at'] = now();
        }

        $feedback->update($payload);

        return redirect()->route('feedback.admin.show', $feedback)
            ->with('toast', 'Feedback status updated to ' . $feedback->getStatusLabel() . '.')
            ->with('toast_type', 'success');
    }

    public function updateAssignment(Request $request, Feedback $feedback): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $assignedUser = null;

        if (!empty($validated['assigned_to'])) {
            $assignedUser = User::query()
                ->whereKey($validated['assigned_to'])
                ->where('is_active', true)
                ->whereIn('role', User::FEEDBACK_MANAGEMENT_ROLES)
                ->firstOrFail();
        }

        $feedback->update([
            'assigned_to' => $assignedUser?->id,
        ]);

        return redirect()->route('feedback.admin.show', $feedback)
            ->with('toast', $assignedUser
                ? 'Feedback assigned to ' . $assignedUser->getFullName() . '.'
                : 'Feedback assignment cleared.')
            ->with('toast_type', 'success');
    }

    public function storeNote(Request $request, Feedback $feedback): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $validated = $request->validate([
            'note_content' => ['required', 'string', 'max:5000'],
        ]);

        InternalNote::create([
            'feedback_id' => $feedback->id,
            'author_id' => Auth::id(),
            'content' => $validated['note_content'],
            'is_coo_comment' => Auth::user()?->isCOO() ?? false,
        ]);

        $feedback->update([
            'status' => $feedback->status === 'new' ? 'under_review' : $feedback->status,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('feedback.admin.show', $feedback)
            ->with('toast', 'Internal comment saved successfully.')
            ->with('toast_type', 'success');
    }

    public function storeResponse(Request $request, Feedback $feedback): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $validated = $request->validate([
            'response_content' => ['required', 'string', 'max:5000'],
        ]);

        $response = PatientResponse::create([
            'feedback_id' => $feedback->id,
            'content' => $validated['response_content'],
            'sent_by' => Auth::id(),
            'is_public' => true,
        ]);

        $feedback->update([
            'status' => 'responded',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'resolved_at' => now(),
        ]);

        if ($feedback->patient_email) {
            Mail::to($feedback->patient_email)->send(new FeedbackResponseMail($feedback, $response));
        }

        return redirect()->route('feedback.admin.show', $feedback)
            ->with('toast', $feedback->patient_email ? 'Response sent and emailed to the client.' : 'Response saved and published on the tracking portal.')
            ->with('toast_type', 'success');
    }
}
