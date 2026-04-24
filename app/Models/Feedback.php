<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback_submissions';

    protected $fillable = [
        'reference_no',
        'patient_name',
        'email',
        'phone',
        'service_units',
        'department_id',
        'service_category',
        'department_type',
        'wing',
        'theme',
        'sentiment',
        'feedback_type',
        'service_rating',
        'confidentiality_respected',
        'confidentiality_comment',
        'visit_date',
        'location',
        'overall_experience',
        'improvement_suggestion',
        'message',
        'is_urgent',
        'attachment_path',
        'consent_given',
        'source',
        'status',
        'assigned_to',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'resolved_at',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'service_units' => 'array',
        'confidentiality_respected' => 'boolean',
        'is_urgent' => 'boolean',
        'consent_given' => 'boolean',
        'reviewed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    const LOCATIONS = [
        'hq'     => 'CCBRT Hospital HQ',
        'moshi'  => 'CCBRT Moshi',
        'tegeta' => 'CCBRT Tegeta Branch',
    ];

    const SERVICE_UNITS_OPD = [
        'eye'                  => 'Eye',
        'orthopaedic'          => 'Orthopaedic',
        'physiotherapy'        => 'Physiotherapy',
        'physician'            => 'Physician (Internal Medicine)',
        'gynaecology'          => 'Gynaecology',
        'ent'                  => 'ENT',
        'prosthetics_orthotics'=> 'Prosthetics & Orthotics',
        'pharmacy'             => 'Pharmacy',
        'pediatrics'           => 'Pediatrics',
        'dialysis'             => 'Dialysis',
        'plastic_surgery'      => 'Plastic Surgery',
        'general_surgery'      => 'General Surgery',
        'radiology'            => 'Radiology',
        'dermatology'          => 'Dermatology',
        'laboratory'           => 'Laboratory',
        'ogd'                  => 'OGD',
    ];

    const SERVICE_UNITS_IPD = [
        'private_ward' => 'Private Ward',
        'general_ward' => 'General Ward',
        'labour_ward'  => 'Labour Ward',
    ];

    const SERVICE_UNITS_THEATRE = [
        'theatre' => 'Theatre (OTD)',
    ];

    const SERVICE_UNITS = [
        'eye'                  => 'Eye',
        'orthopaedic'          => 'Orthopaedic',
        'physiotherapy'        => 'Physiotherapy',
        'physician'            => 'Physician (Internal Medicine)',
        'gynaecology'          => 'Gynaecology',
        'ent'                  => 'ENT',
        'prosthetics_orthotics'=> 'Prosthetics & Orthotics',
        'pharmacy'             => 'Pharmacy',
        'pediatrics'           => 'Pediatrics',
        'dialysis'             => 'Dialysis',
        'plastic_surgery'      => 'Plastic Surgery',
        'general_surgery'      => 'General Surgery',
        'radiology'            => 'Radiology',
        'dermatology'          => 'Dermatology',
        'laboratory'           => 'Laboratory',
        'ogd'                  => 'OGD',
        'private_ward'         => 'Private Ward',
        'general_ward'         => 'General Ward',
        'labour_ward'          => 'Labour Ward',
        'theatre'              => 'Theatre (OTD)',
        'other'                => 'Other',
    ];

    const SERVICE_CATEGORIES = [
        'opd'     => 'Outpatient (OPD)',
        'ipd'     => 'Inpatient (IPD)',
        'theatre' => 'Theatre (OTD)',
        'mixed'   => 'Mixed',
        'other'   => 'Other',
    ];

    const DEPARTMENT_TYPES = [
        'opd'     => 'OPD',
        'ipd'     => 'IPD',
        'theatre' => 'Theatre',
        'mixed'   => 'Mixed',
        'other'   => 'Other',
    ];

    const WINGS = [
        'private'  => 'Private Wing',
        'maternity'=> 'Maternity Wing',
        'standard' => 'Standard Wing',
        'mixed'    => 'Mixed',
        'other'    => 'Other',
    ];

    const THEMES = [
        'client_experience'          => 'Client Experience',
        'client_outcome'             => 'Client Outcome',
        'client_satisfaction'        => 'Client Satisfaction',
        'customer_care_staff'        => 'Customer Care / Staff Attitude',
        'enviro_housekeeping'        => 'Enviro / House Keeping / Facilities',
        'general_positive_feedback'  => 'General Positive Feedback',
        'staff_appreciation'         => 'Staff Appreciation',
        'well_equipped'              => 'Well Equipped',
        'delayed_slow_service'       => 'Delayed / Slow Service',
        'few_staff'                  => 'Few Staff',
        'inadequate_information'     => 'Inadequate Information',
        'waiting_time'               => 'Waiting Time',
        'billing_issues'             => 'Billing Issues',
        'medication_issues'          => 'Medication Issues',
        'other'                      => 'Other',
    ];

    const SENTIMENTS = [
        'positive' => 'Positive',
        'negative' => 'Negative',
        'neutral'  => 'Neutral',
    ];

    const FEEDBACK_TYPES = [
        'compliment' => 'Compliment',
        'complaint' => 'Complaint',
        'suggestion' => 'Suggestion',
        'enquiry' => 'Enquiry',
    ];

    const SERVICE_RATINGS = [
        'poor' => 'Poor',
        'average' => 'Average',
        'good' => 'Good',
        'excellent' => 'Excellent',
    ];

    const SOURCES = [
        'portal'     => 'Portal',
        'manual'     => 'Manual / Paper Form',
        'calls'      => 'Calls',
        'walk_in'    => 'Walk In',
        'ward_visit' => 'Ward Visit',
        'paper_form' => 'Paper Form',
        'other'      => 'Other',
    ];

    const COLLECTION_MEANS = [
        'calls'       => 'Calls',
        'walk_in'     => 'Walk In',
        'ward_visit'  => 'Ward Visit',
        'paper_form'  => 'Paper Form',
        'other'       => 'Other',
    ];

    const STATUSES = [
        'new' => 'New',
        'under_review' => 'Under Review',
        'responded' => 'Responded',
        'closed' => 'Closed',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Department::class, 'department_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(InternalNote::class, 'feedback_id');
    }

    public function patientResponses(): HasMany
    {
        return $this->hasMany(PatientResponse::class, 'feedback_id');
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(Escalation::class, 'feedback_id');
    }

    public function getPublicResponse(): ?PatientResponse
    {
        return $this->patientResponses()
            ->where('is_public', true)
            ->latest()
            ->first();
    }

    public function getLatestResponseAttribute(): ?PatientResponse
    {
        return $this->patientResponses
            ->sortByDesc('created_at')
            ->first();
    }

    public function getReportExcerptAttribute(): string
    {
        return (string) ($this->overall_experience ?: $this->message ?: '');
    }

    public function getSourceLabel(): string
    {
        return self::SOURCES[$this->source] ?? ucfirst((string) $this->source);
    }

    public function getSubmitterRoleLabel(): string
    {
        if ($this->source === 'portal' || !$this->createdBy) {
            return 'Customer';
        }

        return $this->createdBy->getRoleLabel();
    }

    public static function generateReferenceNo(): string
    {
        $year = date('Y');
        $prefix = "CCBRT-{$year}-";
        
        // Get the last feedback for this year
        $lastFeedback = self::where('reference_no', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastFeedback) {
            $lastNumber = (int) substr($lastFeedback->reference_no, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'new'          => 'bg-danger',
            'under_review' => 'bg-warning text-dark',
            'responded'    => 'bg-success',
            'closed'       => 'bg-secondary',
            default        => 'bg-secondary',
        };
    }

    public function getStatusBadge(): string
    {
        $class = $this->getStatusBadgeClass();
        $label = $this->getStatusLabel();
        return "<span class=\"badge {$class}\">{$label}</span>";
    }

    /**
     * Accessors — bridge view field names to actual DB columns
     */
    public function getReferenceNumberAttribute(): string
    {
        return $this->reference_no ?? '';
    }

    public function getPatientEmailAttribute(): ?string
    {
        return $this->email;
    }

    public function getPatientPhoneAttribute(): ?string
    {
        return $this->phone;
    }

    public function getIsPriorityAttribute(): bool
    {
        return (bool) $this->is_urgent;
    }

    public function getAttachmentAttribute(): ?string
    {
        return $this->attachment_path;
    }

    public function getServiceUnitsLabelsAttribute(): array
    {
        return collect($this->service_units ?? [])
            ->map(fn ($unit) => __('portal.options.service_units.' . $unit, [], app()->getLocale()))
            ->values()
            ->all();
    }

    public function getServiceUnitsSummaryAttribute(): ?string
    {
        $labels = $this->service_units_labels;

        return empty($labels) ? null : implode(', ', $labels);
    }

    public function getServiceCategoryLabel(): string
    {
        return self::SERVICE_CATEGORIES[$this->service_category] ?? ucfirst((string) $this->service_category);
    }

    public function getDepartmentTypeLabel(): string
    {
        return self::DEPARTMENT_TYPES[$this->department_type] ?? '—';
    }

    public function getWingLabel(): string
    {
        return self::WINGS[$this->wing] ?? '—';
    }

    public function getThemeLabel(): string
    {
        return self::THEMES[$this->theme] ?? '—';
    }

    public function getSentimentLabel(): string
    {
        return self::SENTIMENTS[$this->sentiment] ?? '—';
    }

    public function getServiceRatingLabel(): string
    {
        return __('portal.options.service_ratings.' . $this->service_rating, [], app()->getLocale());
    }

    public function getFeedbackTypeLabel(): string
    {
        return __('portal.options.feedback_types.' . $this->feedback_type, [], app()->getLocale());
    }

    public function getConfidentialityLabel(): ?string
    {
        if (is_null($this->confidentiality_respected)) {
            return null;
        }

        return $this->confidentiality_respected
            ? __('portal.common.yes')
            : __('portal.common.no');
    }

    public function getStatusLabel(): string
    {
        return __('portal.options.statuses.' . $this->status, [], app()->getLocale());
    }
}
