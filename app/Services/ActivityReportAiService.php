<?php

namespace App\Services;

use App\Models\ActivityReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ActivityReportAiService
{
    public function isConfigured(): bool
    {
        return (bool) config('ai.enabled') && !empty(config('ai.api_key'));
    }

    /**
     * @return array{content: string, model: string, report_count: int}
     */
    public function summarize(ActivityReport $report): array
    {
        $report->loadMissing(['staff', 'activity']);

        $systemPrompt = <<<'PROMPT'
You are an assistant for Africa CDC regional staff administrators. Summarize activity reports clearly and professionally for leadership review. Use markdown headings and bullet points. Be factual—only use information from the report provided.
PROMPT;

        $userPrompt = "Summarize the following activity report for an admin reviewer.\n\n"
            . "Include:\n"
            . "1. **Executive Summary** (2-3 sentences)\n"
            . "2. **Key Outcomes**\n"
            . "3. **Challenges** (if any)\n"
            . "4. **Recommendations** (if any)\n"
            . "5. **Review Notes** — anything the admin should follow up on\n\n"
            . $this->formatReport($report);

        $content = $this->chat($systemPrompt, $userPrompt);

        return [
            'content' => $content,
            'model' => config('ai.model'),
            'report_count' => 1,
        ];
    }

    /**
     * @param  Collection<int, ActivityReport>  $reports
     * @return array{content: string, model: string, report_count: int}
     */
    public function mergeReports(Collection $reports): array
    {
        if ($reports->count() < 2) {
            throw new RuntimeException('Select at least two reports to merge.');
        }

        $max = (int) config('ai.max_reports_per_merge', 10);
        if ($reports->count() > $max) {
            throw new RuntimeException("You can merge at most {$max} reports at once.");
        }

        $reports->loadMissing(['staff', 'activity']);

        $systemPrompt = <<<'PROMPT'
You are an assistant for Africa CDC regional staff administrators. Merge multiple activity reports into one coherent briefing. Identify shared themes, consolidated outcomes, overlapping challenges, and unified recommendations. Note any gaps or contradictions between reports. Use markdown headings and bullet points. Be factual—only use information from the reports provided.
PROMPT;

        $blocks = $reports->values()->map(function (ActivityReport $report, int $index) {
            return '--- REPORT ' . ($index + 1) . " ---\n" . $this->formatReport($report);
        })->implode("\n\n");

        $userPrompt = "Merge and synthesize the following {$reports->count()} activity reports into a single admin briefing.\n\n"
            . "Include:\n"
            . "1. **Consolidated Executive Summary**\n"
            . "2. **Activities Covered** (brief list)\n"
            . "3. **Combined Key Outcomes**\n"
            . "4. **Shared & Distinct Challenges**\n"
            . "5. **Consolidated Recommendations**\n"
            . "6. **Admin Action Items**\n\n"
            . $blocks;

        $content = $this->chat($systemPrompt, $userPrompt);

        return [
            'content' => $content,
            'model' => config('ai.model'),
            'report_count' => $reports->count(),
        ];
    }

    private function formatReport(ActivityReport $report): string
    {
        $lines = [
            'Title: ' . $report->title,
            'Staff: ' . ($report->staff?->full_name ?? 'Unknown') . ' (' . ($report->staff?->staff_id ?? 'N/A') . ')',
            'Report Date: ' . $report->report_date->format('Y-m-d'),
            'Status: ' . $report->status_label,
            'Calendar Activity: ' . ($report->activity?->title ?? 'Standalone (not linked)'),
        ];

        if ($report->activity) {
            $lines[] = 'Activity Dates: '
                . $report->activity->start_date->format('Y-m-d')
                . ' to '
                . $report->activity->end_date->format('Y-m-d');
            if ($report->activity->location) {
                $lines[] = 'Activity Location: ' . $report->activity->location;
            }
        }

        $lines[] = '';
        $lines[] = 'Summary: ' . $report->summary;

        if ($report->outcomes) {
            $lines[] = 'Outcomes: ' . $report->outcomes;
        }
        if ($report->challenges) {
            $lines[] = 'Challenges: ' . $report->challenges;
        }
        if ($report->recommendations) {
            $lines[] = 'Recommendations: ' . $report->recommendations;
        }

        return implode("\n", $lines);
    }

    private function chat(string $systemPrompt, string $userPrompt): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException(
                'AI is not configured. Set AI_API_KEY in your .env file (see .env.example).'
            );
        }

        $response = Http::withToken(config('ai.api_key'))
            ->timeout((int) config('ai.timeout', 120))
            ->acceptJson()
            ->post(config('ai.base_url') . '/chat/completions', [
                'model' => config('ai.model'),
                'temperature' => 0.3,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

        if ($response->failed()) {
            $message = $response->json('error.message')
                ?? $response->json('message')
                ?? 'AI provider returned an error (HTTP ' . $response->status() . ').';

            throw new RuntimeException($message);
        }

        $content = $response->json('choices.0.message.content');

        if (empty($content)) {
            throw new RuntimeException('AI provider returned an empty response.');
        }

        return trim($content);
    }
}
