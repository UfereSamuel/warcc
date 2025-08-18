<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityRequest extends Model
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
        'justification',
        'expected_participants',
        'estimated_budget',
        'admin_notes',
        'rejection_reason',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'approved_activity_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'estimated_budget' => 'decimal:2',
    ];

    // Relationships
    public function requester(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reviewed_by');
    }

    public function approvedActivity(): BelongsTo
    {
        return $this->belongsTo(ActivityCalendar::class, 'approved_activity_id');
    }

    // Accessors
    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'pending' => 'warning',
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

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    public function getFormattedBudgetAttribute(): string
    {
        if (!$this->estimated_budget) {
            return 'Not specified';
        }
        return 'GHS ' . number_format($this->estimated_budget, 2);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRecentFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByRequester($query, $staffId)
    {
        return $query->where('requested_by', $staffId);
    }
}
