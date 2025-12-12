<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function booted()
    {
        static::addGlobalScope('chat_filter', function ($builder) {
            // Apply only for Chatify routes to avoid breaking other app parts
            if (request()->is('chatify') || request()->is('chatify/*')) {
                if (!auth()->check()) return;
                
                $user = auth()->user();
                
                if ($user->tipo_usuario === 'empleador') {
                    // Employers see their employees
                    $builder->where('empleador_id', $user->id);
                } elseif ($user->tipo_usuario === 'empleado') { 
                    // Employees see their employer
                    // Also maybe see other employees of same employer? 
                    // User request said: "empresa ... a profesionales y viceversa". 
                    // Implies mainly vertical communication. Let's stick to Employer <-> Employee for now.
                    $builder->where('id', $user->empleador_id);
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // En el modelo User
    protected $fillable = [
        'name',
        'email',
        'job_title',
        'password',
        'tipo_usuario',
        'empleador_id',
        'is_manager',
        'is_superadmin',
        'google_id',
        'avatar',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_manager' => 'boolean',
            'is_superadmin' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_superadmin;
    }

    public function empleados(): HasMany
    {
        return $this->hasMany(User::class, 'empleador_id');
    }

    public function empleador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'empleador_id');
    }

    public function workHours(): HasMany
    {
        return $this->hasMany(WorkHours::class);
    }

    public function signature()
    {
        return $this->hasOne(UserSignature::class);
    }


    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'visible_para', 'id');
    }


    public function puedeAsignarTareas(): bool
    {
        return $this->tipo_usuario === 'empleador' || $this->is_manager;
    }

    public function promoverAManager(): void
    {
        if ($this->tipo_usuario === 'empleado') {
            $this->is_manager = true;
            $this->save();
        }
    }

    public function degradarDeManager(): void
    {
        if ($this->tipo_usuario === 'empleado' && $this->is_manager) {
            $this->is_manager = false;
            $this->save();
        }
    }

    public function compañerosDeTrabajo()
    {
        if ($this->tipo_usuario === 'empleador') {
            return $this->empleados;
        } else {
            return User::where('empleador_id', $this->empleador_id)
                       ->where('id', '!=', $this->id)
                       ->get();
        }
    }


    // Chat Relationships
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'from_user_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'to_user_id');
    }

    public function getChatContacts()
    {
        if ($this->tipo_usuario === 'empleador') {
            return $this->empleados;
        } else {
            return User::where('id', $this->empleador_id)->get();
        }
    }

    public function activeStatus()
    {
        return \Illuminate\Support\Facades\Cache::has('user-is-online-' . $this->id);
    }
}
