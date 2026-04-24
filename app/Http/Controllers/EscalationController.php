<?php

namespace App\Http\Controllers;

use App\Mail\EscalationMail;
use App\Models\Escalation;
use App\Models\Feedback;
use App\Models\Hod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EscalationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->except(['respond', 'submitResponse']);
    }

    public function index(Request $request): View
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $escalations = Escalation::with(['feedback', 'hod', 'escalatedBy'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderByDesc('escalated_at')
            ->paginate(25)
            ->withQueryString();

        $counts = [
            'pending'   => Escalation::where('status', 'pending')->count(),
            'responded' => Escalation::where('status', 'responded')->count(),
        ];

        return view('escalations.index', compact('escalations', 'counts'));
    }

    public function store(Request $request, Feedback $feedback): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $validated = $request->validate([
            'hod_id'  => ['required', 'exists:hods,id'],
            'message' => ['nullable', 'string', 'max:3000'],
        ]);

        $hod = Hod::findOrFail($validated['hod_id']);

        $escalation = Escalation::create([
            'reference'    => Escalation::generateReference(),
            'feedback_id'  => $feedback->id,
            'hod_id'       => $hod->id,
            'escalated_by' => Auth::id(),
            'token'        => Escalation::generateToken(),
            'message'      => $validated['message'],
            'status'       => 'pending',
            'escalated_at' => now(),
        ]);

        try {
            Mail::to($hod->email)->send(new EscalationMail($escalation, $feedback, $hod));
        } catch (\Exception $e) {
            Log::error('Failed to send escalation email: ' . $e->getMessage());
        }

        return redirect()->route('feedback.admin.show', $feedback)
            ->with('toast', "Escalated to {$hod->name} ({$hod->department}). Reference: {$escalation->reference}")
            ->with('toast_type', 'success');
    }

    public function respond(string $token): View
    {
        $escalation = Escalation::where('token', $token)
            ->with(['feedback', 'hod'])
            ->firstOrFail();

        abort_if($escalation->isResponded(), 410);

        return view('escalations.respond', compact('escalation'));
    }

    public function submitResponse(Request $request, string $token): RedirectResponse
    {
        $escalation = Escalation::where('token', $token)->firstOrFail();

        abort_if($escalation->isResponded(), 410);

        $validated = $request->validate([
            'hod_name'     => ['required', 'string', 'max:255'],
            'hod_response' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $escalation->update([
            'hod_name'     => $validated['hod_name'],
            'hod_response' => $validated['hod_response'],
            'status'       => 'responded',
            'responded_at' => now(),
        ]);

        return redirect()->route('escalations.done', $token);
    }

    public function done(string $token): View
    {
        $escalation = Escalation::where('token', $token)->with('feedback')->firstOrFail();
        return view('escalations.done', compact('escalation'));
    }
}
