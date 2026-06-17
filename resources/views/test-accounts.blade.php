<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Accounts - RCC Staff Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --africa-green: #348F41;
            --africa-gold: #B4A269;
        }

        .test-header {
            background: linear-gradient(135deg, var(--africa-green), var(--africa-gold));
            color: white;
            padding: 2rem 0;
        }

        .test-card {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .btn-test {
            background-color: var(--africa-green);
            border-color: var(--africa-green);
            color: white;
        }

        .btn-test:hover {
            background-color: var(--africa-gold);
            border-color: var(--africa-gold);
            color: white;
        }

        .warning-banner {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="test-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-vials me-3"></i>
                        RCC Staff Management - Test Accounts
                    </h1>
                    <p class="mb-0 mt-2">Quick access to test the staff dashboard and attendance system</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('home') }}" class="btn btn-light">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <!-- Warning Banner -->
        <div class="alert warning-banner mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div>
                    <strong>Development Mode Only!</strong>
                    <p class="mb-0">These test accounts are for development and testing purposes only. Remove this functionality in production.</p>
                </div>
            </div>
        </div>

        <!-- Test Accounts -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Staff Member - John Doe
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Staff ID:</strong> RCC-002<br>
                            <strong>Email:</strong> john.doe@africacdc.org<br>
                            <strong>Position:</strong> Public Health Officer<br>
                            <strong>Department:</strong> Disease Surveillance
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-info">Regular Staff</span>
                            <span class="badge bg-success">Sample Attendance Data</span>
                        </div>
                        <p class="text-muted small">
                            Test the staff dashboard, attendance tracking, GPS clock in/out, and profile management features.
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('test.login', 'RCC-002') }}" class="btn btn-test w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login as John Doe
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Staff Member - Jane Smith
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Staff ID:</strong> RCC-003<br>
                            <strong>Email:</strong> jane.smith@africacdc.org<br>
                            <strong>Position:</strong> Epidemiologist<br>
                            <strong>Department:</strong> Capacity Building
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-info">Regular Staff</span>
                            <span class="badge bg-success">Sample Attendance Data</span>
                        </div>
                        <p class="text-muted small">
                            Alternative staff account to test different user perspectives and data isolation.
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('test.login', 'RCC-003') }}" class="btn btn-test w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login as Jane Smith
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card test-card h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-user-shield me-2"></i>
                            Admin - Sarah Johnson
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Staff ID:</strong> RCC-004<br>
                            <strong>Email:</strong> sarah.johnson@africacdc.org<br>
                            <strong>Position:</strong> Regional Coordinator<br>
                            <strong>Department:</strong> Administration
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-danger">Administrator</span>
                            <span class="badge bg-warning text-dark">Full Access</span>
                        </div>
                        <p class="text-muted small">
                            Test admin features including staff management, reports, and administrative functions.
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('test.login', 'RCC-004') }}" class="btn btn-test w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login as Sarah Johnson
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features to Test -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="fas fa-list-check me-2"></i>
                    Features Available for Testing
                </h3>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>
                            Staff Features
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Dashboard:</strong> Overview with statistics and quick actions
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>GPS Attendance:</strong> Clock in/out with location tracking
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Attendance History:</strong> View past records with filtering
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Profile Management:</strong> Update personal information
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Interactive Maps:</strong> OpenStreetMap integration
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Export Data:</strong> Download attendance records as CSV
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Technical Features
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Real-time Updates:</strong> Live clock and location detection
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>AJAX Operations:</strong> Seamless user experience
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Mobile Responsive:</strong> Works on all devices
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Professional UI:</strong> AdminLTE with Africa CDC branding
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Security:</strong> Authentication guards and CSRF protection
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Data Validation:</strong> Input sanitization and validation
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Testing Instructions
                        </h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li class="mb-2">Click on any of the "Login as..." buttons above to access the staff dashboard</li>
                            <li class="mb-2">Test the GPS clock in/out functionality (allow location access when prompted)</li>
                            <li class="mb-2">Navigate through different sections: Dashboard, Attendance, History, Profile</li>
                            <li class="mb-2">Try filtering attendance history by month and exporting to CSV</li>
                            <li class="mb-2">Update profile information and upload a profile picture</li>
                            <li class="mb-2">View interactive maps showing attendance locations</li>
                            <li class="mb-2">Test on different devices to verify mobile responsiveness</li>
                        </ol>
                        <div class="alert alert-warning mt-3">
                            <strong>Note:</strong> Sample attendance data has been created for the past week to demonstrate the history and statistics features.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
