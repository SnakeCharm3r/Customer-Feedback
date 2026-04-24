<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    private const SERVICE_UNIT_CATEGORY_MAP = [
        'eye' => 'eye_surgery',
        'orthopaedic' => 'rehabilitation',
        'physiotherapy' => 'rehabilitation',
        'physician' => 'outpatient',
        'gynaecology' => 'outpatient',
        'ent' => 'outpatient',
        'prosthetics_orthotics' => 'rehabilitation',
        'pharmacy' => 'pharmacy',
        'radiology' => 'outpatient',
        'laboratory' => 'outpatient',
        'other' => 'other',
    ];

    /**
     * Show the feedback submission form
     */
    public function create()
    {
        return view('feedback.create');
    }

    /**
     * Store a new feedback submission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'service_units' => 'nullable|array',
            'service_units.*' => 'in:eye,orthopaedic,physiotherapy,physician,gynaecology,ent,prosthetics_orthotics,pharmacy,radiology,laboratory,other',
            'feedback_type' => 'required|in:compliment,complaint,suggestion,enquiry',
            'service_rating' => 'required|in:poor,average,good,excellent',
            'confidentiality_respected' => 'nullable|in:1,0',
            'confidentiality_comment' => 'nullable|string|max:1000|required_if:confidentiality_respected,0',
            'visit_date' => 'nullable|date',
            'location' => 'nullable|in:' . implode(',', array_keys(\App\Models\Feedback::LOCATIONS)),
            'overall_experience' => 'required_unless:feedback_type,compliment|nullable|string|min:10',
            'improvement_suggestion' => 'nullable|string|max:2000',
            'message' => 'nullable|string|max:2000',
            'is_urgent' => 'boolean',
            'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
            'consent_given' => 'required|boolean',
        ], [
            'overall_experience.min' => __('portal.validation.overall_experience_min'),
            'overall_experience.required_unless' => __('portal.validation.overall_experience_min'),
            'service_rating.required' => __('portal.validation.service_rating_required'),
            'confidentiality_comment.required_if' => __('portal.validation.confidentiality_comment_required_if'),
            'consent_given.required' => __('portal.validation.consent_required'),
            'attachment.max' => __('portal.validation.attachment_max'),
            'attachment.mimes' => __('portal.validation.attachment_mimes'),
        ]);

        $serviceUnits = $validated['service_units'] ?? [];
        $serviceCategory = $this->resolveServiceCategory($serviceUnits);

        // Generate unique reference number
        $referenceNo = Feedback::generateReferenceNo();

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('feedback-attachments', 'public');
        }

        // Create feedback record
        $feedback = Feedback::create([
            'reference_no' => $referenceNo,
            'patient_name' => $validated['patient_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'service_units' => $serviceUnits,
            'service_category' => $serviceCategory,
            'feedback_type' => $validated['feedback_type'],
            'service_rating' => $validated['service_rating'],
            'confidentiality_respected' => $request->filled('confidentiality_respected')
                ? $request->boolean('confidentiality_respected')
                : null,
            'confidentiality_comment' => $validated['confidentiality_comment'] ?? null,
            'visit_date' => $validated['visit_date'],
            'location' => $validated['location'] ?? null,
            'overall_experience' => $validated['overall_experience'] ?? null,
            'improvement_suggestion' => $validated['improvement_suggestion'] ?? null,
            'message' => $validated['message'] ?? '',
            'is_urgent' => $request->boolean('is_urgent'),
            'attachment_path' => $attachmentPath,
            'consent_given' => $request->boolean('consent_given'),
            'source' => 'portal',
            'status' => 'new',
        ]);

        // Redirect to confirmation page
        return redirect()->route('feedback.confirmation', ['reference' => $referenceNo])
            ->with('success', 'Your feedback has been submitted successfully.');
    }

    /**
     * Show confirmation page after submission
     */
    public function confirmation($reference)
    {
        $feedback = Feedback::where('reference_no', $reference)->firstOrFail();
        
        return view('feedback.confirmation', compact('feedback'));
    }

    /**
     * Show the track feedback form
     */
    public function trackForm(Request $request)
    {
        if (!$request->filled('reference_no')) {
            return view('feedback.track');
        }

        $feedback = Feedback::where('reference_no', $request->reference_no)->first();

        if (!$feedback) {
            return view('feedback.track', [
                'referenceLookupError' => __('portal.feedback_track.not_found'),
            ]);
        }

        $publicResponse = $feedback->getPublicResponse();

        return view('feedback.track', compact('feedback', 'publicResponse'));
    }

    /**
     * Track feedback by reference number
     */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'reference_no' => 'required|string',
        ]);

        $feedback = Feedback::where('reference_no', $validated['reference_no'])->first();

        if (!$feedback) {
            return back()->withErrors(['reference_no' => __('portal.feedback_track.not_found')]);
        }

        $publicResponse = $feedback->getPublicResponse();

        return view('feedback.track', compact('feedback', 'publicResponse'));
    }

    private function resolveServiceCategory(array $serviceUnits): string
    {
        foreach ($serviceUnits as $unit) {
            if (isset(self::SERVICE_UNIT_CATEGORY_MAP[$unit])) {
                return self::SERVICE_UNIT_CATEGORY_MAP[$unit];
            }
        }

        return 'other';
    }
}
