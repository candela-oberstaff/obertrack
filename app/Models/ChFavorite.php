<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChFavorite extends Model
{
    protected $table = 'ch_favorites';

    protected $fillable = [
        'id',
        'user_id',
        'favorite_id',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
