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

    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivityReport::class, 'activity_calendar_id');
    }

    public function weeklyTrackers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WeeklyTracker::class, 'activity_calendar_id');
    }

    public function activityRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ActivityRequest::class, 'approved_activity_id');
    }

    public function requiresReport(): bool
    {
        return in_array($this->type, ['training', 'event', 'mission', 'workshop'], true);
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
            'mission' => 'purple',
            'workshop' => 'teal',
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
            'mission' => 'Mission',
            'workshop' => 'Workshop',
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
