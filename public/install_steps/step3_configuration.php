<div class="text-center mb-4">
    <h2><i class="fas fa-cog me-2"></i>Application Configuration</h2>
    <p class="text-muted">Configure your application settings and database connection</p>
</div>

<form method="POST" id="configForm">
    <input type="hidden" name="action" value="save_config">

    <!-- Application Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Application Settings</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="app_name" class="form-label">Application Name</label>
                        <input type="text" class="form-control" id="app_name" name="app_name"
                               value="WARCC Staff Management System" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="app_url" class="form-label">Application URL</label>
                        <input type="url" class="form-control" id="app_url" name="app_url"
                               value="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) ?>" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Database Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-database me-2"></i>Database Settings</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="db_host" class="form-label">Database Host</label>
                        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="db_port" class="form-label">Database Port</label>
                        <input type="number" class="form-control" id="db_port" name="db_port" value="3306" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="db_database" class="form-label">Database Name</label>
                        <input type="text" class="form-control" id="db_database" name="db_database" value="warcc" required>
                        <div class="form-text">The database will be created if it doesn't exist</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="db_username" class="form-label">Database Username</label>
                        <input type="text" class="form-control" id="db_username" name="db_username" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="db_password" class="form-label">Database Password</label>
                        <input type="password" class="form-control" id="db_password" name="db_password">
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-primary" id="testDbBtn" onclick="testDatabase()">
                        <div class="spinner-border spinner-border-sm loading" role="status" aria-hidden="true"></div>
                        <span class="btn-text">Test Connection</span>
                    </button>
                </div>
            </div>
            <div id="dbTestResult" class="mt-3" style="display: none;"></div>
        </div>
    </div>

    <!-- Email Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Email Settings (Optional)</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mail_driver" class="form-label">Mail Driver</label>
                        <select class="form-select" id="mail_driver" name="mail_driver">
                            <option value="smtp">SMTP</option>
                            <option value="sendmail">Sendmail</option>
                            <option value="log" selected>Log (for testing)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mail_host" class="form-label">SMTP Host</label>
                        <input type="text" class="form-control" id="mail_host" name="mail_host" placeholder="smtp.gmail.com">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mail_port" class="form-label">SMTP Port</label>
                        <input type="number" class="form-control" id="mail_port" name="mail_port" value="587">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mail_encryption" class="form-label">Encryption</label>
                        <select class="form-select" id="mail_encryption" name="mail_encryption">
                            <option value="tls" selected>TLS</option>
                            <option value="ssl">SSL</option>
                            <option value="">None</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mail_from" class="form-label">From Email</label>
                        <input type="email" class="form-control" id="mail_from" name="mail_from" placeholder="noreply@yoursite.com">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mail_username" class="form-label">SMTP Username</label>
                        <input type="text" class="form-control" id="mail_username" name="mail_username">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mail_password" class="form-label">SMTP Password</label>
                        <input type="password" class="form-control" id="mail_password" name="mail_password">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Account -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Administrator Account</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="admin_name" class="form-label">Admin Name</label>
                        <input type="text" class="form-control" id="admin_name" name="admin_name" value="System Administrator" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Admin Email</label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email" value="admin@africacdc.org" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="admin_password" class="form-label">Admin Password</label>
                        <input type="password" class="form-control" id="admin_password" name="admin_password" required minlength="8">
                        <div class="form-text">Minimum 8 characters</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="admin_password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="admin_password_confirm" name="admin_password_confirm" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="install.php?step=2" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Previous
        </a>
        <button type="submit" class="btn btn-primary" id="proceedBtn" disabled>
            Review Configuration <i class="fas fa-arrow-right ms-2"></i>
        </button>
    </div>
</form>

<script>
// Password confirmation validation
document.getElementById('admin_password_confirm').addEventListener('input', function() {
    const password = document.getElementById('admin_password').value;
    const confirm = this.value;

    if (password !== confirm) {
        this.setCustomValidity('Passwords do not match');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

// Auto-fill from email when mail driver changes
document.getElementById('mail_driver').addEventListener('change', function() {
    const fromEmail = document.getElementById('mail_from');
    if (!fromEmail.value && this.value === 'smtp') {
        const appUrl = document.getElementById('app_url').value;
        if (appUrl) {
            try {
                const domain = new URL(appUrl).hostname;
                fromEmail.value = 'noreply@' + domain;
            } catch (e) {
                // Invalid URL, ignore
            }
        }
    }
});
</script>
