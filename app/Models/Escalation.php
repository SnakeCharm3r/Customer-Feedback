<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Escalation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'feedback_id',
        'hod_id',
        'escalated_by',
        'token',
        'message',
        'status',
        'hod_response',
        'hod_name',
        'responded_at',
        'escalated_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'escalated_at' => 'datetime',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    public function hod(): BelongsTo
    {
        return $this->belongsTo(Hod::class);
    }

    public function escalatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'ESC-' . now()->format('Y') . '-' . strtoupper(Str::random(6));
        } while (static::where('reference', $ref)->exists());
        return $ref;
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isResponded(): bool
    {
        return $this->status === 'responded';
    }

    public function elapsedHours(): ?float
    {
        $end = $this->responded_at ?? now();
        return round($this->escalated_at->diffInMinutes($end) / 60, 1);
    }
}
