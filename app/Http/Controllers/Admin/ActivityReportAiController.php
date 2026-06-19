<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityReport;
use App\Services\ActivityReportAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityReportAiController extends Controller
{
    public function __construct(
        private readonly ActivityReportAiService $aiService
    ) {}

    public function status(): JsonResponse
    {
        return response()->json([
            'configured' => $this->aiService->isConfigured(),
            'model' => config('ai.model'),
        ]);
    }

    public function summarize(ActivityReport $activityReport): JsonResponse
    {
        if ($activityReport->status === 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Draft reports cannot be summarized until submitted by staff.',
            ], 422);
        }

        try {
            $result = $this->aiService->summarize($activityReport);

            return response()->json([
                'success' => true,
                'action' => 'summarize',
                'title' => 'AI Summary: ' . $activityReport->title,
                ...$result,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function merge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'report_ids' => 'required|array|min:2',
            'report_ids.*' => 'integer|exists:activity_reports,id',
        ]);

        $reports = ActivityReport::with(['staff', 'activity'])
            ->whereIn('id', $validated['report_ids'])
            ->where('status', '!=', 'draft')
            ->get();

        if ($reports->count() < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Select at least two submitted or reviewed reports to merge.',
            ], 422);
        }

        if ($reports->count() !== count($validated['report_ids'])) {
            return response()->json([
                'success' => false,
                'message' => 'Draft reports were excluded. Only submitted or reviewed reports can be merged.',
            ], 422);
        }

        try {
            $result = $this->aiService->mergeReports($reports);

            return response()->json([
                'success' => true,
                'action' => 'merge',
                'title' => 'Merged Briefing (' . $reports->count() . ' reports)',
                ...$result,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    private function errorResponse(\Throwable $e): JsonResponse
    {
        $status = str_contains($e->getMessage(), 'not configured') ? 503 : 502;

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], $status);
    }
}
