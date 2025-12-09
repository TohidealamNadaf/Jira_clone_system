<?php
declare(strict_types=1);

/**
 * Notifications Center Page - Jira Style Design
 * Displays all notifications with modern UI
 */

use App\Core\View;

View::extends('layouts.app');
View::section('content');
?>

<div class="notifications-container">
    <!-- Header Section -->
    <div class="notifications-header">
        <div class="header-content">
            <h1>Notifications</h1>
            <p class="header-subtitle">You have <strong><?= $unreadCount ?></strong> unread notification<?= $unreadCount !== 1 ? 's' : '' ?></p>
        </div>
        <?php if ($unreadCount > 0): ?>
            <button id="markAllReadBtn" class="btn btn-primary btn-lg">
                <i class="bi bi-check-all"></i> Mark All as Read
            </button>
        <?php endif; ?>
    </div>

    <div class="notifications-wrapper">
        <!-- Main Content -->
        <div class="notifications-content">
            <!-- Filter Tabs -->
            <div class="notification-filters">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        All (<span class="count"><?= $totalCount ?></span>)
                    </button>
                    <button class="filter-tab" data-filter="unread">
                        Unread (<span class="count"><?= $unreadCount ?></span>)
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="notifications-list">
                <?php if (empty($notifications)): ?>
                    <!-- Empty State -->
                    <div class="empty-state">
                        <svg class="empty-state-icon" fill="none" viewBox="0 0 48 48">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M24 10v2M10 24h2M24 36v2M36 24h2"/>
                            <circle cx="24" cy="24" r="12" fill="none" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        <h5>All caught up!</h5>
                        <p>You have no notifications</p>
                    </div>
                <?php else: ?>
                    <!-- Notifications Grid -->
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-card <?= $notification['is_read'] ? 'read' : 'unread' ?>" 
                             data-notification-id="<?= $notification['id'] ?>">
                            <div class="notification-wrapper">
                                <!-- Type Badge -->
                                <div class="notification-type-badge" data-type="<?= htmlspecialchars($notification['type']) ?>">
                                    <span class="badge"><?= formatNotificationType($notification['type']) ?></span>
                                </div>

                                <!-- Content -->
                                <div class="notification-main">
                                    <div class="notification-header">
                                        <a href="<?= htmlspecialchars($notification['action_url'] ?? '#') ?>" 
                                           class="notification-title">
                                            <?= htmlspecialchars($notification['title']) ?>
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="new-indicator">New</span>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <?php if ($notification['message']): ?>
                                        <p class="notification-message">
                                            <?= htmlspecialchars($notification['message']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="notification-meta">
                                        <span class="timestamp"><?= formatTime($notification['created_at']) ?></span>
                                        <?php if ($notification['priority'] === 'high'): ?>
                                            <span class="priority-badge priority-high">
                                                <i class="bi bi-exclamation-circle-fill"></i> High Priority
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Actions -->
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
                                            title="Delete notification">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="notifications-pagination">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li><a href="?page=1" class="page-link">First</a></li>
                            <li><a href="?page=<?= $page - 1 ?>" class="page-link">Previous</a></li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li><a href="?page=<?= $i ?>" class="page-link <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a></li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li><a href="?page=<?= $page + 1 ?>" class="page-link">Next</a></li>
                            <li><a href="?page=<?= $totalPages ?>" class="page-link">Last</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="notifications-sidebar">
            <!-- Stats Card -->
            <div class="sidebar-card stats-card">
                <h6>Notification Stats</h6>
                <div class="stat-item">
                    <div class="stat-label">Total</div>
                    <div class="stat-value"><?= $totalCount ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Unread</div>
                    <div class="stat-value unread-count"><?= $unreadCount ?></div>
                </div>
            </div>

            <!-- Preferences Card -->
            <div class="sidebar-card">
                <h6>Preferences</h6>
                <a href="<?= url('/profile/notifications') ?>" class="sidebar-link">
                    <i class="bi bi-gear"></i> Notification Settings
                </a>
            </div>

            <!-- Quick Links Card -->
            <div class="sidebar-card">
                <h6>Quick Links</h6>
                <a href="<?= url('/dashboard') ?>" class="sidebar-link">
                    <i class="bi bi-house"></i> Dashboard
                </a>
                <a href="<?= url('/projects') ?>" class="sidebar-link">
                    <i class="bi bi-kanban"></i> Projects
                </a>
                <a href="<?= url('/search') ?>" class="sidebar-link">
                    <i class="bi bi-search"></i> Search Issues
                </a>
            </div>
        </aside>
    </div>
</div>

<style>
.notifications-container {
    min-height: calc(100vh - 80px);
    background: #f6f8fa;
    padding: 24px 0;
}

.notifications-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 24px 24px;
    max-width: 1400px;
    margin: 0 auto;
}

.header-content h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
    color: #161b22;
}

.header-subtitle {
    margin: 8px 0 0;
    color: #656d76;
    font-size: 14px;
}

.notifications-wrapper {
    display: flex;
    gap: 24px;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
}

.notifications-content {
    flex: 1;
    min-width: 0;
}

.notification-filters {
    margin-bottom: 16px;
}

.filter-tabs {
    display: flex;
    gap: 16px;
    border-bottom: 1px solid #d0d7de;
    padding-bottom: 0;
}

.filter-tab {
    background: none;
    border: none;
    padding: 12px 0;
    margin-bottom: -1px;
    font-size: 14px;
    font-weight: 500;
    color: #656d76;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
}

.filter-tab:hover {
    color: #161b22;
}

.filter-tab.active {
    color: #0052cc;
    border-bottom-color: #0052cc;
}

.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.empty-state {
    text-align: center;
    padding: 60px 24px;
    background: white;
    border-radius: 12px;
    color: #656d76;
}

.empty-state-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 16px;
    color: #d0d7de;
}

.empty-state h5 {
    margin: 0 0 8px;
    font-size: 16px;
    font-weight: 600;
    color: #161b22;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

.notification-card {
    background: white;
    border: 1px solid #d0d7de;
    border-radius: 12px;
    transition: all 0.2s ease;
}

.notification-card:hover {
    border-color: #b6e3ff;
    box-shadow: 0 3px 12px rgba(0, 82, 204, 0.1);
}

.notification-card.unread {
    border-left: 4px solid #0052cc;
    background: #f6faff;
}

.notification-wrapper {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
}

.notification-type-badge {
    flex-shrink: 0;
    margin-top: 2px;
}

.notification-type-badge .badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
}

.notification-type-badge[data-type="issue_created"] .badge {
    background: #deebff;
    color: #0052cc;
}

.notification-type-badge[data-type="issue_assigned"] .badge {
    background: #fff7d6;
    color: #974f0c;
}

.notification-type-badge[data-type="issue_commented"] .badge {
    background: #e3fcef;
    color: #216e4e;
}

.notification-type-badge[data-type="issue_status_changed"] .badge {
    background: #ffe7d9;
    color: #974f0c;
}

.notification-type-badge[data-type="test"] .badge {
    background: #f0ebff;
    color: #5e4db2;
}

.notification-main {
    flex: 1;
    min-width: 0;
}

.notification-header {
    margin-bottom: 8px;
}

.notification-title {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 500;
    color: #0052cc;
    text-decoration: none;
    word-break: break-word;
}

.notification-title:hover {
    text-decoration: underline;
}

.new-indicator {
    display: inline-block;
    padding: 2px 6px;
    background: #0052cc;
    color: white;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
}

.notification-message {
    margin: 8px 0;
    font-size: 13px;
    color: #656d76;
    line-height: 1.5;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
}

.timestamp {
    color: #656d76;
}

.priority-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
}

.priority-badge.priority-high {
    background: #ffeceb;
    color: #ae2a19;
}

.notification-actions {
    display: flex;
    gap: 4px;
    flex-shrink: 0;
}

.action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid #d0d7de;
    border-radius: 6px;
    background: white;
    color: #656d76;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 14px;
}

.action-btn:hover {
    color: #161b22;
    border-color: #b6e3ff;
    background: #f6faff;
}

.action-btn.delete-btn:hover {
    color: #da3633;
    border-color: #ffcccc;
    background: #fff0ef;
}

.notifications-pagination {
    margin-top: 24px;
    text-align: center;
}

.pagination {
    display: inline-flex;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.page-link {
    display: inline-block;
    padding: 8px 12px;
    border: 1px solid #d0d7de;
    border-radius: 6px;
    color: #0052cc;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.page-link:hover:not(.active) {
    background: #f6f8fa;
    border-color: #b6e3ff;
}

.page-link.active {
    background: #0052cc;
    border-color: #0052cc;
    color: white;
}

.notifications-sidebar {
    width: 320px;
    flex-shrink: 0;
}

.sidebar-card {
    background: white;
    border: 1px solid #d0d7de;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
}

.sidebar-card h6 {
    margin: 0 0 12px;
    font-size: 13px;
    font-weight: 600;
    color: #161b22;
    text-transform: uppercase;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #d0d7de;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    font-size: 13px;
    color: #656d76;
}

.stat-value {
    font-size: 18px;
    font-weight: 600;
    color: #161b22;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 0;
    color: #0052cc;
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f1f3;
}

.sidebar-link:last-child {
    border-bottom: none;
}

.sidebar-link:hover {
    color: #003d82;
    text-decoration: underline;
}

.sidebar-link i {
    flex-shrink: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .notifications-wrapper {
        flex-direction: column;
    }

    .notifications-sidebar {
        width: 100%;
    }

    .notifications-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .notification-wrapper {
        flex-wrap: wrap;
    }

    .notification-actions {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>

<script>
// Get base URL from app configuration using url() helper
const APP_BASE_URL = '<?= rtrim(url("/"), "/") ?>';
console.log('APP_BASE_URL:', APP_BASE_URL);

document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.dataset.filter;
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.notification-card').forEach(card => {
                if (filter === 'all') {
                    card.style.display = 'block';
                } else if (filter === 'unread') {
                    card.style.display = card.classList.contains('unread') ? 'block' : 'none';
                }
            });
        });
    });

    // Mark single notification as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            const notificationId = btn.dataset.notificationId;
            
            console.log('Marking notification as read:', notificationId);
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const url = `${APP_BASE_URL}/api/v1/notifications/${notificationId}/read`;
                
                console.log('Marking notification as read:', notificationId);
                console.log('Fetching:', url);
                
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken || ''
                    }
                });

                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok) {
                    const card = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    card?.classList.remove('unread');
                    card?.classList.add('read');
                    btn.remove();
                    updateStats();
                } else {
                    alert('Error: ' + (data.error || 'Failed to mark as read'));
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
                alert('Error: ' + error.message);
            }
        });
    });

    // Delete notification
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (!confirm('Delete this notification?')) return;

            const notificationId = btn.dataset.notificationId;

            console.log('Deleting notification:', notificationId);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const url = `${APP_BASE_URL}/api/v1/notifications/${notificationId}`;
                
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-Token': csrfToken || ''
                    }
                });

                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok) {
                    const card = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    card?.remove();
                    updateStats();
                } else {
                    alert('Error: ' + (data.error || 'Failed to delete notification'));
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
                alert('Error: ' + error.message);
            }
        });
    });

    // Mark all as read
    document.getElementById('markAllReadBtn')?.addEventListener('click', async (e) => {
        e.preventDefault();

        console.log('Marking all notifications as read');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const url = `${APP_BASE_URL}/api/v1/notifications/read-all`;
            
            console.log('Fetching:', url);
            
            const response = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken || ''
                }
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (response.ok) {
                // Update UI immediately without full reload
                document.querySelectorAll('.notification-card.unread').forEach(card => {
                    card.classList.remove('unread');
                    card.classList.add('read');
                    const btn = card.querySelector('.mark-read-btn');
                    if (btn) btn.remove();
                });
                document.getElementById('markAllReadBtn').remove();
                updateStats();
            } else {
                alert('Error: ' + (data.error || 'Failed to mark all as read'));
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
            alert('Error: ' + error.message);
        }
    });

    // Update stats
    function updateStats() {
        const unreadCount = document.querySelectorAll('.notification-card.unread').length;
        document.querySelectorAll('.unread-count').forEach(el => {
            el.textContent = unreadCount;
        });
    }

    // Click on notification to go to action URL
    document.querySelectorAll('.notification-title').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.href && this.href !== '#') {
                e.preventDefault();
                window.location.href = this.href;
            }
        });
    });
});
</script>

<?php
View::endSection();

// Helper functions
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
