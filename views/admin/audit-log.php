<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>
<?php
// Ensure pagination variables are accessible
$currentPage = $pagination['current_page'] ?? 1;
$totalPages = $pagination['last_page'] ?? 1;
?>

<style>
    /* ============================================
   AUDIT LOG - ENTERPRISE JIRA DESIGN
   ============================================ */

    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6F123F;
        --jira-blue-light: #E77817;
        --jira-dark: #161B22;
        --jira-gray: #626F86;
        --jira-light: #F7F8FA;
        --jira-border: #DFE1E6;
        --jira-white: #FFFFFF;
        --transition-speed: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ============ BREADCRUMB NAVIGATION ============ */
    .audit-breadcrumb {
        background: var(--jira-white);
        border-bottom: 1px solid var(--jira-border);
        padding: 12px 32px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .breadcrumb-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
        transition: color var(--transition-speed);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--jira-gray);
        margin: 0 4px;
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-weight: 600;
    }

    /* ============ PAGE HEADER ============ */
    .audit-page-header {
        background: var(--jira-white);
        border-bottom: 1px solid var(--jira-border);
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .header-left {
        flex: 1;
    }

    .page-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0 0 4px 0;
        letter-spacing: -0.2px;
    }

    .page-meta {
        font-size: 12px;
        color: var(--jira-gray);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--jira-light);
        padding: 3px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
        color: var(--jira-gray);
    }

    .header-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        color: var(--jira-dark);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all var(--transition-speed);
    }

    .action-button:hover {
        background: var(--jira-light);
        border-color: #B6C2CF;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        color: var(--jira-dark);
    }

    .action-button.primary {
        background: var(--jira-blue);
        border-color: var(--jira-blue);
        color: var(--jira-white);
    }

    .action-button.primary:hover {
        background: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
        color: var(--jira-white);
    }

    /* ============ MAIN CONTENT ============ */
    .audit-content-wrapper {
        background: var(--jira-light);
        padding: 20px 24px;
        min-height: calc(100vh - 300px);
    }

    /* ============ FILTERS CARD ============ */
    .filters-card {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
    }

    .filters-header {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--jira-border);
    }

    .filters-header h3 {
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .filter-select {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        padding: 6px 10px;
        font-size: 13px;
        color: var(--jira-dark);
        transition: all var(--transition-speed);
        cursor: pointer;
    }

    .filter-select:hover {
        border-color: #B6C2CF;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
        padding-top: 6px;
    }

    .btn-filter {
        background: var(--jira-blue);
        color: var(--jira-white);
        border: none;
        border-radius: 4px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-speed);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter:hover {
        background: var(--jira-blue-dark);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(139, 25, 86, 0.2);
    }

    .btn-clear {
        background: transparent;
        color: var(--jira-gray);
        border: none;
        padding: 7px 12px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: color var(--transition-speed);
    }

    .btn-clear:hover {
        color: var(--jira-blue);
        text-decoration: underline;
    }

    /* ============ TABLE CARD ============ */
    .table-card {
        background: var(--jira-white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
    }

    .table-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--jira-border);
        background: var(--jira-light);
    }

    .table-header h3 {
        font-size: 14px;
        font-weight: 600;
        color: var(--jira-dark);
        margin: 0;
    }

    .audit-table-wrapper {
        overflow-x: auto;
    }

    .audit-table {
        width: 100%;
        border-collapse: collapse;
    }

    .audit-table thead {
        background: var(--jira-light);
        border-bottom: 2px solid var(--jira-border);
    }

    .audit-table th {
        padding: 10px 16px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .audit-table tbody tr {
        border-bottom: 1px solid var(--jira-border);
        transition: background var(--transition-speed);
    }

    .audit-table tbody tr:hover {
        background: var(--jira-light);
    }

    .audit-table tbody tr:last-child {
        border-bottom: none;
    }

    .audit-table td {
        padding: 12px 16px;
        font-size: 13px;
        color: var(--jira-dark);
        vertical-align: middle;
    }

    /* ============ TABLE COMPONENTS ============ */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--jira-blue);
        color: var(--jira-white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .user-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--jira-dark);
    }

    .timestamp-text {
        font-size: 12px;
        color: var(--jira-gray);
        font-variant-numeric: tabular-nums;
    }

    .action-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-create {
        background: rgba(54, 179, 126, 0.1);
        color: #006644;
    }

    .badge-update {
        background: rgba(0, 82, 204, 0.1);
        color: #0747A6;
    }

    .badge-delete {
        background: rgba(255, 86, 48, 0.1);
        color: #BF2600;
    }

    .badge-login {
        background: rgba(139, 25, 86, 0.1);
        color: var(--jira-blue);
    }

    .badge-logout {
        background: rgba(98, 111, 134, 0.1);
        color: var(--jira-gray);
    }

    .badge-permission {
        background: rgba(255, 171, 0, 0.1);
        color: #974F0C;
    }

    .entity-cell {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .entity-type {
        font-size: 13px;
        font-weight: 500;
        color: var(--jira-dark);
    }

    .entity-id {
        font-size: 11px;
        font-family: 'Monaco', 'Courier New', monospace;
        color: var(--jira-gray);
    }

    .description-text {
        font-size: 12px;
        color: var(--jira-gray);
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ip-text {
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 11px;
        color: var(--jira-gray);
        background: var(--jira-light);
        padding: 3px 6px;
        border-radius: 3px;
        display: inline-block;
    }

    .details-btn {
        background: none;
        border: 1px solid var(--jira-border);
        color: var(--jira-gray);
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all var(--transition-speed);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .details-btn:hover {
        background: var(--jira-blue);
        border-color: var(--jira-blue);
        color: var(--jira-white);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(139, 25, 86, 0.2);
    }

    /* ============ EMPTY STATE ============ */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.3;
    }

    .empty-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--jira-dark);
        margin-bottom: 8px;
    }

    .empty-text {
        font-size: 13px;
        color: var(--jira-gray);
    }

    /* ============ PAGINATION ============ */
    .pagination-wrapper {
        padding: 12px 16px;
        background: var(--jira-light);
        border-top: 1px solid var(--jira-border);
        display: flex;
        justify-content: center;
        gap: 4px;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 6px;
        border: 1px solid var(--jira-border);
        background: var(--jira-white);
        color: var(--jira-dark);
        border-radius: 4px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all var(--transition-speed);
    }

    .page-btn:hover {
        background: var(--jira-light);
        border-color: var(--jira-blue);
        color: var(--jira-blue);
    }

    .page-btn.active {
        background: var(--jira-blue);
        border-color: var(--jira-blue);
        color: var(--jira-white);
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 1024px) {
        .audit-content-wrapper {
            padding: 20px;
        }

        .filters-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .audit-breadcrumb {
            padding: 10px 16px;
            font-size: 12px;
        }

        .audit-page-header {
            padding: 20px 16px;
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .page-title {
            font-size: 24px;
        }

        .header-actions {
            width: 100%;
        }

        .action-button {
            flex: 1;
            justify-content: center;
        }

        .audit-content-wrapper {
            padding: 16px;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn-filter,
        .btn-clear {
            width: 100%;
            justify-content: center;
        }

        /* Hide less important columns on mobile */
        .audit-table th:nth-child(6),
        .audit-table td:nth-child(6),
        .audit-table th:nth-child(1),
        .audit-table td:nth-child(1) {
            display: none;
        }

        .description-text {
            max-width: 150px;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 20px;
        }

        .filters-card,
        .table-card {
            border-radius: 6px;
        }

        /* Show only essential columns on small mobile */
        .audit-table th:nth-child(5),
        .audit-table td:nth-child(5) {
            display: none;
        }
    }
</style>

<!-- Breadcrumb Navigation -->
<div class="audit-breadcrumb">
    <a href="<?= url('/admin') ?>" class="breadcrumb-link">
        <i class="bi bi-gear"></i> Administration
    </a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Audit Log</span>
</div>

<!-- Page Header -->
<div class="audit-page-header">
    <div class="header-left">
        <h1 class="page-title">Audit Log</h1>
        <div class="page-meta">
            <span class="meta-badge">
                <i class="bi bi-journal-text"></i>
                <?= number_format($totalEntries ?? 0) ?> Total Entries
            </span>
        </div>
    </div>
    <div class="header-actions">
        <button class="action-button primary" onclick="exportLog()">
            <i class="bi bi-download"></i>
            Export CSV
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="audit-content-wrapper">

    <!-- Filters Card -->
    <div class="filters-card">
        <div class="filters-header">
            <i class="bi bi-funnel"></i>
            <h3>Filter Activities</h3>
        </div>

        <form action="<?= url('/admin/audit-log') ?>" method="GET">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Action Type</label>
                    <select name="action" class="filter-select">
                        <option value="">All Actions</option>
                        <option value="login" <?= ($filters['action'] ?? '') === 'login' ? 'selected' : '' ?>>Login
                        </option>
                        <option value="logout" <?= ($filters['action'] ?? '') === 'logout' ? 'selected' : '' ?>>Logout
                        </option>
                        <option value="create" <?= ($filters['action'] ?? '') === 'create' ? 'selected' : '' ?>>Create
                        </option>
                        <option value="update" <?= ($filters['action'] ?? '') === 'update' ? 'selected' : '' ?>>Update
                        </option>
                        <option value="delete" <?= ($filters['action'] ?? '') === 'delete' ? 'selected' : '' ?>>Delete
                        </option>
                        <option value="permission" <?= ($filters['action'] ?? '') === 'permission' ? 'selected' : '' ?>>
                            Permission</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Entity Type</label>
                    <select name="entity" class="filter-select">
                        <option value="">All Entities</option>
                        <option value="user" <?= ($filters['entity'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="project" <?= ($filters['entity'] ?? '') === 'project' ? 'selected' : '' ?>>Project
                        </option>
                        <option value="issue" <?= ($filters['entity'] ?? '') === 'issue' ? 'selected' : '' ?>>Issue
                        </option>
                        <option value="role" <?= ($filters['entity'] ?? '') === 'role' ? 'selected' : '' ?>>Role</option>
                        <option value="settings" <?= ($filters['entity'] ?? '') === 'settings' ? 'selected' : '' ?>>
                            Settings</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">User</label>
                    <select name="user_id" class="filter-select">
                        <option value="">All Users</option>
                        <?php foreach ($users ?? [] as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                <?= e($u['display_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Time Period</label>
                    <select name="date_range" class="filter-select">
                        <option value="">All Time</option>
                        <option value="today" <?= ($filters['date_range'] ?? '') === 'today' ? 'selected' : '' ?>>Today
                        </option>
                        <option value="week" <?= ($filters['date_range'] ?? '') === 'week' ? 'selected' : '' ?>>This Week
                        </option>
                        <option value="month" <?= ($filters['date_range'] ?? '') === 'month' ? 'selected' : '' ?>>This
                            Month</option>
                        <option value="quarter" <?= ($filters['date_range'] ?? '') === 'quarter' ? 'selected' : '' ?>>Last
                            3 Months</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <i class="bi bi-search"></i> Apply Filters
                </button>
                <a href="<?= url('/admin/audit-log') ?>" class="btn-clear">
                    Clear All
                </a>
            </div>
        </form>
    </div>

    <!-- Activity Table Card -->
    <div class="table-card">
        <div class="table-header">
            <h3>System Activities</h3>
        </div>

        <div class="audit-table-wrapper">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th style="width: 160px;">Timestamp</th>
                        <th style="width: 180px;">User</th>
                        <th style="width: 120px;">Action</th>
                        <th style="width: 150px;">Entity</th>
                        <th>Description</th>
                        <th style="width: 130px;">IP Address</th>
                        <th style="width: 80px; text-align: center;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($entries)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">📋</div>
                                    <div class="empty-title">No activities found</div>
                                    <div class="empty-text">Try adjusting your filters to see more results</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td>
                                    <span class="timestamp-text"><?= format_datetime($entry['created_at']) ?></span>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($entry['user_name'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <span class="user-name"><?= e($entry['user_name'] ?? 'System') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = match ($entry['action']) {
                                        'create' => 'badge-create',
                                        'update' => 'badge-update',
                                        'delete' => 'badge-delete',
                                        'login' => 'badge-login',
                                        'logout' => 'badge-logout',
                                        'permission' => 'badge-permission',
                                        default => 'badge-logout'
                                    };
                                    ?>
                                    <span class="action-badge <?= $badgeClass ?>">
                                        <?= ucfirst($entry['action']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="entity-cell">
                                        <span class="entity-type"><?= ucfirst($entry['entity_type'] ?? '-') ?></span>
                                        <?php if ($entry['entity_id'] ?? null): ?>
                                            <span class="entity-id">ID: <?= $entry['entity_id'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="description-text" title="<?= e($entry['description'] ?? '') ?>">
                                        <?= e($entry['description'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="ip-text"><?= e($entry['ip_address'] ?? '-') ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <button class="details-btn"
                                        onclick="showDetails(<?= htmlspecialchars(json_encode($entry)) ?>)"
                                        data-bs-toggle="modal" data-bs-target="#detailModal" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (($totalPages ?? 1) > 1): ?>
            <div class="pagination-wrapper">
                <?php if ($currentPage > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="page-btn" title="First Page">
                        &laquo;
                    </a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" class="page-btn"
                        title="Previous Page">
                        &lsaquo;
                    </a>
                <?php endif; ?>

                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                        class="page-btn <?= $i === $currentPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>" class="page-btn"
                        title="Next Page">
                        &rsaquo;
                    </a>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>" class="page-btn"
                        title="Last Page">
                        &raquo;
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="bi bi-info-circle me-2"></i>Activity Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modal Specific Styles */
    .modal-header {
        background: var(--jira-light);
        border-bottom: 2px solid var(--jira-border);
    }

    .modal-title {
        color: var(--jira-dark);
        font-weight: 700;
    }

    .detail-row {
        padding: 16px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-size: 14px;
        color: var(--jira-dark);
        font-weight: 500;
    }

    .detail-code {
        background: var(--jira-light);
        padding: 12px;
        border-radius: 6px;
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 12px;
        color: var(--jira-gray);
        word-break: break-all;
        line-height: 1.5;
    }

    .changes-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 12px;
    }

    .changes-section h6 {
        font-size: 13px;
        font-weight: 700;
        color: var(--jira-dark);
        margin-bottom: 8px;
    }

    .changes-content {
        background: var(--jira-light);
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        padding: 12px;
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 11px;
        color: var(--jira-gray);
        max-height: 300px;
        overflow-y: auto;
        white-space: pre-wrap;
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .changes-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
    function showDetails(entry) {
        const content = document.getElementById('detailContent');
        const hasChanges = entry.old_values || entry.new_values;

        let html = `
        <div class="detail-row">
            <div class="detail-label">Timestamp</div>
            <div class="detail-value">${entry.created_at}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">User</div>
            <div class="detail-value">${entry.user_name || 'System'} <span style="color: #626F86;">(ID: ${entry.user_id || 'N/A'})</span></div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Action</div>
            <div class="detail-value" style="text-transform: capitalize;">${entry.action}</div>
        </div>
        <div class="detail-row">
                <div class="detai            l-label">Entity</div>
            <div class="detail-value">${entry.entity_type || 'N/A'} ${entry.entity_id ? '<span style="color: #626F86;">(ID: ' + entry.entity_id + ')</span>' : ''}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Description</div>
            <div class="detail-value">${entry.description || 'No description available'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">IP Address</div>
            <div class="detail-code">${entry.ip_address || 'N/A'}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">User Agent</div>
            <div class="detail-code">${entry.user_agent || 'N/A'}</div>
        </div>
    `;

        if (hasChanges) {
            html += `
            <div class="detail-row">
                <div class="detail-label">Changes</div>
                <div class="changes-grid">
                    <div class="changes-section">
                        <h6>Old Values</h6>
                        <div class="changes-content">${JSON.stringify(entry.old_values || {}, null, 2)}</div>
                    </div>
                    <div class="changes-section">
                        <h6>New Values</h6>
                        <div class="changes-content">${JSON.stringify(entry.new_values || {}, null, 2)}</div>
                    </div>
                </div>
            </div>
        `;
        }

        content.innerHTML = html;
    }

    function exportLog() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'csv');
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }
</script>
<?php \App\Core\View::endSection(); ?>