<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'start_date',
        'end_date',
        'priority',
        'completed',
        'status', // Added status
    ];

    protected $appends = ['description_html'];

    protected $casts = [
        'completed' => 'boolean',
        'start_date' => 'datetime:Y-m-d',
        'end_date' => 'datetime:Y-m-d',
    ];

    public function getDescriptionHtmlAttribute(): string
    {
        if (!$this->description) {
            return '';
        }

        $desc = trim($this->description);
        $lowerDesc = strtolower($desc);

        // If it starts with a doctype, html tag, or contains a style tag,
        // we assume it's a full HTML snippet/document and return it as-is.
        // This avoids markdown parser stripping or messing with head/style tags.
        if (str_contains($lowerDesc, '<!doctype html') || 
            str_contains($lowerDesc, '<html') || 
            str_contains($lowerDesc, '<style')) {
            return $this->description;
        }
        
        // Use markdown with options that allow links and HTML input
        return Str::markdown($this->description, [
            'html_input' => 'allow',
            'allow_unsafe_links' => true,
        ]);
    }

    // Status Constants
    const STATUS_TODO = 'por_hacer';
    const STATUS_IN_PROGRESS = 'en_proceso';
    const STATUS_COMPLETED = 'finalizado';

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    public function isTeamTask(): bool
    {
        return $this->assignees()->count() > 1;
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // visibleTo is replaced by assignees, keeping method for backward compatibility if needed, but returning first assignee
    public function visibleTo(): BelongsTo
    {
         // This is a bit tricky with ManyToMany, but for legacy support we might need to adjust logic elsewhere
         // For now, removing it is safer to force updating usages.
         return $this->belongsTo(User::class, 'deleted_column_placeholder'); // This will fail if used, which is good for catching bugs
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
