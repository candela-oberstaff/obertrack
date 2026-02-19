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
                    // Companies see their professionals
                    $builder->where('empleador_id', $user->id);
                } elseif ($user->tipo_usuario === 'empleado') { 
                    // Professionals see their company
                    // Also maybe see other professionals of same company? 
                    // User request said: "empresa ... a profesionales y viceversa". 
                    // Implies mainly vertical communication. Let's stick to Company <-> Professional for now.
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
        'phone_number',
        'location',
        'country',
        'city',
        'company_name',
        'related_contact',
        'job_title',
        'password',
        'tipo_usuario',
        'empleador_id',
        'is_manager',
        'is_superadmin',
        'google_id',
        'avatar',
        'google_calendar_token',
        'google_calendar_email',
        'timezone',
        'google_calendar_notifications',
        'google_calendar_notification_minutes',
        'google_forms_token',
        'google_forms_email',
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
            'google_calendar_notifications' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_superadmin;
    }

    public function profesionales(): HasMany
    {
        return $this->hasMany(User::class, 'empleador_id');
    }

    public function empresa(): BelongsTo
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

    public function assignedTasks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }


    public function puedeAsignarTareas(): bool
    {
        return $this->tipo_usuario === 'empleador' || $this->is_manager;
    }

    public function isEmpresaOrSuperAdmin(): bool
    {
        return $this->tipo_usuario === 'empleador' || $this->is_superadmin;
    }

    public function promoverAManager(): void
    {
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $this->id)
            ->update(['is_manager' => \Illuminate\Support\Facades\DB::raw('true')]);
    }

    public function degradarDeManager(): void
    {
        \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $this->id)
            ->update(['is_manager' => \Illuminate\Support\Facades\DB::raw('false')]);
    }

    public function compañerosDeTrabajo()
    {
        if ($this->tipo_usuario === 'empleador') {
            return $this->profesionales;
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
            return $this->profesionales;
        } else {
            return User::where('id', $this->empleador_id)->get();
        }
    }

    public function activeStatus()
    {
        return \Illuminate\Support\Facades\Cache::has('user-is-online-' . $this->id);
    }

    public function getDashboardRoute(): string
    {
        // Explicitly check if is_superadmin is true (handles null, false, and true correctly)
        if ($this->is_superadmin === true) {
            return 'admin.dashboard';
        }

        if ($this->tipo_usuario === 'empleador') {
            return 'empresa.dashboard';
        }

        // Professionals go to the main dashboard which shows dashboard-professional.blade.php
        return 'dashboard';
    }

    public function isGoogleCalendarConnected(): bool
    {
        return !empty($this->google_calendar_token);
    }

    public function isGoogleFormsConnected(): bool
    {
        return !empty($this->google_forms_token);
    }
}
