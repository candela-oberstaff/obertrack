<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MassEmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'segment',
        'subject',
        'recipient_count',
        'target_user_id',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
