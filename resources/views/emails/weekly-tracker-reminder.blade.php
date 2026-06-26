<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weekly Tracker Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #348F41; color: #fff; padding: 16px 20px; border-radius: 6px 6px 0 0;">
        <h1 style="margin: 0; font-size: 20px;">{{ $appName }}</h1>
        <p style="margin: 6px 0 0; opacity: 0.9;">Weekly tracker reminder</p>
    </div>

    <div style="border: 1px solid #e5e5e5; border-top: none; padding: 24px 20px; border-radius: 0 0 6px 6px;">
        <p>Hello <strong>{{ $staff->full_name }}</strong>,</p>

        @if($isSundayReminder)
            <p>
                A new work week is starting. Please submit your <strong>weekly tracker</strong> for
                <strong>{{ $weekLabel }}</strong> and let us know whether you are at the duty station,
                on mission, or on leave.
            </p>
        @else
            <p>
                Our records show your <strong>weekly tracker</strong> for
                <strong>{{ $weekLabel }}</strong> has not been submitted yet.
                Please complete and submit it as soon as possible.
            </p>
        @endif

        <p style="margin: 24px 0;">
            <a href="{{ $trackerUrl }}"
               style="display: inline-block; background: #348F41; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 4px; font-weight: bold; margin-right: 8px;">
                Open Weekly Tracker
            </a>
            <a href="{{ $createUrl }}"
               style="display: inline-block; background: #fff; color: #348F41; text-decoration: none; padding: 11px 23px; border-radius: 4px; font-weight: bold; border: 1px solid #348F41;">
                Create This Week's Tracker
            </a>
        </p>

        <p style="font-size: 14px; color: #666;">
            You can also sign in to your
            <a href="{{ $dashboardUrl }}" style="color: #348F41;">staff dashboard</a>.
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 24px 0;">

        <p style="font-size: 12px; color: #999; margin: 0;">
            This is an automated reminder from {{ $appName }}.
            If you have already submitted your weekly tracker, please disregard this message.
        </p>
    </div>
</body>
</html>
