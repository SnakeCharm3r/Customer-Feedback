<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemSettingController extends Controller
{
    public function asset(string $asset): BinaryFileResponse
    {
        $settings = SystemSetting::current();
        $path = $settings->assetPathFor($asset);

        abort_unless($path, 404);

        $fullPath = Storage::disk('public')->path($path);

        abort_unless(is_file($fullPath), 404);

        return response()->file($fullPath, [
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function edit(): View
    {
        $this->authorizeAccess();

        return view('settings.edit', [
            'settings' => SystemSetting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeAccess();

        $settings = SystemSetting::current();

        $validated = $request->validate([
            'organization_name' => ['required', 'string', 'max:120'],
            'portal_name' => ['required', 'string', 'max:120'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'home_hero_title' => ['nullable', 'string', 'max:160'],
            'home_hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'home_primary_cta_label' => ['nullable', 'string', 'max:80'],
            'home_secondary_cta_label' => ['nullable', 'string', 'max:80'],
            'footer_about_text' => ['nullable', 'string', 'max:1000'],
            'footer_location_text' => ['nullable', 'string', 'max:255'],
            'footer_hours_text' => ['nullable', 'string', 'max:255'],
            'footer_privacy_text' => ['nullable', 'string', 'max:1000'],
            'privacy_policy_url' => ['nullable', 'url', 'max:255'],
            'terms_of_use_url' => ['nullable', 'url', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:120'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'login_max_attempts' => ['required', 'integer', 'min:1', 'max:20'],
            'login_lockout_minutes' => ['required', 'integer', 'min:1', 'max:120'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,svg,webp', 'max:1024'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $validated['logo_path'] = null;
        }

        if ($request->boolean('remove_favicon') && $settings->favicon_path) {
            Storage::disk('public')->delete($settings->favicon_path);
            $validated['favicon_path'] = null;
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $validated['logo_path'] = $request->file('logo')->store('system-settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($settings->favicon_path) {
                Storage::disk('public')->delete($settings->favicon_path);
            }

            $validated['favicon_path'] = $request->file('favicon')->store('system-settings', 'public');
        }

        unset($validated['logo'], $validated['favicon'], $validated['remove_logo'], $validated['remove_favicon']);

        $settings->fill($validated)->save();

        return back()->with('status', 'System settings updated successfully.');
    }

    private function authorizeAccess(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}