<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<style>
:root {
  --jira-blue: #8B1956;
  --jira-blue-dark: #6F123F;
  --jira-blue-light: #E77817;
  --text-primary: #161B22;
  --text-secondary: #626F86;
  --bg-primary: #FFFFFF;
  --bg-secondary: #F7F8FA;
  --border-color: #DFE1E6;
  --danger: #ED3C32;
  --success: #216E4E;
  --warning: #974F0C;
  --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
  --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
  --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
  --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ============ BREADCRUMB NAVIGATION ============ */
.audit-breadcrumb-nav {
  padding: 12px 32px;
  font-size: 13px;
  color: var(--text-secondary);
  background: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
  display: flex;
  align-items: center;
  gap: 8px;
}

.audit-breadcrumb-nav a {
  color: var(--jira-blue);
  text-decoration: none;
  font-weight: 500;
  transition: color var(--transition);
}

.audit-breadcrumb-nav a:hover {
  color: var(--jira-blue-dark);
}

.audit-breadcrumb-nav span {
  color: var(--text-secondary);
  margin: 0 0;
}

.audit-breadcrumb-nav .breadcrumb-current {
  color: var(--text-primary);
  font-weight: 600;
}

/* ============ PAGE HEADER ============ */
.audit-page-header {
  background: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
  padding: 24px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 24px;
}

.audit-header-left {
  flex: 1;
}

.audit-page-header h1 {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  padding: 0;
  letter-spacing: -0.3px;
}

.audit-page-header p {
  font-size: 14px;
  color: var(--text-secondary);
  margin: 6px 0 0 0;
}

.audit-header-right {
  display: flex;
  gap: 12px;
  align-items: center;
}

/* ============ CONTAINER ============ */
.audit-log-container {
  background: var(--bg-secondary);
  padding: 24px 32px;
  min-height: calc(100vh - 200px);
}

/* ============ FILTERS SECTION ============ */

.audit-filters {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 24px;
  box-shadow: var(--shadow-sm);
}

.audit-filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.audit-filter-group {
  display: flex;
  flex-direction: column;
}

.audit-filter-group label {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.audit-filter-group select {
  border: 1px solid var(--border-color);
  border-radius: 6px;
  padding: 8px 12px;
  font-size: 14px;
  color: var(--text-primary);
  background: var(--bg-primary);
  transition: all var(--transition);
}

.audit-filter-group select:focus {
  outline: none;
  border-color: var(--primary-main);
  box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

.audit-filter-actions {
  display: flex;
  gap: 8px;
  align-items: flex-end;
}

/* ============ BUTTONS ============ */
.audit-export-btn {
  padding: 10px 20px;
  background: var(--jira-blue);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.audit-export-btn:hover {
  background: var(--jira-blue-dark);
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

.audit-filter-btn {
  padding: 10px 20px;
  background: var(--jira-blue);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
}

.audit-filter-btn:hover {
  background: var(--jira-blue-dark);
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

.audit-clear-btn {
  padding: 10px 20px;
  background: var(--bg-primary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all var(--transition);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.audit-clear-btn:hover {
  background: var(--bg-secondary);
  border-color: var(--jira-blue);
  color: var(--jira-blue);
}

.audit-table-container {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}

.audit-table-header {
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-color);
  background: var(--bg-secondary);
  font-size: 14px;
  color: var(--text-secondary);
  font-weight: 600;
}

.audit-table {
  width: 100%;
  border-collapse: collapse;
}

.audit-table thead {
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border-color);
}

.audit-table th {
  padding: 12px 16px;
  text-align: left;
  font-size: 13px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.audit-table tbody tr {
  border-bottom: 1px solid var(--border-color);
  transition: background var(--transition);
}

.audit-table tbody tr:hover {
  background: var(--bg-secondary);
}

.audit-table td {
  padding: 14px 16px;
  font-size: 14px;
  color: var(--text-primary);
  vertical-align: middle;
}

.audit-timestamp {
  font-size: 13px;
  color: var(--text-secondary);
  font-variant-numeric: tabular-nums;
}

.audit-user {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* ============ USER AVATAR ============ */
.audit-user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 600;
  flex-shrink: 0;
  flex-basis: 32px;
}

.audit-user-name {
  font-size: 14px;
  color: var(--text-primary);
  font-weight: 500;
}

.audit-action-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.audit-action-login {
  background: rgba(33, 110, 78, 0.1);
  color: var(--success);
}

.audit-action-logout {
  background: rgba(98, 111, 134, 0.1);
  color: var(--text-secondary);
}

.audit-action-create {
  background: rgba(139, 25, 86, 0.1);
  color: var(--jira-blue);
}

.audit-action-update {
  background: rgba(15, 105, 185, 0.1);
  color: #0F69B9;
}

.audit-action-delete {
  background: rgba(237, 60, 50, 0.1);
  color: var(--danger);
}

.audit-action-permission {
  background: rgba(151, 79, 12, 0.1);
  color: var(--warning);
}

.audit-entity {
  font-size: 13px;
  color: var(--text-secondary);
}

.audit-entity-id {
  font-weight: 600;
  color: var(--text-primary);
}

.audit-description {
  font-size: 13px;
  color: var(--text-secondary);
  max-width: 300px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.audit-ip {
  font-family: 'Monaco', 'Courier New', monospace;
  font-size: 12px;
  color: var(--text-secondary);
  background: var(--bg-secondary);
  padding: 2px 6px;
  border-radius: 3px;
}

/* ============ ACTION BUTTON ============ */
.audit-action-btn {
  padding: 8px 12px;
  background: var(--bg-secondary);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  border-radius: 4px;
  font-size: 13px;
  cursor: pointer;
  transition: all var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 36px;
  min-height: 36px;
}

.audit-action-btn:hover {
  background: var(--jira-blue);
  color: white;
  border-color: var(--jira-blue);
  box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

.audit-empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary);
}

.audit-empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.audit-empty-text {
  font-size: 16px;
  font-weight: 500;
  margin-bottom: 8px;
}

.audit-empty-sub {
  font-size: 13px;
  color: var(--text-secondary);
}

.audit-pagination {
  padding: 16px 20px;
  border-top: 1px solid var(--border-color);
  background: var(--bg-secondary);
  display: flex;
  justify-content: center;
  gap: 4px;
}

.audit-pagination-btn {
  padding: 6px 12px;
  border: 1px solid var(--border-color);
  background: var(--bg-primary);
  color: var(--text-primary);
  border-radius: 4px;
  font-size: 13px;
  cursor: pointer;
  transition: all var(--transition);
}

/* ============ PAGINATION ============ */
.audit-pagination-btn:hover {
  border-color: var(--jira-blue);
  color: var(--jira-blue);
}

.audit-pagination-btn.active {
  background: var(--jira-blue);
  color: white;
  border-color: var(--jira-blue);
}

/* ============ RESPONSIVE DESIGN ============ */
@media (max-width: 1024px) {
  .audit-log-container {
    padding: 20px 24px;
  }

  .audit-page-header {
    padding: 20px 24px;
  }

  .audit-filters-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .audit-table th, .audit-table td {
    padding: 12px 14px;
    font-size: 13px;
  }

  .audit-filters {
    padding: 18px;
  }
}

@media (max-width: 768px) {
  .audit-log-container {
    padding: 16px 20px;
  }

  .audit-page-header {
    padding: 16px 20px;
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .audit-header-right {
    width: 100%;
  }

  .audit-export-btn {
    width: 100%;
    justify-content: center;
  }

  .audit-breadcrumb-nav {
    padding: 10px 20px;
    font-size: 12px;
  }

  .audit-filters-grid {
    grid-template-columns: 1fr;
    gap: 14px;
  }

  .audit-filter-actions {
    flex-direction: column;
    gap: 10px;
  }

  .audit-filter-btn, .audit-clear-btn {
    width: 100%;
    justify-content: center;
  }

  .audit-table th:nth-child(n+5) {
    display: none;
  }

  .audit-table td:nth-child(n+5) {
    display: none;
  }

  .audit-table th, .audit-table td {
    padding: 10px 12px;
    font-size: 12px;
  }

  .audit-description {
    max-width: 180px;
    font-size: 12px;
  }

  .audit-user-name {
    font-size: 13px;
  }
}

@media (max-width: 576px) {
  .audit-log-container {
    padding: 12px 16px;
  }

  .audit-page-header {
    padding: 12px 16px;
  }

  .audit-page-header h1 {
    font-size: 22px;
  }

  .audit-page-header p {
    font-size: 12px;
  }

  .audit-breadcrumb-nav {
    padding: 8px 16px;
    font-size: 11px;
  }

  .audit-filters {
    padding: 14px;
    margin-bottom: 18px;
  }

  .audit-export-btn {
    padding: 8px 14px;
    font-size: 12px;
  }

  .audit-filter-btn {
    padding: 8px 14px;
    font-size: 12px;
  }

  .audit-clear-btn {
    padding: 8px 14px;
    font-size: 12px;
  }

  .audit-table th:nth-child(n+4) {
    display: none;
  }

  .audit-table td:nth-child(n+4) {
    display: none;
  }

  .audit-table th, .audit-table td {
    padding: 10px 8px;
    font-size: 11px;
  }

  .audit-timestamp {
    font-size: 11px;
  }

  .audit-user-avatar {
    width: 28px;
    height: 28px;
    font-size: 10px;
  }

  .audit-user-name {
    font-size: 12px;
  }

  .audit-description {
    max-width: 120px;
    font-size: 11px;
  }

  .audit-action-badge {
    padding: 3px 8px;
    font-size: 10px;
  }

  .audit-action-btn {
    padding: 6px 8px;
    font-size: 11px;
    min-width: 32px;
    min-height: 32px;
  }

  .audit-pagination {
    padding: 12px 14px;
    gap: 2px;
  }

  .audit-pagination-btn {
    padding: 6px 10px;
    font-size: 11px;
  }
}

@media (max-width: 480px) {
  .audit-page-header h1 {
    font-size: 18px;
  }

  .audit-filters-grid {
    gap: 10px;
  }

  .audit-table th:nth-child(n+3) {
    display: none;
  }

  .audit-table td:nth-child(n+3) {
    display: none;
  }

  .audit-description {
    max-width: 100px;
    font-size: 10px;
  }

  .audit-timestamp {
    font-size: 10px;
  }

  .audit-user {
    gap: 6px;
  }

  .audit-user-avatar {
    width: 26px;
    height: 26px;
    font-size: 9px;
  }
}
</style>

<!-- Breadcrumb Navigation -->
<div class="audit-breadcrumb-nav">
    <a href="<?= url('/admin') ?>">
        <i class="bi bi-gear" style="margin-right: 6px;"></i>Administration
    </a>
    <span>/</span>
    <span class="breadcrumb-current">Audit Log</span>
</div>

<!-- Page Header -->
<div class="audit-page-header">
    <div class="audit-header-left">
        <h1>Audit Log</h1>
        <p>System Activity Log - <?= $totalEntries ?? 0 ?> entries found</p>
    </div>
    <div class="audit-header-right">
        <button class="audit-export-btn" onclick="exportLog()" title="Export audit log as CSV">
            <i class="bi bi-download"></i> Export CSV
        </button>
    </div>
</div>

<div class="audit-log-container">

    <!-- Filters -->
    <div class="audit-filters">
        <form action="<?= url('/admin/audit-log') ?>" method="GET">
            <div class="audit-filters-grid">
                <div class="audit-filter-group">
                    <label for="filter-action">Action Type</label>
                    <select id="filter-action" name="action">
                        <option value="">All Actions</option>
                        <option value="login" <?= ($filters['action'] ?? '') === 'login' ? 'selected' : '' ?>>Login</option>
                        <option value="logout" <?= ($filters['action'] ?? '') === 'logout' ? 'selected' : '' ?>>Logout</option>
                        <option value="create" <?= ($filters['action'] ?? '') === 'create' ? 'selected' : '' ?>>Create</option>
                        <option value="update" <?= ($filters['action'] ?? '') === 'update' ? 'selected' : '' ?>>Update</option>
                        <option value="delete" <?= ($filters['action'] ?? '') === 'delete' ? 'selected' : '' ?>>Delete</option>
                        <option value="permission" <?= ($filters['action'] ?? '') === 'permission' ? 'selected' : '' ?>>Permission Change</option>
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label for="filter-entity">Entity Type</label>
                    <select id="filter-entity" name="entity">
                        <option value="">All Entities</option>
                        <option value="user" <?= ($filters['entity'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="project" <?= ($filters['entity'] ?? '') === 'project' ? 'selected' : '' ?>>Project</option>
                        <option value="issue" <?= ($filters['entity'] ?? '') === 'issue' ? 'selected' : '' ?>>Issue</option>
                        <option value="role" <?= ($filters['entity'] ?? '') === 'role' ? 'selected' : '' ?>>Role</option>
                        <option value="settings" <?= ($filters['entity'] ?? '') === 'settings' ? 'selected' : '' ?>>Settings</option>
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label for="filter-user">User</label>
                    <select id="filter-user" name="user_id">
                        <option value="">All Users</option>
                        <?php foreach ($users ?? [] as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($filters['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= e($u['display_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label for="filter-date">Date Range</label>
                    <select id="filter-date" name="date_range">
                        <option value="">All Time</option>
                        <option value="today" <?= ($filters['date_range'] ?? '') === 'today' ? 'selected' : '' ?>>Today</option>
                        <option value="week" <?= ($filters['date_range'] ?? '') === 'week' ? 'selected' : '' ?>>This Week</option>
                        <option value="month" <?= ($filters['date_range'] ?? '') === 'month' ? 'selected' : '' ?>>This Month</option>
                        <option value="quarter" <?= ($filters['date_range'] ?? '') === 'quarter' ? 'selected' : '' ?>>Last 3 Months</option>
                    </select>
                </div>
            </div>

            <div class="audit-filter-actions" style="margin-top: 16px;">
                <button type="submit" class="audit-filter-btn">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="<?= url('/admin/audit-log') ?>" class="audit-clear-btn" style="text-decoration: none;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Audit Log Table -->
    <div class="audit-table-container">
        <div class="audit-table-header">
            System Activity Log
        </div>
        <div style="overflow-x: auto;">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th style="width: 50px; text-align: center;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries ?? [] as $entry): ?>
                    <tr>
                        <td>
                            <span class="audit-timestamp"><?= format_datetime($entry['created_at']) ?></span>
                        </td>
                        <td>
                            <div class="audit-user">
                                <div class="audit-user-avatar" title="<?= e($entry['user_name'] ?? 'System') ?>">
                                    <?= strtoupper(substr($entry['user_name'] ?? 'S', 0, 1)) ?>
                                </div>
                                <span class="audit-user-name"><?= e($entry['user_name'] ?? 'System') ?></span>
                            </div>
                        </td>
                        <td>
                            <?php 
                            $action = $entry['action'];
                            $actionClass = 'audit-action-' . $action;
                            ?>
                            <span class="audit-action-badge <?= $actionClass ?>">
                                <?= ucfirst($action) ?>
                            </span>
                        </td>
                        <td>
                            <div class="audit-entity">
                                <div><?= ucfirst($entry['entity_type'] ?? '-') ?></div>
                                <?php if ($entry['entity_id'] ?? null): ?>
                                <div class="audit-entity-id">#<?= $entry['entity_id'] ?></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="audit-description" title="<?= e($entry['description'] ?? '') ?>">
                                <?= e($entry['description'] ?? '-') ?>
                            </span>
                        </td>
                        <td>
                            <span class="audit-ip"><?= e($entry['ip_address'] ?? '-') ?></span>
                        </td>
                        <td style="text-align: center;">
                            <button class="audit-action-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal" 
                                    onclick="showDetails(<?= htmlspecialchars(json_encode($entry)) ?>)"
                                    title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($entries)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="audit-empty-state">
                                <div class="audit-empty-icon">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div class="audit-empty-text">No audit log entries found</div>
                                <div class="audit-empty-sub">Try adjusting your filters or date range</div>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (($totalPages ?? 1) > 1): ?>
        <div class="audit-pagination">
            <?php if ($currentPage > 1): ?>
            <a class="audit-pagination-btn" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">
                &laquo; First
            </a>
            <a class="audit-pagination-btn" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>">
                &lsaquo; Previous
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
            <a class="audit-pagination-btn <?= $i === $currentPage ? 'active' : '' ?>" 
               href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
            <a class="audit-pagination-btn" href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>">
                Next &rsaquo;
            </a>
            <a class="audit-pagination-btn" href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>">
                Last &raquo;
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border: 1px solid var(--border-color); box-shadow: var(--shadow-lg);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color); padding: 20px 24px; background: var(--bg-primary);">
                <h5 class="modal-title" id="detailModalTitle" style="font-size: 18px; font-weight: 600; color: var(--text-primary);">
                    <i class="bi bi-journal-text me-2" style="color: var(--jira-blue);"></i>Audit Log Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: opacity(0.5);"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background: var(--bg-secondary);">
                <div id="detailContent"></div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 16px 24px; background: var(--bg-primary);">
                <button type="button" class="btn" data-bs-dismiss="modal" 
                        style="padding: 10px 20px; background: var(--jira-blue); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: all var(--transition); font-size: 14px;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Styles -->
<style>
/* ============ DETAIL MODAL STYLES ============ */
.detail-item {
  margin-bottom: 16px;
  padding: 14px;
  background: var(--bg-primary);
  border-radius: 6px;
  border: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
}

.detail-label {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 6px;
}

.detail-value {
  font-size: 14px;
  color: var(--text-primary);
  font-weight: 500;
}

.detail-code {
  background: var(--bg-secondary);
  padding: 10px;
  border-radius: 4px;
  border: 1px solid var(--border-color);
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Courier New', monospace;
  font-size: 12px;
  color: var(--text-secondary);
  word-break: break-all;
  line-height: 1.4;
}

.detail-changes {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-top: 12px;
}

.detail-changes-section h6 {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 10px;
  padding-bottom: 8px;
  border-bottom: 1px solid var(--border-color);
}

.detail-changes-pre {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  padding: 10px;
  border-radius: 4px;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Courier New', monospace;
  font-size: 11px;
  color: var(--text-secondary);
  max-height: 200px;
  overflow-y: auto;
  line-height: 1.4;
}

@media (max-width: 768px) {
  .detail-changes {
    grid-template-columns: 1fr;
  }

  .detail-item {
    padding: 12px;
    margin-bottom: 14px;
  }

  .detail-value {
    font-size: 13px;
  }

  .detail-code {
    font-size: 11px;
    padding: 8px;
  }
}

@media (max-width: 576px) {
  .detail-item {
    padding: 10px;
    margin-bottom: 12px;
  }

  .detail-label {
    font-size: 11px;
  }

  .detail-value {
    font-size: 12px;
  }

  .detail-code {
    font-size: 10px;
    padding: 6px;
  }

  .detail-changes-section h6 {
    font-size: 12px;
  }

  .detail-changes-pre {
    font-size: 10px;
    padding: 8px;
    max-height: 150px;
  }
}
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
function showDetails(entry) {
    const content = document.getElementById('detailContent');
    
    let html = `
        <div class="detail-item">
            <div class="detail-label">Timestamp</div>
            <div class="detail-value">${entry.created_at}</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">User</div>
            <div class="detail-value">${entry.user_name || 'System'} <span style="color: #626F86;">(ID: ${entry.user_id || '-'})</span></div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Action</div>
            <div class="detail-value" style="text-transform: capitalize; font-weight: 600; color: #8B1956;">${entry.action}</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Entity Type</div>
            <div class="detail-value">${entry.entity_type || '-'}</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Entity ID</div>
            <div class="detail-value">${entry.entity_id || '-'}</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">Description</div>
            <div class="detail-value">${entry.description || '-'}</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">IP Address</div>
            <div class="detail-code">${entry.ip_address || '-'}</div>
        </div>
        
        <div class="detail-item">
            <div class="detail-label">User Agent</div>
            <div class="detail-code" style="word-break: break-word;">${entry.user_agent || '-'}</div>
        </div>
    `;
    
    if (entry.old_values || entry.new_values) {
        html += `
            <div class="detail-item">
                <div class="detail-label">Changes</div>
                <div class="detail-changes">
                    <div class="detail-changes-section">
                        <h6>Old Values</h6>
                        <pre class="detail-changes-pre">${JSON.stringify(entry.old_values || {}, null, 2)}</pre>
                    </div>
                    <div class="detail-changes-section">
                        <h6>New Values</h6>
                        <pre class="detail-changes-pre">${JSON.stringify(entry.new_values || {}, null, 2)}</pre>
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
