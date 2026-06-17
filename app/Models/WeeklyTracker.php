<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyTracker extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'week_start_date',
        'week_end_date',
        'status',
        'remarks',
        'mission_title',
        'mission_type',
        'mission_start_date',
        'mission_end_date',
        'mission_purpose',
        'mission_documents',
        'leave_type_id',
        'leave_start_date',
        'leave_end_date',
        'leave_approval_document',
        'submission_status',
        'submitted_at',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'edit_request_status',
        'edit_requested_at',
        'edit_approved_at',
        'edit_approved_by',
        'edit_rejection_reason',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'week_end_date' => 'date',
        'mission_start_date' => 'date',
        'mission_end_date' => 'date',
        'leave_start_date' => 'date',
        'leave_end_date' => 'date',
        'mission_documents' => 'array',
        'leave_approval_document' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'edit_requested_at' => 'datetime',
        'edit_approved_at' => 'datetime',
    ];

    // Relationships
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function editApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edit_approved_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reviewed_by');
    }

    // Accessors
    public function getWeekRangeAttribute(): string
    {
        return $this->week_start_date->format('M d') . ' - ' . $this->week_end_date->format('M d, Y');
    }

    // Scopes
    public function scopeCurrentWeek($query)
    {
        $startOfWeek = now()->startOfWeek();
        return $query->whereDate('week_start_date', $startOfWeek);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('submission_status', 'submitted');
    }

    public function scopePending($query)
    {
        return $query->where('submission_status', 'pending');
    }
}
