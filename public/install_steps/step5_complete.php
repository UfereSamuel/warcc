<?php
if (!isset($_SESSION['config']) || !file_exists('../../.installed')) {
    header('Location: install.php?step=1');
    exit;
}

$config = $_SESSION['config'];
$installationResults = $_SESSION['installation_results'] ?? [];
?>

<div class="text-center mb-4">
    <div class="text-success mb-3">
        <i class="fas fa-check-circle" style="font-size: 4rem;"></i>
    </div>
    <h2 class="text-success">Installation Complete!</h2>
    <p class="text-muted">Your WARCC Staff Management System is now ready to use</p>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Installation Summary</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($installationResults)): ?>
        <div class="installation-log">
            <?php foreach ($installationResults as $result): ?>
            <div class="d-flex align-items-center mb-2">
                <span class="me-3">
                    <?php if ($result['success']): ?>
                        <i class="fas fa-check-circle text-success"></i>
                    <?php else: ?>
                        <i class="fas fa-times-circle text-danger"></i>
                    <?php endif; ?>
                </span>
                <span class="flex-grow-1"><?= htmlspecialchars($result['description']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="alert alert-success mt-3">
            <i class="fas fa-database me-2"></i>
            Database setup completed successfully with all required tables and initial data.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Admin Access</h5>
            </div>
            <div class="card-body">
                <p><strong>Admin Panel URL:</strong></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="<?= rtrim(htmlspecialchars($config['app_url']), '/') ?>/auth/admin-login" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard(this.previousElementSibling)">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <p><strong>Login Credentials:</strong></p>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?= htmlspecialchars($config['admin_email']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Password:</strong></td>
                        <td><em>The password you set during installation</em></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Public Website</h5>
            </div>
            <div class="card-body">
                <p><strong>Public Site URL:</strong></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="<?= rtrim(htmlspecialchars($config['app_url']), '/') ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard(this.previousElementSibling)">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <p class="text-muted">
                    The public website includes information about activities, events, and staff login portal.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-warning mt-4">
    <i class="fas fa-shield-alt me-2"></i>
    <strong>Security Recommendations:</strong>
    <ul class="mb-0 mt-2">
        <li>Delete the <code>install.php</code> file and <code>install_steps/</code> directory from your server</li>
        <li>Change the default admin password after first login</li>
        <li>Set up SSL certificate for HTTPS</li>
        <li>Configure email settings if you haven't already</li>
        <li>Set up regular database backups</li>
    </ul>
</div>

<div class="alert alert-info">
    <i class="fas fa-book me-2"></i>
    <strong>Next Steps:</strong>
    <ul class="mb-0 mt-2">
        <li>Log in to the admin panel and explore the system</li>
        <li>Add staff members and configure their permissions</li>
        <li>Set up leave types and organizational structure</li>
        <li>Configure Microsoft SSO if needed</li>
        <li>Customize the public website content</li>
    </ul>
</div>

<div class="text-center mt-4">
    <a href="<?= rtrim(htmlspecialchars($config['app_url']), '/') ?>/auth/admin-login" class="btn btn-primary btn-lg me-3">
        <i class="fas fa-sign-in-alt me-2"></i>Go to Admin Panel
    </a>
    <a href="<?= rtrim(htmlspecialchars($config['app_url']), '/') ?>" class="btn btn-outline-primary btn-lg">
        <i class="fas fa-home me-2"></i>View Public Site
    </a>
</div>

<div class="text-center mt-4">
    <small class="text-muted">
        Installation completed on <?= date('F j, Y \a\t g:i A') ?><br>
        WARCC Staff Management System v1.0
    </small>
</div>

<script>
function copyToClipboard(element) {
    element.select();
    element.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(element.value);

    const button = element.nextElementSibling;
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    button.classList.add('text-success');

    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('text-success');
    }, 2000);
}

// Clear installation session data
<?php
unset($_SESSION['config']);
unset($_SESSION['installation_results']);
?>
</script>
