<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHours extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'work_date', 'hours_worked', 'user_comment', 'approved', 'approval_comment', 'absence_reason'];

    protected $casts = [
        'approved' => 'boolean',
        'work_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}