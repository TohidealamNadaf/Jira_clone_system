<?php \App\Core\View::extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<div class="wf-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="wf-breadcrumb">
        <a href="<?= url('/dashboard') ?>" class="wf-breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="wf-breadcrumb-sep">/</span>
        <a href="<?= url('/projects') ?>" class="wf-breadcrumb-link">Projects</a>
        <span class="wf-breadcrumb-sep">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="wf-breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="wf-breadcrumb-sep">/</span>
        <span class="wf-breadcrumb-current">Workflows</span>
    </div>

    <!-- Page Header -->
    <div class="wf-page-header">
        <div class="wf-header-left">
            <h1 class="wf-page-title">Project Workflows</h1>
            <p class="wf-page-subtitle">Manage how issues progress from creation to resolution</p>
        </div>
        <div class="wf-header-right">
            <a href="<?= url("/projects/{$project['key']}") ?>" class="wf-action-button">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="wf-page-content">
        <!-- Sidebar Navigation -->
        <aside class="wf-sidebar">
            <nav class="wf-sidebar-nav">
                <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="wf-nav-item">
                    <i class="bi bi-gear"></i>
                    <span>Details</span>
                </a>
                <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="wf-nav-item">
                    <i class="bi bi-shield-check"></i>
                    <span>Access</span>
                </a>
                <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="wf-nav-item">
                    <i class="bi bi-box"></i>
                    <span>Components</span>
                </a>
                <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="wf-nav-item">
                    <i class="bi bi-tag"></i>
                    <span>Versions</span>
                </a>
                <a href="<?= url("/projects/{$project['key']}/workflows") ?>" class="wf-nav-item wf-nav-active">
                    <i class="bi bi-diagram-3"></i>
                    <span>Workflows</span>
                </a>
            </nav>
        </aside>

        <!-- Content Area -->
        <main class="wf-content">
            <!-- Active Workflows Card -->
            <div class="wf-card">
                <div class="wf-card-header">
                    <div class="wf-card-header-content">
                        <h2 class="wf-card-title">Active Workflows</h2>
                        <p class="wf-card-subtitle">These workflows are currently used by this project</p>
                    </div>
                </div>

                <div class="wf-card-body">
                    <?php if (empty($workflows)): ?>
                        <div class="wf-empty-state">
                            <div class="wf-empty-icon">📊</div>
                            <h3 class="wf-empty-title">No Workflows</h3>
                            <p class="wf-empty-text">No workflows are currently assigned to this project. Create one to get started.</p>
                        </div>
                    <?php else: ?>
                        <div class="wf-table-wrapper">
                            <table class="wf-table">
                                <thead>
                                    <tr>
                                        <th class="wf-th-name">Workflow</th>
                                        <th class="wf-th-types">Issue Types</th>
                                        <th class="wf-th-status">Status</th>
                                        <th class="wf-th-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($workflows as $wf): ?>
                                        <tr class="wf-table-row">
                                            <td class="wf-td-name">
                                                <div class="wf-workflow-info">
                                                    <div class="wf-wf-icon">
                                                        <i class="bi bi-diagram-3"></i>
                                                    </div>
                                                    <div class="wf-wf-details">
                                                        <div class="wf-wf-name">
                                                            <?= e($wf['name']) ?>
                                                            <?php if ($wf['is_default'] ?? false): ?>
                                                                <span class="wf-badge wf-badge-default">System</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if ($wf['description'] ?? null): ?>
                                                            <p class="wf-wf-description"><?= e($wf['description']) ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="wf-td-types">
                                                <?php if ($wf['issue_type_id'] ?? null): ?>
                                                    <span class="wf-badge wf-badge-primary">
                                                        <?= e($wf['issue_type_name'] ?? 'Issue') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="wf-badge wf-badge-outline">All Types</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="wf-td-status">
                                                <div class="wf-status-badge <?= ($wf['is_active'] ?? true) ? 'wf-status-active' : 'wf-status-inactive' ?>">
                                                    <span class="wf-status-dot"></span>
                                                    <?= ($wf['is_active'] ?? true) ? 'Active' : 'Inactive' ?>
                                                </div>
                                            </td>
                                            <td class="wf-td-actions">
                                                <div class="wf-action-group">
                                                    <button class="wf-icon-button" title="View Details" onclick="viewWorkflow(<?= $wf['id'] ?>)">
                                                        <i class="bi bi-eye"></i>
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

            <!-- Understanding Workflows Card -->
            <div class="wf-card wf-card-info">
                <div class="wf-card-header">
                    <div class="wf-card-header-content">
                        <h2 class="wf-card-title">Understanding Workflows</h2>
                    </div>
                </div>
                <div class="wf-card-body">
                    <div class="wf-info-grid">
                        <div class="wf-info-block">
                            <div class="wf-info-icon">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <div class="wf-info-content">
                                <h3>Shared Workflows</h3>
                                <p>Workflows are shared across projects. Changes made to a workflow may affect other projects using it.</p>
                            </div>
                        </div>
                        <div class="wf-info-block">
                            <div class="wf-info-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <div class="wf-info-content">
                                <h3>Admin Only</h3>
                                <p>Only administrators can modify the structure of a workflow (adding statuses or transitions).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
/* ============================================
   WORKFLOWS PAGE - ENTERPRISE DESIGN SYSTEM
   ============================================ */

:root {
    --wf-primary: #8B1956 !important;
    --wf-primary-dark: #6F123F !important;
    --wf-dark: #161B22 !important;
    --wf-gray: #626F86 !important;
    --wf-border: #DFE1E6 !important;
    --wf-bg-light: #F7F8FA !important;
    --wf-success: #216E4E !important;
    --wf-warning: #E77817 !important;
}

/* Wrapper & Layout */
.wf-page-wrapper {
    background-color: var(--wf-bg-light);
    min-height: 100vh;
}

/* Breadcrumb Navigation */
.wf-breadcrumb {
    background: white;
    border-bottom: 1px solid var(--wf-border);
    padding: 12px 32px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    flex-wrap: wrap;
}

.wf-breadcrumb-link {
    color: var(--wf-primary);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
    font-weight: 500;
}

.wf-breadcrumb-link:hover {
    color: var(--wf-primary-dark);
    text-decoration: underline;
}

.wf-breadcrumb-sep {
    color: var(--wf-gray);
    opacity: 0.5;
}

.wf-breadcrumb-current {
    color: var(--wf-dark);
    font-weight: 600;
}

/* Page Header */
.wf-page-header {
    background: white;
    border-bottom: 1px solid var(--wf-border);
    padding: 32px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
}

.wf-header-left {
    flex: 1;
}

.wf-page-title {
    font-size: 32px;
    font-weight: 700;
    color: var(--wf-dark);
    margin: 0 0 8px 0;
    letter-spacing: -0.2px;
}

.wf-page-subtitle {
    font-size: 15px;
    color: var(--wf-gray);
    margin: 0;
}

.wf-header-right {
    display: flex;
    gap: 12px;
    align-items: center;
}

.wf-action-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 1px solid var(--wf-border);
    border-radius: 6px;
    color: var(--wf-dark);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.wf-action-button:hover {
    background: var(--wf-bg-light);
    border-color: var(--wf-primary);
    color: var(--wf-primary);
    transform: translateY(-1px);
}

/* Main Layout */
.wf-page-content {
    display: flex;
    gap: 24px;
    padding: 32px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Sidebar Navigation */
.wf-sidebar {
    width: 260px;
    flex-shrink: 0;
}

.wf-sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 0;
    background: white;
    border: 1px solid var(--wf-border);
    border-radius: 8px;
    overflow: hidden;
}

.wf-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    color: var(--wf-gray);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    border-bottom: 1px solid var(--wf-border);
    transition: all 0.2s;
}

.wf-nav-item:last-child {
    border-bottom: none;
}

.wf-nav-item:hover {
    background: var(--wf-bg-light);
    color: var(--wf-primary);
    padding-left: 18px;
}

.wf-nav-item i {
    font-size: 16px;
}

.wf-nav-active {
    background: var(--wf-bg-light);
    color: var(--wf-primary);
    border-left: 3px solid var(--wf-primary);
    padding-left: 13px;
}

/* Content Area */
.wf-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Cards */
.wf-card {
    background: white;
    border: 1px solid var(--wf-border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    transition: all 0.2s;
}

.wf-card:hover {
    border-color: var(--wf-primary);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.wf-card-header {
    background: var(--wf-bg-light);
    border-bottom: 1px solid var(--wf-border);
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.wf-card-header-content {
    flex: 1;
}

.wf-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--wf-dark);
    margin: 0 0 4px 0;
}

.wf-card-subtitle {
    font-size: 13px;
    color: var(--wf-gray);
    margin: 0;
}

.wf-card-body {
    padding: 24px;
}

/* Empty State */
.wf-empty-state {
    text-align: center;
    padding: 60px 40px;
}

.wf-empty-icon {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.wf-empty-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--wf-dark);
    margin: 0 0 8px 0;
}

.wf-empty-text {
    font-size: 14px;
    color: var(--wf-gray);
    margin: 0;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* Table */
.wf-table-wrapper {
    overflow-x: auto;
}

.wf-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.wf-table thead {
    background: var(--wf-bg-light);
    border-bottom: 2px solid var(--wf-border);
}

.wf-table th {
    padding: 12px 16px;
    text-align: left;
    font-weight: 600;
    color: var(--wf-gray);
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.wf-th-name {
    width: 40%;
}

.wf-th-types {
    width: 20%;
}

.wf-th-status {
    width: 15%;
}

.wf-th-actions {
    width: 15%;
    text-align: right;
    padding-right: 20px;
}

.wf-table-row {
    border-bottom: 1px solid var(--wf-border);
    transition: background-color 0.2s;
}

.wf-table-row:hover {
    background: var(--wf-bg-light);
}

.wf-table td {
    padding: 16px;
    vertical-align: middle;
}

/* Workflow Info */
.wf-workflow-info {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.wf-wf-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--wf-bg-light);
    border-radius: 6px;
    color: var(--wf-primary);
    font-size: 18px;
    flex-shrink: 0;
}

.wf-wf-details {
    flex: 1;
}

.wf-wf-name {
    font-weight: 600;
    color: var(--wf-dark);
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.wf-wf-description {
    font-size: 12px;
    color: var(--wf-gray);
    margin: 0;
}

/* Badges */
.wf-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.wf-badge-default {
    background: var(--wf-bg-light);
    color: var(--wf-primary);
    border: 1px solid var(--wf-primary);
}

.wf-badge-primary {
    background: var(--wf-primary);
    color: white;
}

.wf-badge-outline {
    background: transparent;
    color: var(--wf-gray);
    border: 1px solid var(--wf-border);
}

/* Status Badge */
.wf-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    color: white !important;
}

.wf-status-active {
    background: var(--wf-success);
    color: white !important;
}

.wf-status-inactive {
    background: var(--wf-warning);
    color: white !important;
}

.wf-status-dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

/* Action Group */
.wf-td-actions {
    text-align: right;
}

.wf-action-group {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

.wf-icon-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    background: transparent;
    border: 1px solid var(--wf-border);
    border-radius: 4px;
    color: var(--wf-gray);
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s;
}

.wf-icon-button:hover {
    background: var(--wf-bg-light);
    border-color: var(--wf-primary);
    color: var(--wf-primary);
}

/* Info Grid */
.wf-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.wf-info-block {
    display: flex;
    gap: 16px;
    padding: 20px;
    background: var(--wf-bg-light);
    border-radius: 6px;
    border: 1px solid var(--wf-border);
}

.wf-info-icon {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: white;
    border-radius: 6px;
    color: var(--wf-primary);
    font-size: 20px;
    flex-shrink: 0;
}

.wf-info-content h3 {
    font-size: 14px;
    font-weight: 600;
    color: var(--wf-dark);
    margin: 0 0 8px 0;
}

.wf-info-content p {
    font-size: 13px;
    color: var(--wf-gray);
    margin: 0;
    line-height: 1.5;
}

.wf-card-info {
    margin-top: 12px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .wf-page-content {
        flex-direction: column;
        padding: 20px;
    }

    .wf-sidebar {
        width: 100%;
    }

    .wf-sidebar-nav {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        border-radius: 6px;
    }

    .wf-nav-item {
        border-bottom: 1px solid var(--wf-border);
        border-left: none;
    }

    .wf-nav-active {
        border-left: none;
        border-bottom: 3px solid var(--wf-primary);
        padding-left: 16px;
    }

    .wf-page-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
    }

    .wf-info-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .wf-breadcrumb {
        padding: 12px 16px;
        font-size: 12px;
        gap: 6px;
    }

    .wf-page-header {
        padding: 16px;
    }

    .wf-page-title {
        font-size: 24px;
    }

    .wf-page-content {
        padding: 16px;
        gap: 16px;
    }

    .wf-card-body {
        padding: 16px;
    }

    .wf-table {
        font-size: 12px;
    }

    .wf-table th {
        padding: 10px 12px;
    }

    .wf-table td {
        padding: 12px;
    }

    .wf-th-name {
        width: 50%;
    }

    .wf-th-types {
        width: 25%;
    }

    .wf-th-status {
        display: none;
    }

    .wf-th-actions {
        width: 25%;
        padding-right: 12px;
    }

    .wf-workflow-info {
        gap: 8px;
    }

    .wf-wf-icon {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }

    .wf-info-grid {
        grid-template-columns: 1fr;
    }

    .wf-info-block {
        gap: 12px;
        padding: 16px;
    }

    .wf-info-icon {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }

    .wf-info-content h3 {
        font-size: 13px;
    }

    .wf-info-content p {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .wf-breadcrumb {
        padding: 8px 12px;
        font-size: 11px;
    }

    .wf-page-header {
        padding: 12px;
        gap: 12px;
    }

    .wf-page-title {
        font-size: 20px;
    }

    .wf-page-subtitle {
        font-size: 13px;
    }

    .wf-action-button {
        padding: 8px 12px;
        font-size: 12px;
    }

    .wf-sidebar-nav {
        grid-template-columns: 1fr;
    }

    .wf-nav-item {
        padding: 10px 12px;
    }

    .wf-table-wrapper {
        overflow-x: auto;
    }

    .wf-table {
        min-width: 400px;
    }

    .wf-card-header {
        padding: 12px;
    }

    .wf-card-body {
        padding: 12px;
    }

    .wf-empty-state {
        padding: 40px 20px;
    }

    .wf-empty-icon {
        font-size: 36px;
    }

    .wf-empty-title {
        font-size: 16px;
    }

    .wf-empty-text {
        font-size: 12px;
    }
}
</style>

<script>
    function viewWorkflow(id) {
        // Redirect to admin workflow view
        window.location.href = '<?= url("/admin/workflows") ?>/' + id;
    }
</script>

<?php \App\Core\View::endSection(); ?>
