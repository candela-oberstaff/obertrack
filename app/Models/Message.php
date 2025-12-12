<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'message',
        'attachment_path',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function scopeBetween($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('from_user_id', $user1Id)->where('to_user_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('from_user_id', $user2Id)->where('to_user_id', $user1Id);
        })->orderBy('created_at', 'asc');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }
}
