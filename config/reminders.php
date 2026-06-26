<?php

return [

    'enabled' => env('REMINDERS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Activity report due reminders
    |--------------------------------------------------------------------------
    */
    'activity_reports' => [
        'enabled' => env('REMINDER_ACTIVITY_REPORTS_ENABLED', true),
        // Minimum days between repeat reminders for the same staff + activity
        'cooldown_days' => (int) env('REMINDER_ACTIVITY_REPORT_COOLDOWN_DAYS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Weekly tracker submission reminders
    |--------------------------------------------------------------------------
    */
    'weekly_trackers' => [
        'sunday_enabled' => env('REMINDER_WEEKLY_TRACKER_SUNDAY_ENABLED', true),
        'daily_enabled' => env('REMINDER_WEEKLY_TRACKER_DAILY_ENABLED', true),
        'sunday_at' => env('REMINDER_WEEKLY_TRACKER_SUNDAY_AT', '18:00'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Microsoft Graph sender
    |--------------------------------------------------------------------------
    |
    | Must be a licensed mailbox/user in your Azure AD tenant with Mail.Send
    | application permission granted to the app registration.
    |
    */
    'microsoft' => [
        'send_as' => env('MICROSOFT_MAIL_FROM', env('MAIL_FROM_ADDRESS')),
    ],

];
