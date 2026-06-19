<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Report' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid #007bff; }
        .header h1 { color: #007bff; margin: 0 0 6px; font-size: 20px; }
        .header p { margin: 2px 0; color: #666; }
        .section { margin-bottom: 18px; }
        .section-title { background: #f8f9fa; padding: 6px 10px; font-weight: bold; border-left: 4px solid #007bff; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        .text-center { text-align: center; }
        .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #ddd; text-align: center; font-size: 9px; color: #999; }
        .stat-grid { width: 100%; margin-bottom: 12px; }
        .stat-grid td { width: 25%; text-align: center; border: 1px solid #ddd; padding: 10px; }
        .stat-value { font-size: 16px; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <div class="header">
        <h1>@yield('title', 'WARCC Report')</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
        <p>Generated: {{ $generatedAt }}</p>
    </div>

    @yield('content')

    <div class="footer">
        Africa CDC Western RCC — Confidential Internal Report
    </div>
</body>
</html>
