<?php
$requirements = checkRequirements();
$allPassed = !in_array(false, $requirements, true);
?>

<div class="text-center mb-4">
    <h2><i class="fas fa-list-check me-2"></i>System Requirements</h2>
    <p class="text-muted">Checking if your server meets the minimum requirements</p>
</div>

<div class="requirements-list">
    <?php foreach ($requirements as $requirement => $passed): ?>
    <div class="requirement-check">
        <span><?= $requirement ?></span>
        <span class="status-icon <?= $passed ? 'success' : 'error' ?>">
            <i class="fas <?= $passed ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
        </span>
    </div>
    <?php endforeach; ?>
</div>

<?php if (!$allPassed): ?>
<div class="alert alert-danger mt-4">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Some requirements are not met!</strong><br>
    Please install the missing PHP extensions or upgrade your PHP version before continuing.
</div>
<?php else: ?>
<div class="alert alert-success mt-4">
    <i class="fas fa-check-circle me-2"></i>
    <strong>Great!</strong> Your server meets all the requirements.
</div>
<?php endif; ?>

<div class="d-flex justify-content-between mt-4">
    <div></div>
    <a href="install.php?step=2" class="btn btn-primary <?= !$allPassed ? 'disabled' : '' ?>">
        Next Step <i class="fas fa-arrow-right ms-2"></i>
    </a>
</div>
