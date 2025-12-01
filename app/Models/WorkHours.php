<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHours extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'work_date', 'hours_worked', 'approved', 'approval_comment',];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}