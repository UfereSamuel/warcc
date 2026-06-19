<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Report Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #348F41; color: #fff; padding: 16px 20px; border-radius: 6px 6px 0 0;">
        <h1 style="margin: 0; font-size: 20px;">{{ $appName }}</h1>
        <p style="margin: 6px 0 0; opacity: 0.9;">Post-activity report reminder</p>
    </div>

    <div style="border: 1px solid #e5e5e5; border-top: none; padding: 24px 20px; border-radius: 0 0 6px 6px;">
        <p>Hello <strong>{{ $staff->full_name }}</strong>,</p>

        <p>
            The following activity has been marked <strong>completed</strong> and requires your
            post-activity report (mission / training / workshop summary):
        </p>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold; width: 120px;">Activity</td>
                <td style="padding: 8px 0;">{{ $activity->title }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Type</td>
                <td style="padding: 8px 0;">{{ $activity->type_label }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Dates</td>
                <td style="padding: 8px 0;">
                    {{ $activity->start_date->format('M d, Y') }}
                    @if($activity->start_date->ne($activity->end_date))
                        – {{ $activity->end_date->format('M d, Y') }}
                    @endif
                </td>
            </tr>
            @if($activity->location)
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Location</td>
                    <td style="padding: 8px 0;">{{ $activity->location }}</td>
                </tr>
            @endif
        </table>

        <p style="margin: 24px 0;">
            <a href="{{ $submitUrl }}"
               style="display: inline-block; background: #348F41; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 4px; font-weight: bold;">
                Submit Activity Report
            </a>
        </p>

        <p style="font-size: 14px; color: #666;">
            You can also sign in to your
            <a href="{{ $dashboardUrl }}" style="color: #348F41;">staff dashboard</a>
            to view all pending reports.
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 24px 0;">

        <p style="font-size: 12px; color: #999; margin: 0;">
            This is an automated reminder from {{ $appName }}.
            If you have already submitted your report, please disregard this message.
        </p>
    </div>
</body>
</html>
