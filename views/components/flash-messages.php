<?php
$flashTypes = [
    'success' => ['class' => 'success', 'icon' => 'check-circle-fill'],
    'error' => ['class' => 'danger', 'icon' => 'exclamation-triangle-fill'],
    'warning' => ['class' => 'warning', 'icon' => 'exclamation-circle-fill'],
    'info' => ['class' => 'info', 'icon' => 'info-circle-fill'],
];
?>

<?php foreach ($flashTypes as $type => $config): ?>
    <?php if ($message = ($flash[$type] ?? null)): ?>
    <div class="alert alert-<?= $config['class'] ?> alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="bi bi-<?= $config['icon'] ?> me-2"></i>
        <div><?= e($message) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if (!empty($errors) && is_array($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-2">
        <?php foreach ($errors as $field => $fieldErrors): ?>
            <?php foreach ((array) $fieldErrors as $error): ?>
            <li><?= e($error) ?></li>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
