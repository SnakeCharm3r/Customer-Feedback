<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Department extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'categories',
        'hod_id',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'categories' => 'array',
    ];

    const CATEGORIES = [
        'opd'     => 'Outpatient (OPD)',
        'ipd'     => 'Inpatient (IPD)',
        'theatre' => 'Theatre (OTD)',
        'other'   => 'Other',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Department $dept) {
            if (empty($dept->slug)) {
                $dept->slug = static::uniqueSlug($dept->name);
            }
        });
    }

    private static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hod(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Hod::class, 'hod_id');
    }

    public function getCategoriesLabel(): string
    {
        $cats = $this->categories ?? [];
        if (empty($cats)) {
            return '—';
        }
        return implode(', ', array_map(
            fn($k) => self::CATEGORIES[$k] ?? ucfirst($k),
            $cats
        ));
    }
}
