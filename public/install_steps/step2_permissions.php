<?php
$permissions = checkPermissions();
$allWritable = !in_array(false, $permissions, true);
?>

<div class="text-center mb-4">
    <h2><i class="fas fa-lock me-2"></i>Directory Permissions</h2>
    <p class="text-muted">Checking if the required directories are writable</p>
</div>

<div class="requirements-list">
    <?php foreach ($permissions as $path => $writable): ?>
    <div class="requirement-check">
        <span><?= $path ?></span>
        <span class="status-icon <?= $writable ? 'success' : 'error' ?>">
            <i class="fas <?= $writable ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
        </span>
    </div>
    <?php endforeach; ?>
</div>

<?php if (!$allWritable): ?>
<div class="alert alert-danger mt-4">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Some directories are not writable!</strong><br>
    Please set the correct permissions for the directories marked with an error.
    <div class="mt-3">
        <strong>Run these commands on your server:</strong>
        <pre class="bg-dark text-light p-3 rounded mt-2">
sudo chown -R www-data:www-data /var/www/warcc
sudo chmod -R 755 /var/www/warcc
sudo chmod -R 775 /var/www/warcc/storage
sudo chmod -R 775 /var/www/warcc/bootstrap/cache</pre>
    </div>
</div>
<?php else: ?>
<div class="alert alert-success mt-4">
    <i class="fas fa-check-circle me-2"></i>
    <strong>Perfect!</strong> All required directories are writable.
</div>
<?php endif; ?>

<div class="d-flex justify-content-between mt-4">
    <a href="install.php?step=1" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Previous
    </a>
    <a href="install.php?step=3" class="btn btn-primary <?= !$allWritable ? 'disabled' : '' ?>">
        Next Step <i class="fas fa-arrow-right ms-2"></i>
    </a>
</div>
