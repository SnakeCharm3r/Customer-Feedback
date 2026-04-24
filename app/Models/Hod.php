<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hod extends Model
{
    use HasFactory;

    protected $table = 'hods';

    protected $fillable = [
        'name',
        'department',
        'email',
        'phone',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function escalations(): HasMany
    {
        return $this->hasMany(Escalation::class, 'hod_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
