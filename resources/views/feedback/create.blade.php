@extends('layouts.public')

@section('title', __('portal.meta.feedback_create_title'))

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
                <div class="info-box">
                    <h6 class="mb-2" style="color: var(--ccbrt-navy);">
                        <i class="bi bi-info-circle me-2"></i>{{ __('portal.feedback_create.info_title') }}
                    </h6>
                    <ul class="mb-0 ps-3">
                        <li>{{ __('portal.feedback_create.info_items.required_fields') }}</li>
                        <li>{{ __('portal.feedback_create.info_items.anonymous') }}</li>
                        <li>{{ __('portal.feedback_create.info_items.response') }}</li>
                        <li>{{ __('portal.feedback_create.info_items.review') }}</li>
                    </ul>
                </div>

                <!-- Form Card -->
                <div class="card card-ccbrt">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>{{ __('portal.feedback_create.form_title') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('feedback.store') }}" method="POST" enctype="multipart/form-data">
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

                            <!-- Contact Information -->
                            <h5 class="mb-3" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
                                {{ __('portal.feedback_create.sections.contact_information') }} <small class="text-muted fw-normal">({{ __('portal.common.optional') }})</small>
                            </h5>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="patient_name" class="form-label">{{ __('portal.feedback_create.fields.full_name') }}</label>
                                    <input type="text" class="form-control form-control-ccbrt @error('patient_name') is-invalid @enderror" 
                                           id="patient_name" name="patient_name" value="{{ old('patient_name') }}"
                                           placeholder="{{ __('portal.feedback_create.fields.full_name_placeholder') }}">
                                    @error('patient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ __('portal.feedback_create.fields.email') }}</label>
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
                                    <label for="phone" class="form-label">{{ __('portal.feedback_create.fields.phone') }}</label>
                                    <input type="tel" class="form-control form-control-ccbrt @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}"
                                           placeholder="{{ __('portal.feedback_create.fields.phone_placeholder') }}">
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

                            @php
                                $serviceUnits = \App\Models\Feedback::SERVICE_UNITS;
                                $serviceRatings = \App\Models\Feedback::SERVICE_RATINGS;
                            @endphp

                            <h5 class="mb-3 mt-4" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">{{ __('portal.feedback_create.sections.customer_experience') }}</h5>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ __('portal.feedback_create.questions.service_offered') }}</label>
                                <div class="row row-cols-1 row-cols-md-2 g-2 mt-1">
                                    @foreach($serviceUnits as $value => $label)
                                        <div class="col">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_units[]" id="service_unit_{{ $value }}"
                                                       value="{{ $value }}" {{ in_array($value, old('service_units', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="service_unit_{{ $value }}">{{ __('portal.options.service_units.' . $value) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('service_units')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('service_units.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label required fw-semibold">{{ __('portal.feedback_create.questions.service_rating') }}</label>
                                <div class="d-flex flex-wrap gap-3 mt-2">
                                    @foreach($serviceRatings as $value => $label)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="service_rating" id="service_rating_{{ $value }}"
                                                   value="{{ $value }}" {{ old('service_rating') == $value ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="service_rating_{{ $value }}">{{ __('portal.options.service_ratings.' . $value) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('service_rating')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label fw-semibold">{{ __('portal.feedback_create.questions.confidentiality') }}</label>
                                    <div class="d-flex gap-4 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="confidentiality_respected" id="confidentiality_yes"
                                                   value="1" {{ old('confidentiality_respected') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="confidentiality_yes">{{ __('portal.common.yes') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="confidentiality_respected" id="confidentiality_no"
                                                   value="0" {{ old('confidentiality_respected') === '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="confidentiality_no">{{ __('portal.common.no') }}</label>
                                        </div>
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

                            <div class="mb-4">
                                <label for="overall_experience" class="form-label required fw-semibold">{{ __('portal.feedback_create.fields.overall_experience') }}</label>
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
                                <label for="improvement_suggestion" class="form-label fw-semibold">{{ __('portal.feedback_create.fields.improvement_suggestion') }}</label>
                                <textarea class="form-control form-control-ccbrt @error('improvement_suggestion') is-invalid @enderror"
                                          id="improvement_suggestion" name="improvement_suggestion" rows="4"
                                          placeholder="{{ __('portal.feedback_create.fields.improvement_suggestion_placeholder') }}">{{ old('improvement_suggestion') }}</textarea>
                                @error('improvement_suggestion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <h5 class="mb-3 mt-4" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">{{ __('portal.feedback_create.sections.additional_details') }}</h5>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required">{{ __('portal.feedback_create.fields.feedback_type') }}</label>
                                    <div class="d-flex flex-wrap gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="feedback_type" id="type_compliment"
                                                   value="compliment" {{ old('feedback_type') == 'compliment' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="type_compliment">{{ __('portal.options.feedback_types.compliment') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="feedback_type" id="type_complaint"
                                                   value="complaint" {{ old('feedback_type') == 'complaint' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="type_complaint">{{ __('portal.options.feedback_types.complaint') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="feedback_type" id="type_suggestion"
                                                   value="suggestion" {{ old('feedback_type') == 'suggestion' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="type_suggestion">{{ __('portal.options.feedback_types.suggestion') }}</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="feedback_type" id="type_enquiry"
                                                   value="enquiry" {{ old('feedback_type') == 'enquiry' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="type_enquiry">{{ __('portal.options.feedback_types.enquiry') }}</label>
                                        </div>
                                    </div>
                                    @error('feedback_type')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="message" class="form-label">{{ __('portal.feedback_create.fields.message') }}</label>
                                    <textarea class="form-control form-control-ccbrt @error('message') is-invalid @enderror"
                                              id="message" name="message" rows="4"
                                              placeholder="{{ __('portal.feedback_create.fields.message_placeholder') }}">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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

                            <h5 class="mb-3 mt-4" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">{{ __('portal.feedback_create.sections.additional_options') }}</h5>

                            <div class="mb-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_urgent" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_urgent">
                                        <strong>{{ __('portal.feedback_create.fields.urgent') }}</strong> - {{ __('portal.feedback_create.fields.urgent_help') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input @error('consent_given') is-invalid @enderror" type="checkbox" 
                                           id="consent_given" name="consent_given" value="1" {{ old('consent_given') ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="consent_given">
                                        {{ __('portal.feedback_create.fields.consent') }} 
                                        <span class="text-danger">*</span>
                                    </label>
                                </div>
                                @error('consent_given')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                                <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-lg px-4">{{ __('portal.common.cancel') }}</a>
                                <button type="submit" class="btn btn-ccbrt-primary btn-lg px-5">
                                    <i class="bi bi-send me-2"></i>{{ __('portal.feedback_create.submit_button') }}
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
    const overallExperienceField = document.getElementById('overall_experience');
    const overallExperienceCount = document.getElementById('overallExperienceCount');
    const overallExperienceHelp = document.getElementById('overallExperienceHelp');

    const syncOverallExperienceHelp = function(length) {
        if (!overallExperienceCount || !overallExperienceHelp) {
            return;
        }

        overallExperienceCount.textContent = length;
        overallExperienceHelp.innerHTML = overallExperienceHelp.dataset.template.replace('__COUNT__', String(length)).replace('__COUNT__', String(length));
        overallExperienceHelp.prepend(overallExperienceCount);
        overallExperienceCount.insertAdjacentText('afterend', ' ');
        overallExperienceCount.style.color = length >= 10 ? 'var(--ccbrt-teal)' : '#dc3545';
    };

    if (overallExperienceField && overallExperienceCount && overallExperienceHelp) {
        overallExperienceField.addEventListener('input', function() {
            syncOverallExperienceHelp(this.value.length);
        });

        syncOverallExperienceHelp(overallExperienceField.value.length);
    }
</script>
@endpush
