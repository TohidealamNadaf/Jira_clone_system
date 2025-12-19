<?php
declare(strict_types=1);

/**
 * Notifications Center Page - Enterprise Jira-Like Design
 * Professional notification management with theme-aware styling
 */

use App\Core\View;

View::extends('layouts.app');
View::section('content');
?>

<style>
/* ===== CSS VARIABLES (INHERIT FROM APP THEME) ===== */
:root {
    --color-primary: #8B1956;
    --color-primary-dark: #6F123F;
    --color-primary-light: #E77817;
    --text-primary: #161B22;
    --text-secondary: #626F86;
    --text-tertiary: #656D76;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    --bg-tertiary: #F6FAFF;
    --border-color: #DFE1E6;
    --border-light: #D0D7DE;
    --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Force plum theme on all notification page links */
.notifications-wrapper a {
    color: var(--color-primary) !important;
    text-decoration: none !important;
}

.notifications-wrapper a:hover {
    color: var(--color-primary-dark) !important;
    text-decoration: underline !important;
}

/* ===== PAGE WRAPPER ===== */
.notifications-wrapper {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 80px);
    background: var(--bg-secondary);
}

/* ===== BREADCRUMB ===== */
.breadcrumb-nav {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    padding: 12px 32px;
    font-size: 13px;
    box-shadow: var(--shadow-sm);
}

.breadcrumb-nav a {
    color: var(--color-primary) !important;
    text-decoration: none !important;
    transition: color var(--transition);
}

.breadcrumb-nav a:hover {
    color: var(--color-primary-dark) !important;
    text-decoration: underline !important;
}

.breadcrumb-nav a i {
    margin-right: 6px;
}

.breadcrumb-separator {
    color: var(--text-tertiary);
    margin: 0 8px;
}

.breadcrumb-current {
    color: var(--text-primary);
    font-weight: 500;
}

/* ===== PAGE HEADER ===== */
.page-header {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    padding: 16px 32px;
}

.page-header-content {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
}

.header-title-section {
    display: flex;
    align-items: center;
    gap: 16px;
    flex: 1;
    min-width: 0;
}

.header-title-section h1 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    letter-spacing: -0.2px;
}

.header-title-section p {
    margin: 0;
    font-size: 13px;
    color: var(--text-secondary);
    white-space: nowrap;
}

.header-actions {
    flex-shrink: 0;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all var(--transition);
    background: var(--color-primary);
    color: white;
}

.btn:hover {
    background: var(--color-primary-dark);
}

.btn:active {
    transform: scale(0.98);
}

/* ===== MAIN CONTENT ===== */
.notifications-content {
    flex: 1;
    padding: 24px 32px;
    max-width: 1400px;
    margin: 0 auto;
    width: 100%;
    box-sizing: border-box;
    display: flex;
    gap: 24px;
}

/* ===== SIDEBAR ===== */
.notifications-sidebar {
    width: 300px;
    flex-shrink: 0;
}

.sidebar-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.sidebar-card:hover {
    box-shadow: var(--shadow-md);
}

.sidebar-card:last-child {
    margin-bottom: 0;
}

.sidebar-card h6 {
    margin: 0 0 16px 0;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Stat Items */
.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-light);
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    font-size: 13px;
    color: var(--text-secondary);
    font-weight: 500;
}

.stat-value {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
}

/* Sidebar Links */
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    color: var(--color-primary) !important;
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 500;
    transition: all var(--transition);
    border-bottom: 1px solid var(--border-light);
}

.sidebar-link:last-child {
    border-bottom: none;
}

.sidebar-link:hover {
    color: var(--color-primary-dark) !important;
    text-decoration: underline !important;
}

.sidebar-link i {
    font-size: 16px;
    flex-shrink: 0;
}

/* ===== MAIN NOTIFICATIONS AREA ===== */
.notifications-main {
    flex: 1;
    min-width: 0;
}

/* Filter Tabs */
.filter-section {
    margin-bottom: 20px;
}

.filter-tabs {
    display: flex;
    gap: 24px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0;
}

.filter-tab {
    background: none;
    border: none;
    padding: 12px 0;
    margin-bottom: -1px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    border-bottom: 2px solid transparent !important;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-tab:hover {
    color: var(--text-primary) !important;
}

.filter-tab.active {
    color: var(--color-primary) !important;
    border-bottom-color: var(--color-primary) !important;
}

.filter-count {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    background: var(--bg-secondary);
    padding: 2px 6px;
    border-radius: 12px;
}

.filter-tab.active .filter-count {
    background: rgba(139, 25, 86, 0.1);
    color: var(--color-primary);
}

/* Notifications List */
.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 40px;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    text-align: center;
}

.empty-icon {
    font-size: 72px;
    color: var(--color-primary);
    opacity: 0.2;
    margin-bottom: 16px;
}

.empty-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.empty-text {
    font-size: 14px;
    color: var(--text-secondary);
}

/* Notification Card */
.notification-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 16px 20px;
    transition: all var(--transition);
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.notification-card.unread {
    border-left: 3px solid var(--color-primary);
    background: var(--bg-tertiary);
}

.notification-card:hover {
    border-color: var(--border-light);
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

/* Badge */
.notification-badge {
    flex-shrink: 0;
    margin-top: 2px;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
    letter-spacing: 0.3px;
    background: rgba(139, 25, 86, 0.1);
    color: var(--color-primary);
}

/* Card Body */
.notification-body {
    flex: 1;
    min-width: 0;
}

.notification-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.notification-title {
    font-size: 15px;
    font-weight: 500;
    color: var(--color-primary) !important;
    text-decoration: none !important;
    transition: color var(--transition);
}

.notification-title:hover {
    color: var(--color-primary-dark) !important;
    text-decoration: underline !important;
}

.new-badge {
    display: inline-block;
    padding: 2px 8px;
    background: var(--color-primary);
    color: white;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}

.notification-message {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 8px 0;
    line-height: 1.5;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
}

.notification-time {
    color: var(--text-tertiary);
}

.priority-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    background: #ffeceb;
    color: #ae2a19;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 11px;
}

/* Card Actions */
.notification-actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}

.action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-primary);
    color: var(--text-secondary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition);
    font-size: 14px;
}

.action-btn:hover {
    color: var(--text-primary);
    border-color: var(--color-primary);
    background: rgba(139, 25, 86, 0.05);
}

.action-btn.delete-btn:hover {
    color: #da3633;
    border-color: #ffcccc;
    background: #fff0ef;
}

/* Pagination */
.pagination-section {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}

.pagination {
    display: inline-flex;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination li {
    margin: 0;
}

.pagination-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-primary);
    color: var(--color-primary) !important;
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition);
}

.pagination-link:hover:not(.active) {
    background: var(--bg-secondary);
    border-color: var(--color-primary) !important;
}

.pagination-link.active {
    background: var(--color-primary) !important;
    border-color: var(--color-primary) !important;
    color: white !important;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1023px) {
    .notifications-content {
        flex-direction: column;
        padding: 20px 24px;
    }

    .notifications-sidebar {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .sidebar-card {
        margin-bottom: 0;
    }

    .page-header {
        padding: 14px 24px;
    }

    .breadcrumb-nav {
        padding: 12px 24px;
    }

    .header-title-section h1 {
        font-size: 18px;
    }
}

@media (max-width: 767px) {
    .page-header-content {
        flex-direction: row;
        align-items: center;
    }

    .header-title-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .header-title-section h1 {
        font-size: 18px;
    }

    .header-title-section p {
        font-size: 12px;
    }

    .header-actions {
        flex-shrink: 0;
    }

    .btn {
        padding: 8px 12px;
        font-size: 13px;
    }

    .notifications-content {
        flex-direction: column;
        padding: 16px 16px;
    }

    .notifications-sidebar {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .filter-tabs {
        gap: 16px;
    }

    .notification-card {
        flex-direction: column;
        gap: 12px;
    }

    .notification-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .breadcrumb-nav {
        padding: 12px 16px;
        font-size: 12px;
    }

    .page-header {
        padding: 20px 16px;
    }

    .pagination {
        flex-wrap: wrap;
        gap: 6px;
    }

    .pagination-link {
        padding: 6px 10px;
        font-size: 12px;
    }
}

@media (max-width: 479px) {
    .page-header {
        padding: 12px 16px;
    }

    .header-title-section h1 {
        font-size: 16px;
    }

    .header-title-section p {
        font-size: 11px;
    }

    .sidebar-card {
        padding: 16px;
    }

    .notification-card {
        padding: 12px 16px;
    }

    .notification-title {
        font-size: 14px;
    }

    .filter-tab {
        font-size: 13px;
    }

    .breadcrumb-nav {
        padding: 10px 16px;
        font-size: 11px;
    }
}
</style>

<div class="notifications-wrapper">
    <!-- Breadcrumb -->
    <div class="breadcrumb-nav">
        <a href="<?= url('/dashboard') ?>"><i class="bi bi-house"></i> Home</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Notifications</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="header-title-section">
                <h1>Notifications</h1>
                <p><?= $unreadCount ?> unread</p>
            </div>
            <?php if ($unreadCount > 0): ?>
                <div class="header-actions">
                    <button id="markAllReadBtn" class="btn">
                        <i class="bi bi-check-all"></i> Mark All as Read
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="notifications-content">
        <!-- Sidebar -->
        <aside class="notifications-sidebar">
            <div class="sidebar-card">
                <h6>Statistics</h6>
                <div class="stat-item">
                    <span class="stat-label">Total</span>
                    <span class="stat-value"><?= $totalCount ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Unread</span>
                    <span class="stat-value unread-count"><?= $unreadCount ?></span>
                </div>
            </div>

            <div class="sidebar-card">
                <h6>Preferences</h6>
                <a href="<?= url('/profile/notifications') ?>" class="sidebar-link">
                    <i class="bi bi-gear"></i> Notification Settings
                </a>
            </div>

            <div class="sidebar-card">
                <h6>Quick Links</h6>
                <a href="<?= url('/dashboard') ?>" class="sidebar-link">
                    <i class="bi bi-house"></i> Dashboard
                </a>
                <a href="<?= url('/projects') ?>" class="sidebar-link">
                    <i class="bi bi-kanban"></i> Projects
                </a>
                <a href="<?= url('/issues') ?>" class="sidebar-link">
                    <i class="bi bi-list-check"></i> Issues
                </a>
                <a href="<?= url('/search') ?>" class="sidebar-link">
                    <i class="bi bi-search"></i> Search
                </a>
            </div>
        </aside>

        <!-- Main Notifications Area -->
        <div class="notifications-main">
            <!-- Filter Tabs -->
            <div class="filter-section">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        All <span class="filter-count"><?= $totalCount ?></span>
                    </button>
                    <button class="filter-tab" data-filter="unread">
                        Unread <span class="filter-count"><?= $unreadCount ?></span>
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="notifications-list">
                <?php if (empty($notifications)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-bell-slash"></i>
                        </div>
                        <h5 class="empty-title">All caught up!</h5>
                        <p class="empty-text">You have no notifications</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-card <?= $notification['is_read'] ? '' : 'unread' ?>" 
                             data-notification-id="<?= $notification['id'] ?>">
                            <div class="notification-badge">
                                <span class="badge"><?= formatNotificationType($notification['type']) ?></span>
                            </div>
                            <div class="notification-body">
                                <div class="notification-title-row">
                                    <a href="<?= htmlspecialchars($notification['action_url'] ?? '#') ?>" class="notification-title">
                                        <?= htmlspecialchars($notification['title']) ?>
                                    </a>
                                    <?php if (!$notification['is_read']): ?>
                                        <span class="new-badge">New</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($notification['message']): ?>
                                    <p class="notification-message">
                                        <?= htmlspecialchars($notification['message']) ?>
                                    </p>
                                <?php endif; ?>
                                <div class="notification-meta">
                                    <span class="notification-time"><?= formatTime($notification['created_at']) ?></span>
                                    <?php if ($notification['priority'] === 'high'): ?>
                                        <span class="priority-badge">
                                            <i class="bi bi-exclamation-circle-fill"></i> High
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="notification-actions">
                                <?php if (!$notification['is_read']): ?>
                                    <button class="action-btn mark-read-btn" 
                                            data-notification-id="<?= $notification['id'] ?>"
                                            title="Mark as read">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="action-btn delete-btn" 
                                        data-notification-id="<?= $notification['id'] ?>"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="pagination-section">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li><a href="?page=1" class="pagination-link"><i class="bi bi-chevron-bar-left"></i></a></li>
                            <li><a href="?page=<?= $page - 1 ?>" class="pagination-link"><i class="bi bi-chevron-left"></i></a></li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li><a href="?page=<?= $i ?>" class="pagination-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a></li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li><a href="?page=<?= $page + 1 ?>" class="pagination-link"><i class="bi bi-chevron-right"></i></a></li>
                            <li><a href="?page=<?= $totalPages ?>" class="pagination-link"><i class="bi bi-chevron-bar-right"></i></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const APP_BASE_URL = '<?= rtrim(url("/"), "/") ?>';

document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeActions();
});

function initializeFilters() {
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.notification-card').forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'flex';
                } else if (filter === 'unread') {
                    card.style.display = card.classList.contains('unread') ? 'flex' : 'none';
                }
            });
        });
    });
}

function initializeActions() {
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            const notificationId = btn.dataset.notificationId;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const url = `${APP_BASE_URL}/api/v1/notifications/${notificationId}/read`;
                
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken || ''
                    }
                });

                if (response.ok) {
                    const card = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    card?.classList.remove('unread');
                    btn.remove();
                    updateStats();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (!confirm('Delete notification?')) return;

            const notificationId = btn.dataset.notificationId;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const url = `${APP_BASE_URL}/api/v1/notifications/${notificationId}`;
                
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-Token': csrfToken || ''
                    }
                });

                if (response.ok) {
                    const card = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    card?.remove();
                    updateStats();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });

    document.getElementById('markAllReadBtn')?.addEventListener('click', async (e) => {
        e.preventDefault();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const url = `${APP_BASE_URL}/api/v1/notifications/read-all`;
            
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken || ''
                }
            });

            if (response.ok) {
                document.querySelectorAll('.notification-card.unread').forEach(card => {
                    card.classList.remove('unread');
                    const btn = card.querySelector('.mark-read-btn');
                    if (btn) btn.remove();
                });
                document.getElementById('markAllReadBtn').remove();
                updateStats();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    });
}

function updateStats() {
    const unreadCount = document.querySelectorAll('.notification-card.unread').length;
    document.querySelectorAll('.unread-count').forEach(el => {
        el.textContent = unreadCount;
    });
}
</script>

<?php
View::endSection();

function formatNotificationType(string $type): string {
    return match($type) {
        'issue_created' => 'Created',
        'issue_assigned' => 'Assigned',
        'issue_commented' => 'Commented',
        'issue_status_changed' => 'Status',
        'issue_mentioned' => 'Mentioned',
        'issue_watched' => 'Watched',
        'project_created' => 'Project',
        'project_member_added' => 'Member',
        'comment_reply' => 'Reply',
        'custom' => 'Custom',
        'test' => 'Test',
        default => ucfirst(str_replace('_', ' ', $type)),
    };
}

function formatTime(string $timestamp): string {
    $date = new DateTime($timestamp);
    $now = new DateTime();
    $diff = $now->diff($date);

    if ($diff->days === 0) {
        if ($diff->h === 0) {
            return $diff->i . 'm ago';
        }
        return $diff->h . 'h ago';
    }

    if ($diff->days === 1) {
        return 'Yesterday at ' . $date->format('H:i');
    }

    if ($diff->days < 7) {
        return $diff->days . 'd ago';
    }

    return $date->format('M d, Y');
}
?>
