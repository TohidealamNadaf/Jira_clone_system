<?php \App\Core\View::extends('layouts.auth'); ?>

<?php \App\Core\View::section('content'); ?>

<style>
    .login-form {
        width: 100%;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #161B22;
        margin-bottom: 6px;
        letter-spacing: 0.2px;
    }

    .form-input {
        width: 100%;
        padding: 8px 12px;
        font-size: 14px;
        border: 1px solid #DFE1E6;
        border-radius: 4px;
        background: #FFFFFF;
        color: #161B22;
        transition: 0.2s ease;
        font-family: inherit;
        box-sizing: border-box;
    }

    .form-input::placeholder {
        color: #AEB5C0;
    }

    .form-input:focus {
        outline: none;
        border-color: #8B1956;
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
    }

    .login-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 16px 0 20px 0;
        gap: 8px;
        font-size: 13px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        color: #161B22;
    }

    .checkbox-input {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #8B1956;
    }

    .forgot-link {
        color: #8B1956;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .forgot-link:hover {
        color: #6F123F;
        text-decoration: underline;
    }

    .btn-login {
        width: 100%;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 600;
        background: #8B1956;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.2s ease;
        font-family: inherit;
        min-height: 40px;
    }

    .btn-login:hover {
        background: #6F123F;
    }

    .btn-login:active {
        background: #6F123F;
    }

    .btn-login:focus {
        outline: 2px solid #8B1956;
        outline-offset: 2px;
    }

    .login-alert {
        padding: 8px 12px;
        margin-bottom: 16px;
        background: #FFECEB;
        border: 1px solid #ED3C32;
        border-radius: 4px;
        color: #AE2A19;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .alert-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-close {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
        flex-shrink: 0;
    }

    .demo-section {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #DFE1E6;
        font-size: 12px;
        color: #626F86;
        text-align: center;
    }

    .demo-section strong {
        display: block;
        color: #161B22;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .demo-section code {
        background: #F7F8FA;
        padding: 2px 4px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: #161B22;
    }

    /* Responsive */
    @media (max-width: 767px) {
        .login-options {
            flex-direction: column;
            align-items: flex-start;
            margin: 14px 0 18px 0;
        }

        .form-input {
            font-size: 16px;
        }

        .btn-login {
            min-height: 44px;
        }
    }
</style>

<?php if (isset($flash['error'])): ?>
<div class="login-alert" role="alert">
    <div class="alert-content">
        <i class="bi bi-exclamation-circle-fill" style="font-size: 14px;"></i>
        <span><?= e($flash['error']) ?></span>
    </div>
</div>
<?php endif; ?>

<form action="<?= url('/login') ?>" method="POST" class="login-form" novalidate>
    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">

    <div class="form-group">
        <label for="email" class="form-label">Email address</label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="example@company.com"
            required 
            autofocus
            autocomplete="email">
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-input" 
            placeholder="Password"
            required
            autocomplete="current-password">
    </div>

    <div class="login-options">
        <label class="checkbox-label">
            <input type="checkbox" name="remember" value="1" class="checkbox-input">
            Remember me
        </label>
        <a href="<?= url('/forgot-password') ?>" class="forgot-link">Forgot password?</a>
    </div>

    <button type="submit" class="btn-login">Sign in</button>
</form>

<div class="demo-section">
    <strong>Demo Credentials</strong>
    <code>admin@example.com</code> / <code>Admin@123</code>
</div>

<?php \App\Core\View::endSection(); ?>
