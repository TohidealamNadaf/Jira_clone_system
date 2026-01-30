<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<?php
// Get current user ID for permission checks
$authUser = auth();
$currentUserId = $authUser ? $authUser['id'] : null;
?>

<div class="issue-detail-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-section">
        <div class="breadcrumb">
            <a href="<?= url('/') ?>" class="breadcrumb-link">
                <i class="bi bi-house-door"></i> Home
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url("/projects/{$issue['project_key']}") ?>" class="breadcrumb-link">
                <?= e($issue['project_name']) ?>
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current"><?= e($issue['issue_key']) ?></span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="issue-main-container">
        <!-- Left Content -->
        <div class="issue-left-panel">
            <!-- Issue Header Card -->
            <div class="issue-header-card">
                <!-- Issue Key, Type, Status -->
                <div class="issue-header-top">
                    <div class="issue-key-group">
                        <div class="issue-type-icon" style="background-color: <?= e($issue['issue_type_color']) ?>">
                            <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                        </div>
                        <h1 class="issue-key"><?= e($issue['issue_key']) ?></h1>
                        <span class="issue-status-badge"
                            style="background-color: <?= e($issue['status_color']) ?>; color: white !important;">
                            <?= e($issue['status_name']) ?>
                        </span>
                    </div>
                    <div class="issue-actions-group">
                        <?php if (can('issues.edit', $issue['project_id']) || $issue['reporter_id'] === $currentUserId): ?>
                            <a href="#" onclick="CreateIssueModal.openEdit('<?= $issue['issue_key'] ?>'); return false;"
                                class="btn btn-sm btn-outline">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        <?php endif; ?>
                        <!-- Attachments Button -->
                        <button type="button" class="btn btn-sm btn-outline" data-bs-toggle="modal"
                            data-bs-target="#attachmentsModal">
                            <i class="bi bi-paperclip"></i> Attachments
                            <?php if (!empty($issue['attachments'])): ?>
                                <span class="badge bg-secondary text-white ms-1"
                                    style="font-size: 0.7rem; padding: 2px 6px;"><?= count((array) ($issue['attachments'] ?? [])) ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-container">
                            <button class="btn btn-sm btn-outline" onclick="toggleMenu(this)">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <div class="dropdown-menu" style="display: none;">
                                <?php if (can('issues.assign', $issue['project_id'])): ?>
                                    <a href="#" onclick="assignIssue(); return false;" class="dropdown-item">
                                        <i class="bi bi-person"></i> Assign
                                    </a>
                                <?php endif; ?>
                                <a href="#" onclick="watchIssue(<?= $isWatching ? 'true' : 'false' ?>); return false;"
                                    class="dropdown-item">
                                    <i class="bi bi-eye<?= $isWatching ? '-slash' : '' ?>"></i>
                                    <?= $isWatching ? 'Unwatch' : 'Watch' ?>
                                </a>
                                <?php if ($issue['reporter_id'] !== user_id()): ?>
                                    <a href="#" onclick="voteIssue(<?= $hasVoted ? 'true' : 'false' ?>); return false;"
                                        class="dropdown-item">
                                        <i class="bi bi-hand-thumbs-<?= $hasVoted ? 'down' : 'up' ?>"></i>
                                        <?= $hasVoted ? 'Remove Vote' : 'Vote' ?>
                                    </a>
                                <?php endif; ?>
                                <?php if (can('issues.link', $issue['project_id'])): ?>
                                    <hr class="dropdown-divider">
                                    <a href="#" onclick="linkIssue(); return false;" class="dropdown-item">
                                        <i class="bi bi-link"></i> Link Issue
                                    </a>
                                <?php endif; ?>
                                <?php if (can('issues.log_work', $issue['project_id'])): ?>
                                    <a href="#" onclick="logWork(); return false;" class="dropdown-item">
                                        <i class="bi bi-clock"></i> Log Work
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Issue Summary/Title -->
                <h2 class="issue-summary"><?= e($issue['summary']) ?></h2>

                <!-- Description -->
                <?php if ($issue['description']): ?>
                    <div class="description-section">
                        <h3 class="section-label">Description</h3>
                        <div class="description-content">
                            <?= $issue['description'] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Details Grid -->
                <div class="details-grid">
                    <?php if ($issue['assignee_name']): ?>
                        <div class="detail-cell">
                            <div class="detail-label">Assignee</div>
                            <div class="detail-value">
                                <?php if ($issue['assignee_avatar']): ?>
                                    <img src="<?= e(avatar($issue['assignee_avatar'])) ?>" class="avatar-sm" alt="Avatar">
                                <?php else: ?>
                                    <div class="avatar-initial-sm"><?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span><?= e($issue['assignee_name']) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="detail-cell">
                        <div class="detail-label">Reporter</div>
                        <div class="detail-value">
                            <?php if ($issue['reporter_avatar']): ?>
                                <img src="<?= e(avatar($issue['reporter_avatar'])) ?>" class="avatar-sm" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-initial-sm"><?= strtoupper(substr($issue['reporter_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <span><?= e($issue['reporter_name']) ?></span>
                        </div>
                    </div>

                    <?php if ($issue['priority_name']): ?>
                        <div class="detail-cell">
                            <div class="detail-label">Priority</div>
                            <div class="detail-value">
                                <span class="badge" style="background-color: <?= e($issue['priority_color']) ?>">
                                    <?= e($issue['priority_name']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($issue['due_date']): ?>
                        <div class="detail-cell">
                            <div class="detail-label">Due Date</div>
                            <div class="detail-value">
                                <i class="bi bi-calendar"></i>
                                <span><?= format_date($issue['due_date']) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($issue['story_points']): ?>
                        <div class="detail-cell">
                            <div class="detail-label">Story Points</div>
                            <div class="detail-value">
                                <i class="bi bi-hash"></i> <?= e($issue['story_points']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($issue['labels'])): ?>
                        <div class="detail-cell">
                            <div class="detail-label">Labels</div>
                            <div class="detail-value labels-row">
                                <?php foreach ((array) ($issue['labels'] ?? []) as $label): ?>
                                    <span class="label-badge" style="background-color: <?= e($label['color'] ?? '#ccc') ?>">
                                        <?= e($label['name'] ?? '') ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="section-card">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="bi bi-chat-left-text"></i> Comments
                        <?php if (!empty($issue['comments'])): ?>
                            <span class="section-badge"><?= count((array) ($issue['comments'] ?? [])) ?></span>
                        <?php endif; ?>
                    </h3>
                    <?php if (!empty($issue['comments'])): ?>
                        <button id="toggle-all-comments" class="btn btn-sm btn-outline"
                            title="Collapse/Expand all comments">
                            <i class="bi bi-chevron-up"></i> Collapse
                        </button>
                    <?php endif; ?>
                </div>
                <div class="section-content">
                    <!-- Add Comment Form -->
                    <?php if ($currentUserId): ?>
                        <form id="comment-form" class="comment-form">
                            <div class="form-group">
                                <label class="form-label"><i class="bi bi-pencil-square"></i> Add a comment</label>
                                <textarea class="form-control" name="body" rows="3" placeholder="Write your comment here..."
                                    required></textarea>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Post Comment
                                </button>
                                <button type="reset" class="btn btn-outline">
                                    <i class="bi bi-x-circle"></i> Clear
                                </button>
                            </div>
                        </form>
                        <div class="divider"></div>
                    <?php endif; ?>

                    <!-- Comments List -->
                    <div id="comments-list">
                        <?php if (empty($issue['comments'])): ?>
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-chat-left-quote"></i></div>
                                <p class="empty-text">No comments yet. Be the first to comment!</p>
                            </div>
                        <?php else: ?>
                            <div class="comments-container" id="comments-container">
                                <?php
                                $commentsPerPage = 5;
                                $totalComments = count((array) ($issue['comments'] ?? []));
                                $showInitial = min($commentsPerPage, $totalComments);
                                ?>
                                <?php for ($i = 0; $i < $showInitial; $i++):
                                    $comment = $issue['comments'][$i];
                                    ?>
                                    <div class="comment-item" id="comment-<?= $comment['id'] ?>">
                                        <div class="comment-header-row">
                                            <div class="comment-user-info">
                                                <?php if (($comment['user']['avatar'] ?? null)): ?>
                                                    <img src="<?= e(avatar($comment['user']['avatar'])) ?>" class="avatar-md"
                                                        alt="Avatar">
                                                <?php else: ?>
                                                    <div class="avatar-initial-md">
                                                        <?= strtoupper(substr(($comment['user']['first_name'] ?? 'U'), 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="user-meta">
                                                    <strong
                                                        class="user-name"><?= e($comment['user']['display_name'] ?? 'Unknown User') ?></strong>
                                                    <span
                                                        class="comment-time"><?= time_ago($comment['created_at']) ?><?php if ($comment['updated_at'] !== $comment['created_at']): ?>
                                                            <em>(edited)</em><?php endif; ?></span>
                                                </div>
                                            </div>
                                            <div class="comment-actions">
                                                <?php
                                                $canEditDelete = ($comment['user_id'] === $currentUserId) ||
                                                    can('comments.edit_all', $issue['project_id']) ||
                                                    can('comments.delete_all', $issue['project_id']);
                                                ?>
                                                <?php if ($canEditDelete): ?>
                                                    <button class="action-btn edit-comment-btn"
                                                        data-comment-id="<?= $comment['id'] ?>"
                                                        data-issue-key="<?= $issue['issue_key'] ?>" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button class="action-btn delete-comment-btn"
                                                        data-comment-id="<?= $comment['id'] ?>"
                                                        data-issue-key="<?= $issue['issue_key'] ?>" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="comment-body"><?= nl2br(e($comment['body'])) ?></div>
                                    </div>
                                <?php endfor; ?>

                                <?php if ($totalComments > $showInitial): ?>
                                    <div class="load-more-section">
                                        <button id="load-more-comments" class="btn btn-outline">
                                            <i class="bi bi-arrow-down"></i> Load More (<?= $totalComments - $showInitial ?>
                                            remaining)
                                        </button>
                                    </div>
                                    <div id="comments-data" style="display:none;">
                                        <?php for ($i = $showInitial; $i < $totalComments; $i++):
                                            $comment = $issue['comments'][$i];
                                            ?>
                                            <div class="comment-item" id="comment-<?= $comment['id'] ?>">
                                                <div class="comment-header-row">
                                                    <div class="comment-user-info">
                                                        <?php if (($comment['user']['avatar'] ?? null)): ?>
                                                            <img src="<?= e(avatar($comment['user']['avatar'])) ?>" class="avatar-md"
                                                                alt="Avatar">
                                                        <?php else: ?>
                                                            <div class="avatar-initial-md">
                                                                <?= strtoupper(substr(($comment['user']['first_name'] ?? 'U'), 0, 1)) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="user-meta">
                                                            <strong
                                                                class="user-name"><?= e($comment['user']['display_name'] ?? 'Unknown User') ?></strong>
                                                            <span
                                                                class="comment-time"><?= time_ago($comment['created_at']) ?><?php if ($comment['updated_at'] !== $comment['created_at']): ?>
                                                                    <em>(edited)</em><?php endif; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="comment-actions">
                                                        <?php
                                                        $canEditDelete = ($comment['user_id'] === $currentUserId) ||
                                                            can('comments.edit_all', $issue['project_id']) ||
                                                            can('comments.delete_all', $issue['project_id']);
                                                        ?>
                                                        <?php if ($canEditDelete): ?>
                                                            <button class="action-btn edit-comment-btn"
                                                                data-comment-id="<?= $comment['id'] ?>"
                                                                data-issue-key="<?= $issue['issue_key'] ?>" title="Edit">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <button class="action-btn delete-comment-btn"
                                                                data-comment-id="<?= $comment['id'] ?>"
                                                                data-issue-key="<?= $issue['issue_key'] ?>" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="comment-body"><?= nl2br(e($comment['body'])) ?></div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Attachments Section moved to Modal -->

            <!-- Work Logs Section -->
            <?php if (!empty($issue['worklogs'])): ?>
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title"><i class="bi bi-clock-history"></i> Work Log</h3>
                    </div>
                    <div class="section-content">
                        <table class="logs-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Time Spent</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($issue['worklogs'] ?? [] as $worklog): ?>
                                    <tr>
                                        <td><?= e(($worklog['user']['display_name'] ?? 'Unknown')) ?></td>
                                        <td><?= format_time($worklog['time_spent'] ?? 0) ?></td>
                                        <td><?= format_date($worklog['started_at'] ?? '') ?></td>
                                        <td><?= e($worklog['description'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Linked Issues Section -->
            <?php if (!empty(($links['outward'] ?? [])) || !empty(($links['inward'] ?? []))): ?>
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title"><i class="bi bi-link"></i> Linked Issues</h3>
                    </div>
                    <div class="section-content">
                        <div class="links-list">
                            <?php if (!empty($links['outward'] ?? [])): ?>
                                <?php foreach ($links['outward'] as $link): ?>
                                    <div class="link-item">
                                        <span class="link-badge"
                                            style="background-color: <?= e($link['issue_type_color'] ?? '#ccc') ?>">
                                            <i class="bi bi-<?= e($link['issue_type_icon'] ?? 'link') ?>"></i>
                                        </span>
                                        <a href="<?= url("/issue/{$link['issue_key']}") ?>" class="link-key">
                                            <?= e($link['issue_key'] ?? 'Unknown') ?>
                                        </a>
                                        <span class="link-desc"><?= e($link['description'] ?? '') ?></span>
                                        <span class="link-status"
                                            style="background-color: <?= e($link['status_color'] ?? '#ccc') ?>; color: white !important;">
                                            <?= e($link['status_name'] ?? 'Unknown') ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <?php if (!empty($links['inward'] ?? [])): ?>
                                <?php foreach ($links['inward'] as $link): ?>
                                    <div class="link-item">
                                        <span class="link-badge"
                                            style="background-color: <?= e($link['issue_type_color'] ?? '#ccc') ?>">
                                            <i class="bi bi-<?= e($link['issue_type_icon'] ?? 'link') ?>"></i>
                                        </span>
                                        <a href="<?= url("/issue/{$link['issue_key']}") ?>" class="link-key">
                                            <?= e($link['issue_key'] ?? 'Unknown') ?>
                                        </a>
                                        <span class="link-desc"><?= e($link['description'] ?? '') ?></span>
                                        <span class="link-status"
                                            style="background-color: <?= e($link['status_color'] ?? '#ccc') ?>; color: white !important;">
                                            <?= e($link['status_name'] ?? 'Unknown') ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Activity Section -->
            <div class="section-card">
                <div class="section-header activity-header" onclick="toggleActivity(this)">
                    <h3 class="section-title">
                        <i class="bi bi-clock-history"></i> Activity
                        <?php if (!empty($history)): ?>
                            <span class="section-badge"><?= count((array) ($history ?? [])) ?></span>
                        <?php endif; ?>
                    </h3>
                    <button class="btn btn-sm btn-outline activity-toggle-btn" type="button">
                        <i class="bi bi-chevron-up"></i>
                    </button>
                </div>
                <div class="section-content activity-content">
                    <?php if (empty($history)): ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-info-circle"></i></div>
                            <p class="empty-text">No activity yet. Changes will appear here.</p>
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($history as $entry): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <strong><?= e($entry['user_name'] ?? 'System') ?></strong>
                                        changed <strong><?= e($entry['field']) ?></strong>
                                        <?php if ($entry['old_value']): ?>
                                            from <em>"<?= e($entry['old_value']) ?>"</em>
                                        <?php endif; ?>
                                        <?php if ($entry['new_value']): ?>
                                            to <em>"<?= e($entry['new_value']) ?>"</em>
                                        <?php endif; ?>
                                        <div class="timeline-time"><?= time_ago($entry['created_at']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="issue-right-panel">
            <!-- Status Transitions -->
            <?php if (!empty($transitions)): ?>
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <h4 class="sidebar-title">Status</h4>
                    </div>
                    <div class="sidebar-content">
                        <div class="button-group">
                            <?php foreach ($transitions as $transition): ?>
                                <button class="btn btn-block btn-primary"
                                    onclick="transitionIssue(<?= $transition['status_id'] ?>, '<?= e($transition['status_name']) ?>')">
                                    <?= e($transition['status_name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Details Card -->
            <div class="sidebar-card">
                <div class="sidebar-header">
                    <h4 class="sidebar-title">Details</h4>
                </div>
                <div class="sidebar-content">
                    <div class="detail-row">
                        <span class="detail-label">Type</span>
                        <span class="detail-badge"
                            style="background-color: <?= e($issue['issue_type_color']) ?>; color: <?= contrast_color($issue['issue_type_color']) ?> !important; display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; border-radius: 4px;">
                            <?php if (!empty($issue['issue_type_icon'])): ?>
                                <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                            <?php endif; ?>
                            <?= e($issue['issue_type_name'] ?: 'Type') ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Priority</span>
                        <span class="detail-badge"
                            style="background-color: <?= e($issue['priority_color']) ?>; color: <?= contrast_color($issue['priority_color']) ?> !important; display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; border-radius: 4px;">
                            <?php if (!empty($issue['priority_icon'])): ?>
                                <i class="bi bi-<?= e($issue['priority_icon']) ?>"></i>
                            <?php endif; ?>
                            <?= e($issue['priority_name'] ?: 'Priority') ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <?php if (!empty($transitions)): ?>
                            <div class="dropdown">
                                <button class="detail-badge dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    style="background-color: <?= e($issue['status_color']) ?>; color: <?= contrast_color($issue['status_color']) ?> !important; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                    <?= e($issue['status_name']) ?>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <h6 class="dropdown-header">Current: <?= e($issue['status_name']) ?></h6>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <h6 class="dropdown-header">Transition To:</h6>
                                    </li>
                                    <?php foreach ($transitions as $transition): ?>
                                        <li>
                                            <a class="dropdown-item" href="#"
                                                onclick="transitionIssue(<?= $transition['status_id'] ?>, '<?= e($transition['status_name']) ?>'); return false;">
                                                <?= e($transition['status_name']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <span class="detail-badge"
                                style="background-color: <?= e($issue['status_color']) ?>; color: <?= contrast_color($issue['status_color']) ?> !important;">
                                <?= e($issue['status_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if ($issue['start_date']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Start Date</span>
                            <span class="detail-value"><?= format_date($issue['start_date']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($issue['end_date']): ?>
                        <div class="detail-row">
                            <span class="detail-label">End Date</span>
                            <span class="detail-value"><?= format_date($issue['end_date']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="detail-row">
                        <span class="detail-label">Created</span>
                        <span class="detail-value"><?= format_date($issue['created_at']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Updated</span>
                        <span class="detail-value"><?= format_date($issue['updated_at']) ?></span>
                    </div>
                    <?php if ($issue['resolved_at']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Resolved</span>
                            <span class="detail-value"><?= format_date($issue['resolved_at']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- People Card -->
            <div class="sidebar-card">
                <div class="sidebar-header">
                    <h4 class="sidebar-title">People</h4>
                </div>
                <div class="sidebar-content">
                    <div class="people-field">
                        <span class="detail-label">Assignee</span>
                        <?php if ($issue['assignee_name']): ?>
                            <div class="people-value">
                                <?php if ($issue['assignee_avatar']): ?>
                                    <img src="<?= e(avatar($issue['assignee_avatar'])) ?>" class="avatar-sm" alt="Avatar">
                                <?php else: ?>
                                    <div class="avatar-initial-sm"><?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span><?= e($issue['assignee_name']) ?></span>
                            </div>
                        <?php else: ?>
                            <div class="people-value unassigned">Unassigned</div>
                        <?php endif; ?>
                    </div>
                    <div class="people-field">
                        <span class="detail-label">Reporter</span>
                        <div class="people-value">
                            <?php if ($issue['reporter_avatar']): ?>
                                <img src="<?= e(avatar($issue['reporter_avatar'])) ?>" class="avatar-sm" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar-initial-sm"><?= strtoupper(substr($issue['reporter_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <span><?= e($issue['reporter_name']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scroll-to-top" class="scroll-to-top-btn">
        <i class="bi bi-arrow-up"></i>
    </button>
</div>

<!-- Modals -->
<!-- Transition Modal -->
<div class="modal fade" id="transitionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transition Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="transition-form">
                <div class="modal-body" style="padding-top: 20px;">
                    <p>Transition to: <strong id="transition-status"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Transition</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assign-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Assignee</label>
                        <select class="form-control" id="assignee" name="assignee_id" required>
                            <option value="">Select a user...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Link Modal -->
<div class="modal fade" id="linkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Link Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="link-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Link Type</label>
                        <select class="form-control" id="link-type-id" name="link_type_id" required>
                            <option value="">Select link type...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Target Issue</label>
                        <input type="text" class="form-control" id="target-issue-key" name="target_issue_key"
                            placeholder="e.g., BP-1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Log Work Modal -->
<div class="modal fade" id="logWorkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Work</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="logwork-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Time Spent (hours)</label>
                        <input type="number" class="form-control" id="time-spent" name="time_spent" step="0.5" min="0"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Started At</label>
                        <input type="datetime-local" class="form-control" id="started-at" name="started_at" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="work-description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Log Work</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* ========================================
   Root Variables & Base Styles
   ======================================== */
    :root {
        /* Brand Colors */
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-blue-light: rgba(139, 25, 86, 0.1) !important;

        /* Text Colors */
        --text-primary: #161B22 !important;
        --text-secondary: #57606A !important;
        --text-tertiary: #738496 !important;
        --text-muted: #97A0AF !important;
        --text-white: #FFFFFF !important;

        /* Background Colors */
        --bg-primary: #FFFFFF !important;
        --bg-secondary: #F7F8FA !important;
        --bg-tertiary: #ECEDF0 !important;

        /* Borders & Dividers */
        --border-color: #DFE1E6;
        --border-light: #EBECF0;

        /* Functional Colors */
        --color-success: #36B37E;
        --color-warning: #FFAB00;
        --color-error: #FF5630;

        /* Transitions */
        --transition: 200ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-color: var(--bg-secondary);
        color: var(--text-primary);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    /* ========================================
   Breadcrumb
   ======================================== */
    .breadcrumb-section {
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        padding: 12px 32px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        padding: 0;
        font-size: 13px;
        list-style: none;
    }

    .breadcrumb-link {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
        transition: all var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--text-secondary);
        margin: 0 2px;
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* ========================================
   Main Layout
   ======================================== */
    .issue-detail-wrapper {
        background: var(--bg-secondary);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .issue-main-container {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
        padding: 24px 32px;
        max-width: 1600px;
        margin: 0 auto;
        width: 100%;
        flex: 1;
    }

    .issue-left-panel {
        display: flex;
        flex-direction: column;
        gap: 24px;
        min-width: 0;
    }

    .issue-right-panel {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ========================================
   Issue Header Card
   ======================================== */
    .issue-header-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    }

    .issue-header-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .issue-key-group {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .issue-type-icon {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        flex-shrink: 0;
    }

    .issue-key {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .issue-status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        color: white;
    }

    .issue-actions-group {
        display: flex;
        gap: 8px;
    }

    .btn {
        padding: 8px 16px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: var(--bg-primary);
        color: var(--text-primary);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .btn:hover {
        background: var(--bg-secondary);
        border-color: var(--text-secondary);
    }

    .btn-primary {
        background: var(--jira-blue);
        color: var(--text-white);
        border-color: var(--jira-blue);
    }

    .btn-primary:hover {
        background: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
    }

    .btn-outline {
        background: var(--bg-primary);
        border-color: var(--border-color);
        color: var(--text-primary);
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-block {
        width: 100%;
        text-align: center;
    }

    /* Dropdown */
    .dropdown-container {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 4px;
        min-width: 200px;
        box-shadow: 0 4px 12px rgba(9, 30, 66, 0.15);
        z-index: 100;
    }

    .dropdown-menu .dropdown-item {
        display: block;
        width: 100%;
        padding: 10px 16px;
        text-align: left;
        background: none;
        border: none;
        border-bottom: 1px solid var(--border-light);
        color: var(--text-primary);
        text-decoration: none;
        cursor: pointer;
        font-size: 13px;
        transition: all var(--transition);
    }

    .dropdown-menu .dropdown-item:last-child {
        border-bottom: none;
    }

    .dropdown-menu .dropdown-item:hover {
        background: var(--bg-secondary);
    }

    .dropdown-divider {
        border: none;
        border-top: 1px solid var(--border-light);
        margin: 4px 0;
        height: 0;
    }

    /* Issue Title & Description */
    .issue-summary {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 24px 0;
        letter-spacing: -0.3px;
        line-height: 1.3;
    }

    .description-section {
        margin-bottom: 24px;
    }

    .section-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .description-content {
        padding: 12px 16px;
        background: var(--bg-secondary);
        border-radius: 4px;
        border-left: 3px solid var(--jira-blue);
        font-size: 14px;
        line-height: 1.6;
    }

    /* Details Grid */
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        padding: 16px 0;
        border-top: 1px solid var(--border-light);
        border-bottom: 1px solid var(--border-light);
        margin: 24px 0;
    }

    .detail-cell {
        display: flex;
        flex-direction: column;
    }

    .detail-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .detail-value {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-primary);
    }

    .detail-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        color: white;
        background: var(--jira-blue);
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        color: white;
    }

    .label-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        color: white;
        margin-right: 4px;
    }

    .labels-row {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }

    /* Avatars */
    .avatar-sm {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
    }

    .avatar-md {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .avatar-initial-sm {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--jira-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 600;
    }

    .avatar-initial-md {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--jira-blue);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
    }

    /* ========================================
   Section Cards
   ======================================== */
    .section-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    }

    .section-header {
        padding: 12px 16px;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .section-header.activity-header {
        cursor: pointer;
        transition: background var(--transition);
    }

    .section-header.activity-header:hover {
        background: #EBECF0;
    }

    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-badge {
        display: inline-block;
        background: var(--bg-tertiary);
        color: var(--text-secondary);
        padding: 2px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        margin-left: auto;
    }

    .section-content {
        padding: 16px;
    }

    /* ========================================
   Comments
   ======================================== */
    .comment-form {
        background: var(--bg-secondary);
        padding: 12px;
        border-radius: 4px;
        border: 1px solid var(--border-light);
        margin-bottom: 16px;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 13px;
        color: var(--text-primary);
        background: var(--bg-primary);
        font-family: inherit;
        transition: border-color var(--transition);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }

    .divider {
        border: none;
        border-top: 1px solid var(--border-light);
        margin: 12px 0;
    }

    .comments-container {
        max-height: 800px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .comments-container::-webkit-scrollbar {
        width: 6px;
    }

    .comments-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .comments-container::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }

    .comment-item {
        padding: 12px;
        border: 1px solid var(--border-light);
        border-radius: 4px;
        margin-bottom: 12px;
        background: var(--bg-primary);
        transition: all var(--transition);
        animation: slideIn 0.3s ease-out;
    }

    .comment-item:hover {
        border-color: var(--border-color);
        box-shadow: 0 4px 12px rgba(9, 30, 66, 0.08);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .comment-header-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }

    .comment-user-info {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }

    .user-meta {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .user-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .comment-time {
        font-size: 12px;
        color: var(--text-secondary);
    }

    .comment-actions {
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity var(--transition);
    }

    .comment-item:hover .comment-actions {
        opacity: 1;
    }

    .action-btn {
        padding: 4px 6px;
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 13px;
        transition: all var(--transition);
    }

    .action-btn:hover {
        color: var(--text-primary);
    }

    .delete-comment-btn:hover {
        color: var(--color-error);
    }

    .comment-body {
        font-size: 13px;
        color: var(--text-primary);
        line-height: 1.5;
        word-wrap: break-word;
    }

    .empty-state {
        text-align: center;
        padding: 32px 16px;
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-text {
        font-size: 13px;
        margin: 0;
    }

    .load-more-section {
        text-align: center;
        margin: 16px 0;
    }

    /* ========================================
   Attachments
   ======================================== */
    .attachments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
        padding: 0;
    }

    .attachment-item {
        padding: 16px;
        border: 1px solid var(--border-light);
        border-radius: 8px;
        background: var(--bg-primary);
        display: flex;
        gap: 16px;
        align-items: flex-start;
        transition: all var(--transition);
        position: relative;
    }

    .attachment-item:hover {
        border-color: var(--jira-blue);
        background: rgba(139, 25, 86, 0.02);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* File Icon Circle */
    .attachment-icon {
        flex-shrink: 0;
    }

    .file-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    /* Attachment Info */
    .attachment-info {
        flex: 1;
        min-width: 0;
    }

    .attachment-name {
        display: block;
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        word-break: break-word;
        transition: color var(--transition);
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .attachment-name:hover {
        color: var(--jira-blue);
    }

    .attachment-meta {
        display: flex;
        gap: 12px;
        font-size: 12px;
        color: var(--text-secondary);
    }

    .file-size,
    .file-time {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .file-size i,
    .file-time i {
        font-size: 11px;
        opacity: 0.7;
    }

    /* Download Button */
    .attachment-download {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-secondary);
        color: var(--jira-blue);
        text-decoration: none;
        font-size: 16px;
        transition: all var(--transition);
        border: 1px solid var(--border-light);
    }

    .attachment-download:hover {
        background: var(--jira-blue);
        color: white;
        border-color: var(--jira-blue);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
    }

    /* ========================================
   Tables
   ======================================== */
    .logs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .logs-table thead {
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-light);
    }

    .logs-table th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .logs-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-light);
        color: var(--text-primary);
    }

    .logs-table tbody tr:hover {
        background: var(--bg-secondary);
    }

    /* ========================================
   Links
   ======================================== */
    .links-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .link-item {
        padding: 12px;
        border: 1px solid var(--border-light);
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all var(--transition);
    }

    .link-item:hover {
        border-color: var(--border-color);
        background: var(--bg-secondary);
    }

    .link-badge {
        width: 28px;
        height: 28px;
        border-radius: 3px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 13px;
        flex-shrink: 0;
    }

    .link-key {
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        transition: color var(--transition);
    }

    .link-key:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .link-desc {
        color: var(--text-secondary);
        font-size: 12px;
        flex: 1;
    }

    .link-status {
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        color: white;
        white-space: nowrap;
    }

    /* ========================================
   Activity/Timeline
   ======================================== */
    .activity-content {
        transition: max-height 0.3s ease, overflow 0.3s ease;
        max-height: 600px;
        overflow-y: auto;
    }

    .activity-content.collapsed {
        max-height: 0;
        overflow: hidden;
        padding: 0 !important;
    }

    .activity-toggle-btn {
        padding: 4px 8px;
    }

    .timeline {
        position: relative;
        padding-left: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 4px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border-light);
    }

    .timeline-item {
        position: relative;
        padding-left: 20px;
        margin-bottom: 16px;
    }

    .timeline-marker {
        position: absolute;
        left: -8px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: white;
        border: 2px solid var(--jira-blue);
    }

    .timeline-content {
        font-size: 13px;
        color: var(--text-primary);
        line-height: 1.5;
    }

    .timeline-time {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    /* ========================================
   Sidebar Cards
   ======================================== */
    .sidebar-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    }

    .sidebar-header {
        padding: 12px 16px;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-light);
    }

    .sidebar-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .sidebar-content {
        padding: 12px;
    }

    .button-group {
        display: grid;
        gap: 8px;
    }

    .detail-row {
        padding: 8px 0;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .people-field {
        margin-bottom: 16px;
    }

    .people-field:last-child {
        margin-bottom: 0;
    }

    .people-value {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
        font-size: 13px;
        color: var(--text-primary);
    }

    .people-value.unassigned {
        color: var(--text-secondary);
    }

    /* ========================================
   Scroll to Top Button
   ======================================== */
    .scroll-to-top-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--jira-blue);
        color: white;
        border: none;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
        transition: all var(--transition);
        z-index: 99;
        font-size: 18px;
    }

    .scroll-to-top-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(139, 25, 86, 0.4);
        background: var(--jira-blue-dark);
    }

    /* ========================================
   Modals
   ======================================== */
    .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-light);
        background: var(--bg-secondary);
    }

    .modal-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 16px 20px;
        border-top: 1px solid var(--border-light);
        background: var(--bg-secondary);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 20px;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        transition: color var(--transition);
    }

    .btn-close:hover {
        color: var(--text-primary);
    }

    /* ========================================
   Responsive Design
   ======================================== */
    @media (max-width: 1024px) {
        .issue-main-container {
            grid-template-columns: 1fr;
            gap: 16px;
            padding: 16px 20px;
        }

        .issue-right-panel {
            order: -1;
        }
    }

    @media (max-width: 768px) {
        .issue-header-top {
            flex-direction: column;
            align-items: flex-start;
        }

        .issue-actions-group {
            width: 100%;
        }

        .issue-summary {
            font-size: 18px;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .section-header {
            flex-wrap: wrap;
        }

        .comments-container {
            max-height: 400px;
        }

        .attachments-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .issue-main-container {
            padding: 12px 16px;
        }

        .issue-header-card,
        .section-card,
        .sidebar-card {
            border-radius: 6px;
        }

        .issue-summary {
            font-size: 16px;
        }

        .scroll-to-top-btn {
            bottom: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            font-size: 16px;
        }
    }
</style>

<script>
    let currentTransitionStatusId = null;

    function toggleMenu(btn) {
        const menu = btn.nextElementSibling;
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown-container')) {
                menu.style.display = 'none';
            }
        }, { once: true });
    }

    function toggleActivity(header) {
        const content = header.nextElementSibling;
        const toggleBtn = header.querySelector('.activity-toggle-btn i');
        content.classList.toggle('collapsed');

        if (content.classList.contains('collapsed')) {
            toggleBtn.className = 'bi bi-chevron-down';
        } else {
            toggleBtn.className = 'bi bi-chevron-up';
        }
    }

    function transitionIssue(statusId, statusName) {
        currentTransitionStatusId = statusId;
        document.getElementById('transition-status').textContent = statusName;
        new bootstrap.Modal(document.getElementById('transitionModal')).show();
    }

    function assignIssue() {
        const baseUrl = window.location.pathname.split('/public/')[0] + '/public';

        fetch(`${baseUrl}/api/v1/projects/<?= $issue['project_key'] ?>/members`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById('assignee');
                select.innerHTML = '<option value="">Select a user...</option>';
                if (data.members) {
                    data.members.forEach(member => {
                        const option = document.createElement('option');
                        option.value = member.id;
                        option.textContent = member.display_name;
                        select.appendChild(option);
                    });
                }
                new bootstrap.Modal(document.getElementById('assignModal')).show();
            })
            .catch(err => {
                console.error('Error loading members:', err);
                showNotification('Failed to load team members', 'error');
            });
    }

    function linkIssue() {
        const baseUrl = window.location.pathname.split('/public/')[0] + '/public';

        fetch(`${baseUrl}/api/v1/link-types`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById('link-type-id');
                select.innerHTML = '<option value="">Select link type...</option>';
                if (data.link_types) {
                    data.link_types.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.id;
                        option.textContent = type.name;
                        select.appendChild(option);
                    });
                }
                new bootstrap.Modal(document.getElementById('linkModal')).show();
            })
            .catch(err => {
                console.error('Error loading link types:', err);
                showNotification('Failed to load link types', 'error');
            });
    }

    function logWork() {
        const now = new Date();
        const localDateTime = now.toISOString().slice(0, 16);
        document.getElementById('started-at').value = localDateTime;
        new bootstrap.Modal(document.getElementById('logWorkModal')).show();
    }

    function showNotification(message, type = 'info') {
        const alertClass = `alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'}`;
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alert.style.zIndex = '9999';
        alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Form submissions
        document.getElementById('transition-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
            try {
                const response = await fetch(`${baseUrl}/api/v1/issues/<?= $issue['issue_key'] ?>/transitions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ status_id: currentTransitionStatusId })
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('transitionModal')).hide();
                    showNotification('Issue transitioned successfully', 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showNotification(data.error || 'Failed to transition issue', 'error');
                }
            } catch (error) {
                showNotification('Error: ' + error.message, 'error');
            }
        });

        document.getElementById('comment-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = document.querySelector('[name="body"]').value;
            const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
            try {
                const response = await fetch(`${baseUrl}/issue/<?= $issue['issue_key'] ?>/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ body })
                });
                if (response.ok) {
                    window.location.reload();
                } else {
                    showNotification('Failed to add comment', 'error');
                }
            } catch (error) {
                showNotification('Error: ' + error.message, 'error');
            }
        });

        document.getElementById('assign-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const assigneeId = document.getElementById('assignee').value;
            if (!assigneeId) {
                showNotification('Please select an assignee', 'error');
                return;
            }
            const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
            try {
                const response = await fetch(`${baseUrl}/issue/<?= $issue['issue_key'] ?>/assign`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ assignee_id: assigneeId })
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
                    showNotification('Issue assigned successfully', 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showNotification(data.error || 'Failed to assign issue', 'error');
                }
            } catch (error) {
                showNotification('Error: ' + error.message, 'error');
            }
        });

        document.getElementById('link-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const linkTypeId = document.getElementById('link-type-id').value;
            const targetIssueKey = document.getElementById('target-issue-key').value;
            if (!linkTypeId || !targetIssueKey) {
                showNotification('Please fill in all fields', 'error');
                return;
            }
            const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
            try {
                const response = await fetch(`${baseUrl}/issue/<?= $issue['issue_key'] ?>/link`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ link_type_id: parseInt(linkTypeId), target_issue_key: targetIssueKey })
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('linkModal')).hide();
                    showNotification('Issue linked successfully', 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showNotification(data.error || 'Failed to link issue', 'error');
                }
            } catch (error) {
                showNotification('Error: ' + error.message, 'error');
            }
        });

        document.getElementById('logwork-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const timeSpent = document.getElementById('time-spent').value;
            const startedAt = document.getElementById('started-at').value;
            const description = document.getElementById('work-description').value;
            if (!timeSpent || !startedAt) {
                showNotification('Please fill in required fields', 'error');
                return;
            }
            const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
            try {
                const response = await fetch(`${baseUrl}/issue/<?= $issue['issue_key'] ?>/logwork`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ time_spent: parseFloat(timeSpent) * 3600, started_at: startedAt, description })
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('logWorkModal')).hide();
                    showNotification('Work logged successfully', 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showNotification(data.error || 'Failed to log work', 'error');
                }
            } catch (error) {
                showNotification('Error: ' + error.message, 'error');
            }
        });

        // Load more comments
        document.getElementById('load-more-comments')?.addEventListener('click', function () {
            const data = document.getElementById('comments-data');
            const container = document.getElementById('comments-container');
            if (data) {
                container.innerHTML += data.innerHTML;
                this.parentElement.remove();
                data.remove();
            }
        });

        // Toggle all comments
        document.getElementById('toggle-all-comments')?.addEventListener('click', function (e) {
            e.preventDefault();
            const container = document.getElementById('comments-container');
            const isExpanded = container.style.maxHeight !== '0px';
            container.style.maxHeight = isExpanded ? '0' : 'none';
            container.style.overflow = isExpanded ? 'hidden' : 'visible';
            this.innerHTML = isExpanded ?
                '<i class="bi bi-chevron-down"></i> Expand' :
                '<i class="bi bi-chevron-up"></i> Collapse';
        });

        // Scroll to top
        const scrollBtn = document.getElementById('scroll-to-top');
        window.addEventListener('scroll', function () {
            scrollBtn.style.display = window.pageYOffset > 300 ? 'flex' : 'none';
        });

        scrollBtn?.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Comment edit/delete
        document.addEventListener('click', function (e) {
            if (e.target.closest('.edit-comment-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.edit-comment-btn');
                const commentId = btn.dataset.commentId;
                const commentItem = document.getElementById('comment-' + commentId);
                const commentBody = commentItem.querySelector('.comment-body');
                const originalText = commentBody.textContent.trim();

                // Replace comment body with edit form
                commentBody.innerHTML = `
                <textarea class="form-control" id="edit-textarea-${commentId}" rows="3" style="margin-bottom: 8px;">${originalText}</textarea>
                <div class="form-actions" style="display: flex; gap: 8px;">
                    <button class="btn btn-sm btn-primary" onclick="saveCommentEdit(${commentId})">
                        <i class="bi bi-check-circle"></i> Save
                    </button>
                    <button class="btn btn-sm btn-outline" onclick="cancelCommentEdit(${commentId}, '${originalText.replace(/'/g, "\\'")}')">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                </div>
            `;

                // Focus textarea
                document.getElementById(`edit-textarea-${commentId}`).focus();
            }
            if (e.target.closest('.delete-comment-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-comment-btn');

                // Show confirmation dialog
                if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
                    return;
                }

                const commentId = btn.dataset.commentId;
                const baseUrl = window.location.pathname.split('/public/')[0] + '/public';

                fetch(`${baseUrl}/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success || response.ok) {
                            const commentElement = document.getElementById('comment-' + commentId);
                            if (commentElement) {
                                commentElement.style.opacity = '0.5';
                                setTimeout(() => {
                                    commentElement.remove();
                                    showNotification('Comment deleted successfully', 'success');
                                }, 200);
                            }
                        } else {
                            showNotification(data.error || 'Failed to delete comment', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        showNotification('Error deleting comment: ' + error.message, 'error');
                    });
            }
        });

        // Comment edit functions
        window.saveCommentEdit = async function (commentId) {
            const textarea = document.getElementById(`edit-textarea-${commentId}`);
            const newBody = textarea.value.trim();

            if (!newBody) {
                showNotification('Comment cannot be empty', 'error');
                return;
            }

            const baseUrl = window.location.pathname.split('/public/')[0] + '/public';

            try {
                const response = await fetch(`${baseUrl}/comments/${commentId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ body: newBody })
                });

                const data = await response.json();

                if (data.success || response.ok) {
                    const commentItem = document.getElementById('comment-' + commentId);
                    commentItem.querySelector('.comment-body').innerHTML = `${newBody}<br><em style="color: #999; font-size: 12px;">(edited)</em>`;
                    showNotification('Comment updated successfully', 'success');
                } else {
                    showNotification(data.error || 'Failed to update comment', 'error');
                }
            } catch (error) {
                console.error('Edit error:', error);
                showNotification('Error updating comment: ' + error.message, 'error');
            }
        };

        window.cancelCommentEdit = function (commentId, originalText) {
            const commentItem = document.getElementById('comment-' + commentId);
            commentItem.querySelector('.comment-body').textContent = originalText;
        };

        // Watch/Vote helpers
        window.watchIssue = function (isWatching) {
            const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
            const action = isWatching ? 'unwatch' : 'watch';
            fetch(`${baseUrl}/issue/<?= $issue['issue_key'] ?>/${action}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin'
            }).then(() => {
                showNotification(`Issue ${action}ed`, 'success');
                setTimeout(() => window.location.reload(), 500);
            });
        };

        window.voteIssue = function (hasVoted) {
            const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
            const action = hasVoted ? 'unvote' : 'vote';
            fetch(`${baseUrl}/issue/<?= $issue['issue_key'] ?>/${action}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin'
            }).then(() => {
                showNotification(`Vote ${action}d`, 'success');
                setTimeout(() => window.location.reload(), 500);
            });
        };
    });
</script>

<!-- Attachments Modal -->
<div class="modal fade" id="attachmentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-paperclip me-2"></i>Attachments
                    <?php if (!empty($issue['attachments'])): ?>
                    <span class="badge bg-secondary ms-2"><?= count((array) ($issue['attachments'] ?? [])) ?></span>
                    <?php endif; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (empty($issue['attachments'])): ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-file-earmark"></i></div>
                    <p class="empty-text">No attachments yet.</p>
                </div>
                <?php else: ?>
                <div class="attachments-grid">
                    <?php foreach ((array) ($issue['attachments'] ?? []) as $attachment): ?>
                    <div class="attachment-item">
                        <div class="attachment-icon">
                            <?php
                            $ext = strtolower(pathinfo($attachment['original_name'], PATHINFO_EXTENSION));
                            $iconClass = 'file-earmark';
                            $bgColor = '#8B1956';

                            if (in_array($ext, ['pdf'])) {
                                $iconClass = 'file-pdf';
                                $bgColor = '#D1453B';
                            } elseif (in_array($ext, ['doc', 'docx'])) {
                                $iconClass = 'file-word';
                                $bgColor = '#2B579A';
                            } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                $iconClass = 'file-spreadsheet';
                                $bgColor = '#217346';
                            } elseif (in_array($ext, ['ppt', 'pptx'])) {
                                $iconClass = 'file-slides';
                                $bgColor = '#D24726';
                            } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                $iconClass = 'file-image';
                                $bgColor = '#546E7A';
                            } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
                                $iconClass = 'file-zip';
                                $bgColor = '#FF9800';
                            } elseif (in_array($ext, ['txt'])) {
                                $iconClass = 'file-text';
                                $bgColor = '#757575';
                            }
                            ?>
                            <div class="file-icon-circle" style="background-color: <?= $bgColor ?>">
                                <i class="bi bi-<?= $iconClass ?>"></i>
                            </div>
                        </div>

                        <div class="attachment-info">
                            <a href="<?= url("/attachments/{$attachment['id']}") ?>" target="_blank"
                                class="attachment-name" title="<?= e($attachment['original_name']) ?>">
                                <?= e($attachment['original_name']) ?>
                            </a>
                            <div class="attachment-meta">
                                <span class="file-size">
                                    <i class="bi bi-disc"></i>
                                    <?php
                                    $size = $attachment['file_size'];
                                    if ($size < 1024) {
                                        echo $size . ' B';
                                    } elseif ($size < 1024 * 1024) {
                                        echo round($size / 1024, 1) . ' KB';
                                    } else {
                                        echo round($size / (1024 * 1024), 1) . ' MB';
                                    }
                                    ?>
                                </span>
                                <span class="file-time">
                                    <i class="bi bi-clock"></i>
                                    <?= time_ago($attachment['created_at']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="attachment-action-btn preview-trigger"
                                data-url="<?= url("/attachments/{$attachment['id']}") ?>"
                                data-mime="<?= $attachment['mime_type'] ?>"
                                data-filename="<?= e($attachment['original_name']) ?>" title="Preview">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="<?= url("/attachments/{$attachment['id']}") ?>" target="_blank"
                                class="attachment-action-btn" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        style="max-width: 1200px; margin-top: 10px;">
        <div class="modal-content h-100" style="min-height: 80vh;">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye"></i> <span id="previewTitle">File Preview</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="previewBody">
                <div class="d-flex justify-content-center align-items-center h-100 text-muted">
                    <div class="spinner-border text-primary me-2" role="status"></div>
                    <span>Loading preview...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Preview Functionality
        const previewModalEl = document.getElementById('previewModal');
        const attachmentsModalEl = document.getElementById('attachmentsModal');

        let previewModalInstance = null;
        let attachmentsModalInstance = null;

        // Initialize instances
        if (previewModalEl) previewModalInstance = new bootstrap.Modal(previewModalEl);
        if (attachmentsModalEl) attachmentsModalInstance = new bootstrap.Modal(attachmentsModalEl); // Create new or get existing

        // Handle Preview Button Click
        document.querySelectorAll('.preview-trigger').forEach(btn => {
            btn.addEventListener('click', function () {
                const url = this.getAttribute('data-url');
                const mime = this.getAttribute('data-mime');
                const filename = this.getAttribute('data-filename');

                // Update Content
                const previewBody = document.getElementById('previewBody');
                const previewTitle = document.getElementById('previewTitle');

                previewTitle.textContent = filename;
                previewBody.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100 p-5"><div class="spinner-border text-primary me-2"></div> Loading...</div>';

                // 1. Hide Attachments Modal
                // We use getting instance from DOM element to be safe
                const currentAttModal = bootstrap.Modal.getInstance(attachmentsModalEl);
                if (currentAttModal) {
                    currentAttModal.hide();
                }

                // 2. Show Preview Modal (after short delay to allow nice transition)
                setTimeout(() => {
                    if (previewModalInstance) {
                        previewModalInstance.show();
                        // Render content
                        renderPreview(url, mime, previewBody, filename);
                    }
                }, 200);
            });
        });

        // Handle Preview Modal Close -> Restore Attachments Modal
        if (previewModalEl) {
            previewModalEl.addEventListener('hidden.bs.modal', function () {
                // Re-open attachments modal
                if (attachmentsModalEl) {
                    const currentAttModal = new bootstrap.Modal(attachmentsModalEl);
                    currentAttModal.show();
                }
            });
        }

        function renderPreview(url, mime, container, filename) {
            // Append preview param for local files
            const previewUrl = url + (url.includes('?') ? '&' : '?') + 'preview=1';

            // Native Browser Support (PDF, Image, Audio, Video, Text)
            const nativeTypes = [
                'application/pdf',
                'image/',
                'video/',
                'audio/',
                'text/',
                'application/json',
                'application/javascript'
            ];

            const isNative = nativeTypes.some(type => mime.startsWith(type) || mime === 'application/pdf');

            if (isNative) {
                if (mime === 'application/pdf') {
                    container.innerHTML = `<iframe src="${previewUrl}" width="100%" height="100%" style="border:none; min-height: 80vh;"></iframe>`;
                } else if (mime.startsWith('image/')) {
                    container.innerHTML = `<div class="d-flex justify-content-center align-items-center h-100 bg-light p-4"><img src="${previewUrl}" class="img-fluid" style="max-height: 80vh;" onerror="this.outerHTML='<div class=\'text-center text-muted\'><i class=\'bi bi-file-earmark-x display-1\'></i><p class=\'mt-2\'>Preview not available or file not found</p></div>'"></div>`;
                } else if (mime.startsWith('video/')) {
                    container.innerHTML = `<div class="d-flex justify-content-center align-items-center h-100 bg-black"><video controls class="w-100" style="max-height: 80vh;"><source src="${previewUrl}" type="${mime}"></video></div>`;
                } else if (mime.startsWith('audio/')) {
                    container.innerHTML = `<div class="d-flex justify-content-center align-items-center h-100 bg-light p-5"><audio controls class="w-75"><source src="${previewUrl}" type="${mime}"></audio></div>`;
                } else {
                    // Text/Code
                    fetch(previewUrl).then(res => res.text()).then(txt => {
                        container.innerHTML = `<pre class="p-4 bg-light m-0 overflow-auto" style="height: 80vh;"><code>${txt.replace(/</g, '&lt;')}</code></pre>`;
                    });
                }
            }
            // Google Docs Viewer for Office Files (DOC, PPT, XLS)
            else {
                const isLocalhost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname) || window.location.hostname.endsWith('.test');

                if (isLocalhost) {
                    container.innerHTML = `
                        <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                            <i class="bi bi-laptop display-1 text-muted mb-3"></i>
                            <h4>Localhost / Private Network Detected</h4>
                            <p class="text-muted mb-4" style="max-width: 400px;">
                                Google Docs Viewer requires a public URL to preview Office documents (${mime}). 
                                Since you are running locally, this service cannot reach your file.
                            </p>
                            <a href="${url}" class="btn btn-primary"><i class="bi bi-download"></i> Download File Instead</a>
                        </div>
                    `;
                } else {
                    // Public Server - Use Google Viewer
                    const encodedUrl = encodeURIComponent(window.location.origin + url + '&preview=1');
                    const googleViewerUrl = `https://docs.google.com/viewer?url=${encodedUrl}&embedded=true`;

                    container.innerHTML = `
                        <div class="d-flex flex-column h-100">
                            <div class="alert alert-info m-0 rounded-0 p-2 small">
                                <i class="bi bi-info-circle"></i> Preview via Google Docs
                            </div>
                            <iframe src="${googleViewerUrl}" width="100%" height="100%" style="border:none; flex:1; min-height: 70vh;"></iframe>
                        </div>
                    `;
                }
            }
        }
    });
</script>

<style>
    .attachment-action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        color: #6B778C;
        transition: all 0.2s;
        background: transparent;
        border: none;
        text-decoration: none;
    }

    .attachment-action-btn:hover {
        background-color: #EBECF0;
        color: #172B4D;
    }

    button.attachment-action-btn {
        padding: 0;
        /* Reset button padding */
    }
</style>

<?php \App\Core\View::endSection(); ?>