<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecoveryHour extends Model
{
    protected $fillable = [
        'user_id',
        'recovery_date',
        'hours_recovered',
        'activities',
        'approved',
        'approved_at'
    ];

    protected $casts = [
        'recovery_date' => 'date',
        'hours_recovered' => 'decimal:2',
        'approved_at' => 'datetime'
    ];

    /**
     * Get the user that owns the recovery hours.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
