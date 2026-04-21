<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_id',
        'content',
        'sent_by',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class, 'feedback_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
