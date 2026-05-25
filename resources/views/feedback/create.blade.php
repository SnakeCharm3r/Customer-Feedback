@extends('layouts.public')

@section('title', __('portal.meta.feedback_create_title'))

<style>
    .feedback-form-shell { max-width: 920px; margin-inline: auto; }
    .professional-note { color: #5e6b73; font-size: 0.9rem; line-height: 1.5; }
    .form-card-subtitle { margin: 0.25rem 0 0; color: rgba(255,255,255,0.78); font-size: 0.88rem; font-weight: 400; }

    #feedbackSubmitButton:disabled,
    #feedbackSubmitButton[aria-busy="true"],
    #feedbackSubmitButton .submit-loading-state { color: #fff; }
    #feedbackSubmitButton:disabled { opacity: 0.85; }
    #feedbackSubmitButton .spinner-border { color: #fff; border-color: currentColor; border-right-color: transparent; }

    /* ── Section headers ── */
    .form-section-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; margin-top: 2.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e9ecef; }
    .form-section-header:first-child { margin-top: 0; }
    .section-badge { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, var(--ccbrt-navy) 0%, var(--ccbrt-teal) 100%); color: #fff; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 3px 8px rgba(6,83,33,0.22); }
    .form-section-header h5 { margin: 0; color: var(--ccbrt-navy); font-weight: 700; font-size: 1rem; }
    .form-section-header .section-optional { margin-left: 0.4rem; font-size: 0.8rem; font-weight: 400; color: #6c757d; }

    /* ── Feedback type cards ── */
    .feedback-type-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 0.75rem; margin-top: 0.5rem; }
    .feedback-type-radio, .rating-radio, .service-unit-check, .confidentiality-radio { position: absolute; opacity: 0; pointer-events: none; }
    .feedback-type-card { display: flex; flex-direction: column; align-items: center; gap: 0.55rem; padding: 1.1rem 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; background: #fff; cursor: pointer; text-align: center; font-weight: 600; font-size: 0.875rem; color: #4b5563; transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s, background 0.2s; user-select: none; }
    .feedback-type-card:hover { border-color: #94a3b8; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.07); }
    .feedback-type-card .type-icon { width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; transition: transform 0.2s; }
    .feedback-type-compliment .type-icon { background: #dcfce7; color: #15803d; }
    .feedback-type-complaint  .type-icon { background: #fee2e2; color: #dc2626; }
    .feedback-type-suggestion .type-icon { background: #fef3c7; color: #d97706; }
    .feedback-type-enquiry    .type-icon { background: #dbeafe; color: #2563eb; }
    .feedback-type-radio:checked + .feedback-type-compliment { border-color: #15803d; background: #f0fdf4; color: #15803d; box-shadow: 0 0 0 3px rgba(21,128,61,0.15); }
    .feedback-type-radio:checked + .feedback-type-complaint  { border-color: #dc2626; background: #fef2f2; color: #b91c1c; box-shadow: 0 0 0 3px rgba(220,38,38,0.15); }
    .feedback-type-radio:checked + .feedback-type-suggestion { border-color: #d97706; background: #fffbeb; color: #92400e; box-shadow: 0 0 0 3px rgba(217,119,6,0.15); }
    .feedback-type-radio:checked + .feedback-type-enquiry    { border-color: #2563eb; background: #eff6ff; color: #1d4ed8; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
    .feedback-type-radio:focus-visible + .feedback-type-card, .rating-radio:focus-visible + .rating-card, .service-unit-check:focus-visible + .service-unit-pill, .confidentiality-radio:focus-visible + .confidentiality-choice { outline: 3px solid rgba(21,128,61,0.22); outline-offset: 2px; }

    /* ── Rating cards ── */
    .rating-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 0.65rem; margin-top: 0.5rem; }
    .rating-card { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.4rem; padding: 0.95rem; border: 2px solid #e2e8f0; border-radius: 8px; background: #fff; cursor: pointer; text-align: left; font-size: 0.8rem; font-weight: 600; color: #6b7280; transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s, background 0.2s; user-select: none; }
    .rating-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.07); }
    .rating-card .rating-icon { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: var(--ccbrt-teal); font-size: 1rem; }
    .rating-card strong { display: block; color: #1f2937; font-size: 0.86rem; }
    .rating-card small { display: block; color: #64748b; font-weight: 500; line-height: 1.35; }
    .rating-radio[value="poor"]:checked      + .rating-card { border-color: #ef4444; background: #fef2f2; color: #b91c1c; box-shadow: 0 0 0 3px rgba(239,68,68,0.15); }
    .rating-radio[value="average"]:checked   + .rating-card { border-color: #f59e0b; background: #fffbeb; color: #92400e; box-shadow: 0 0 0 3px rgba(245,158,11,0.15); }
    .rating-radio[value="good"]:checked      + .rating-card { border-color: #22c55e; background: #f0fdf4; color: #15803d; box-shadow: 0 0 0 3px rgba(34,197,94,0.15); }
    .rating-radio[value="excellent"]:checked + .rating-card { border-color: #7c3aed; background: #faf5ff; color: #6d28d9; box-shadow: 0 0 0 3px rgba(124,58,237,0.15); }

    /* ── Service unit pills ── */
    .service-units-panel { display: grid; gap: 0.9rem; margin-top: 0.75rem; }
    .service-unit-group { border: 1px solid #dbe7df; border-radius: 8px; background: #fbfdfb; padding: 0.95rem; }
    .service-unit-group-title { display: flex; align-items: center; gap: 0.45rem; margin-bottom: 0.7rem; color: var(--ccbrt-navy); font-size: 0.86rem; font-weight: 700; }
    .service-units-pills { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .service-unit-pill { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.9rem; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 0.82rem; font-weight: 500; color: #374151; cursor: pointer; background: #fff; transition: border-color 0.15s, background 0.15s, color 0.15s; user-select: none; }
    .service-unit-pill:hover { border-color: var(--ccbrt-teal); color: var(--ccbrt-teal); }
    .service-unit-check:checked + .service-unit-pill { background: var(--ccbrt-teal); color: #fff; border-color: var(--ccbrt-teal); }

    /* ── Confidentiality choice ── */
    .confidentiality-choice-group { display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; margin-top: 0.65rem; }
    .confidentiality-choice { border: 1.5px solid #d1d5db; border-radius: 8px; background: #fff; cursor: pointer; padding: 0.75rem 0.9rem; font-size: 0.88rem; font-weight: 700; color: #334155; transition: border-color 0.15s, background 0.15s, color 0.15s, box-shadow 0.15s; }
    .confidentiality-choice i { margin-right: 0.35rem; }
    .confidentiality-choice:hover { border-color: var(--ccbrt-teal); color: var(--ccbrt-teal); }
    .confidentiality-radio:checked + .confidentiality-choice { border-color: var(--ccbrt-teal); background: #f0fdf4; color: var(--ccbrt-navy); box-shadow: 0 0 0 3px rgba(21,128,61,0.12); }

    /* ── Option cards ── */
    .feedback-options-panel { display: grid; gap: 1rem; }
    .feedback-option-card { border: 1px solid #d9e6e1; border-radius: 8px; background: linear-gradient(180deg, #ffffff 0%, #f8fbfa 100%); padding: 1rem 1.1rem; transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease; }
    .feedback-option-card.is-invalid { border-color: #dc3545; background: linear-gradient(180deg, #fff8f8 0%, #fff 100%); }
    .feedback-option-card--urgent.is-active  { border-color: #f0ad4e; box-shadow: 0 0 0 4px rgba(240,173,78,0.16); }
    .feedback-option-card--consent.is-active { border-color: var(--ccbrt-teal); box-shadow: 0 0 0 4px rgba(43,125,108,0.14); }
    .feedback-option-card__toggle { display: flex; align-items: flex-start; gap: 0.85rem; }
    .feedback-option-card__icon { width: 2.5rem; height: 2.5rem; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem; }
    .feedback-option-card--urgent  .feedback-option-card__icon { background: rgba(240,173,78,0.16); color: #9a6700; }
    .feedback-option-card--consent .feedback-option-card__icon { background: rgba(43,125,108,0.14); color: var(--ccbrt-teal); }
    .feedback-option-card .form-check-input { margin-top: 0.2rem; flex-shrink: 0; }
    .feedback-option-card__body  { flex: 1; min-width: 0; }
    .feedback-option-card__label { display: inline-flex; align-items: center; gap: 0.45rem; font-weight: 700; color: var(--ccbrt-navy); margin-bottom: 0.25rem; cursor: pointer; }
    .feedback-option-card__hint  { margin: 0; color: #5e6b73; font-size: 0.95rem; line-height: 1.5; }
    .feedback-option-card__meta  { margin-top: 0.85rem; padding-top: 0.85rem; border-top: 1px solid #e5efeb; }
    .phone-followup-note { display: flex; align-items: flex-start; gap: 0.55rem; margin-top: 0.5rem; padding: 0.75rem 0.85rem; border-radius: 8px; background: #fff7e8; color: #6b5200; font-size: 0.92rem; line-height: 1.45; }
    .phone-followup-note i { margin-top: 0.1rem; }
    .phone-followup-note.d-none { display: none !important; }
    .consent-error { margin-top: 0.75rem; }
    .card-ccbrt { overflow: hidden; }

    /* ── Info chips ── */
    .info-chips { display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; margin-top: 0.75rem; }
    @media (max-width: 575px) { .info-chips { grid-template-columns: 1fr; } }
    .info-chip { display: flex; align-items: flex-start; gap: 0.65rem; padding: 0.7rem 0.85rem; border-radius: 8px; background: #fff; border: 1.5px solid #d6eade; font-size: 0.83rem; color: #2d4a38; line-height: 1.4; }
    .info-chip-icon { width: 28px; height: 28px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0; }
    .info-chip--required .info-chip-icon { background: #fee2e2; color: #dc2626; }
    .info-chip--anon     .info-chip-icon { background: #e0f2fe; color: #0369a1; }
    .info-chip--email    .info-chip-icon { background: #fef3c7; color: #d97706; }
    .info-chip--review   .info-chip-icon { background: #dcfce7; color: #15803d; }
    .info-chip strong { display: block; font-weight: 600; margin-bottom: 0.1rem; color: #1a3626; font-size: 0.82rem; }

    /* ── Responsive ── */
    @media (max-width: 576px) {
        .feedback-type-grid { grid-template-columns: 1fr 1fr; }
        .feedback-type-card { padding: 0.85rem 0.5rem; font-size: 0.8rem; }
        .feedback-type-card .type-icon { width: 38px; height: 38px; font-size: 1rem; }
        .rating-grid { grid-template-columns: 1fr 1fr; }
        .rating-card { padding: 0.75rem; font-size: 0.78rem; }
        .rating-card .rating-icon { width: 30px; height: 30px; }
        .form-section-header { margin-top: 1.5rem; }
        .form-section-header h5 { font-size: 0.92rem; }
        .feedback-option-card { padding: 0.85rem; }
        .feedback-option-card__icon { width: 2rem; height: 2rem; font-size: 0.95rem; }
        .d-md-flex.justify-content-md-end { flex-direction: column !important; gap: 0.65rem; }
        .d-md-flex.justify-content-md-end .btn { width: 100%; }
        .hero-title { font-size: 1.25rem !important; }
        .info-chips { grid-template-columns: 1fr; }
        .confidentiality-choice-group { grid-template-columns: 1fr; }
    }
</style>

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 3rem 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title" style="font-size: 2rem;">{{ __('portal.feedback_create.hero_title') }}</h1>
                <p class="hero-subtitle mb-0">
                    {{ __('portal.feedback_create.hero_subtitle') }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Feedback Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Info Box -->
                <div class="info-box feedback-form-shell">
                    <h6 class="mb-0" style="color: var(--ccbrt-navy);">
                        <i class="bi bi-info-circle me-2"></i>{{ __('portal.feedback_create.info_title') }}
                    </h6>
                    <div class="info-chips">
                        <div class="info-chip info-chip--required">
                            <span class="info-chip-icon"><i class="bi bi-asterisk"></i></span>
                            <div><strong>{{ __('portal.feedback_create.info_items.required_fields') }}</strong> {{ __('portal.feedback_create.info_chips.required_detail') }}</div>
                        </div>
                        <div class="info-chip info-chip--anon">
                            <span class="info-chip-icon"><i class="bi bi-person-dash"></i></span>
                            <div><strong>{{ __('portal.feedback_create.info_items.anonymous') }}</strong> {{ __('portal.feedback_create.info_chips.anon_detail') }}</div>
                        </div>
                        <div class="info-chip info-chip--email">
                            <span class="info-chip-icon"><i class="bi bi-envelope-at"></i></span>
                            <div><strong>{{ __('portal.feedback_create.info_items.response') }}</strong> {{ __('portal.feedback_create.info_chips.email_detail') }}</div>
                        </div>
                        <div class="info-chip info-chip--review">
                            <span class="info-chip-icon"><i class="bi bi-shield-check"></i></span>
                            <div><strong>{{ __('portal.feedback_create.info_items.review') }}</strong> {{ __('portal.feedback_create.info_chips.review_detail') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Confidentiality Disclaimer -->
                <div class="alert mb-4 feedback-form-shell" style="background-color: #f0f7f4; border-left: 4px solid var(--ccbrt-teal); border-radius: 6px;">
                    <h6 class="mb-2" style="color: var(--ccbrt-teal);"><i class="bi bi-shield-lock me-2"></i>Confidentiality &amp; Privacy Notice</h6>
                    <p class="mb-0 small">All information submitted through this form is treated with the strictest confidentiality. Your feedback will only be used to improve our services and will never be shared, disclosed, or used in any way that could harm or identify you without your consent.</p>
                </div>

                <!-- Form Card -->
                <div class="card card-ccbrt feedback-form-shell">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>{{ __('portal.feedback_create.form_title') }}</h4>
                    </div>
                    <div class="card-body">
                        <form id="feedbackForm" action="{{ route('feedback.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            @if ($errors->any())
                                <div class="alert alert-danger alert-ccbrt mb-4">
                                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>{{ __('portal.feedback_create.errors_title') }}</h6>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- ① Contact Information -->
                            <div class="form-section-header">
                                <span class="section-badge">1</span>
                                <h5>{{ __('portal.feedback_create.sections.contact_information') }} <span class="section-optional">({{ __('portal.common.optional') }})</span></h5>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_name" class="form-label">{{ __('portal.feedback_create.fields.full_name') }} <span class="text-muted fw-normal small">({{ __('portal.common.optional') }})</span></label>
                                    <input type="text" class="form-control form-control-ccbrt @error('patient_name') is-invalid @enderror" 
                                           id="patient_name" name="patient_name" value="{{ old('patient_name') }}"
                                           placeholder="{{ __('portal.feedback_create.fields.full_name_placeholder') }}">
                                    @error('patient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ __('portal.feedback_create.fields.email') }} <span class="text-muted fw-normal small">({{ __('portal.common.optional') }})</span></label>
                                    <input type="email" class="form-control form-control-ccbrt @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}"
                                           placeholder="{{ __('portal.feedback_create.fields.email_placeholder') }}">
                                    <div class="form-text text-muted">{{ __('portal.feedback_create.fields.email_help') }}</div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ __('portal.feedback_create.fields.phone') }} <span class="text-muted fw-normal small">({{ __('portal.common.optional') }})</span></label>
                                    <input type="tel" class="form-control form-control-ccbrt @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" {{ old('is_urgent') ? 'required' : '' }}
                                           data-required-message="{{ __('portal.validation.phone_required_if_urgent') }}"
                                           placeholder="{{ __('portal.feedback_create.fields.phone_placeholder') }}">
                                    <div id="phoneFollowupNote" class="phone-followup-note {{ old('is_urgent') ? '' : 'd-none' }}" aria-live="polite">
                                        <i class="bi bi-telephone-forward"></i>
                                        <span>{{ __('portal.feedback_create.fields.phone_followup_note') }}</span>
                                    </div>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="visit_date" class="form-label">{{ __('portal.feedback_create.fields.visit_date') }}</label>
                                    <input type="date" class="form-control form-control-ccbrt @error('visit_date') is-invalid @enderror" 
                                           id="visit_date" name="visit_date" value="{{ old('visit_date') }}">
                                    @error('visit_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- ② Feedback Type -->
                            <div class="form-section-header">
                                <span class="section-badge">2</span>
                                <h5>{{ __('portal.feedback_create.fields.feedback_type') }} <span class="text-danger">*</span></h5>
                            </div>

                            <div class="mb-4">
                                <div class="feedback-type-grid">
                                    <input class="feedback-type-radio" type="radio" name="feedback_type" id="type_compliment" value="compliment" {{ old('feedback_type') == 'compliment' ? 'checked' : '' }} required>
                                    <label class="feedback-type-card feedback-type-compliment" for="type_compliment">
                                        <span class="type-icon"><i class="bi bi-hand-thumbs-up-fill"></i></span>
                                        {{ __('portal.options.feedback_types.compliment') }}
                                    </label>

                                    <input class="feedback-type-radio" type="radio" name="feedback_type" id="type_complaint" value="complaint" {{ old('feedback_type') == 'complaint' ? 'checked' : '' }} required>
                                    <label class="feedback-type-card feedback-type-complaint" for="type_complaint">
                                        <span class="type-icon"><i class="bi bi-exclamation-circle-fill"></i></span>
                                        {{ __('portal.options.feedback_types.complaint') }}
                                    </label>

                                    <input class="feedback-type-radio" type="radio" name="feedback_type" id="type_suggestion" value="suggestion" {{ old('feedback_type') == 'suggestion' ? 'checked' : '' }} required>
                                    <label class="feedback-type-card feedback-type-suggestion" for="type_suggestion">
                                        <span class="type-icon"><i class="bi bi-lightbulb-fill"></i></span>
                                        {{ __('portal.options.feedback_types.suggestion') }}
                                    </label>

                                    <input class="feedback-type-radio" type="radio" name="feedback_type" id="type_enquiry" value="enquiry" {{ old('feedback_type') == 'enquiry' ? 'checked' : '' }} required>
                                    <label class="feedback-type-card feedback-type-enquiry" for="type_enquiry">
                                        <span class="type-icon"><i class="bi bi-question-circle-fill"></i></span>
                                        {{ __('portal.options.feedback_types.enquiry') }}
                                    </label>
                                </div>
                                @error('feedback_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">CCBRT Location <span class="text-muted fw-normal small">({{ __('portal.common.optional') }})</span></label>
                                    <select class="form-select form-control-ccbrt @error('location') is-invalid @enderror"
                                            id="location" name="location">
                                        <option value="">— Select the branch you visited —</option>
                                        @foreach(\App\Models\Feedback::getLocations() as $value => $label)
                                            <option value="{{ $value }}" {{ old('location') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- ③ Customer Experience -->
                            <div class="form-section-header">
                                <span class="section-badge">3</span>
                                <h5>{{ __('portal.feedback_create.sections.customer_experience') }}</h5>
                            </div>

                            {{-- ── Mabinti Centre compliment extra fields (shown via JS when location=mabinti + type=compliment) ── --}}
                            <div id="mabinti-compliment-fields" class="mb-4" style="display:none;">
                                <div class="alert mb-3" style="background:#f0fdf4;border-left:4px solid #15803d;border-radius:6px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-shop" style="color:#15803d;font-size:1.2rem;"></i>
                                        <div>
                                            <strong style="color:#14532d;">Mabinti Centre — Compliment Submission</strong>
                                            <p class="mb-0 small text-muted mt-1">Your name and organisation (if applicable) will be included in our records. All fields below are optional.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="organization_name" class="form-label">Organisation / Company <span class="text-muted fw-normal small">(optional)</span></label>
                                        <input type="text" class="form-control form-control-ccbrt @error('organization_name') is-invalid @enderror"
                                               id="organization_name" name="organization_name"
                                               value="{{ old('organization_name') }}"
                                               placeholder="e.g. TRA, Danish Ambassador Tanzania">
                                        <div class="form-text text-muted">Enter the name of your company or organisation if you are writing on their behalf.</div>
                                        @error('organization_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="submitter_location_text" class="form-label">Your Location / From <span class="text-muted fw-normal small">(optional)</span></label>
                                        <input type="text" class="form-control form-control-ccbrt @error('submitter_location_text') is-invalid @enderror"
                                               id="submitter_location_text" name="submitter_location_text"
                                               value="{{ old('submitter_location_text') }}"
                                               placeholder="e.g. Dar es Salaam, Kenya, UK Embassy">
                                        @error('submitter_location_text')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label for="mabinti_compliment_text" class="form-label fw-semibold">Your Compliment <span class="text-muted fw-normal small">(optional)</span></label>
                                    <textarea class="form-control form-control-ccbrt @error('message') is-invalid @enderror"
                                              id="mabinti_compliment_text" name="message" rows="4"
                                              placeholder="Share your kind words about Mabinti Centre — our products, team, or service...">{{ old('message') }}</textarea>
                                    <div class="form-text text-muted">This will be recorded as your compliment text.</div>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- ── Standard service / experience questions (hidden when Mabinti compliment) ── --}}
                            <div id="standard-experience-fields">

                            @php
                                $serviceRatings  = \App\Models\Feedback::SERVICE_RATINGS;
                                $allClientUnits  = \App\Models\Feedback::SERVICE_UNITS_OPD
                                                 + \App\Models\Feedback::SERVICE_UNITS_IPD
                                                 + \App\Models\Feedback::SERVICE_UNITS_THEATRE;
                                $oldUnits        = old('service_units', []);
                            @endphp

                            <div class="mb-4" id="service-units-section">
                                <label class="form-label fw-semibold">1. {{ __('portal.feedback_create.questions.service_offered') }}</label>
                                <p class="professional-note mb-0" id="service-units-hint">{{ __('portal.feedback_create.questions.service_offered_hint') }}</p>
                                <div id="service-units-panel" class="service-units-panel">
                                    @php
                                        $unitGroups = [
                                            ['icon' => 'bi-hospital',         'label' => __('portal.feedback_create.service_groups.opd'),     'units' => \App\Models\Feedback::SERVICE_UNITS_OPD],
                                            ['icon' => 'bi-building',         'label' => __('portal.feedback_create.service_groups.ipd'),     'units' => \App\Models\Feedback::SERVICE_UNITS_IPD],
                                            ['icon' => 'bi-clipboard2-pulse', 'label' => __('portal.feedback_create.service_groups.theatre'), 'units' => \App\Models\Feedback::SERVICE_UNITS_THEATRE],
                                        ];
                                    @endphp
                                    @foreach($unitGroups as $group)
                                        @if(count($group['units']))
                                        <div class="service-unit-group">
                                            <div class="service-unit-group-title">
                                                <i class="bi {{ $group['icon'] }}"></i>{{ $group['label'] }}
                                            </div>
                                            <div class="service-units-pills">
                                                @foreach($group['units'] as $value => $label)
                                                    <input class="service-unit-check" type="checkbox" name="service_units[]" id="su_{{ $value }}" value="{{ $value }}" {{ in_array($value, $oldUnits) ? 'checked' : '' }}>
                                                    <label class="service-unit-pill" for="su_{{ $value }}">{{ $label }}</label>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                @error('service_units')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                {{-- Other text input (shown when Other checkbox is ticked) --}}
                                <div id="service-unit-other-wrap" class="mt-2" style="display:none;">
                                    <input type="text"
                                           class="form-control form-control-ccbrt @error('service_unit_other_text') is-invalid @enderror"
                                           id="service_unit_other_text" name="service_unit_other_text"
                                           value="{{ old('service_unit_other_text') }}"
                                           placeholder="Please specify the product or service you received...">
                                    @error('service_unit_other_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">2. {{ __('portal.feedback_create.questions.service_rating') }} <span class="text-danger">*</span></label>
                                <div class="rating-grid">
                                    @php
                                        $ratingMeta = [
                                            'poor'      => ['icon' => 'bi-emoji-frown',   'sub' => __('portal.options.service_ratings_sub.poor')],
                                            'average'   => ['icon' => 'bi-emoji-neutral', 'sub' => __('portal.options.service_ratings_sub.average')],
                                            'good'      => ['icon' => 'bi-emoji-smile',   'sub' => __('portal.options.service_ratings_sub.good')],
                                            'excellent' => ['icon' => 'bi-stars',         'sub' => __('portal.options.service_ratings_sub.excellent')],
                                        ];
                                    @endphp
                                    @foreach($serviceRatings as $value => $label)
                                        <input class="rating-radio" type="radio" name="service_rating" id="service_rating_{{ $value }}" value="{{ $value }}" {{ old('service_rating') == $value ? 'checked' : '' }} required>
                                        <label class="rating-card" for="service_rating_{{ $value }}">
                                            <span class="rating-icon"><i class="bi {{ $ratingMeta[$value]['icon'] ?? 'bi-star' }}"></i></span>
                                            <span>
                                                <strong>{{ __('portal.options.service_ratings.' . $value) }}</strong>
                                                <small>{{ $ratingMeta[$value]['sub'] ?? '' }}</small>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('service_rating')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Fields hidden when feedback_type = compliment --}}
                            <div id="non-compliment-fields">

                            {{-- Q3: Mabinti → product satisfaction | Standard → confidentiality --}}
                            <div id="q3-confidentiality-block" class="row mb-4">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label fw-semibold">3. {{ __('portal.feedback_create.questions.confidentiality') }}</label>
                                    <div class="confidentiality-choice-group">
                                        <input class="confidentiality-radio" type="radio" name="confidentiality_respected" id="confidentiality_yes" value="1" {{ old('confidentiality_respected') === '1' ? 'checked' : '' }}>
                                        <label class="confidentiality-choice" for="confidentiality_yes"><i class="bi bi-check-circle"></i>{{ __('portal.common.yes') }}</label>

                                        <input class="confidentiality-radio" type="radio" name="confidentiality_respected" id="confidentiality_no" value="0" {{ old('confidentiality_respected') === '0' ? 'checked' : '' }}>
                                        <label class="confidentiality-choice" for="confidentiality_no"><i class="bi bi-x-circle"></i>{{ __('portal.common.no') }}</label>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="confidentiality_comment" class="form-label">{{ __('portal.feedback_create.fields.confidentiality_comment') }}</label>
                                    <textarea class="form-control form-control-ccbrt @error('confidentiality_comment') is-invalid @enderror"
                                              id="confidentiality_comment" name="confidentiality_comment" rows="3"
                                              placeholder="{{ __('portal.feedback_create.fields.confidentiality_comment_placeholder') }}">{{ old('confidentiality_comment') }}</textarea>
                                    @error('confidentiality_comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="q3-product-satisfaction-block" class="row mb-4" style="display:none;">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label fw-semibold">3. Were you satisfied with the delivery and quality / colour of the product?</label>
                                    <div class="confidentiality-choice-group">
                                        <input class="confidentiality-radio" type="radio" name="product_satisfied" id="product_satisfied_yes" value="1" {{ old('product_satisfied') === '1' ? 'checked' : '' }}>
                                        <label class="confidentiality-choice" for="product_satisfied_yes"><i class="bi bi-check-circle"></i>Yes</label>

                                        <input class="confidentiality-radio" type="radio" name="product_satisfied" id="product_satisfied_no" value="0" {{ old('product_satisfied') === '0' ? 'checked' : '' }}>
                                        <label class="confidentiality-choice" for="product_satisfied_no"><i class="bi bi-x-circle"></i>No</label>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label for="product_satisfaction_comment" class="form-label">If no, please explain</label>
                                    <textarea class="form-control form-control-ccbrt @error('product_satisfaction_comment') is-invalid @enderror"
                                              id="product_satisfaction_comment" name="product_satisfaction_comment" rows="3"
                                              placeholder="Tell us what was wrong with the delivery, quality or colour...">{{ old('product_satisfaction_comment') }}</textarea>
                                    @error('product_satisfaction_comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="overall_experience" class="form-label required fw-semibold">4. {{ __('portal.feedback_create.fields.overall_experience') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control form-control-ccbrt @error('overall_experience') is-invalid @enderror"
                                          id="overall_experience" name="overall_experience" rows="4" required
                                          placeholder="{{ __('portal.feedback_create.fields.overall_experience_placeholder') }}">{{ old('overall_experience') }}</textarea>
                                <div id="overallExperienceHelp" class="form-text text-muted" data-template="{{ __('portal.common.character_count', ['count' => '__COUNT__', 'min' => 10]) }}">
                                    <span id="overallExperienceCount">0</span> {{ __('portal.common.character_count', ['count' => '', 'min' => 10]) }}
                                </div>
                                @error('overall_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="improvement_suggestion" class="form-label fw-semibold">5. {{ __('portal.feedback_create.fields.improvement_suggestion') }}</label>
                                <textarea class="form-control form-control-ccbrt @error('improvement_suggestion') is-invalid @enderror"
                                          id="improvement_suggestion" name="improvement_suggestion" rows="4"
                                          placeholder="{{ __('portal.feedback_create.fields.improvement_suggestion_placeholder') }}">{{ old('improvement_suggestion') }}</textarea>
                                @error('improvement_suggestion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            </div>{{-- end #non-compliment-fields --}}

                            </div>{{-- end #standard-experience-fields --}}

                            <!-- ④ Additional Details -->
                            <div class="form-section-header">
                                <span class="section-badge">4</span>
                                <h5>{{ __('portal.feedback_create.sections.additional_details') }}</h5>
                            </div>

                            <div class="mb-4" id="standard-message-field">
                                <label for="message" class="form-label">{{ __('portal.feedback_create.fields.message') }}</label>
                                <textarea class="form-control form-control-ccbrt @error('message') is-invalid @enderror"
                                          id="message" name="message" rows="4"
                                          placeholder="{{ __('portal.feedback_create.fields.message_placeholder') }}">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="attachment" class="form-label">{{ __('portal.feedback_create.fields.attachment') }}</label>
                                <input type="file" class="form-control form-control-ccbrt @error('attachment') is-invalid @enderror" 
                                       id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf">
                                <div class="form-text text-muted">
                                    {{ __('portal.feedback_create.fields.attachment_help') }}
                                </div>
                                @error('attachment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ⑤ Additional Options -->
                            <div class="form-section-header">
                                <span class="section-badge">5</span>
                                <h5>{{ __('portal.feedback_create.sections.additional_options') }}</h5>
                            </div>

                            <div class="feedback-options-panel mb-4">
                                <div id="urgentOptionCard" class="feedback-option-card feedback-option-card--urgent {{ old('is_urgent') ? 'is-active' : '' }}">
                                    <div class="feedback-option-card__toggle">
                                        <input class="form-check-input" type="checkbox" id="is_urgent" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }}>
                                        <span class="feedback-option-card__icon" aria-hidden="true">
                                            <i class="bi bi-exclamation-triangle"></i>
                                        </span>
                                        <div class="feedback-option-card__body">
                                            <label class="feedback-option-card__label" for="is_urgent">
                                                {{ __('portal.feedback_create.fields.urgent') }}
                                            </label>
                                            <p class="feedback-option-card__hint">{{ __('portal.feedback_create.fields.urgent_help') }}</p>

                                            <div class="feedback-option-card__meta">
                                                <div id="urgentPhoneAlert" class="alert alert-warning py-2 px-3 mb-0 {{ old('is_urgent') ? '' : 'd-none' }}" role="alert">
                                                    {{ __('portal.feedback_create.fields.urgent_phone_alert') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="consentOptionCard" class="feedback-option-card feedback-option-card--consent {{ old('consent_given') ? 'is-active' : '' }} @error('consent_given') is-invalid @enderror">
                                    <div class="feedback-option-card__toggle">
                                        <input class="form-check-input @error('consent_given') is-invalid @enderror" type="checkbox"
                                               id="consent_given" name="consent_given" value="1" {{ old('consent_given') ? 'checked' : '' }} required>
                                        <span class="feedback-option-card__icon" aria-hidden="true">
                                            <i class="bi bi-shield-check"></i>
                                        </span>
                                        <div class="feedback-option-card__body">
                                            <label class="feedback-option-card__label" for="consent_given">
                                                {{ __('portal.feedback_create.fields.consent') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <p class="feedback-option-card__hint">{{ __('portal.feedback_create.fields.consent_help') }}</p>

                                            @error('consent_given')
                                                <div class="text-danger small consent-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5 pt-3 border-top">
                                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg px-4">{{ __('portal.common.cancel') }}</a>
                                <button type="submit" id="feedbackSubmitButton" class="btn btn-ccbrt-primary btn-lg px-5" data-loading-text="{{ __('portal.feedback_create.submitting_button') }}">
                                    <span class="submit-idle-state"><i class="bi bi-send me-2"></i>{{ __('portal.feedback_create.submit_button') }}</span>
                                    <span class="submit-loading-state d-none"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>{{ __('portal.feedback_create.submitting_button') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // --- Overall experience character counter ---
    const overallExperienceField = document.getElementById('overall_experience');
    const overallExperienceCount = document.getElementById('overallExperienceCount');
    const overallExperienceHelp  = document.getElementById('overallExperienceHelp');

    const syncOverallExperienceHelp = function(length) {
        if (!overallExperienceHelp) return;
        if (overallExperienceCount) overallExperienceCount.textContent = length;
        overallExperienceHelp.style.color = length >= 10 ? 'var(--ccbrt-teal)' : '#dc3545';
    };

    if (overallExperienceField && overallExperienceCount && overallExperienceHelp) {
        overallExperienceField.addEventListener('input', function() {
            syncOverallExperienceHelp(this.value.length);
        });
        syncOverallExperienceHelp(overallExperienceField.value.length);
    }

    // --- Show / hide complaint-specific fields based on feedback type ---
    const nonComplimentBlock        = document.getElementById('non-compliment-fields');
    const standardExperienceFields   = document.getElementById('standard-experience-fields');
    const standardMessageField        = document.getElementById('standard-message-field');
    const mabintiComplimentFields     = document.getElementById('mabinti-compliment-fields');
    const mabintiComplimentTextarea   = document.getElementById('mabinti_compliment_text');
    const feedbackTypeRadios = document.querySelectorAll('input[name="feedback_type"]');
    const urgentCheckbox = document.getElementById('is_urgent');
    const phoneField = document.getElementById('phone');
    const consentCheckbox = document.getElementById('consent_given');
    const urgentOptionCard = document.getElementById('urgentOptionCard');
    const consentOptionCard = document.getElementById('consentOptionCard');
    const phoneFollowupNote = document.getElementById('phoneFollowupNote');

    const q3ConfidentialityBlock    = document.getElementById('q3-confidentiality-block');
    const q3ProductSatisfactionBlock = document.getElementById('q3-product-satisfaction-block');

    function isMabintiLocation() {
        const locEl = document.getElementById('location');
        return locEl && locEl.value === 'mabinti';
    }

    function isMabintiComplimentMode() {
        const typeEl = document.querySelector('input[name="feedback_type"]:checked');
        return isMabintiLocation() && typeEl && typeEl.value === 'compliment';
    }

    function applyQ3Visibility() {
        const isMabinti = isMabintiLocation();
        if (q3ConfidentialityBlock)     q3ConfidentialityBlock.style.display     = isMabinti ? 'none' : '';
        if (q3ProductSatisfactionBlock) q3ProductSatisfactionBlock.style.display = isMabinti ? ''     : 'none';
    }

    function applyFeedbackTypeVisibility(type) {
        const isCompliment  = (type === 'compliment');
        const isMabinti     = isMabintiComplimentMode();

        if (isCompliment) {
            if (nonComplimentBlock) nonComplimentBlock.style.display = 'none';
            if (overallExperienceField) overallExperienceField.required = false;
        } else {
            if (nonComplimentBlock) nonComplimentBlock.style.display = '';
            if (overallExperienceField) overallExperienceField.required = true;
        }

        if (isMabinti) {
            if (mabintiComplimentFields)   mabintiComplimentFields.style.display   = '';
            if (standardExperienceFields)  standardExperienceFields.style.display  = 'none';
            if (standardMessageField)      standardMessageField.style.display      = 'none';
            if (overallExperienceField)    overallExperienceField.required         = false;
        } else {
            if (mabintiComplimentFields)   mabintiComplimentFields.style.display   = 'none';
            if (standardExperienceFields)  standardExperienceFields.style.display  = '';
            if (standardMessageField)      standardMessageField.style.display      = '';
        }

        applyQ3Visibility();
    }

    feedbackTypeRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            applyFeedbackTypeVisibility(this.value);
        });
    });

    function syncUrgentPhoneRequirement() {
        if (!urgentCheckbox || !phoneField) return;

        const phoneRequiredMessage = phoneField.dataset.requiredMessage || '';

        phoneField.required = urgentCheckbox.checked;

        const urgentPhoneAlert = document.getElementById('urgentPhoneAlert');

        if (urgentPhoneAlert) {
            urgentPhoneAlert.classList.toggle('d-none', !urgentCheckbox.checked);
        }

        if (urgentOptionCard) {
            urgentOptionCard.classList.toggle('is-active', urgentCheckbox.checked);
        }

        if (phoneFollowupNote) {
            phoneFollowupNote.classList.toggle('d-none', !urgentCheckbox.checked);
        }

        if (!urgentCheckbox.checked) {
            phoneField.setCustomValidity('');
            phoneField.classList.remove('is-invalid');
            return;
        }

        if (!phoneField.value.trim()) {
            phoneField.setCustomValidity(phoneRequiredMessage);
            phoneField.focus();
            return;
        }

        phoneField.setCustomValidity('');
    }

    if (urgentCheckbox && phoneField) {
        urgentCheckbox.addEventListener('change', syncUrgentPhoneRequirement);
        phoneField.addEventListener('input', function() {
            if (!urgentCheckbox.checked) { phoneField.setCustomValidity(''); return; }
            phoneField.setCustomValidity(this.value.trim() ? '' : (this.dataset.requiredMessage || ''));
        });
        phoneField.addEventListener('invalid', function() {
            if (urgentCheckbox.checked && !this.value.trim()) this.setCustomValidity(this.dataset.requiredMessage || '');
        });
        syncUrgentPhoneRequirement();
    }

    if (consentCheckbox && consentOptionCard) {
        const syncConsentCardState = function() { consentOptionCard.classList.toggle('is-active', consentCheckbox.checked); };
        consentCheckbox.addEventListener('change', syncConsentCardState);
        syncConsentCardState();
    }

    const feedbackForm   = document.getElementById('feedbackForm');
    const submitButton   = document.getElementById('feedbackSubmitButton');
    if (feedbackForm && submitButton) {
        feedbackForm.addEventListener('submit', function(event) {
            if (!feedbackForm.checkValidity()) return;
            submitButton.disabled = true;
            submitButton.setAttribute('aria-busy', 'true');
            submitButton.querySelector('.submit-idle-state')?.classList.add('d-none');
            submitButton.querySelector('.submit-loading-state')?.classList.remove('d-none');
        });
    }

    // Set correct initial state on page load (handles old() values after validation)
    (function() {
        const checked = document.querySelector('input[name="feedback_type"]:checked');
        applyFeedbackTypeVisibility(checked ? checked.value : '');
    })();

    // Re-apply when location changes (Mabinti mode depends on both)
    (function() {
        const locEl = document.getElementById('location');
        if (!locEl) return;
        locEl.addEventListener('change', function() {
            const checked = document.querySelector('input[name="feedback_type"]:checked');
            applyFeedbackTypeVisibility(checked ? checked.value : '');
        });
    })();

    // ── Location-aware service panel swap ────────────────────────
    (function () {
        const locationMap = @json($locationServiceMap ?? []);

        const locationSelect = document.getElementById('location');
        const panel          = document.getElementById('service-units-panel');
        const hintEl         = document.getElementById('service-units-hint');

        if (!locationSelect || !panel) return;

        const defaultHTML    = panel.innerHTML;
        const defaultHint    = hintEl ? hintEl.textContent : '';

        let idCounter = 1000;

        function buildCustomPanel(groups) {
            let html = '';
            for (const [groupName, items] of Object.entries(groups)) {
                html += `<div class="service-unit-group">
                    <div class="service-unit-group-title"><i class="bi bi-bag"></i>${groupName}</div>
                    <div class="service-units-pills">`;
                items.forEach(item => {
                    const uid = 'su_custom_' + (idCounter++);
                    html += `<input class="service-unit-check" type="checkbox" name="service_units[]" id="${uid}" value="${item.key}">
                             <label class="service-unit-pill" for="${uid}">${item.label}</label>`;
                });
                html += `</div></div>`;
            }
            return html;
        }

        function onLocationChange() {
            const key = locationSelect.value;
            if (key && locationMap[key]) {
                panel.innerHTML = buildCustomPanel(locationMap[key]);
                if (hintEl) hintEl.textContent = 'Select all products or services related to your visit.';
            } else {
                panel.innerHTML = defaultHTML;
                if (hintEl) hintEl.textContent = defaultHint;
            }
        }

        locationSelect.addEventListener('change', function() {
            onLocationChange();
            syncOtherTextInput();
        });

        // Restore on page load if old location was set
        const oldLocation = locationSelect.value;
        if (oldLocation && locationMap[oldLocation]) {
            onLocationChange();
            // Re-check any old service_units values
            const oldUnits = @json(old('service_units', []));
            oldUnits.forEach(val => {
                const cb = panel.querySelector('input[value="' + val + '"]');
                if (cb) cb.checked = true;
            });
        }
    })();

    // ── Other service unit text input toggle ─────────────────────
    (function () {
        const otherWrap = document.getElementById('service-unit-other-wrap');
        const otherText = document.getElementById('service_unit_other_text');
        const panel     = document.getElementById('service-units-panel');
        if (!otherWrap || !panel) return;

        function syncOtherWrap() {
            const otherCb = panel.querySelector('input[value="other"]');
            const checked = otherCb && otherCb.checked;
            otherWrap.style.display = checked ? '' : 'none';
            if (!checked && otherText) otherText.value = '';
        }

        panel.addEventListener('change', function (e) {
            if (e.target && e.target.value === 'other') syncOtherWrap();
        });

        // Expose for location-change hook
        window.syncOtherTextInput = syncOtherWrap;

        // Initial state
        syncOtherWrap();
        @if(old('service_unit_other_text'))
            const otherCbInit = panel.querySelector('input[value="other"]');
            if (otherCbInit) { otherCbInit.checked = true; syncOtherWrap(); }
        @endif
    })();
</script>
@endpush
