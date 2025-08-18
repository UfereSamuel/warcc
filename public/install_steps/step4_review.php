<?php
if (!isset($_SESSION['config'])) {
    header('Location: install.php?step=3');
    exit;
}

$config = $_SESSION['config'];
?>

<div class="text-center mb-4">
    <h2><i class="fas fa-eye me-2"></i>Review Configuration</h2>
    <p class="text-muted">Please review your settings before proceeding with the installation</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Application Settings</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?= htmlspecialchars($config['app_name']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>URL:</strong></td>
                        <td><?= htmlspecialchars($config['app_url']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-database me-2"></i>Database Settings</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Host:</strong></td>
                        <td><?= htmlspecialchars($config['db_host']) ?>:<?= htmlspecialchars($config['db_port']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Database:</strong></td>
                        <td><?= htmlspecialchars($config['db_database']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><?= htmlspecialchars($config['db_username']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Password:</strong></td>
                        <td><?= $config['db_password'] ? str_repeat('*', 8) : '<em>No password</em>' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Email Settings</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Driver:</strong></td>
                        <td><?= htmlspecialchars($config['mail_driver']) ?></td>
                    </tr>
                    <?php if ($config['mail_driver'] === 'smtp'): ?>
                    <tr>
                        <td><strong>Host:</strong></td>
                        <td><?= htmlspecialchars($config['mail_host']) ?>:<?= htmlspecialchars($config['mail_port']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Encryption:</strong></td>
                        <td><?= htmlspecialchars($config['mail_encryption']) ?: 'None' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><?= htmlspecialchars($config['mail_username']) ?: '<em>Not set</em>' ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>From Email:</strong></td>
                        <td><?= htmlspecialchars($config['mail_from']) ?: '<em>Not set</em>' ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Administrator</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?= htmlspecialchars($config['admin_name']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?= htmlspecialchars($config['admin_email']) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Password:</strong></td>
                        <td><?= str_repeat('*', strlen($config['admin_password'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>What will happen during installation:</strong>
    <ul class="mb-0 mt-2">
        <li>Create and configure the <code>.env</code> file</li>
        <li>Generate application encryption key</li>
        <li>Run database migrations and create tables</li>
        <li>Seed initial data (leave types, admin user, etc.)</li>
        <li>Cache application configuration</li>
        <li>Mark the system as installed</li>
    </ul>
</div>

<form method="POST" id="installForm">
    <input type="hidden" name="action" value="install">

    <div class="d-flex justify-content-between mt-4">
        <a href="install.php?step=3" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Configuration
        </a>
        <button type="submit" class="btn btn-success btn-lg" id="installBtn">
            <i class="fas fa-rocket me-2"></i>Start Installation
        </button>
    </div>
</form>

<script>
document.getElementById('installForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('installBtn');
    btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></div>Installing...';
    btn.disabled = true;
});
</script>
