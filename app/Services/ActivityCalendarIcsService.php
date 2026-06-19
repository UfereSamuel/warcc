<?php

namespace App\Services;

use App\Models\ActivityCalendar;
use Illuminate\Support\Collection;

class ActivityCalendarIcsService
{
    public function generate(Collection $activities): string
    {
        $calendarName = config('calendar.feed_name', 'WARCC RCC Activities');
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?: 'warcc.local';

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Africa CDC WARCC//Activity Calendar//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'NAME:'.$this->escape($calendarName),
            'X-WR-CALNAME:'.$this->escape($calendarName),
            'REFRESH-INTERVAL;VALUE=DURATION:PT1H',
            'X-PUBLISHED-TTL:PT1H',
        ];

        foreach ($activities as $activity) {
            $lines = array_merge($lines, $this->buildEventLines($activity, $domain));
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines)."\r\n";
    }

    /**
     * @return list<string>
     */
    private function buildEventLines(ActivityCalendar $activity, string $domain): array
    {
        $description = collect([
            $activity->description,
            'Type: '.$activity->type_label,
            'Status: '.ucfirst(str_replace('_', ' ', $activity->status)),
        ])->filter()->implode("\n");

        $endDate = $activity->end_date->copy()->addDay();

        return [
            'BEGIN:VEVENT',
            'UID:warcc-activity-'.$activity->id.'@'.$domain,
            'DTSTAMP:'.$this->formatUtc(now()),
            'DTSTART;VALUE=DATE:'.$activity->start_date->format('Ymd'),
            'DTEND;VALUE=DATE:'.$endDate->format('Ymd'),
            'SUMMARY:'.$this->escape($activity->title),
            'DESCRIPTION:'.$this->escape($description),
            'LOCATION:'.$this->escape($activity->location ?? ''),
            'STATUS:CONFIRMED',
            'CATEGORIES:'.$this->escape($activity->type_label),
            'LAST-MODIFIED:'.$this->formatUtc($activity->updated_at ?? $activity->created_at ?? now()),
            'END:VEVENT',
        ];
    }

    private function formatUtc(\DateTimeInterface $date): string
    {
        return $date->setTimezone(new \DateTimeZone('UTC'))->format('Ymd\THis\Z');
    }

    private function escape(string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\n", "\r"],
            ['\\\\', '\\;', '\\,', '\\n', ''],
            $value
        );
    }
}
