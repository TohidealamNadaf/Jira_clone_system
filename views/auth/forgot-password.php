<?php \App\Core\View::extends('layouts.auth'); ?>

<?php \App\Core\View::section('content'); ?>

<h4 class="text-center mb-4">Reset your password</h4>
<p class="text-muted text-center mb-4">Enter your email address and we'll send you a link to reset your password.</p>

<form action="<?= url('/forgot-password') ?>" method="POST">
    <?= csrf_field() ?>
    
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input type="email" class="form-control <?= has_error('email') ? 'is-invalid' : '' ?>" 
                   id="email" name="email" value="<?= e(old('email')) ?>" required autofocus>
            <?php if (has_error('email')): ?>
            <div class="invalid-feedback"><?= e(error('email')) ?></div>
            <?php endif; ?>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-envelope me-2"></i> Send Reset Link
    </button>
</form>

<div class="text-center mt-4">
    <a href="<?= url('/login') ?>" class="text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Back to login
    </a>
</div>

<?php \App\Core\View::endSection(); ?>
