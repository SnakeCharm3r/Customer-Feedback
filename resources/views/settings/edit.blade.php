@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<style>
    .settings-shell {
        --settings-border: rgba(148, 163, 184, 0.18);
    }

    .settings-shell .card {
        border: 1px solid var(--settings-border);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        border-radius: 1.1rem;
    }

    .settings-sticky {
        position: sticky;
        top: 92px;
    }

    .settings-nav-link {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        width: 100%;
        padding: 0.8rem 0.95rem;
        border-radius: 0.9rem;
        color: #334155;
        font-weight: 600;
        text-decoration: none;
        background: #fff;
        border: 1px solid rgba(148, 163, 184, 0.18);
        transition: all 0.2s ease;
    }

    .settings-nav-link:hover {
        color: #065321;
        border-color: rgba(6, 83, 33, 0.2);
        background: rgba(6, 83, 33, 0.04);
    }

    .settings-section {
        scroll-margin-top: 110px;
    }

    .settings-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: rgba(6, 83, 33, 0.08);
        color: #065321;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .settings-preview-tile {
        border: 1px dashed rgba(148, 163, 184, 0.45);
        border-radius: 1rem;
        padding: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .settings-mini-label {
        color: #64748b;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .settings-summary-item + .settings-summary-item {
        border-top: 1px solid rgba(148, 163, 184, 0.15);
        margin-top: 1rem;
        padding-top: 1rem;
    }

    .settings-textarea {
        min-height: 120px;
    }

    @media (max-width: 1199.98px) {
        .settings-sticky {
            position: static;
            top: auto;
        }
    }
</style>

<div class="settings-shell">
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <span class="settings-kicker mb-2"><i class="bi bi-sliders2"></i>System Settings</span>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-xl-3">
                <div class="settings-sticky">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="settings-mini-label mb-3">Manage Areas</div>
                            <div class="d-grid gap-2">
                                <a href="#section-branding" class="settings-nav-link"><i class="bi bi-palette2"></i><span>Branding</span></a>
                                <a href="#section-homepage" class="settings-nav-link"><i class="bi bi-window"></i><span>Homepage Content</span></a>
                                <a href="#section-footer" class="settings-nav-link"><i class="bi bi-layout-text-window-reverse"></i><span>Footer Content</span></a>
                                <a href="#section-contact" class="settings-nav-link"><i class="bi bi-envelope-paper"></i><span>Contact and Email</span></a>
                                <a href="#section-security" class="settings-nav-link"><i class="bi bi-shield-lock"></i><span>Security</span></a>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-save me-2"></i>Save Settings
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="mb-1">Current Preview</h5>
                            <p class="text-muted small mb-0">Quick view of the active public-facing values.</p>
                        </div>
                        <div class="card-body">
                            <div class="settings-preview-tile text-center mb-3">
                                <img src="{{ $settings->logoUrl() }}" alt="Current logo" class="img-fluid mb-3" style="max-height:96px; object-fit:contain;">
                                <div class="fw-semibold">{{ $settings->organization_name }}</div>
                                <div class="text-muted small">{{ $settings->portal_name }}</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between border rounded-3 p-3">
                                <div>
                                    <div class="fw-semibold">Favicon</div>
                                    <div class="text-muted small">Browser tab icon</div>
                                </div>
                                <img src="{{ $settings->faviconUrl() }}" alt="Current favicon" style="width:32px; height:32px; object-fit:contain;">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-white border-0 pb-0">
                            <h5 class="mb-1">Live Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="settings-summary-item">
                                <div class="settings-mini-label mb-1">Homepage</div>
                                <div class="fw-semibold">{{ $settings->homeHeroTitle() }}</div>
                                <div class="text-muted small mt-1">{{ $settings->homePrimaryCtaLabel() }} / {{ $settings->homeSecondaryCtaLabel() }}</div>
                            </div>
                            <div class="settings-summary-item">
                                <div class="settings-mini-label mb-1">Footer</div>
                                <div class="fw-semibold">{{ $settings->footerLocationText() }}</div>
                                <div class="text-muted small mt-1">{{ $settings->footerHoursText() }}</div>
                            </div>
                            <div class="settings-summary-item">
                                <div class="settings-mini-label mb-1">Public Contact</div>
                                <div class="fw-semibold">{{ $settings->contact_email ?: 'Not set' }}</div>
                                <div class="fw-semibold">{{ $settings->contact_phone ?: 'Not set' }}</div>
                            </div>
                            <div class="settings-summary-item">
                                <div class="settings-mini-label mb-1">Security</div>
                                <div class="fw-semibold">{{ $settings->login_max_attempts }} attempts</div>
                                <div class="fw-semibold">{{ $settings->login_lockout_minutes }} minute lockout</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9">
                <div id="section-branding" class="card settings-section mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="mb-1">Branding</h5>
                        <p class="text-muted small mb-0">Update names and brand assets used across the dashboard, login pages, and public portal.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="organization_name" class="form-label fw-semibold">Organization Name</label>
                                <input type="text" id="organization_name" name="organization_name" value="{{ old('organization_name', $settings->organization_name) }}" class="form-control @error('organization_name') is-invalid @enderror" required>
                                @error('organization_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="portal_name" class="form-label fw-semibold">Portal / System Name</label>
                                <input type="text" id="portal_name" name="portal_name" value="{{ old('portal_name', $settings->portal_name) }}" class="form-control @error('portal_name') is-invalid @enderror" required>
                                @error('portal_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="logo" class="form-label fw-semibold">Logo</label>
                                <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png,.svg,.webp" class="form-control @error('logo') is-invalid @enderror">
                                <div class="form-text">Used in the dashboard, login pages, and public portal.</div>
                                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if($settings->logo_path)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_logo" id="remove_logo" value="1">
                                        <label class="form-check-label" for="remove_logo">Remove custom logo and use the default asset</label>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="favicon" class="form-label fw-semibold">Favicon</label>
                                <input type="file" id="favicon" name="favicon" accept=".ico,.png,.svg,.webp" class="form-control @error('favicon') is-invalid @enderror">
                                <div class="form-text">Shown in browser tabs and bookmarks.</div>
                                @error('favicon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if($settings->favicon_path)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_favicon" id="remove_favicon" value="1">
                                        <label class="form-check-label" for="remove_favicon">Remove custom favicon and use the default asset</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div id="section-homepage" class="card settings-section mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="mb-1">Homepage Content</h5>
                        <p class="text-muted small mb-0">Control the opening message and call-to-action buttons on the public homepage. Leave fields blank to keep the translated default text.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="home_hero_title" class="form-label fw-semibold">Hero Title</label>
                                <input type="text" id="home_hero_title" name="home_hero_title" value="{{ old('home_hero_title', $settings->homeHeroTitle()) }}" class="form-control @error('home_hero_title') is-invalid @enderror" placeholder="Leave blank to use the default translated title">
                                @error('home_hero_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="home_hero_subtitle" class="form-label fw-semibold">Hero Subtitle</label>
                                <textarea id="home_hero_subtitle" name="home_hero_subtitle" class="form-control settings-textarea @error('home_hero_subtitle') is-invalid @enderror" placeholder="Describe the message shown below the main homepage title">{{ old('home_hero_subtitle', $settings->homeHeroSubtitle()) }}</textarea>
                                @error('home_hero_subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="home_primary_cta_label" class="form-label fw-semibold">Primary Button Label</label>
                                <input type="text" id="home_primary_cta_label" name="home_primary_cta_label" value="{{ old('home_primary_cta_label', $settings->homePrimaryCtaLabel()) }}" class="form-control @error('home_primary_cta_label') is-invalid @enderror" placeholder="For example: Share Your Feedback">
                                @error('home_primary_cta_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="home_secondary_cta_label" class="form-label fw-semibold">Secondary Button Label</label>
                                <input type="text" id="home_secondary_cta_label" name="home_secondary_cta_label" value="{{ old('home_secondary_cta_label', $settings->homeSecondaryCtaLabel()) }}" class="form-control @error('home_secondary_cta_label') is-invalid @enderror" placeholder="For example: Track My Feedback">
                                @error('home_secondary_cta_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="section-footer" class="card settings-section mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="mb-1">Footer Content</h5>
                        <p class="text-muted small mb-0">Manage the descriptive text, location details, hours, and policy links shown in the public footer.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="footer_about_text" class="form-label fw-semibold">About Text</label>
                                <textarea id="footer_about_text" name="footer_about_text" class="form-control settings-textarea @error('footer_about_text') is-invalid @enderror" placeholder="Short description about the hospital or feedback system shown in the footer">{{ old('footer_about_text', $settings->footerAboutText()) }}</textarea>
                                <div class="form-text">If left blank, the existing translated footer description remains in use.</div>
                                @error('footer_about_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="footer_location_text" class="form-label fw-semibold">Location Text</label>
                                <input type="text" id="footer_location_text" name="footer_location_text" value="{{ old('footer_location_text', $settings->footerLocationText()) }}" class="form-control @error('footer_location_text') is-invalid @enderror" placeholder="Hospital address or location note">
                                @error('footer_location_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="footer_hours_text" class="form-label fw-semibold">Hours Text</label>
                                <input type="text" id="footer_hours_text" name="footer_hours_text" value="{{ old('footer_hours_text', $settings->footerHoursText()) }}" class="form-control @error('footer_hours_text') is-invalid @enderror" placeholder="Opening hours or response hours">
                                @error('footer_hours_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="footer_privacy_text" class="form-label fw-semibold">Privacy / Terms Intro Text</label>
                                <textarea id="footer_privacy_text" name="footer_privacy_text" class="form-control settings-textarea @error('footer_privacy_text') is-invalid @enderror" placeholder="Short privacy or trust message shown above the footer links">{{ old('footer_privacy_text', $settings->footerPrivacyText()) }}</textarea>
                                @error('footer_privacy_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="privacy_policy_url" class="form-label fw-semibold">Privacy Policy URL</label>
                                <input type="url" id="privacy_policy_url" name="privacy_policy_url" value="{{ old('privacy_policy_url', $settings->privacyPolicyUrl()) }}" class="form-control @error('privacy_policy_url') is-invalid @enderror" placeholder="https://example.org/privacy-policy">
                                @error('privacy_policy_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="terms_of_use_url" class="form-label fw-semibold">Terms of Use URL</label>
                                <input type="url" id="terms_of_use_url" name="terms_of_use_url" value="{{ old('terms_of_use_url', $settings->termsOfUseUrl()) }}" class="form-control @error('terms_of_use_url') is-invalid @enderror" placeholder="https://example.org/terms-of-use">
                                @error('terms_of_use_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="section-contact" class="card settings-section mb-4">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="mb-1">Contact and Email</h5>
                        <p class="text-muted small mb-0">Set the public contact details and the sender identity used for outgoing emails.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label fw-semibold">Public Contact Email</label>
                                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}" class="form-control @error('contact_email') is-invalid @enderror">
                                @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label fw-semibold">Public Contact Phone</label>
                                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}" class="form-control @error('contact_phone') is-invalid @enderror">
                                @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mail_from_name" class="form-label fw-semibold">Outgoing Mail From Name</label>
                                <input type="text" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings->mail_from_name) }}" class="form-control @error('mail_from_name') is-invalid @enderror" required>
                                @error('mail_from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mail_from_address" class="form-label fw-semibold">Outgoing Mail From Address</label>
                                <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings->mail_from_address) }}" class="form-control @error('mail_from_address') is-invalid @enderror" required>
                                @error('mail_from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div id="section-security" class="card settings-section">
                    <div class="card-header bg-white border-0 pb-0">
                        <h5 class="mb-1">Security</h5>
                        <p class="text-muted small mb-0">Control how many login failures are allowed before a temporary lockout applies.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="login_max_attempts" class="form-label fw-semibold">Maximum Login Attempts</label>
                                <input type="number" id="login_max_attempts" name="login_max_attempts" min="1" max="20" value="{{ old('login_max_attempts', $settings->login_max_attempts) }}" class="form-control @error('login_max_attempts') is-invalid @enderror" required>
                                <div class="form-text">Applies before a user is temporarily blocked from signing in.</div>
                                @error('login_max_attempts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="login_lockout_minutes" class="form-label fw-semibold">Lockout Duration (minutes)</label>
                                <input type="number" id="login_lockout_minutes" name="login_lockout_minutes" min="1" max="120" value="{{ old('login_lockout_minutes', $settings->login_lockout_minutes) }}" class="form-control @error('login_lockout_minutes') is-invalid @enderror" required>
                                <div class="form-text">New failed login attempts will respect this duration immediately after saving.</div>
                                @error('login_lockout_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection