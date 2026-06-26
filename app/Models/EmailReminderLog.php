<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailReminderLog extends Model
{
    protected $fillable = [
        'staff_id',
        'activity_calendar_id',
        'week_start_date',
        'reminder_type',
        'recipient_email',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'week_start_date' => 'date',
    ];

    public const TYPE_ACTIVITY_REPORT_DUE = 'activity_report_due';

    public const TYPE_WEEKLY_TRACKER_SUNDAY = 'weekly_tracker_sunday';

    public const TYPE_WEEKLY_TRACKER_DAILY = 'weekly_tracker_daily';

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(ActivityCalendar::class, 'activity_calendar_id');
    }
}
