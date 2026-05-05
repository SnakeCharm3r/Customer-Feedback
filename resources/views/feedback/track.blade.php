@extends('layouts.public')

@section('title', __('portal.meta.feedback_track_title'))

@section('content')
<!-- Page Header -->
<section class="hero-section" style="padding: 3rem 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <h1 class="hero-title" style="font-size: 2rem;">
                    <i class="bi bi-search me-2"></i>{{ __('portal.feedback_track.hero_title') }}
                </h1>
                <p class="hero-subtitle mb-0">
                    {{ __('portal.feedback_track.hero_subtitle') }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Track Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card card-ccbrt">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-input-cursor me-2"></i>{{ __('portal.feedback_track.card_title') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('feedback.track.submit') }}" method="POST">
                            @csrf
                            
                            @if (!empty($referenceLookupError))
                                <div class="alert alert-danger mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>{{ $referenceLookupError }}
                                </div>
                            @elseif ($errors->has('lookup'))
                                <div class="alert alert-danger mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first('lookup') }}
                                </div>
                            @endif

                            <div class="mb-4">
                                <label for="lookup" class="form-label required">{{ __('portal.feedback_track.reference_label') }}</label>
                                <div class="input-group input-group-lg mb-3">
                                    <span class="input-group-text" style="background-color: var(--ccbrt-navy); color: white; border-color: var(--ccbrt-navy);">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control form-control-ccbrt @error('lookup') is-invalid @enderror" 
                                           id="lookup" name="lookup" 
                                           value="{{ old('lookup', $lookup ?? request('lookup', request('reference_no'))) }}"
                                           placeholder="{{ __('portal.feedback_track.reference_placeholder') }}" required>
                                </div>
                                <div class="form-text text-muted">
                                    {{ __('portal.feedback_track.reference_help') }}
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-ccbrt-primary btn-lg">
                                    <i class="bi bi-search me-2"></i>{{ __('portal.feedback_track.submit_button') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Results Section (shown when feedback is found) -->
@if(isset($feedback))
<section class="pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-ccbrt">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>{{ __('portal.feedback_track.details_title') }}</h5>
                        <span class="badge bg-{{ $feedback->status == 'new' ? 'primary' : ($feedback->status == 'responded' ? 'info' : 'secondary') }} fs-6">
                            {{ $feedback->getStatusLabel() }}
                        </span>
                    </div>
                    <div class="card-body">
                        <!-- Reference Number -->
                        <div class="reference-box" style="padding: 1.5rem;">
                            <p class="mb-1 opacity-75">{{ __('portal.feedback_track.reference_box_label') }}</p>
                            <div class="reference-number" style="font-size: 1.5rem;">{{ $feedback->reference_no }}</div>
                        </div>

                        <!-- Status Timeline -->
                        <h6 class="mb-3 mt-4" style="color: var(--ccbrt-navy);">{{ __('portal.feedback_track.status_timeline_title') }}</h6>
                        <div class="status-timeline mb-4">
                            <div class="status-step {{ in_array($feedback->status, ['new', 'responded', 'closed']) ? 'completed' : '' }}
                                        {{ $feedback->status == 'new' ? 'active' : '' }}">
                                <div class="status-dot">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <div class="status-label">{{ __('portal.options.statuses.new') }}</div>
                            </div>
                            <div class="status-step {{ in_array($feedback->status, ['responded', 'closed']) ? 'completed' : '' }}
                                        {{ $feedback->status == 'responded' ? 'active' : '' }}">
                                <div class="status-dot">
                                    <i class="bi bi-chat-left-text"></i>
                                </div>
                                <div class="status-label">{{ __('portal.options.statuses.responded') }}</div>
                            </div>
                            <div class="status-step {{ $feedback->status == 'closed' ? 'completed' : '' }}
                                        {{ $feedback->status == 'closed' ? 'active' : '' }}">
                                <div class="status-dot">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div class="status-label">{{ __('portal.options.statuses.closed') }}</div>
                            </div>
                        </div>

                        <!-- Feedback Details -->
                        <h6 class="mb-3" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
                            {{ __('portal.feedback_track.submission_info') }}
                        </h6>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">{{ __('portal.feedback_track.submitted_on') }}</small>
                                <p class="mb-0 fw-medium">{{ $feedback->created_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">{{ __('portal.feedback_track.service_category') }}</small>
                                <p class="mb-0 fw-medium">{{ $feedback->getServiceCategoryLabel() }}</p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">{{ __('portal.feedback_track.feedback_type') }}</small>
                                <p class="mb-0 fw-medium">{{ $feedback->getFeedbackTypeLabel() }}</p>
                            </div>
                            @if($feedback->service_units_summary)
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">{{ __('portal.feedback_track.service_offered') }}</small>
                                <p class="mb-0 fw-medium">{{ $feedback->service_units_summary }}</p>
                            </div>
                            @endif
                            @if($feedback->service_rating)
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">{{ __('portal.feedback_track.service_rating') }}</small>
                                <p class="mb-0 fw-medium">{{ $feedback->getServiceRatingLabel() }}</p>
                            </div>
                            @endif
                            @if(!is_null($feedback->confidentiality_respected))
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">{{ __('portal.feedback_track.confidentiality_kept') }}</small>
                                <p class="mb-0 fw-medium">{{ $feedback->getConfidentialityLabel() }}</p>
                            </div>
                            @endif
                            @if($feedback->visit_date)
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">{{ __('portal.feedback_track.date_of_visit') }}</small>
                                <p class="mb-0 fw-medium">{{ $feedback->visit_date->format('F j, Y') }}</p>
                            </div>
                            @endif
                        </div>

                        <h6 class="mb-3" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
                            {{ __('portal.feedback_track.overall_experience') }}
                        </h6>
                        <div class="p-3 rounded-3 mb-4" style="background-color: #f8f9fa;">
                            <p class="mb-0">{{ $feedback->overall_experience ?: $feedback->message }}</p>
                        </div>

                        @if($feedback->improvement_suggestion)
                        <h6 class="mb-3" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
                            {{ __('portal.feedback_track.suggested_improvements') }}
                        </h6>
                        <div class="p-3 rounded-3 mb-4" style="background-color: #f8f9fa;">
                            <p class="mb-0">{{ $feedback->improvement_suggestion }}</p>
                        </div>
                        @endif

                        @if($feedback->confidentiality_comment)
                        <h6 class="mb-3" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
                            {{ __('portal.feedback_track.confidentiality_explanation') }}
                        </h6>
                        <div class="p-3 rounded-3 mb-4" style="background-color: #fff8e1;">
                            <p class="mb-0">{{ $feedback->confidentiality_comment }}</p>
                        </div>
                        @endif

                        <h6 class="mb-3" style="color: var(--ccbrt-navy); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
                            {{ __('portal.feedback_track.additional_comments') }}
                        </h6>
                        <div class="p-3 rounded-3 mb-4" style="background-color: #f8f9fa;">
                            <p class="mb-0">{{ $feedback->message ?: __('portal.feedback_track.no_additional_comments') }}</p>
                        </div>

                        <!-- Public Response (if available) -->
                        @if($publicResponse)
                        <h6 class="mb-3" style="color: var(--ccbrt-teal); border-bottom: 2px solid #e9ecef; padding-bottom: 0.5rem;">
                            <i class="bi bi-reply me-2"></i>{{ __('portal.feedback_track.response_title') }}
                        </h6>
                        <div class="p-3 rounded-3 mb-4" style="background-color: #e8f5e9; border-left: 4px solid var(--ccbrt-teal);">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-person-circle me-1"></i>{{ __('portal.common.quality_assurance_team') }}
                                </small>
                                <small class="text-muted">{{ $publicResponse->created_at->format('F j, Y') }}</small>
                            </div>
                            <p class="mb-0">{{ $publicResponse->content }}</p>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('portal.feedback_track.no_response') }}
                        </div>
                        @endif

                        @if($feedback->is_urgent)
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>{{ __('portal.feedback_create.fields.urgent') }}.</strong> {{ __('portal.feedback_track.urgent_alert') }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-4">
                    <a href="{{ route('feedback.track') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>{{ __('portal.feedback_track.track_another') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection
