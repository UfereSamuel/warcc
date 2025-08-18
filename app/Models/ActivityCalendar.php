<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'type',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }

    // Accessors
    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'done' => 'success',
            'ongoing' => 'warning',
            'not_yet_started' => 'info',
            default => 'secondary'
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'meeting' => 'primary',
            'training' => 'info',
            'event' => 'success',
            'holiday' => 'warning',
            'deadline' => 'danger',
            default => 'secondary'
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'meeting' => 'Meeting',
            'training' => 'Training',
            'event' => 'Event',
            'holiday' => 'Holiday',
            'deadline' => 'Deadline',
            default => 'Unknown'
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'done');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeNotStarted($query)
    {
        return $query->where('status', 'not_yet_started');
    }
}
