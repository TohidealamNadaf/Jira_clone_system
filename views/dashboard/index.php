<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<style>
:root {
  --jira-blue: #8B1956;
  --jira-blue-dark: #6F123F;
  --jira-light-blue: #f0dce5;
  --text-primary: #161B22;
  --text-secondary: #626F86;
  --bg-primary: #FFFFFF;
  --bg-secondary: #F7F8FA;
  --border-color: #DFE1E6;
  --success: #216E4E;
  --warning: #974F0C;
  --danger: #ED3C32;
  --info: #E77817;
  --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
  --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
  --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
  --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
  box-sizing: border-box;
}

/* Page Wrapper - Inherits background from layout */
.dashboard-wrapper {
  background: transparent;
  min-height: auto;
  padding: 0;
  margin: 0;
}

/* Header - Minimalist Design */
.dashboard-header-section {
  background: var(--bg-primary);
  border-bottom: 1px solid var(--border-color);
  box-shadow: var(--shadow-sm);
}

.dashboard-header-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 24px;
}

.dashboard-header-left {
  flex: 1;
  min-width: 300px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.dashboard-avatar {
  width: 48px;
  height: 48px;
  border-radius: 6px;
  flex-shrink: 0;
  overflow: hidden;
  background: var(--bg-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: 700;
  color: var(--jira-blue);
  border: 1px solid var(--border-color);
}

.dashboard-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.dashboard-greeting {
  flex: 1;
}

.dashboard-greeting-title {
  font-size: 20px;
  font-weight: 600;
  margin: 0 0 4px 0;
  color: var(--text-primary);
}

.dashboard-greeting-stats {
  font-size: 13px;
  color: var(--text-secondary);
  display: flex;
  gap: 16px;
  align-items: center;
}

.dashboard-greeting-stats span {
  display: flex;
  align-items: center;
  gap: 4px;
}

.dashboard-header-right {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.dashboard-btn {
  padding: 8px 16px;
  border-radius: 6px;
  border: 1px solid var(--border-color);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  background: var(--bg-primary);
  color: var(--text-primary);
}

.dashboard-btn-primary {
  background: var(--jira-blue);
  color: white;
  border-color: var(--jira-blue);
}

.dashboard-btn-primary:hover {
  background: var(--jira-blue-dark);
  border-color: var(--jira-blue-dark);
  transform: translateY(-2px);
}

.dashboard-btn-secondary {
  background: var(--bg-primary);
  color: var(--text-primary);
  border-color: var(--border-color);
}

.dashboard-btn-secondary:hover {
  background: var(--bg-secondary);
  border-color: var(--jira-blue);
}

/* Main Container */
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px;
}

/* Stats Grid */
.dashboard-stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
}

.stat-card {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-left: 4px solid var(--jira-blue);
  border-radius: 8px;
  padding: 20px;
  box-shadow: var(--shadow-sm);
  text-decoration: none;
  color: inherit;
  transition: all var(--transition);
  display: flex;
  align-items: center;
  gap: 16px;
  cursor: pointer;
}

.stat-card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

.stat-card.danger {
  border-left-color: var(--danger);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
  background: var(--bg-secondary);
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  line-height: 1;
}

.stat-label {
  font-size: 13px;
  color: var(--text-secondary);
  margin-top: 4px;
  font-weight: 500;
}

/* Main Grid */
.dashboard-grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 24px;
  margin-bottom: 24px;
}

/* Card Styling */
.card {
  background: var(--bg-primary);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
}

.card-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
  background: var(--bg-primary);
}

.card-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.card-body {
  padding: 0;
}

/* Tabs */
.tabs-wrapper {
  display: flex;
  border-bottom: 2px solid var(--border-color);
  margin: 0;
  padding: 0 24px;
  background: var(--bg-primary);
}

.tab-button {
  padding: 12px 16px;
  border: none;
  background: transparent;
  color: var(--text-secondary);
  cursor: pointer;
  font-weight: 600;
  font-size: 14px;
  position: relative;
  transition: all var(--transition);
  border-bottom: 2px solid transparent;
  margin-bottom: -2px;
}

.tab-button:hover {
  color: var(--text-primary);
}

.tab-button.active {
  color: var(--jira-blue);
  border-bottom-color: var(--jira-blue);
}

.tab-badge {
  background: var(--jira-blue);
  color: white;
  border-radius: 12px;
  padding: 2px 8px;
  font-size: 11px;
  font-weight: 700;
  margin-left: 6px;
  display: inline-block;
}

/* Filters */
.filters-section {
  padding: 16px 24px;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  background: var(--bg-primary);
}

.filter-btn {
  padding: 6px 12px;
  border: 1px solid var(--border-color);
  background: white;
  color: var(--text-primary);
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 500;
  transition: all var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.filter-btn:hover {
  background: var(--bg-secondary);
  border-color: var(--jira-blue);
}

.filter-btn.active {
  background: var(--jira-blue);
  color: white;
  border-color: var(--jira-blue);
}

/* Issue List */
.issue-list {
  max-height: 500px;
  overflow-y: auto;
}

.issue-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  border-bottom: 1px solid var(--border-color);
  text-decoration: none;
  color: inherit;
  transition: all var(--transition);
}

.issue-row:last-child {
  border-bottom: none;
}

.issue-row:hover {
  background: var(--bg-secondary);
}

.issue-type-icon {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 14px;
  flex-shrink: 0;
  font-weight: 700;
}

.issue-key {
  font-weight: 700;
  color: var(--jira-blue);
  min-width: 80px;
  font-size: 13px;
}

.issue-summary {
  flex: 1;
  font-size: 13px;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 700;
  color: white;
  white-space: nowrap;
}

.badge-danger {
  background: var(--danger);
}

.badge-warning {
  background: var(--warning);
}

.badge-success {
  background: var(--success);
}

.issue-status {
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 700;
  color: white;
  white-space: nowrap;
}

.issue-priority {
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 700;
  color: white;
  white-space: nowrap;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-secondary);
}

.empty-icon {
  font-size: 56px;
  opacity: 0.5;
  margin-bottom: 12px;
}

.empty-text {
  font-size: 14px;
  margin: 0 0 16px 0;
  font-weight: 500;
}

/* Sidebar */
.sidebar {
  display: flex;
  flex-direction: column;
  /* gap: 5px; */
}

/* Project List */
.project-list {
  max-height: 400px;
  overflow-y: auto;
}

.project-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 24px;
  border-bottom: 1px solid var(--border-color);
  text-decoration: none;
  color: inherit;
  transition: all var(--transition);
}

.project-row:last-child {
  border-bottom: none;
}

.project-row:hover {
  background: var(--bg-secondary);
}

.project-avatar {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 12px;
  font-weight: 700;
  color: white;
  background: var(--jira-blue);
}

.project-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 6px;
}

.project-info {
  flex: 1;
  min-width: 0;
}

.project-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.project-key {
  font-size: 12px;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.project-badge {
  background: var(--jira-blue);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 700;
  flex-shrink: 0;
}

/* Workload */
.workload-row {
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--border-color);
}

.workload-row:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.workload-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 6px;
  gap: 8px;
}

.workload-project {
  text-decoration: none;
  color: var(--jira-blue);
  font-weight: 600;
  font-size: 12px;
  flex: 1;
}

.workload-project:hover {
  text-decoration: underline;
}

.workload-count {
  color: var(--text-secondary);
  font-size: 11px;
  font-weight: 500;
  flex-shrink: 0;
}

.progress-bar {
  height: 4px;
  background: var(--bg-secondary);
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: var(--jira-blue);
  transition: width var(--transition);
}

/* Status Distribution */
.status-section {
  padding-top: 12px;
  border-top: 1px solid var(--border-color);
}

.status-title {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 10px 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.status-grid {
  display: flex;
  gap: 12px;
  align-items: flex-start;
}

.status-pie {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  flex-shrink: 0;
}

.status-legend {
  flex: 1;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px 0;
  font-size: 11px;
}

.status-dot {
  width: 10px;
  height: 10px;
  border-radius: 2px;
  flex-shrink: 0;
}

.status-name {
  flex: 1;
  color: var(--text-primary);
  font-weight: 500;
}

.status-count {
  color: var(--text-secondary);
  font-size: 10px;
  font-weight: 600;
}

/* Activity List */
.activity-list {
  max-height: 400px;
  overflow-y: auto;
}

.activity-row {
  display: flex;
  gap: 10px;
  padding: 10px 24px;
  border-bottom: 1px solid var(--border-color);
}

.activity-row:last-child {
  border-bottom: none;
}

.activity-icon {
  width: 28px;
  height: 28px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
  background: var(--bg-secondary);
}

.activity-content {
  flex: 1;
  min-width: 0;
}

.activity-text {
  font-size: 12px;
  color: var(--text-primary);
  line-height: 1.3;
  margin-bottom: 2px;
}

.activity-text strong {
  font-weight: 600;
}

.activity-text a {
  color: var(--jira-blue);
  text-decoration: none;
  font-weight: 600;
}

.activity-text a:hover {
  text-decoration: underline;
}

.activity-time {
  font-size: 10px;
  color: var(--text-secondary);
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .dashboard-stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .sidebar {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: 24px;
  }

  .dashboard-header-content {
    padding: 20px 24px 40px 24px;
    flex-direction: column;
    align-items: flex-start;
  }

  .dashboard-header-left {
    flex-direction: column;
    width: 100%;
  }

  .dashboard-stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .stat-card {
    padding: 16px;
  }

  .tabs-wrapper {
    padding: 0 16px;
  }

  .filters-section {
    padding: 12px 16px;
  }

  .issue-row,
  .project-row,
  .activity-row {
    padding: 12px 16px;
  }

  .card-header {
    padding: 16px;
  }

  .card-body {
    padding: 16px;
  }
}

@media (max-width: 480px) {
  .dashboard-container {
    padding: 16px;
  }

  .dashboard-header-content {
    padding: 16px;
  }

  .dashboard-stats-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .stat-card {
    padding: 12px;
    gap: 12px;
  }

  .stat-icon {
    width: 40px;
    height: 40px;
    font-size: 18px;
  }

  .stat-value {
    font-size: 20px;
  }

  .issue-row,
  .project-row {
    padding: 10px 12px;
    gap: 8px;
  }

  .issue-summary {
    display: none;
  }

  .issue-key {
    min-width: 60px;
  }

  .sidebar {
    grid-template-columns: 1fr;
  }
}
</style>

<div class="dashboard-wrapper">
  <!-- Header Section -->
  <div class="dashboard-header-section">
    <div class="dashboard-header-content">
      <div class="dashboard-header-left">
        <div class="dashboard-avatar">
          <?php if ($currentUser['avatar'] ?? null): ?>
            <img src="<?= e($currentUser['avatar']) ?>" alt="<?= e($currentUser['first_name'] ?? 'User') ?>">
          <?php else: ?>
            <?= strtoupper(substr($currentUser['first_name'] ?? 'U', 0, 1)) ?>
          <?php endif; ?>
        </div>
        <div class="dashboard-greeting">
          <h1 class="dashboard-greeting-title">Welcome, <?= e($currentUser['first_name'] ?? 'User') ?></h1>
          <div class="dashboard-greeting-stats">
            <span><i class="bi bi-person-check"></i> <?= $stats['assigned_count'] ?? 0 ?> Assigned</span>
            <span><i class="bi bi-play-circle"></i> <?= $stats['in_progress'] ?? 0 ?> In Progress</span>
          </div>
        </div>
      </div>
      <div class="dashboard-header-right">
        <button class="dashboard-btn dashboard-btn-primary" data-bs-toggle="modal" data-bs-target="#quickCreateModal">
          <i class="bi bi-plus-lg"></i> Create
        </button>
        <a href="<?= url('/search?assignee=currentUser()') ?>" class="dashboard-btn dashboard-btn-secondary">
          <i class="bi bi-search"></i> View All
        </a>
      </div>
    </div>
  </div>

  <div class="dashboard-container">
    <!-- Statistics Grid -->
    <div class="dashboard-stats-grid">
      <a href="<?= url('/search?assignee=currentUser()') ?>" class="stat-card">
        <div class="stat-icon">👤</div>
        <div>
          <div class="stat-value"><?= $stats['assigned_count'] ?? 0 ?></div>
          <div class="stat-label">Assigned to Me</div>
        </div>
      </a>

      <a href="<?= url('/search?reporter=currentUser()') ?>" class="stat-card">
        <div class="stat-icon">📝</div>
        <div>
          <div class="stat-value"><?= $stats['reported_count'] ?? 0 ?></div>
          <div class="stat-label">Reported by Me</div>
        </div>
      </a>

      <a href="<?= url('/search?assignee=currentUser()&status=In+Progress') ?>" class="stat-card">
        <div class="stat-icon">▶️</div>
        <div>
          <div class="stat-value"><?= $stats['in_progress'] ?? 0 ?></div>
          <div class="stat-label">In Progress</div>
        </div>
      </a>

      <a href="<?= url('/search?assignee=currentUser()&due=week') ?>" class="stat-card">
        <div class="stat-icon">⏰</div>
        <div>
          <div class="stat-value"><?= $stats['due_soon'] ?? 0 ?></div>
          <div class="stat-label">Due This Week</div>
        </div>
      </a>

      <a href="<?= url('/search?assignee=currentUser()&due=overdue') ?>" class="stat-card <?= ($stats['overdue'] ?? 0) > 0 ? 'danger' : '' ?>">
        <div class="stat-icon">⚠️</div>
        <div>
          <div class="stat-value"><?= $stats['overdue'] ?? 0 ?></div>
          <div class="stat-label">Overdue</div>
        </div>
      </a>
    </div>

    <!-- Main Grid -->
    <div class="dashboard-grid">
      <!-- Left Column -->
      <div>
        <!-- Your Work Card -->
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="bi bi-kanban"></i> Your Work
            </h2>
            <a href="<?= url('/search?assignee=currentUser()') ?>" class="dashboard-btn dashboard-btn-secondary">
              View All
            </a>
          </div>

          <!-- Tabs -->
          <div class="tabs-wrapper" role="tablist">
            <button class="tab-button active" id="assigned-tab" data-bs-toggle="tab" data-bs-target="#assigned-panel" role="tab">
              Assigned to Me <span class="tab-badge"><?= count($assignedIssues ?? []) ?></span>
            </button>
            <button class="tab-button" id="reported-tab" data-bs-toggle="tab" data-bs-target="#reported-panel" role="tab">
              Reported <span class="tab-badge"><?= count($reportedIssues ?? []) ?></span>
            </button>
            <button class="tab-button" id="watched-tab" data-bs-toggle="tab" data-bs-target="#watched-panel" role="tab">
              <i class="bi bi-eye"></i> Watching <span class="tab-badge"><?= count($watchedIssues ?? []) ?></span>
            </button>
          </div>

          <!-- Filters -->
          <div class="filters-section" id="filterButtonsContainer">
            <button type="button" class="filter-btn active" onclick="dashboardFilterIssues('all', event);">
              All
            </button>
            <button type="button" class="filter-btn" onclick="dashboardFilterIssues('high-priority', event);">
              <i class="bi bi-arrow-up"></i> High Priority
            </button>
            <button type="button" class="filter-btn" onclick="dashboardFilterIssues('due-soon', event);">
              <i class="bi bi-clock"></i> Due Soon
            </button>
            <button type="button" class="filter-btn" onclick="dashboardFilterIssues('updated-today', event);">
              <i class="bi bi-calendar-check"></i> Updated Today
            </button>
          </div>

          <!-- Tab Content -->
          <div class="card-body">
            <div class="tab-content">
              <!-- Assigned Panel -->
              <div class="tab-pane fade show active" id="assigned-panel" role="tabpanel">
                <?php if (empty($assignedIssues)): ?>
                  <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <p class="empty-text">No issues assigned to you</p>
                    <button class="dashboard-btn dashboard-btn-primary" data-bs-toggle="modal" data-bs-target="#quickCreateModal">
                      Create your first issue
                    </button>
                  </div>
                <?php else: ?>
                  <div class="issue-list">
                    <?php foreach ($assignedIssues as $issue): ?>
                      <?php
                        $dueBucket = 'none';
                        if (!empty($issue['due_date'])) {
                          $dueTs = strtotime($issue['due_date']);
                          $today = strtotime('today');
                          $week = strtotime('+7 days', $today);
                          if ($dueTs < $today) $dueBucket = 'overdue';
                          elseif ($dueTs <= $week) $dueBucket = 'due-soon';
                        }
                        $updatedToday = date('Y-m-d', strtotime($issue['updated_at'])) === date('Y-m-d');
                        $prioritySlug = strtolower(str_replace(' ', '-', $issue['priority_name'] ?? ''));
                      ?>
                      <a href="<?= url('/issue/' . $issue['issue_key']) ?>"
                         class="issue-row"
                         data-priority="<?= e($prioritySlug) ?>"
                         data-due="<?= e($dueBucket) ?>"
                         data-updated-today="<?= $updatedToday ? 'true' : 'false' ?>">
                        <div class="issue-type-icon" style="background-color: <?= e($issue['issue_type_color'] ?? 'var(--jira-blue)') ?>">
                          <i class="bi bi-<?= e($issue['issue_type_icon'] ?? 'list-check') ?>"></i>
                        </div>
                        <div class="issue-key"><?= e($issue['issue_key']) ?></div>
                        <div class="issue-summary"><?= e($issue['summary']) ?></div>
                        <?php if ($dueBucket === 'overdue'): ?>
                          <span class="badge badge-danger">
                            <i class="bi bi-exclamation-triangle"></i> Overdue
                          </span>
                        <?php elseif ($dueBucket === 'due-soon'): ?>
                          <span class="badge badge-warning">
                            <i class="bi bi-clock"></i> Due soon
                          </span>
                        <?php endif; ?>
                        <span class="issue-priority" style="background-color: <?= e($issue['priority_color'] ?? '#626F86') ?>">
                          <?= e(substr($issue['priority_name'] ?? 'None', 0, 1)) ?>
                        </span>
                        <span class="issue-status" style="background-color: <?= e($issue['status_color'] ?? 'var(--jira-blue)') ?>; color: white !important;">
                          <?= e($issue['status_name'] ?? 'New') ?>
                        </span>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Reported Panel -->
              <div class="tab-pane fade" id="reported-panel" role="tabpanel">
                <?php if (empty($reportedIssues)): ?>
                  <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <p class="empty-text">No issues reported by you</p>
                  </div>
                <?php else: ?>
                  <div class="issue-list">
                    <?php foreach ($reportedIssues as $issue): ?>
                      <?php
                        $dueBucket = 'none';
                        if (!empty($issue['due_date'])) {
                          $dueTs = strtotime($issue['due_date']);
                          $today = strtotime('today');
                          $week = strtotime('+7 days', $today);
                          if ($dueTs < $today) $dueBucket = 'overdue';
                          elseif ($dueTs <= $week) $dueBucket = 'due-soon';
                        }
                        $updatedToday = date('Y-m-d', strtotime($issue['updated_at'])) === date('Y-m-d');
                        $prioritySlug = strtolower(str_replace(' ', '-', $issue['priority_name'] ?? ''));
                      ?>
                      <a href="<?= url('/issue/' . $issue['issue_key']) ?>"
                         class="issue-row"
                         data-priority="<?= e($prioritySlug) ?>"
                         data-due="<?= e($dueBucket) ?>"
                         data-updated-today="<?= $updatedToday ? 'true' : 'false' ?>">
                        <div class="issue-type-icon" style="background-color: <?= e($issue['issue_type_color'] ?? 'var(--jira-blue)') ?>">
                          <i class="bi bi-<?= e($issue['issue_type_icon'] ?? 'list-check') ?>"></i>
                        </div>
                        <div class="issue-key"><?= e($issue['issue_key']) ?></div>
                        <div class="issue-summary"><?= e($issue['summary']) ?></div>
                        <span class="issue-status" style="background-color: <?= e($issue['status_color'] ?? 'var(--jira-blue)') ?>; color: white !important;">
                          <?= e($issue['status_name'] ?? 'New') ?>
                        </span>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Watched Panel -->
              <div class="tab-pane fade" id="watched-panel" role="tabpanel">
                <?php if (empty($watchedIssues)): ?>
                  <div class="empty-state">
                    <div class="empty-icon">👁️</div>
                    <p class="empty-text">You aren't watching any issues</p>
                  </div>
                <?php else: ?>
                  <div class="issue-list">
                    <?php foreach ($watchedIssues as $issue): ?>
                      <?php
                        $dueBucket = 'none';
                        if (!empty($issue['due_date'])) {
                          $dueTs = strtotime($issue['due_date']);
                          $today = strtotime('today');
                          $week = strtotime('+7 days', $today);
                          if ($dueTs < $today) $dueBucket = 'overdue';
                          elseif ($dueTs <= $week) $dueBucket = 'due-soon';
                        }
                        $updatedToday = date('Y-m-d', strtotime($issue['updated_at'])) === date('Y-m-d');
                        $prioritySlug = strtolower(str_replace(' ', '-', $issue['priority_name'] ?? ''));
                      ?>
                      <a href="<?= url('/issue/' . $issue['issue_key']) ?>"
                         class="issue-row"
                         data-priority="<?= e($prioritySlug) ?>"
                         data-due="<?= e($dueBucket) ?>"
                         data-updated-today="<?= $updatedToday ? 'true' : 'false' ?>">
                        <div class="issue-type-icon" style="background-color: <?= e($issue['issue_type_color'] ?? 'var(--jira-blue)') ?>">
                          <i class="bi bi-<?= e($issue['issue_type_icon'] ?? 'list-check') ?>"></i>
                        </div>
                        <div class="issue-key"><?= e($issue['issue_key']) ?></div>
                        <div class="issue-summary"><?= e($issue['summary']) ?></div>
                        <i class="bi bi-eye" style="color: var(--text-secondary);"></i>
                        <span class="issue-status" style="background-color: <?= e($issue['status_color'] ?? 'var(--jira-blue)') ?>; color: white !important;">
                          <?= e($issue['status_name'] ?? 'New') ?>
                        </span>
                      </a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Sidebar -->
      <div class="sidebar">
        <!-- Projects Card -->
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="bi bi-folder"></i> Projects
            </h2>
            <a href="<?= url('/projects') ?>" class="dashboard-btn dashboard-btn-secondary">
              All
            </a>
          </div>
          <div class="card-body">
            <?php if (empty($projects)): ?>
              <div class="empty-state">
                <div class="empty-icon">📁</div>
                <p class="empty-text">No projects yet</p>
              </div>
            <?php else: ?>
              <div class="project-list">
                <?php foreach ($projects as $project): ?>
                  <a href="<?= url('/projects/' . $project['key']) ?>" class="project-row">
                    <div class="project-avatar">
                      <?= strtoupper(substr($project['key'], 0, 2)) ?>
                    </div>
                    <div class="project-info">
                      <div class="project-name"><?= e($project['name']) ?></div>
                      <div class="project-key"><?= e($project['key']) ?></div>
                    </div>
                    <span class="project-badge"><?= $project['open_issues'] ?? 0 ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Workload Card -->
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="bi bi-pie-chart"></i> My Workload
            </h2>
          </div>
          <div class="card-body" style="padding: 20px 24px;">
            <?php if (empty($workloadByProject)): ?>
              <p class="empty-text">No open issues assigned to you.</p>
            <?php else: ?>
              <?php $totalWork = array_sum(array_column($workloadByProject, 'issue_count')); ?>
              <?php foreach ($workloadByProject as $row): ?>
                <?php $percent = $totalWork > 0 ? round(($row['issue_count'] / $totalWork) * 100) : 0; ?>
                <div class="workload-row">
                  <div class="workload-header">
                    <a href="<?= url('/projects/' . $row['key']) ?>" class="workload-project">
                      <?= e($row['key']) ?>
                    </a>
                    <span class="workload-count"><?= $row['issue_count'] ?></span>
                  </div>
                  <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $percent ?>%"></div>
                  </div>
                </div>
              <?php endforeach; ?>

              <?php if (!empty($statusDistribution)): ?>
                <div class="status-section">
                  <h3 class="status-title">Status Distribution</h3>
                  <?php
                    $total = array_sum(array_column($statusDistribution, 'issue_count'));
                    $startAngle = 0;
                    $segments = [];
                    foreach ($statusDistribution as $row) {
                      $angle = $total > 0 ? ($row['issue_count'] / $total) * 360 : 0;
                      $endAngle = $startAngle + $angle;
                      $segments[] = sprintf('%s %sdeg %sdeg', $row['color'], $startAngle, $endAngle);
                      $startAngle = $endAngle;
                    }
                    $gradient = implode(', ', $segments);
                  ?>
                  <div class="status-grid">
                    <div class="status-pie" style="background: conic-gradient(<?= $gradient ?>);"></div>
                    <div class="status-legend">
                      <?php foreach ($statusDistribution as $row): ?>
                        <div class="status-item">
                          <span class="status-dot" style="background-color: <?= e($row['color'] ?? '#DFE1E6') ?>"></span>
                          <span class="status-name"><?= e($row['name'] ?? 'Unknown') ?></span>
                          <span class="status-count"><?= $row['issue_count'] ?></span>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Activity Stream Card -->
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">
              <i class="bi bi-activity"></i> Activity Stream
            </h2>
          </div>
          <div class="card-body">
            <?php if (empty($recentActivity)): ?>
              <div class="empty-state">
                <div class="empty-icon">🔔</div>
                <p class="empty-text">No recent activity</p>
              </div>
            <?php else: ?>
              <div class="activity-list">
                <?php foreach ($recentActivity as $activity): ?>
                  <?php
                    $field = $activity['field'] ?? '';
                    $icon = 'bi-pencil-square';
                    $color = '#626F86';
                    if ($field === 'status') { $icon = 'bi-arrow-left-right'; $color = '#8B1956'; }
                    elseif ($field === 'assignee') { $icon = 'bi-person-check'; $color = '#216E4E'; }
                    elseif ($field === 'comment') { $icon = 'bi-chat-dots'; $color = '#216E4E'; }
                    elseif ($field === 'priority') { $icon = 'bi-exclamation-triangle'; $color = '#974F0C'; }
                    elseif ($field === 'created') { $icon = 'bi-plus-circle'; $color = '#216E4E'; }
                  ?>
                  <div class="activity-row">
                    <div class="activity-icon" style="color: <?= $color ?>;">
                      <i class="bi <?= $icon ?>"></i>
                    </div>
                    <div class="activity-content">
                      <div class="activity-text">
                        <strong><?= e($activity['user_name'] ?? 'System') ?></strong>
                        <?php if ($field === 'comment'): ?>
                          commented on
                        <?php elseif ($field === 'created'): ?>
                          created
                        <?php else: ?>
                          updated <em><?= e($field) ?></em> on
                        <?php endif; ?>
                        <a href="<?= url('/issue/' . $activity['issue_key']) ?>"><?= e($activity['issue_key']) ?></a>
                      </div>
                      <div class="activity-time"><?= time_ago($activity['created_at']) ?></div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function dashboardFilterIssues(filterType, event) {
  if (event) {
    event.preventDefault();
  }

  // Update active button
  var buttons = document.querySelectorAll('#filterButtonsContainer .filter-btn');
  buttons.forEach(function(btn) {
    btn.classList.remove('active');
  });

  if (event && event.currentTarget) {
    event.currentTarget.classList.add('active');
  }

  // Filter issue rows
  var issueRows = document.querySelectorAll('.issue-row');

  issueRows.forEach(function(row) {
    var priority = row.getAttribute('data-priority') || '';
    var due = row.getAttribute('data-due') || '';
    var updatedToday = row.getAttribute('data-updated-today') === 'true';

    var visible = true;
    switch (filterType) {
      case 'high-priority':
        visible = (priority === 'high' || priority === 'highest' || priority === 'critical');
        break;
      case 'due-soon':
        visible = (due === 'due-soon' || due === 'overdue');
        break;
      case 'updated-today':
        visible = updatedToday;
        break;
      default:
        visible = true;
    }

    row.style.display = visible ? 'flex' : 'none';
  });
}
</script>

<?php \App\Core\View::endSection(); ?>
