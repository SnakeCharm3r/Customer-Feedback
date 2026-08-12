<footer class="footer app-footer" aria-label="Application footer">
    <div class="container-fluid">
        <div class="app-footer-shell">
            <div class="app-footer-brand">
                <span class="app-footer-mark" aria-hidden="true">
                    <img src="{{ $systemSettings?->logoUrl() ?? asset('assets/images/ccbrt-logo.svg') }}" alt="">
                </span>
                <div class="app-footer-brand-copy">
                    <strong>{{ $systemSettings?->organization_name ?? 'CCBRT Hospital' }}</strong>
                    <span>{{ $systemSettings?->portal_name ?? 'Customer Feedback Portal' }}</span>
                </div>
            </div>

            <div class="app-footer-meta">
                <div class="app-footer-links">
                    @if($systemSettings?->privacyPolicyUrl())
                        <a href="{{ $systemSettings->privacyPolicyUrl() }}" target="_blank" rel="noopener noreferrer">
                            Privacy
                        </a>
                    @endif

                    @if($systemSettings?->termsOfUseUrl())
                        <a href="{{ $systemSettings->termsOfUseUrl() }}" target="_blank" rel="noopener noreferrer">
                            Terms of use
                        </a>
                    @endif
                </div>

                <p class="app-footer-copyright">
                    &copy; {{ now()->year }} {{ $systemSettings?->organization_name ?? 'CCBRT Hospital' }}.
                    <span>All rights reserved.</span>
                </p>
            </div>
        </div>
    </div>
</footer>
