<?php

declare(strict_types=1);

\App\Core\View::extends('layouts.app');

\App\Core\View::section('content');

$user = $user ?? [];

?>

<style>
    .empty-state-wrapper {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #F7F8FA;
        padding: 40px 20px;
    }

    .empty-state-container {
        text-align: center;
        max-width: 500px;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state-title {
        font-size: 28px;
        font-weight: 700;
        color: #161B22;
        margin-bottom: 12px;
    }

    .empty-state-message {
        font-size: 14px;
        color: #626F86;
        margin-bottom: 32px;
        line-height: 1.6;
    }

    .empty-state-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-primary {
        background-color: #8B1956;
        color: white;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: 0.2s;
        display: inline-block;
    }

    .btn-primary:hover {
        background-color: #6B0F44;
        text-decoration: none;
    }

    .btn-secondary {
        background-color: white;
        color: #8B1956;
        padding: 10px 20px;
        border: 1px solid #DFE1E6;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: 0.2s;
        display: inline-block;
    }

    .btn-secondary:hover {
        background-color: #F7F8FA;
        border-color: #8B1956;
        text-decoration: none;
    }
</style>

<div class="empty-state-wrapper">
    <div class="empty-state-container">
        <div class="empty-state-icon">⏱️</div>
        <h1 class="empty-state-title">No Projects Yet</h1>
        <p class="empty-state-message">
            You're not assigned to any projects. Contact your team lead or administrator to add you to a project to start tracking time.
        </p>
        <div class="empty-state-actions">
            <a href="<?= url('/projects') ?>" class="btn-primary">Browse Projects</a>
            <a href="<?= url('/dashboard') ?>" class="btn-secondary">Go to Dashboard</a>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection();
