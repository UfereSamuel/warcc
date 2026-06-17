<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complaints Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #dc3545;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .complaint {
            margin-bottom: 30px;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fafafa;
        }
        .complaint-header {
            background-color: #dc3545;
            color: white;
            padding: 8px 12px;
            margin: -15px -15px 15px -15px;
            font-weight: bold;
            font-size: 13px;
        }
        .complaint-meta {
            margin-bottom: 10px;
            font-size: 10px;
            color: #666;
        }
        .complaint-meta span {
            display: inline-block;
            margin-right: 15px;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            background-color: #17a2b8;
            color: white;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-secondary {
            background-color: #6c757d;
        }
        .content {
            padding: 10px;
            background-color: white;
            border: 1px solid #e0e0e0;
            margin-bottom: 10px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .section-label {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>WARCC Complaints Report</h1>
        <p>Africa CDC - Workforce Attendance & Resource Coordination Center</p>
        <p><strong>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</strong></p>
    </div>

    <div class="summary">
        <strong>Report Summary:</strong><br>
        Total Complaints: <strong>{{ $complaints->count() }}</strong><br>
        Reviewed: <strong>{{ $complaints->where('is_reviewed', true)->count() }}</strong><br>
        Unreviewed: <strong>{{ $complaints->where('is_reviewed', false)->count() }}</strong><br>
        Anonymous: <strong>{{ $complaints->where('is_anonymous', true)->count() }}</strong>
    </div>

    @foreach($complaints as $index => $complaint)
        <div class="complaint">
            <div class="complaint-header">
                {{ $complaint->complaint_number }}
                @if(!$complaint->is_reviewed)
                    <span style="float: right; font-size: 11px;">⚠ UNREVIEWED</span>
                @endif
            </div>

            <div class="complaint-meta">
                <span>
                    <strong>Category:</strong> 
                    <span class="badge">{{ $complaint->category_label }}</span>
                </span>
                <span>
                    <strong>Date:</strong> {{ $complaint->created_at->format('M d, Y') }}
                </span>
                <span>
                    <strong>Type:</strong> 
                    @if($complaint->is_anonymous)
                        <span class="badge badge-secondary">Anonymous</span>
                    @else
                        <span class="badge">Identified</span>
                    @endif
                </span>
                <span>
                    <strong>Status:</strong> 
                    @if($complaint->is_reviewed)
                        <span class="badge badge-success">Reviewed</span>
                    @else
                        <span class="badge badge-warning">Unreviewed</span>
                    @endif
                </span>
            </div>

            @if(!$complaint->is_anonymous)
            <div style="background-color: #e9ecef; padding: 8px; margin-bottom: 10px; font-size: 10px;">
                <strong>Complainant:</strong> {{ $complaint->complainant_name }} 
                ({{ $complaint->complainant_email }})
                @if($complaint->staff_id && $complaint->staff)
                    | Staff ID: {{ $complaint->staff->staff_id }}
                @endif
            </div>
            @endif

            <div class="section-label">Complaint Description:</div>
            <div class="content">{{ $complaint->description }}</div>

            @if($complaint->suggested_solution)
            <div class="section-label">Suggested Solution:</div>
            <div class="content">{{ $complaint->suggested_solution }}</div>
            @endif

            @if($complaint->evidence_path)
            <div class="section-label">
                Evidence: {{ basename($complaint->evidence_path) }}
            </div>
            @endif

            @if($complaint->admin_notes)
            <div class="section-label">Admin Notes:</div>
            <div class="content" style="background-color: #fff3cd;">{{ $complaint->admin_notes }}</div>
            @endif
        </div>

        @if($index < $complaints->count() - 1 && ($index + 1) % 3 == 0)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="footer">
        <p>This document contains {{ $complaints->count() }} complaint(s) and is confidential.</p>
        <p>For authorized personnel only.</p>
        <p>&copy; {{ date('Y') }} Africa CDC - All Rights Reserved</p>
    </div>
</body>
</html>


