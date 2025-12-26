<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="admin-page-wrapper">
    <!-- Breadcrumb -->
    <nav class="admin-breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-item">Administration</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Workflows</span>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Workflows</h1>
            <p class="page-subtitle">Workflows define the paths an issue can take through its lifecycle</p>
        </div>
        <div class="header-right">
            <button class="action-btn btn-primary" onclick="alert('Create workflow feature coming soon!')">
                <i class="bi bi-plus-lg"></i>
                Create Workflow
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-area">
        <div class="admin-card">
            <div class="card-header">
                <h3 class="card-title">All Workflows</h3>
            </div>
            <div class="card-body">
                <?php if (empty($workflows)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📊</div>
                        <h3 class="empty-title">No Workflows Found</h3>
                        <p class="empty-text">You haven't created any custom workflows yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Projects</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($workflows as $wf): ?>
                                    <tr>
                                        <td>
                                            <div class="wf-name-cell">
                                                <strong><?= e($wf['name']) ?></strong>
                                                <?php if ($wf['is_default'] ?? false): ?>
                                                    <span class="badge badge-info">Default</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted small"><?= e($wf['description']) ?></div>
                                        </td>
                                        <td>
                                            <span class="count-badge"><?= e($wf['project_count'] ?? 0) ?> Projects</span>
                                        </td>
                                        <td>
                                            <span class="status-indicator status-success">Active</span>
                                        </td>
                                        <td class="text-right">
                                            <div class="action-group">
                                                <a href="<?= url("/admin/workflows/{$wf['id']}") ?>" class="icon-btn"
                                                    title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button class="icon-btn" title="Edit"
                                                    onclick="alert('Edit feature coming soon!')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 80px);
        background: #F7F8FA;
    }

    .admin-breadcrumb {
        padding: 12px 32px;
        background: white;
        border-bottom: 1px solid #DFE1E6;
        font-size: 13px;
    }

    .breadcrumb-item {
        color: #8B1956;
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-sep {
        color: #626F86;
        margin: 0 8px;
    }

    .breadcrumb-current {
        color: #161B22;
        font-weight: 600;
    }

    .page-header {
        padding: 24px 32px;
        background: white;
        border-bottom: 1px solid #DFE1E6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #161B22;
        margin: 0 0 4px 0;
    }

    .page-subtitle {
        font-size: 14px;
        color: #626F86;
        margin: 0;
    }

    .content-area {
        padding: 32px;
    }

    .admin-card {
        background: white;
        border: 1px solid #DFE1E6;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        padding: 20px;
        border-bottom: 1px solid #DFE1E6;
    }

    .card-title {
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        padding: 20px;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table th {
        text-align: left;
        padding: 12px 16px;
        background: #F4F5F7;
        color: #626F86;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .admin-table td {
        padding: 16px;
        border-bottom: 1px solid #DFE1E6;
        vertical-align: middle;
        font-size: 14px;
    }

    .wf-name-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-info {
        background: #DEEBFF;
        color: #0747A6;
    }

    .count-badge {
        background: #EAEBEF;
        color: #42526E;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-indicator {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-indicator::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-success::before {
        background: #36B37E;
    }

    .text-right {
        text-align: right !important;
    }

    .action-group {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .icon-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        border: 1px solid #DFE1E6;
        background: white;
        color: #42526E;
        text-decoration: none;
        transition: all 0.2s;
    }

    .icon-btn:hover {
        background: #F4F5F7;
        border-color: #8B1956;
        color: #8B1956;
    }

    .btn-primary {
        background: #8B1956;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #6F123F;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .empty-title {
        margin-bottom: 8px;
    }

    .empty-text {
        color: #626F86;
    }
</style>

<?php \App\Core\View::endSection(); ?>