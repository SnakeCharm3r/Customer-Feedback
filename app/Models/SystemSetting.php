<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'system-settings.current';

    protected $fillable = [
        'organization_name',
        'portal_name',
        'contact_email',
        'contact_phone',
        'home_hero_title',
        'home_hero_subtitle',
        'home_primary_cta_label',
        'home_secondary_cta_label',
        'footer_about_text',
        'footer_location_text',
        'footer_hours_text',
        'footer_privacy_text',
        'privacy_policy_url',
        'terms_of_use_url',
        'mail_from_name',
        'mail_from_address',
        'login_max_attempts',
        'login_lockout_minutes',
        'logo_path',
        'favicon_path',
    ];

    protected $casts = [
        'login_max_attempts' => 'integer',
        'login_lockout_minutes' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(static fn () => self::clearCache());
        static::deleted(static fn () => self::clearCache());
    }

    public static function defaults(): array
    {
        return [
            'organization_name' => 'CCBRT Hospital',
            'portal_name' => 'Customer Feedback Portal',
            'contact_email' => config('mail.from.address', 'feedback@ccbrt.org'),
            'contact_phone' => '+255 22 277 5000',
            'home_hero_title' => null,
            'home_hero_subtitle' => null,
            'home_primary_cta_label' => null,
            'home_secondary_cta_label' => null,
            'footer_about_text' => null,
            'footer_location_text' => null,
            'footer_hours_text' => null,
            'footer_privacy_text' => null,
            'privacy_policy_url' => null,
            'terms_of_use_url' => null,
            'mail_from_name' => config('mail.from.name', 'CCBRT Hospital Quality Assurance'),
            'mail_from_address' => config('mail.from.address', 'feedback@ccbrt.org'),
            'login_max_attempts' => 5,
            'login_lockout_minutes' => 1,
            'logo_path' => null,
            'favicon_path' => null,
        ];
    }

    public static function current(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $settings = self::query()->first();

            if ($settings) {
                return $settings;
            }

            return self::query()->create(self::defaults());
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function logoUrl(): string
    {
        if ($this->hasStoredAsset($this->logo_path)) {
            return route('system-assets.show', [
                'asset' => 'logo',
                'v' => $this->updated_at?->timestamp ?? time(),
            ]);
        }

        return asset('assets/images/ccbrt-logo.svg');
    }

    public function faviconUrl(): string
    {
        if ($this->hasStoredAsset($this->favicon_path)) {
            return route('system-assets.show', [
                'asset' => 'favicon',
                'v' => $this->updated_at?->timestamp ?? time(),
            ]);
        }

        return asset('assets/images/favicon.ico');
    }

    public function appDisplayName(): string
    {
        return trim($this->organization_name . ' ' . $this->portal_name);
    }

    public function homeHeroTitle(): string
    {
        return $this->home_hero_title ?: __('portal.home.hero_title');
    }

    public function homeHeroSubtitle(): string
    {
        return $this->home_hero_subtitle ?: __('portal.home.hero_subtitle');
    }

    public function homePrimaryCtaLabel(): string
    {
        return $this->home_primary_cta_label ?: __('portal.home.primary_cta');
    }

    public function homeSecondaryCtaLabel(): string
    {
        return $this->home_secondary_cta_label ?: __('portal.home.secondary_cta');
    }

    public function footerAboutText(): string
    {
        return $this->footer_about_text ?: __('portal.brand.about');
    }

    public function footerLocationText(): string
    {
        return $this->footer_location_text ?: __('portal.footer.location');
    }

    public function footerHoursText(): string
    {
        return $this->footer_hours_text ?: __('portal.footer.hours');
    }

    public function footerPrivacyText(): string
    {
        return $this->footer_privacy_text ?: __('portal.footer.privacy_copy');
    }

    public function privacyPolicyUrl(): ?string
    {
        return $this->privacy_policy_url ?: null;
    }

    public function termsOfUseUrl(): ?string
    {
        return $this->terms_of_use_url ?: null;
    }

    public function assetPathFor(string $asset): ?string
    {
        return match ($asset) {
            'logo' => $this->logo_path,
            'favicon' => $this->favicon_path,
            default => null,
        };
    }

    private function hasStoredAsset(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        $fullPath = Storage::disk('public')->path($path);

        return File::exists($fullPath);
    }
}