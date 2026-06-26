<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'activity_calendar_id',
        'weekly_tracker_id',
        'title',
        'report_date',
        'summary',
        'outcomes',
        'challenges',
        'recommendations',
        'attachment',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'attachment' => 'array',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ActivityCalendar::class, 'activity_calendar_id');
    }

    public function weeklyTracker(): BelongsTo
    {
        return $this->belongsTo(WeeklyTracker::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reviewed_by');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted' => 'warning',
            'reviewed' => 'success',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'reviewed' => 'Reviewed',
            default => 'Unknown',
        };
    }

    public function scopeByStaff($query, int $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderByDesc('report_date')->orderByDesc('created_at');
    }

    public function isEditableByStaff(): bool
    {
        return $this->status === 'draft';
    }

    public function isMissionReport(): bool
    {
        return $this->weekly_tracker_id !== null;
    }

    public function getReportTypeLabelAttribute(): string
    {
        if ($this->weekly_tracker_id) {
            return 'Mission Report';
        }

        if ($this->activity_calendar_id) {
            return 'Calendar Activity';
        }

        return 'Standalone';
    }

    public function getReportTypeBadgeClassAttribute(): string
    {
        if ($this->weekly_tracker_id) {
            return 'success';
        }

        if ($this->activity_calendar_id) {
            return 'info';
        }

        return 'secondary';
    }

    public function scopeMissionReports($query)
    {
        return $query->whereNotNull('weekly_tracker_id');
    }
}
