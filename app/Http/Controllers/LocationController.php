<?php

namespace App\Http\Controllers;

use App\Models\FeedbackLocation;
use App\Models\LocationServiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'key'        => ['required', 'string', 'max:80', 'alpha_dash', 'unique:feedback_locations,key'],
            'label'      => ['required', 'string', 'max:160'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        FeedbackLocation::create([
            'key'        => $validated['key'],
            'label'      => $validated['label'],
            'is_active'  => true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('settings.edit', ['#section-locations'])
            ->with('location_status', 'Location "' . $validated['label'] . '" added successfully.');
    }

    public function update(Request $request, FeedbackLocation $location): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'key'        => ['required', 'string', 'max:80', 'alpha_dash', 'unique:feedback_locations,key,' . $location->id],
            'label'      => ['required', 'string', 'max:160'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $location->update([
            'key'        => $validated['key'],
            'label'      => $validated['label'],
            'is_active'  => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? $location->sort_order,
        ]);

        return redirect()->route('settings.edit', ['#section-locations'])
            ->with('location_status', 'Location "' . $location->label . '" updated successfully.');
    }

    public function destroy(FeedbackLocation $location): RedirectResponse
    {
        $this->authorizeAdmin();

        $label = $location->label;
        $location->delete();

        return redirect()->route('settings.edit', ['#section-locations'])
            ->with('location_status', 'Location "' . $label . '" deleted.');
    }

    public function toggleActive(FeedbackLocation $location): RedirectResponse
    {
        $this->authorizeAdmin();

        $location->update(['is_active' => ! $location->is_active]);

        $state = $location->is_active ? 'enabled' : 'disabled';

        return redirect()->route('settings.edit', ['#section-locations'])
            ->with('location_status', 'Location "' . $location->label . '" ' . $state . '.');
    }

    // ── Service Items ──────────────────────────────────────────────

    public function storeItem(Request $request, FeedbackLocation $location): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'key'         => ['required', 'string', 'max:80', 'alpha_dash', "unique:location_service_items,key,NULL,id,location_id,{$location->id}"],
            'label'       => ['required', 'string', 'max:160'],
            'group_label' => ['nullable', 'string', 'max:120'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $location->serviceItems()->create([
            'key'         => $validated['key'],
            'label'       => $validated['label'],
            'group_label' => $validated['group_label'] ?? null,
            'is_active'   => true,
            'sort_order'  => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('settings.edit', ['#section-locations'])
            ->with('location_status', 'Service item "' . $validated['label'] . '" added to ' . $location->label . '.');
    }

    public function updateItem(Request $request, FeedbackLocation $location, LocationServiceItem $item): RedirectResponse
    {
        $this->authorizeAdmin();
        abort_unless($item->location_id === $location->id, 404);

        $validated = $request->validate([
            'key'         => ['required', 'string', 'max:80', 'alpha_dash', "unique:location_service_items,key,{$item->id},id,location_id,{$location->id}"],
            'label'       => ['required', 'string', 'max:160'],
            'group_label' => ['nullable', 'string', 'max:120'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $item->update([
            'key'         => $validated['key'],
            'label'       => $validated['label'],
            'group_label' => $validated['group_label'] ?? $item->group_label,
            'is_active'   => $request->boolean('is_active'),
            'sort_order'  => $validated['sort_order'] ?? $item->sort_order,
        ]);

        return redirect()->route('settings.edit', ['#section-locations'])
            ->with('location_status', 'Service item "' . $item->label . '" updated.');
    }

    public function destroyItem(FeedbackLocation $location, LocationServiceItem $item): RedirectResponse
    {
        $this->authorizeAdmin();
        abort_unless($item->location_id === $location->id, 404);

        $label = $item->label;
        $item->delete();

        return redirect()->route('settings.edit', ['#section-locations'])
            ->with('location_status', 'Service item "' . $label . '" removed.');
    }
}
