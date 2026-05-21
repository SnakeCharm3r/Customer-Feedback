@extends('layouts.public')

@section('title', __('portal.meta.home_title'))

@section('content')

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <div class="hero-trust-bar">
                    <span class="hero-trust-chip"><i class="bi bi-shield-lock-fill"></i> {{ __('portal.home.trust.confidential') }}</span>
                    <span class="hero-trust-chip"><i class="bi bi-person-dash-fill"></i> {{ __('portal.home.trust.anonymous') }}</span>
                    <span class="hero-trust-chip"><i class="bi bi-lightning-charge-fill"></i> {{ __('portal.home.trust.fast_response') }}</span>
                </div>
                <h1 class="hero-title">{{ $systemSettings?->homeHeroTitle() ?? __('portal.home.hero_title') }}</h1>
                <p class="hero-subtitle">{{ $systemSettings?->homeHeroSubtitle() ?? __('portal.home.hero_subtitle') }}</p>
                <div class="d-flex flex-wrap gap-3 hero-cta-group">
                    <a href="{{ route('feedback.create') }}" class="btn hero-btn-primary">
                        <span class="hero-btn-shimmer"></span>
                        <i class="bi bi-chat-square-text"></i>
                        <span>{{ $systemSettings?->homePrimaryCtaLabel() ?? __('portal.home.primary_cta') }}</span>
                        <i class="bi bi-arrow-right hero-btn-arrow"></i>
                    </a>
                    <a href="{{ route('feedback.track') }}" class="btn hero-btn-outline">
                        <i class="bi bi-search"></i>
                        <span>{{ $systemSettings?->homeSecondaryCtaLabel() ?? __('portal.home.secondary_cta') }}</span>
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-flex justify-content-center">
                <div class="hero-visual">
                    <div class="hero-visual-scene">
                        <span class="pulse-ring"></span>
                        <span class="pulse-ring"></span>
                        <span class="pulse-ring"></span>
                        <div class="orbit-ring-outer"></div>
                        <div class="med-icon med-icon--tl"><i class="bi bi-heart-pulse-fill"></i></div>
                        <div class="med-icon med-icon--tr"><i class="bi bi-hospital-fill"></i></div>
                        <div class="med-icon med-icon--ml"><i class="bi bi-eye-fill"></i></div>
                        <div class="med-icon med-icon--mr"><i class="bi bi-capsule"></i></div>
                        <div class="med-icon med-icon--bl"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                        <div class="med-icon med-icon--br"><i class="bi bi-activity"></i></div>
                        <i class="bi bi-heart-pulse hero-center-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-700 mb-2" style="color: var(--ccbrt-navy); font-weight: 700;">{{ __('portal.home.process_title') }}</h2>
            <p class="text-muted mb-0">{{ __('portal.home.process_subtitle') }}</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-card-number"><i class="bi bi-pencil-square"></i></div>
                <div>
                    <div class="step-card-title">{{ __('portal.home.process_steps.submit.title') }}</div>
                    <p class="step-card-desc">{{ __('portal.home.process_steps.submit.description') }}</p>
                </div>
            </div>
            <div class="step-card">
                <div class="step-card-number"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="step-card-title">{{ __('portal.home.process_steps.receive.title') }}</div>
                    <p class="step-card-desc">{{ __('portal.home.process_steps.receive.description') }}</p>
                </div>
            </div>
            <div class="step-card">
                <div class="step-card-number"><i class="bi bi-people"></i></div>
                <div>
                    <div class="step-card-title">{{ __('portal.home.process_steps.review.title') }}</div>
                    <p class="step-card-desc">{{ __('portal.home.process_steps.review.description') }}</p>
                </div>
            </div>
            <div class="step-card">
                <div class="step-card-number"><i class="bi bi-arrow-repeat"></i></div>
                <div>
                    <div class="step-card-title">{{ __('portal.home.process_steps.respond.title') }}</div>
                    <p class="step-card-desc">{{ __('portal.home.process_steps.respond.description') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Categories -->
<section class="py-5" style="background: #fff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="mb-2" style="color: var(--ccbrt-navy); font-weight: 700;">{{ __('portal.home.services_title') }}</h2>
            <p class="text-muted mb-0">{{ __('portal.home.services_subtitle') }}</p>
        </div>
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="service-card svc--outpatient">
                    <div class="service-icon"><i class="bi bi-hospital"></i></div>
                    <p class="service-card-title">{{ __('portal.options.service_categories.outpatient') }}</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card svc--inpatient">
                    <div class="service-icon"><i class="bi bi-bed"></i></div>
                    <p class="service-card-title">{{ __('portal.options.service_categories.inpatient') }}</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card svc--eye">
                    <div class="service-icon"><i class="bi bi-eye"></i></div>
                    <p class="service-card-title">{{ __('portal.options.service_categories.eye_surgery') }}</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card svc--rehab">
                    <div class="service-icon"><i class="bi bi-activity"></i></div>
                    <p class="service-card-title">{{ __('portal.options.service_categories.rehabilitation') }}</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card svc--pharmacy">
                    <div class="service-icon"><i class="bi bi-capsule"></i></div>
                    <p class="service-card-title">{{ __('portal.options.service_categories.pharmacy') }}</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card svc--reception">
                    <div class="service-icon"><i class="bi bi-reception-4"></i></div>
                    <p class="service-card-title">{{ __('portal.options.service_categories.reception_admin') }}</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card svc--billing">
                    <div class="service-icon"><i class="bi bi-cash-coin"></i></div>
                    <p class="service-card-title">{{ __('portal.options.service_categories.billing') }}</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="service-card svc--other">
                    <div class="service-icon"><i class="bi bi-grid-3x3-gap"></i></div>
                    <p class="service-card-title">{{ __('portal.options.service_categories.other') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container text-center" style="position: relative; z-index: 1;">
        <div class="cta-badge"><i class="bi bi-megaphone-fill"></i> {{ __('portal.home.cta_badge', ['default' => 'Your Voice Matters']) }}</div>
        <h2 class="text-white mb-3" style="font-weight: 700;">{{ __('portal.home.cta_title') }}</h2>
        <p class="text-white mb-4" style="opacity: 0.88; max-width: 560px; margin-left: auto; margin-right: auto;">
            {{ __('portal.home.cta_subtitle') }}
        </p>
        <div class="cta-actions">
            <a href="{{ route('feedback.create') }}" class="btn hero-btn-primary">
                <span class="hero-btn-shimmer"></span>
                <i class="bi bi-send"></i>
                <span>{{ __('portal.home.cta_button') }}</span>
                <i class="bi bi-arrow-right hero-btn-arrow"></i>
            </a>
            <a href="{{ route('feedback.track') }}" class="btn hero-btn-outline">
                <i class="bi bi-search"></i>
                <span>{{ __('portal.home.secondary_cta') }}</span>
            </a>
        </div>
    </div>
</section>

@endsection
