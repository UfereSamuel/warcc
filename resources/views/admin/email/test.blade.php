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

                <div class="info-box mb-3 {{ $graphConfig['configured'] ? 'bg-success' : 'bg-warning' }}">
                    <span class="info-box-icon {{ $graphConfig['configured'] ? 'bg-success' : 'bg-warning' }}">
                        <i class="fab fa-microsoft"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Microsoft Graph</span>
                        <span class="info-box-number">
                            @if($graphConfig['configured'])
                                Configured
                                <small class="badge badge-light ml-2">SSO + Email</small>
                            @else
                                Not configured
                            @endif
                        </span>
                        @if($graphConfig['configured'])
                            <small class="text-white-50 d-block mt-1">Send as: {{ $graphConfig['mail_from'] }}</small>
                        @endif
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
                        <strong>SMTP not configured</strong> — mail driver is <code>{{ strtoupper($currentConfig['mailer']) }}</code>.
                        Configure Microsoft Graph above (recommended) or SMTP below.
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

<!-- Microsoft Graph Configuration -->
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fab fa-microsoft mr-2"></i>
                    Microsoft Graph (SSO &amp; Email)
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="testGraphConnection()">
                        <i class="fas fa-plug mr-1"></i>Test Connection
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.email.configure.graph') }}">
                @csrf
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Azure app registration</h5>
                        <p class="mb-1">Create an app in Azure Portal → App registrations. Grant these permissions (with admin consent):</p>
                        <ul class="mb-0 pl-3">
                            <li><strong>Application:</strong> <code>Mail.Send</code> (for reminders &amp; notifications)</li>
                            <li><strong>Delegated:</strong> <code>openid</code>, <code>profile</code>, <code>email</code>, <code>User.Read</code> (for staff SSO)</li>
                        </ul>
                    </div>

                    <div class="alert {{ $graphConfig['configured'] ? 'alert-success' : 'alert-warning' }}" id="graph-config-status">
                        <i class="fas fa-{{ $graphConfig['configured'] ? 'check-circle' : 'exclamation-triangle' }} mr-2"></i>
                        <span id="graph-config-status-text">
                            @if($graphConfig['configured'])
                                Microsoft Graph credentials are saved.
                            @else
                                Enter your Azure app credentials below to enable SSO and email reminders.
                            @endif
                        </span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="microsoft_client_id">Application (Client) ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('microsoft_client_id') is-invalid @enderror"
                                       id="microsoft_client_id" name="microsoft_client_id"
                                       value="{{ old('microsoft_client_id', $graphConfig['client_id']) }}"
                                       placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" required>
                                @error('microsoft_client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="microsoft_tenant_id">Directory (Tenant) ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('microsoft_tenant_id') is-invalid @enderror"
                                       id="microsoft_tenant_id" name="microsoft_tenant_id"
                                       value="{{ old('microsoft_tenant_id', $graphConfig['tenant_id']) }}"
                                       placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" required>
                                @error('microsoft_tenant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="microsoft_client_secret">Client Secret</label>
                                <input type="password" class="form-control @error('microsoft_client_secret') is-invalid @enderror"
                                       id="microsoft_client_secret" name="microsoft_client_secret"
                                       placeholder="{{ $graphConfig['secret_configured'] ? '••••••••  (leave blank to keep current)' : 'Required on first setup' }}"
                                       autocomplete="new-password">
                                @error('microsoft_client_secret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($graphConfig['secret_configured'])
                                    <small class="form-text text-success"><i class="fas fa-lock mr-1"></i>A secret is already saved.</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="microsoft_redirect_uri">Redirect URI (SSO callback) <span class="text-danger">*</span></label>
                                <input type="url" class="form-control @error('microsoft_redirect_uri') is-invalid @enderror"
                                       id="microsoft_redirect_uri" name="microsoft_redirect_uri"
                                       value="{{ old('microsoft_redirect_uri', $graphConfig['redirect_uri']) }}"
                                       required>
                                @error('microsoft_redirect_uri')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Must match the redirect URI in your Azure app registration.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="microsoft_mail_from">Send mail as (mailbox) <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('microsoft_mail_from') is-invalid @enderror"
                                       id="microsoft_mail_from" name="microsoft_mail_from"
                                       value="{{ old('microsoft_mail_from', $graphConfig['mail_from']) }}"
                                       placeholder="westernrcc@africacdc.org" required>
                                @error('microsoft_mail_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Licensed mailbox in your tenant used to send reminders and test emails.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="reminder_daily_at">Daily reminder time</label>
                                <input type="time" class="form-control @error('reminder_daily_at') is-invalid @enderror"
                                       id="reminder_daily_at" name="reminder_daily_at"
                                       value="{{ old('reminder_daily_at', $reminderConfig['daily_at']) }}">
                                @error('reminder_daily_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="reminder_cooldown_days">Reminder cooldown (days)</label>
                                <input type="number" min="1" max="30" class="form-control @error('reminder_cooldown_days') is-invalid @enderror"
                                       id="reminder_cooldown_days" name="reminder_cooldown_days"
                                       value="{{ old('reminder_cooldown_days', $reminderConfig['cooldown_days']) }}">
                                @error('reminder_cooldown_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="reminders_enabled" name="reminders_enabled" value="1"
                                       @checked(old('reminders_enabled', $graphConfig['reminders_enabled']))>
                                <label class="custom-control-label" for="reminders_enabled">Enable scheduled email reminders</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="reminder_activity_reports_enabled" name="reminder_activity_reports_enabled" value="1"
                                       @checked(old('reminder_activity_reports_enabled', $graphConfig['activity_reports_enabled']))>
                                <label class="custom-control-label" for="reminder_activity_reports_enabled">Enable activity report due reminders</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Save Microsoft Graph Settings
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

<!-- Activity Report Reminders -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bell mr-2"></i>
                    Activity Report Email Reminders (Microsoft Graph)
                </h3>
            </div>
            <div class="card-body">
                @if($remindersEnabled)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>
                        Reminders are <strong>enabled</strong>. Scheduled daily at <strong>{{ $reminderConfig['daily_at'] }}</strong>
                        ({{ config('app.timezone') }}). Cooldown: {{ $reminderConfig['cooldown_days'] }} days between repeats.
                        Send as: <code>{{ $reminderConfig['send_as'] ?: 'not set' }}</code>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Reminders are <strong>disabled</strong> or Microsoft Graph is not fully configured.
                        Use the <strong>Microsoft Graph</strong> form above to save credentials.
                    </div>
                @endif

                <p class="text-muted">
                    Staff who participated in a completed mission, training, workshop, or event (via weekly tracker or activity request)
                    receive an email prompting them to submit their post-activity report.
                </p>

                @if($reminderPreview->count())
                    <h5 class="mt-3">Pending reminders (would send now)</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th>Email</th>
                                    <th>Activities needing reports</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reminderPreview as $row)
                                    <tr>
                                        <td>{{ $row['staff']->full_name }}</td>
                                        <td>{{ $row['staff']->email }}</td>
                                        <td>
                                            <ul class="mb-0 pl-3">
                                                @foreach($row['activities'] as $activity)
                                                    <li>{{ $activity->title }} <span class="text-muted">({{ $activity->type_label }})</span></li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0"><i class="fas fa-check mr-1"></i>No report reminders are due right now.</p>
                @endif
            </div>
            <div class="card-footer">
                <form method="POST" action="{{ route('admin.email.reminders.activity-reports') }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="dry_run" value="1">
                    <button type="submit" class="btn btn-outline-secondary" {{ !$remindersEnabled ? 'disabled' : '' }}>
                        <i class="fas fa-eye mr-1"></i> Dry Run
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.email.reminders.activity-reports') }}" class="d-inline ml-2"
                      data-warcc-confirm="Send activity report reminder emails now?">
                    @csrf
                    <button type="submit" class="btn btn-primary" {{ !$remindersEnabled ? 'disabled' : '' }}>
                        <i class="fas fa-paper-plane mr-1"></i> Send Reminders Now
                    </button>
                </form>
                <small class="text-muted ml-3">
                    CLI: <code>php artisan reminders:activity-reports --dry-run</code>
                </small>
            </div>
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
        WarccDialog.confirm({
            title: 'Reset form?',
            text: 'All unsaved changes will be lost.',
            icon: 'warning',
            confirmText: 'Reset',
        }).then(function (result) {
            if (result.isConfirmed) {
                document.querySelector('form').reset();
            }
        });
    }

    // Auto-fill from address when username changes
    document.getElementById('mail_username').addEventListener('input', function() {
        const fromAddressField = document.getElementById('mail_from_address');
        if (!fromAddressField.value || fromAddressField.value === 'westernrcc@africacdc.org') {
            fromAddressField.value = this.value;
        }
    });

    // Show Microsoft Graph status when selected (test email panel)
    document.getElementById('mailer_type')?.addEventListener('change', function() {
        const graphStatus = document.getElementById('graph-status');
        if (!graphStatus) return;
        graphStatus.style.display = this.value === 'microsoft-graph' ? 'block' : 'none';
    });

    // Test Microsoft Graph connection
    function testGraphConnection() {
        const statusText = document.getElementById('graph-config-status-text')
            || document.getElementById('graph-status-text');
        const statusBox = document.getElementById('graph-config-status')
            || document.getElementById('graph-status');
        const testBtn = event?.target;

        if (statusText) statusText.textContent = 'Testing connection...';
        if (statusBox) statusBox.className = 'alert alert-info';
        if (testBtn) {
            testBtn.disabled = true;
            testBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Testing...';
        }

        fetch('{{ route("admin.email.test.graph") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (statusText) statusText.textContent = data.message || 'Connection successful! Ready to send emails.';
                if (statusBox) statusBox.className = 'alert alert-success';
            } else {
                if (statusText) statusText.textContent = 'Connection failed: ' + (data.message || 'Unknown error');
                if (statusBox) statusBox.className = 'alert alert-danger';
            }
        })
        .catch(error => {
            if (statusText) statusText.textContent = 'Connection test failed: ' + error.message;
            if (statusBox) statusBox.className = 'alert alert-danger';
        })
        .finally(() => {
            if (testBtn) {
                testBtn.disabled = false;
                testBtn.innerHTML = '<i class="fas fa-plug mr-1"></i>Test Connection';
            }
        });
    }
</script>
@stop
