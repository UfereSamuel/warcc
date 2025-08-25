<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - WARCC System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px 20px;
        }
        .success-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #28a745;
        }
        .info-item strong {
            color: #495057;
            display: block;
            margin-bottom: 5px;
        }
        .info-item span {
            color: #6c757d;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 0;
            color: #6c757d;
            font-size: 12px;
        }
        .test-details {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .test-details h3 {
            color: #1976d2;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid #c3e6cb;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">📧</div>
            <h1>Email System Test</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">{{ $testData['system_name'] }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="status-indicator">
                <span style="font-size: 16px;">✅</span>
                <strong>Email Configuration Successful!</strong>
            </div>

            <p>Congratulations! Your email system is working correctly. This test email confirms that:</p>

            <ul style="color: #495057; line-height: 1.8;">
                <li>✅ SMTP connection is established</li>
                <li>✅ Authentication is working</li>
                <li>✅ Email templates are rendering</li>
                <li>✅ Emails can be sent successfully</li>
            </ul>

            <div class="test-details">
                <h3>📊 Test Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Test Time</strong>
                        <span>{{ $testData['test_time'] }}</span>
                    </div>
                    <div class="info-item">
                        <strong>System</strong>
                        <span>{{ $testData['system_name'] }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Server</strong>
                        <span>{{ $testData['server_info'] }}</span>
                    </div>
                    <div class="info-item">
                        <strong>Laravel Version</strong>
                        <span>{{ app()->version() }}</span>
                    </div>
                </div>
            </div>

            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 15px; margin: 20px 0;">
                <h4 style="color: #856404; margin: 0 0 10px 0;">🔔 Next Steps:</h4>
                <ul style="color: #856404; margin: 0; padding-left: 20px;">
                    <li>Your email system is ready for notifications</li>
                    <li>Staff will receive important system updates</li>
                    <li>Admin notifications are now active</li>
                    <li>Weekly tracker and attendance alerts will work</li>
                </ul>
            </div>

            <p style="margin-bottom: 0;">If you received this email, your WARCC Staff Management System email configuration is working perfectly!</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ $testData['system_name'] }}</strong></p>
            <p>This is an automated test email. Please do not reply to this message.</p>
            <p style="margin-top: 10px; color: #adb5bd;">
                Generated on {{ $testData['test_time'] }} | 
                <a href="https://cbp.africacdc.org/warcc" style="color: #28a745;">Visit System</a>
            </p>
        </div>
    </div>
</body>
</html>
