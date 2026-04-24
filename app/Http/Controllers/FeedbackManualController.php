<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FeedbackManualController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function create(): View
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        return view('feedback.manual.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->canManageComplaints(), 403);

        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'service_units' => 'nullable|array',
            'service_units.*' => 'in:eye,orthopaedic,physiotherapy,physician,gynaecology,ent,prosthetics_orthotics,pharmacy,radiology,laboratory,other',
            'service_category' => 'required|in:outpatient,inpatient,eye_surgery,rehabilitation,pharmacy,reception_admin,billing,other',
            'feedback_type' => 'required|in:compliment,complaint,suggestion,enquiry',
            'service_rating' => 'required|in:poor,average,good,excellent',
            'confidentiality_respected' => 'nullable|in:1,0',
            'confidentiality_comment' => 'nullable|string|max:1000|required_if:confidentiality_respected,0',
            'visit_date' => 'nullable|date',
            'location' => 'nullable|in:' . implode(',', array_keys(\App\Models\Feedback::LOCATIONS)),
            'overall_experience' => 'required|string|min:10',
            'improvement_suggestion' => 'nullable|string|max:2000',
            'message' => 'nullable|string|max:2000',
            'is_urgent' => 'boolean',
            'consent_given' => 'required|boolean',
        ]);

        $serviceUnits = $validated['service_units'] ?? [];

        $referenceNo = Feedback::generateReferenceNo();

        $feedback = Feedback::create([
            'reference_no' => $referenceNo,
            'patient_name' => $validated['patient_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'service_units' => $serviceUnits,
            'service_category' => $validated['service_category'],
            'feedback_type' => $validated['feedback_type'],
            'service_rating' => $validated['service_rating'],
            'confidentiality_respected' => $request->filled('confidentiality_respected')
                ? $request->boolean('confidentiality_respected')
                : null,
            'confidentiality_comment' => $validated['confidentiality_comment'] ?? null,
            'visit_date' => $validated['visit_date'],
            'location' => $validated['location'] ?? null,
            'overall_experience' => $validated['overall_experience'],
            'improvement_suggestion' => $validated['improvement_suggestion'] ?? null,
            'message' => $validated['message'] ?? '',
            'is_urgent' => $request->boolean('is_urgent'),
            'consent_given' => $request->boolean('consent_given'),
            'source' => 'manual',
            'created_by' => Auth::id(),
            'status' => 'new',
        ]);

        return redirect()->route('feedback.admin.show', $feedback)
            ->with('status', 'Manual feedback entry created successfully.');
    }
}
