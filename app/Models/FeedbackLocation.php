<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedbackLocation extends Model
{
    protected $table = 'feedback_locations';

    protected $fillable = ['key', 'label', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer'];

    public function serviceItems(): HasMany
    {
        return $this->hasMany(LocationServiceItem::class, 'location_id');
    }

    public function hasCustomServices(): bool
    {
        return $this->serviceItems()->exists();
    }

    public function groupedServiceItems(bool $activeOnly = true): array
    {
        $items = $this->serviceItems()
            ->when($activeOnly, fn ($q) => $q->active())
            ->ordered()
            ->get();

        $groups = [];
        foreach ($items as $item) {
            $group = $item->group_label ?? 'General';
            $groups[$group][] = $item;
        }
        return $groups;
    }

    public static function orderedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('label')->get();
    }

    public static function allOrdered(): \Illuminate\Database\Eloquent\Collection
    {
        return static::orderBy('sort_order')->orderBy('label')->get();
    }

    public static function toSelectArray(bool $activeOnly = true): array
    {
        $query = $activeOnly ? static::orderedActive() : static::allOrdered();
        return $query->pluck('label', 'key')->toArray();
    }

    public static function withServiceItemsMap(): array
    {
        $locations = static::with(['serviceItems' => function ($q) {
            $q->active()->ordered();
        }])->get();

        $map = [];
        foreach ($locations as $loc) {
            if ($loc->serviceItems->isNotEmpty()) {
                $groups = [];
                foreach ($loc->serviceItems as $item) {
                    $group = $item->group_label ?? 'General';
                    $groups[$group][] = ['key' => $item->key, 'label' => $item->label];
                }
                $map[$loc->key] = $groups;
            }
        }
        return $map;
    }
}
