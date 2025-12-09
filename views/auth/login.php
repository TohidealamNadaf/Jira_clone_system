<?php \App\Core\View::extends('layouts.auth'); ?>

<?php \App\Core\View::section('content'); ?>

<h1 style="color: red; font-size: 24px; margin-bottom: 20px;">TEST LOGIN FORM</h1>

<?php if (isset($flash['error'])): ?>
<div style="background: #ffdddd; color: #cc0000; padding: 10px; margin-bottom: 20px; border: 1px solid #cc0000;">
    <?= e($flash['error']) ?>
</div>
<?php endif; ?>

<form action="<?= url('/login') ?>" method="POST" style="max-width: 400px; margin: 0 auto;">
    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">

    <div style="margin-bottom: 15px;">
        <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold;">Email:</label>
        <input type="email" id="email" name="email" required autofocus
               style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
    </div>

    <div style="margin-bottom: 15px;">
        <label for="password" style="display: block; margin-bottom: 5px; font-weight: bold;">Password:</label>
        <input type="password" id="password" name="password" required
               style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display: flex; align-items: center;">
            <input type="checkbox" name="remember" value="1" style="margin-right: 8px;">
            Remember me
        </label>
    </div>

    <button type="submit" style="width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">
        Sign In
    </button>
</form>

<div style="text-align: center; margin-top: 20px; color: #666;">
    <strong>Test Credentials:</strong><br>
    Email: admin@example.com<br>
    Password: Admin@123
</div>

<?php \App\Core\View::endSection(); ?>
