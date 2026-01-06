<?php \App\Core\View::extends('layouts.auth'); ?>

<?php \App\Core\View::section('content'); ?>

<h4 class="text-center mb-4">Set new password</h4>
<p class="text-muted text-center mb-4">Enter your new password below.</p>

<form action="<?= url('/reset-password') ?>" method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="token" value="<?= e($token ?? '') ?>">
    <input type="hidden" name="email" value="<?= e($email ?? '') ?>">
    
    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input type="password" class="form-control <?= has_error('password') ? 'is-invalid' : '' ?>" 
                   id="password" name="password" required autofocus>
            <?php if (has_error('password')): ?>
            <div class="invalid-feedback"><?= e(error('password')) ?></div>
            <?php endif; ?>
        </div>
        <div class="form-text">Password must be at least 8 characters.</div>
    </div>
    
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control <?= has_error('password_confirmation') ? 'is-invalid' : '' ?>" 
                   id="password_confirmation" name="password_confirmation" required>
            <?php if (has_error('password_confirmation')): ?>
            <div class="invalid-feedback"><?= e(error('password_confirmation')) ?></div>
            <?php endif; ?>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-check-lg me-2"></i> Reset Password
    </button>
</form>

<div class="text-center mt-4">
    <a href="<?= url('/login') ?>" class="text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Back to login
    </a>
</div>

<?php \App\Core\View::endSection(); ?>
