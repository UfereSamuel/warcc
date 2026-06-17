<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complaint {{ $complaint->complaint_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc3545;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background-color: #f8f9fa;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            border-left: 4px solid #dc3545;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 30%;
            background-color: #f8f9fa;
        }
        .content-box {
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #17a2b8;
            color: white;
            border-radius: 3px;
            font-size: 11px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>WARCC Complaint Report</h1>
        <p>Africa CDC - Workforce Attendance & Resource Coordination Center</p>
        <p><strong>Complaint Number: {{ $complaint->complaint_number }}</strong></p>
    </div>

    <div class="section">
        <div class="section-title">Submission Information</div>
        <table class="info-table">
            <tr>
                <td>Complaint Number</td>
                <td>{{ $complaint->complaint_number }}</td>
            </tr>
            <tr>
                <td>Category</td>
                <td>
                    <span class="badge">{{ $complaint->category_label }}</span>
                </td>
            </tr>
            <tr>
                <td>Submitted Date</td>
                <td>{{ $complaint->created_at->format('F d, Y') }} at {{ $complaint->created_at->format('h:i A') }}</td>
            </tr>
            <tr>
                <td>Submission Type</td>
                <td>{{ $complaint->is_anonymous ? 'Anonymous' : 'Identified' }}</td>
            </tr>
            <tr>
                <td>Review Status</td>
                <td>{{ $complaint->is_reviewed ? 'Reviewed' : 'Unreviewed' }}</td>
            </tr>
        </table>
    </div>

    @if(!$complaint->is_anonymous)
    <div class="section">
        <div class="section-title">Complainant Information</div>
        <table class="info-table">
            <tr>
                <td>Name</td>
                <td>{{ $complaint->complainant_name }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $complaint->complainant_email }}</td>
            </tr>
            @if($complaint->complainant_phone)
            <tr>
                <td>Phone</td>
                <td>{{ $complaint->complainant_phone }}</td>
            </tr>
            @endif
            @if($complaint->staff_id && $complaint->staff)
            <tr>
                <td>Staff Member</td>
                <td>{{ $complaint->staff->full_name }} ({{ $complaint->staff->staff_id }})</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Complaint Description</div>
        <div class="content-box">
{{ $complaint->description }}
        </div>
    </div>

    @if($complaint->suggested_solution)
    <div class="section">
        <div class="section-title">Suggested Solution</div>
        <div class="content-box">
{{ $complaint->suggested_solution }}
        </div>
    </div>
    @endif

    @if($complaint->evidence_path)
    <div class="section">
        <div class="section-title">Evidence Attachment</div>
        <p>Evidence file attached: {{ basename($complaint->evidence_path) }}</p>
    </div>
    @endif

    @if($complaint->admin_notes)
    <div class="section">
        <div class="section-title">Admin Notes (Internal)</div>
        <div class="content-box">
{{ $complaint->admin_notes }}
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This document is confidential and for authorized personnel only.</p>
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        <p>&copy; {{ date('Y') }} Africa CDC - All Rights Reserved</p>
    </div>
</body>
</html>


