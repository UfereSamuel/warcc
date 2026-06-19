<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailReminderLog extends Model
{
    protected $fillable = [
        'staff_id',
        'activity_calendar_id',
        'reminder_type',
        'recipient_email',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public const TYPE_ACTIVITY_REPORT_DUE = 'activity_report_due';

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ActivityCalendar::class, 'activity_calendar_id');
    }
}
