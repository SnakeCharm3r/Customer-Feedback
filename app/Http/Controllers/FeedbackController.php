<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    private const OPD_UNITS = [
        'eye','orthopaedic','physiotherapy','physician','gynaecology','ent',
        'prosthetics_orthotics','pharmacy','pediatrics','dialysis','plastic_surgery',
        'general_surgery','radiology','dermatology','laboratory','ogd',
    ];

    private const IPD_UNITS = ['private_ward','general_ward','labour_ward'];

    private const THEATRE_UNITS = ['theatre'];

    private const SENTIMENT_MAP = [
        'compliment' => 'positive',
        'complaint'  => 'negative',
        'suggestion' => 'neutral',
        'enquiry'    => 'neutral',
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
            'phone' => 'nullable|string|max:20|required_if:is_urgent,1',
            'service_units' => 'nullable|array',
            'service_units.*' => 'in:eye,orthopaedic,physiotherapy,physician,gynaecology,ent,prosthetics_orthotics,pharmacy,pediatrics,dialysis,plastic_surgery,general_surgery,radiology,dermatology,laboratory,ogd,private_ward,general_ward,labour_ward,theatre,other',
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
            'phone.required_if' => __('portal.validation.phone_required_if_urgent'),
            'consent_given.required' => __('portal.validation.consent_required'),
            'attachment.max' => __('portal.validation.attachment_max'),
            'attachment.mimes' => __('portal.validation.attachment_mimes'),
        ]);

        $serviceUnits    = $validated['service_units'] ?? [];
        $serviceCategory  = $this->resolveServiceCategory($serviceUnits);
        $departmentType   = $this->resolveDepartmentType($serviceUnits);
        $defaultSentiment = self::SENTIMENT_MAP[$validated['feedback_type']] ?? 'neutral';

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
            'service_units'   => $serviceUnits,
            'service_category' => $serviceCategory,
            'department_type'  => $departmentType,
            'sentiment'        => $defaultSentiment,
            'feedback_type'    => $validated['feedback_type'],
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
        $lookup = trim((string) ($request->input('lookup', $request->input('reference_no', ''))));

        if ($lookup === '') {
            return view('feedback.track');
        }

        $feedback = $this->findFeedbackForTracking($lookup);

        if (!$feedback) {
            return view('feedback.track', [
                'referenceLookupError' => __('portal.feedback_track.not_found'),
                'lookup' => $lookup,
            ]);
        }

        $publicResponse = $feedback->getPublicResponse();

        return view('feedback.track', compact('feedback', 'publicResponse', 'lookup'));
    }

    /**
     * Track feedback by reference number
     */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'lookup' => 'required|string',
        ]);

        $feedback = $this->findFeedbackForTracking($validated['lookup']);

        if (!$feedback) {
            return back()->withInput()->withErrors(['lookup' => __('portal.feedback_track.not_found')]);
        }

        $publicResponse = $feedback->getPublicResponse();

        return view('feedback.track', compact('feedback', 'publicResponse'));
    }

    private function findFeedbackForTracking(string $lookup): ?Feedback
    {
        $lookup = trim($lookup);

        if ($lookup === '') {
            return null;
        }

        return Feedback::query()
            ->where('reference_no', $lookup)
            ->orWhere('phone', $lookup)
            ->latest('created_at')
            ->first();
    }

    private function resolveServiceCategory(array $serviceUnits): string
    {
        $hasOpd     = !empty(array_intersect($serviceUnits, self::OPD_UNITS));
        $hasIpd     = !empty(array_intersect($serviceUnits, self::IPD_UNITS));
        $hasTheatre = !empty(array_intersect($serviceUnits, self::THEATRE_UNITS));

        $types = array_filter([$hasOpd ? 'opd' : null, $hasIpd ? 'ipd' : null, $hasTheatre ? 'theatre' : null]);

        if (count($types) > 1) return 'mixed';
        return array_values($types)[0] ?? 'other';
    }

    private function resolveDepartmentType(array $serviceUnits): string
    {
        return $this->resolveServiceCategory($serviceUnits);
    }
}
