<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'visible_para',
        'start_date',
        'end_date',
        'priority',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];


    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visibleTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visible_para');
    }

    public function readBy()
    {
        return $this->belongsToMany(User::class, 'task_reads')
            ->withTimestamps()
            ->withPivot('read_at');
    }

    public function isReadBy($userId)
    {
        return $this->readBy()->where('user_id', $userId)->exists();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class);
    }
}
