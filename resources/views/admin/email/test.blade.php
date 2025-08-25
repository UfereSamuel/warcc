@extends('adminlte::page')

@section('title', 'Email Configuration & Testing')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-envelope text-primary mr-2"></i>
                    Email Configuration & Testing
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Email Testing</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Current Configuration -->
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-2"></i>
                    Current Email Configuration
                </h3>
            </div>
            <div class="card-body">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-server"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Mail Driver</span>
                        <span class="info-box-number">
                            {{ strtoupper($currentConfig['mailer']) }}
                            @if($currentConfig['mailer'] === 'log')
                                <small class="badge badge-warning ml-2">Testing Mode</small>
                            @elseif($currentConfig['mailer'] === 'smtp')
                                <small class="badge badge-success ml-2">Production Ready</small>
                            @endif
                        </span>
                    </div>
                </div>

                @if($currentConfig['mailer'] === 'smtp')
                    <table class="table table-sm">
                        <tr>
                            <td><strong>SMTP Host:</strong></td>
                            <td>{{ $currentConfig['host'] ?: 'Not configured' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Port:</strong></td>
                            <td>{{ $currentConfig['port'] ?: 'Not configured' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Username:</strong></td>
                            <td>{{ $currentConfig['username'] ?: 'Not configured' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Encryption:</strong></td>
                            <td>{{ strtoupper($currentConfig['encryption'] ?: 'None') }}</td>
                        </tr>
                        <tr>
                            <td><strong>From Address:</strong></td>
                            <td>{{ $currentConfig['from_address'] ?: 'Not configured' }}</td>
                        </tr>
                        <tr>
                            <td><strong>From Name:</strong></td>
                            <td>{{ $currentConfig['from_name'] ?: 'Not configured' }}</td>
                        </tr>
                    </table>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Current mode: LOG</strong><br>
                        Emails are being logged to files instead of being sent. Configure SMTP settings below to send real emails.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Test Email -->
    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Send Test Email
                </h3>
            </div>
            <form method="POST" action="{{ route('admin.email.test.send') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="test_email">Test Email Address</label>
                        <input type="email" class="form-control @error('test_email') is-invalid @enderror" 
                               id="test_email" name="test_email" 
                               value="{{ old('test_email', 'westernrcc@africacdc.org') }}" 
                               placeholder="Enter email to test">
                        @error('test_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            We'll send a test email to this address to verify the configuration.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="mailer_type">Email Method</label>
                        <select class="form-control @error('mailer_type') is-invalid @enderror" 
                                id="mailer_type" name="mailer_type" required>
                            <option value="laravel" {{ old('mailer_type') === 'laravel' ? 'selected' : '' }}>
                                Laravel Mailer (SMTP/Log)
                            </option>
                            <option value="microsoft-graph" {{ old('mailer_type') === 'microsoft-graph' ? 'selected' : '' }}>
                                Microsoft Graph API (Recommended)
                            </option>
                        </select>
                        @error('mailer_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <strong>Microsoft Graph API</strong> is recommended for Office 365 accounts as it's more secure and reliable.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="test_message">Custom Test Message (Optional)</label>
                        <textarea class="form-control @error('test_message') is-invalid @enderror" 
                                  id="test_message" name="test_message" rows="3"
                                  placeholder="Add a custom message to include in the test email...">{{ old('test_message') }}</textarea>
                        @error('test_message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    @if($currentConfig['mailer'] === 'log')
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Note:</strong> Email is in LOG mode. The test will write to log files instead of sending actual emails.
                        </div>
                    @endif

                    <!-- Microsoft Graph API Status -->
                    <div class="alert alert-success" id="graph-status" style="display: none;">
                        <i class="fas fa-check-circle mr-2"></i>
                        <strong>Microsoft Graph API:</strong> <span id="graph-status-text">Ready to test</span>
                        <button type="button" class="btn btn-sm btn-outline-success ml-2" onclick="testGraphConnection()">
                            <i class="fas fa-plug mr-1"></i>Test Connection
                        </button>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Send Test Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email Configuration Form -->
<div class="row">
    <div class="col-12">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tools mr-2"></i>
                    Configure Email Settings
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.email.configure') }}">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Email Setup Instructions</h5>
                        <p>To configure email notifications, you'll need the following information:</p>
                        <ul class="mb-0">
                            <li><strong>SMTP Host:</strong> Your email provider's SMTP server</li>
                            <li><strong>Port:</strong> Usually 587 (TLS) or 465 (SSL)</li>
                            <li><strong>Username:</strong> Your email address (e.g., westernrcc@africacdc.org)</li>
                            <li><strong>Password:</strong> Your email password or app-specific password</li>
                        </ul>
                    </div>

                    <!-- Common Email Providers Quick Setup -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">Quick Setup for Common Providers:</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <h6 class="card-title">Microsoft 365/Outlook</h6>
                                            <small class="text-muted">
                                                Host: smtp-mail.outlook.com<br>
                                                Port: 587<br>
                                                Encryption: TLS
                                            </small>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                                    onclick="fillOutlookSettings()">Use Settings</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <h6 class="card-title">Gmail/Google Workspace</h6>
                                            <small class="text-muted">
                                                Host: smtp.gmail.com<br>
                                                Port: 587<br>
                                                Encryption: TLS
                                            </small>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                                    onclick="fillGmailSettings()">Use Settings</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body p-3">
                                            <h6 class="card-title">Custom SMTP</h6>
                                            <small class="text-muted">
                                                Use your organization's<br>
                                                SMTP server settings<br>
                                                &nbsp;
                                            </small>
                                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" disabled>Manual Setup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mail_host">SMTP Host <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mail_host') is-invalid @enderror" 
                                       id="mail_host" name="mail_host" 
                                       value="{{ old('mail_host', $currentConfig['host']) }}" 
                                       placeholder="smtp-mail.outlook.com" required>
                                @error('mail_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mail_port">Port <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('mail_port') is-invalid @enderror" 
                                       id="mail_port" name="mail_port" 
                                       value="{{ old('mail_port', $currentConfig['port'] ?: 587) }}" 
                                       placeholder="587" min="1" max="65535" required>
                                @error('mail_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="mail_encryption">Encryption <span class="text-danger">*</span></label>
                                <select class="form-control @error('mail_encryption') is-invalid @enderror" 
                                        id="mail_encryption" name="mail_encryption" required>
                                    <option value="tls" {{ old('mail_encryption', $currentConfig['encryption']) === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('mail_encryption', $currentConfig['encryption']) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                                @error('mail_encryption')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mail_username">Email Username <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('mail_username') is-invalid @enderror" 
                                       id="mail_username" name="mail_username" 
                                       value="{{ old('mail_username', $currentConfig['username'] ?: 'westernrcc@africacdc.org') }}" 
                                       placeholder="westernrcc@africacdc.org" required>
                                @error('mail_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mail_password">Email Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('mail_password') is-invalid @enderror" 
                                       id="mail_password" name="mail_password" 
                                       placeholder="Enter email password" required>
                                @error('mail_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Use an app-specific password for better security.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mail_from_address">From Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('mail_from_address') is-invalid @enderror" 
                                       id="mail_from_address" name="mail_from_address" 
                                       value="{{ old('mail_from_address', $currentConfig['from_address'] ?: 'westernrcc@africacdc.org') }}" 
                                       placeholder="westernrcc@africacdc.org" required>
                                @error('mail_from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    This email address will appear as the sender.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mail_from_name">From Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('mail_from_name') is-invalid @enderror" 
                                       id="mail_from_name" name="mail_from_name" 
                                       value="{{ old('mail_from_name', trim($currentConfig['from_name'], '"') ?: 'WARCC Staff Management System') }}" 
                                       placeholder="WARCC Staff Management System" required>
                                @error('mail_from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    This name will appear as the sender name.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Security Note:</strong> Your email password will be stored in the server's environment file. Make sure your server is secure and consider using app-specific passwords instead of your main email password.
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i>
                        Save Email Configuration
                    </button>
                    <button type="button" class="btn btn-secondary ml-2" onclick="clearForm()">
                        <i class="fas fa-undo mr-1"></i>
                        Reset Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .info-box .info-box-icon {
        width: 70px;
    }
    .card .card-header .card-title {
        font-weight: 600;
    }
    .alert h5 {
        font-weight: 600;
    }
</style>
@stop

@section('js')
<script>
    function fillOutlookSettings() {
        document.getElementById('mail_host').value = 'smtp-mail.outlook.com';
        document.getElementById('mail_port').value = '587';
        document.getElementById('mail_encryption').value = 'tls';
    }

    function fillGmailSettings() {
        document.getElementById('mail_host').value = 'smtp.gmail.com';
        document.getElementById('mail_port').value = '587';
        document.getElementById('mail_encryption').value = 'tls';
    }

    function clearForm() {
        if (confirm('Are you sure you want to reset the form? All unsaved changes will be lost.')) {
            document.querySelector('form').reset();
        }
    }

    // Auto-fill from address when username changes
    document.getElementById('mail_username').addEventListener('input', function() {
        const fromAddressField = document.getElementById('mail_from_address');
        if (!fromAddressField.value || fromAddressField.value === 'westernrcc@africacdc.org') {
            fromAddressField.value = this.value;
        }
    });

    // Show Microsoft Graph status when selected
    document.getElementById('mailer_type').addEventListener('change', function() {
        const graphStatus = document.getElementById('graph-status');
        if (this.value === 'microsoft-graph') {
            graphStatus.style.display = 'block';
        } else {
            graphStatus.style.display = 'none';
        }
    });

    // Test Microsoft Graph connection
    function testGraphConnection() {
        const statusText = document.getElementById('graph-status-text');
        const testBtn = event.target;
        
        statusText.textContent = 'Testing connection...';
        testBtn.disabled = true;
        testBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Testing...';

        fetch('{{ route("admin.email.test.graph") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusText.textContent = 'Connection successful! Ready to send emails.';
                document.getElementById('graph-status').className = 'alert alert-success';
            } else {
                statusText.textContent = 'Connection failed: ' + data.message;
                document.getElementById('graph-status').className = 'alert alert-danger';
            }
        })
        .catch(error => {
            statusText.textContent = 'Connection test failed: ' + error.message;
            document.getElementById('graph-status').className = 'alert alert-danger';
        })
        .finally(() => {
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="fas fa-plug mr-1"></i>Test Connection';
        });
    }
</script>
@stop
