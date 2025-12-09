<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<?php 
// Get current user ID for permission checks
$authUser = auth();
$currentUserId = $authUser ? $authUser['id'] : null;
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$issue['project_key']}") ?>"><?= e($issue['project_name']) ?></a></li>
            <li class="breadcrumb-item active"><?= e($issue['issue_key']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Issue Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="badge me-2" style="background-color: <?= e($issue['issue_type_color']) ?>">
                            <i class="bi bi-<?= e($issue['issue_type_icon']) ?>"></i>
                        </span>
                        <h4 class="mb-0 me-3"><?= e($issue['issue_key']) ?></h4>
                        <span class="badge" style="background-color: <?= e($issue['status_color']) ?>">
                            <?= e($issue['status_name']) ?>
                        </span>
                    </div>
                    <div class="btn-group">
                        <?php if (can('issues.edit', $issue['project_id'])): ?>
                        <a href="<?= url("/issue/{$issue['issue_key']}/edit") ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <?php endif; ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <?php if (can('issues.assign', $issue['project_id'])): ?>
                                <li><a class="dropdown-item" href="#" onclick="assignIssue()">
                                    <i class="bi bi-person me-2"></i> Assign
                                </a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="#" onclick="watchIssue(<?= $isWatching ? 'true' : 'false' ?>)">
                                    <i class="bi bi-eye<?= $isWatching ? '-slash' : '' ?> me-2"></i>
                                    <?= $isWatching ? 'Unwatch' : 'Watch' ?>
                                </a></li>
                                <?php if ($issue['reporter_id'] !== user_id()): ?>
                                <li><a class="dropdown-item" href="#" onclick="voteIssue(<?= $hasVoted ? 'true' : 'false' ?>)">
                                    <i class="bi bi-hand-thumbs-<?= $hasVoted ? 'down' : 'up' ?> me-2"></i>
                                    <?= $hasVoted ? 'Remove Vote' : 'Vote' ?>
                                </a></li>
                                <?php endif; ?>
                                <?php if (can('issues.link', $issue['project_id'])): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="linkIssue()">
                                    <i class="bi bi-link me-2"></i> Link Issue
                                </a></li>
                                <?php endif; ?>
                                <?php if (can('issues.log_work', $issue['project_id'])): ?>
                                <li><a class="dropdown-item" href="#" onclick="logWork()">
                                    <i class="bi bi-clock me-2"></i> Log Work
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title mb-3"><?= e($issue['summary']) ?></h5>

                    <?php if ($issue['description']): ?>
                    <div class="mb-4">
                        <h6>Description</h6>
                        <div class="markdown-content">
                            <?= nl2br(e($issue['description'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Issue Details -->
                    <div class="row g-4">
                        <?php if ($issue['assignee_name']): ?>
                        <div class="col-md-6">
                            <h6>Assignee</h6>
                            <div class="d-flex align-items-center">
                                <?php if ($issue['assignee_avatar']): ?>
                                <img src="<?= e($issue['assignee_avatar']) ?>" class="rounded-circle me-2" width="32" height="32">
                                <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                     style="width: 32px; height: 32px; font-size: 14px;">
                                    <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                                </div>
                                <?php endif; ?>
                                <span><?= e($issue['assignee_name']) ?></span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6">
                            <h6>Reporter</h6>
                            <div class="d-flex align-items-center">
                                <?php if ($issue['reporter_avatar']): ?>
                                <img src="<?= e($issue['reporter_avatar']) ?>" class="rounded-circle me-2" width="32" height="32">
                                <?php else: ?>
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                     style="width: 32px; height: 32px; font-size: 14px;">
                                    <?= strtoupper(substr($issue['reporter_name'], 0, 1)) ?>
                                </div>
                                <?php endif; ?>
                                <span><?= e($issue['reporter_name']) ?></span>
                            </div>
                        </div>

                        <?php if ($issue['priority_name']): ?>
                        <div class="col-md-6">
                            <h6>Priority</h6>
                            <span class="badge" style="background-color: <?= e($issue['priority_color']) ?>">
                                <?= e($issue['priority_name']) ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <?php if ($issue['labels']): ?>
                        <div class="col-md-6">
                            <h6>Labels</h6>
                            <div>
                                <?php foreach ($issue['labels'] as $label): ?>
                                <span class="badge me-1" style="background-color: <?= e($label['color']) ?>">
                                    <?= e($label['name']) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($issue['due_date']): ?>
                        <div class="col-md-6">
                            <h6>Due Date</h6>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-calendar me-1"></i>
                                <?= format_date($issue['due_date']) ?>
                            </span>
                        </div>
                        <?php endif; ?>

                        <?php if ($issue['story_points']): ?>
                        <div class="col-md-6">
                            <h6>Story Points</h6>
                            <span class="badge bg-info">
                                <i class="bi bi-hash me-1"></i>
                                <?= e($issue['story_points']) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center" style="gap: 10px;">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-chat-left-text me-2"></i>
                            Comments
                            <?php if (!empty($issue['comments'])): ?>
                            <span class="badge bg-primary ms-2"><?= count($issue['comments']) ?></span>
                            <?php endif; ?>
                        </h6>
                    </div>
                    <?php if (!empty($issue['comments'])): ?>
                    <button class="btn btn-sm btn-outline-secondary flex-shrink-0" id="toggle-all-comments" type="button" style="white-space: nowrap;">
                        <i class="bi bi-chevron-up me-1"></i>Collapse All
                    </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <!-- Add Comment Form (Sticky) -->
                    <?php if (can('issues.comment', $issue['project_id'])): ?>
                    <div id="comment-form-container" class="sticky-comment-form mb-4">
                        <form id="comment-form" class="p-3 bg-light rounded border">
                            <div class="mb-3 mb-0">
                                <label class="form-label d-flex align-items-center">
                                    <i class="bi bi-pencil-square me-2"></i> Add a comment
                                </label>
                                <textarea class="form-control" name="body" rows="3" placeholder="Write your comment here..." required></textarea>
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-check-circle me-1"></i> Post Comment
                                </button>
                                <button type="reset" class="btn btn-light btn-sm">
                                    <i class="bi bi-x-circle me-1"></i> Clear
                                </button>
                            </div>
                        </form>
                    </div>
                    <hr class="my-3">
                    <?php endif; ?>

                    <!-- Comments List with Pagination -->
                    <div id="comments-list">
                        <?php if (empty($issue['comments'])): ?>
                        <div class="alert alert-info text-center py-4">
                            <i class="bi bi-chat-left-quote me-2"></i>
                            No comments yet. Be the first to comment!
                        </div>
                        <?php else: ?>
                        <div class="comments-container" id="comments-container">
                            <!-- Comments will be loaded with pagination -->
                            <?php 
                            $commentsPerPage = 5;
                            $totalComments = count($issue['comments']);
                            $showInitial = min($commentsPerPage, $totalComments);
                            ?>
                            
                            <?php for ($i = 0; $i < $showInitial; $i++): 
                                $comment = $issue['comments'][$i];
                            ?>
                            <div class="comment mb-4 p-3 border rounded comment-item" id="comment-<?= $comment['id'] ?>">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <?php if (($comment['user']['avatar'] ?? null)): ?>
                                        <img src="<?= e($comment['user']['avatar']) ?>" class="rounded-circle" width="48" height="48" alt="Avatar">
                                        <?php else: ?>
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width: 48px; height: 48px; font-weight: bold; font-size: 20px;">
                                            <?= strtoupper(substr(($comment['user']['first_name'] ?? 'U'), 0, 1)) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2 justify-content-between">
                                            <div>
                                                <strong class="me-2"><?= e($comment['user']['display_name'] ?? 'Unknown User') ?></strong>
                                                <small class="text-muted"><?= time_ago($comment['created_at']) ?></small>
                                                <?php if ($comment['updated_at'] !== $comment['created_at']): ?>
                                                <small class="text-muted ms-2"><em>(edited)</em></small>
                                                <?php endif; ?>
                                            </div>
                                            <!-- Edit & Delete Buttons -->
                                             <div class="comment-actions">
                                                 <?php 
                                                 $canEditDelete = ($comment['user_id'] === $currentUserId) || 
                                                                  can('comments.edit_all', $issue['project_id']) ||
                                                                  can('comments.delete_all', $issue['project_id']);
                                                 ?>
                                                 <?php if ($canEditDelete): ?>
                                                <button class="btn btn-sm btn-link text-primary edit-comment-btn" 
                                                        data-comment-id="<?= $comment['id'] ?>" 
                                                        data-issue-key="<?= $issue['issue_key'] ?>"
                                                        title="Edit comment">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-link text-danger delete-comment-btn" 
                                                        data-comment-id="<?= $comment['id'] ?>" 
                                                        data-issue-key="<?= $issue['issue_key'] ?>"
                                                        title="Delete comment">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="comment-body lh-lg">
                                            <?= nl2br(e($comment['body'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endfor; ?>

                            <!-- Load More Button -->
                            <?php if ($totalComments > $showInitial): ?>
                            <div class="text-center mb-3">
                                <button class="btn btn-outline-secondary btn-sm" id="load-more-comments">
                                    <i class="bi bi-arrow-down me-1"></i>
                                    Load More Comments (<?= $totalComments - $showInitial ?> remaining)
                                </button>
                            </div>
                            
                            <!-- Hidden comments data -->
                            <div id="comments-data" style="display:none;">
                                <?php for ($i = $showInitial; $i < $totalComments; $i++): 
                                    $comment = $issue['comments'][$i];
                                ?>
                                <div class="comment mb-4 p-3 border rounded comment-item" id="comment-<?= $comment['id'] ?>">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <?php if (($comment['user']['avatar'] ?? null)): ?>
                                            <img src="<?= e($comment['user']['avatar']) ?>" class="rounded-circle" width="48" height="48" alt="Avatar">
                                            <?php else: ?>
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                 style="width: 48px; height: 48px; font-weight: bold; font-size: 20px;">
                                                <?= strtoupper(substr(($comment['user']['first_name'] ?? 'U'), 0, 1)) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2 justify-content-between">
                                                <div>
                                                    <strong class="me-2"><?= e($comment['user']['display_name'] ?? 'Unknown User') ?></strong>
                                                    <small class="text-muted"><?= time_ago($comment['created_at']) ?></small>
                                                    <?php if ($comment['updated_at'] !== $comment['created_at']): ?>
                                                    <small class="text-muted ms-2"><em>(edited)</em></small>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- Edit & Delete Buttons -->
                                                 <div class="comment-actions">
                                                     <?php 
                                                     $canEditDelete = ($comment['user_id'] === $currentUserId) || 
                                                                      can('comments.edit_all', $issue['project_id']) ||
                                                                      can('comments.delete_all', $issue['project_id']);
                                                     ?>
                                                     <?php if ($canEditDelete): ?>
                                                     <button class="btn btn-sm btn-link text-primary edit-comment-btn" 
                                                             data-comment-id="<?= $comment['id'] ?>" 
                                                             data-issue-key="<?= $issue['issue_key'] ?>"
                                                             title="Edit comment">
                                                         <i class="bi bi-pencil"></i>
                                                     </button>
                                                     <button class="btn btn-sm btn-link text-danger delete-comment-btn" 
                                                             data-comment-id="<?= $comment['id'] ?>" 
                                                             data-issue-key="<?= $issue['issue_key'] ?>"
                                                             title="Delete comment">
                                                         <i class="bi bi-trash"></i>
                                                     </button>
                                                     <?php endif; ?>
                                                 </div>
                                            </div>
                                            <div class="comment-body lh-lg">
                                                <?= nl2br(e($comment['body'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Attachments Section -->
            <?php if (!empty($issue['attachments'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Attachments</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($issue['attachments'] as $attachment): ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-2 border rounded">
                                <i class="bi bi-file-earmark me-3 fs-4 text-muted"></i>
                                <div class="flex-grow-1">
                                    <a href="<?= url("/attachments/{$attachment['id']}") ?>" target="_blank" class="text-decoration-none">
                                        <?= e($attachment['filename']) ?>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <?= format_file_size($attachment['size']) ?> •
                                        <?= time_ago($attachment['created_at']) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Work Logs Section -->
            <?php if (!empty($issue['worklogs'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Work Log</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
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
            </div>
            <?php endif; ?>

            <!-- Issue Links -->
            <?php if (!empty(($links['outward'] ?? [])) || !empty(($links['inward'] ?? []))): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Linked Issues</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($links['outward'] ?? [])): ?>
                            <?php foreach ($links['outward'] as $link): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge me-2" style="background-color: <?= e($link['issue_type_color'] ?? '#ccc') ?>">
                                        <i class="bi bi-<?= e($link['issue_type_icon'] ?? 'link') ?>"></i>
                                    </span>
                                    <a href="<?= url("/issue/{$link['issue_key']}") ?>" class="text-decoration-none">
                                        <?= e($link['issue_key'] ?? 'Unknown') ?>
                                    </a>
                                    <span class="text-muted ms-2">
                                        <?= e($link['description'] ?? '') ?>
                                    </span>
                                </div>
                                <span class="badge" style="background-color: <?= e($link['status_color'] ?? '#ccc') ?>">
                                    <?= e($link['status_name'] ?? 'Unknown') ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($links['inward'] ?? [])): ?>
                            <?php foreach ($links['inward'] as $link): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge me-2" style="background-color: <?= e($link['issue_type_color'] ?? '#ccc') ?>">
                                        <i class="bi bi-<?= e($link['issue_type_icon'] ?? 'link') ?>"></i>
                                    </span>
                                    <a href="<?= url("/issue/{$link['issue_key']}") ?>" class="text-decoration-none">
                                        <?= e($link['issue_key'] ?? 'Unknown') ?>
                                    </a>
                                    <span class="text-muted ms-2">
                                        <?= e($link['description'] ?? '') ?>
                                    </span>
                                </div>
                                <span class="badge" style="background-color: <?= e($link['status_color'] ?? '#ccc') ?>">
                                    <?= e($link['status_name'] ?? 'Unknown') ?>
                                </span>
                            </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <!-- Activity History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center activity-header" style="cursor: pointer;">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        Activity
                        <?php if (!empty($history)): ?>
                        <span class="badge bg-secondary ms-2"><?= count($history) ?></span>
                        <?php endif; ?>
                    </h6>
                    <button class="btn btn-sm btn-outline-secondary activity-toggle" type="button" title="Click to expand/collapse">
                        <i class="bi bi-chevron-up"></i>
                    </button>
                </div>
                <div class="card-body activity-body" id="activity-body">
                    <?php if (empty($history)): ?>
                    <div class="alert alert-info text-center py-4">
                        <i class="bi bi-info-circle me-2"></i>
                        No activity yet. Changes will appear here.
                    </div>
                    <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($history as $entry): ?>
                        <div class="timeline-item mb-3 activity-item">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                         style="width: 32px; height: 32px;">
                                        <i class="bi bi-arrow-repeat text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small">
                                        <strong><?= e($entry['user_name'] ?? 'System') ?></strong>
                                        changed <strong><?= e($entry['field']) ?></strong>
                                        <?php if ($entry['old_value']): ?>
                                        from <em>"<?= e($entry['old_value']) ?>"</em>
                                        <?php endif; ?>
                                        <?php if ($entry['new_value']): ?>
                                        to <em>"<?= e($entry['new_value']) ?>"</em>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted"><?= time_ago($entry['created_at']) ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Scroll to Top Button -->
            <button id="scroll-to-top" class="btn btn-primary btn-lg rounded-circle" style="position: fixed; bottom: 30px; right: 30px; display: none; z-index: 99;">
                <i class="bi bi-arrow-up"></i>
            </button>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Status Transitions -->
            <?php if (!empty($transitions)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php foreach ($transitions as $transition): ?>
                        <button class="btn btn-outline-primary btn-sm"
                                onclick="transitionIssue(<?= $transition['status_id'] ?>, '<?= e($transition['status_name']) ?>')">
                            <?= e($transition['status_name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Issue Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Type:</strong>
                        <span class="badge ms-1" style="background-color: <?= e($issue['issue_type_color']) ?>">
                            <?= e($issue['issue_type_name']) ?>
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Priority:</strong>
                        <span class="badge ms-1" style="background-color: <?= e($issue['priority_color']) ?>">
                            <?= e($issue['priority_name']) ?>
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Status:</strong>
                        <span class="badge ms-1" style="background-color: <?= e($issue['status_color']) ?>">
                            <?= e($issue['status_name']) ?>
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Created:</strong>
                        <span class="text-muted ms-1"><?= format_date($issue['created_at']) ?></span>
                    </div>
                    <div class="mb-2">
                        <strong>Updated:</strong>
                        <span class="text-muted ms-1"><?= format_date($issue['updated_at']) ?></span>
                    </div>
                    <?php if ($issue['resolved_at']): ?>
                    <div class="mb-2">
                        <strong>Resolved:</strong>
                        <span class="text-muted ms-1"><?= format_date($issue['resolved_at']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- People -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">People</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Assignee:</strong><br>
                        <?php if ($issue['assignee_name']): ?>
                        <div class="d-flex align-items-center mt-1">
                            <?php if ($issue['assignee_avatar']): ?>
                            <img src="<?= e($issue['assignee_avatar']) ?>" class="rounded-circle me-2" width="24" height="24">
                            <?php else: ?>
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                 style="width: 24px; height: 24px; font-size: 10px;">
                                <?= strtoupper(substr($issue['assignee_name'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span class="small"><?= e($issue['assignee_name']) ?></span>
                        </div>
                        <?php else: ?>
                        <span class="text-muted small">Unassigned</span>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Reporter:</strong><br>
                        <div class="d-flex align-items-center mt-1">
                            <?php if ($issue['reporter_avatar']): ?>
                            <img src="<?= e($issue['reporter_avatar']) ?>" class="rounded-circle me-2" width="24" height="24">
                            <?php else: ?>
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                 style="width: 24px; height: 24px; font-size: 10px;">
                                <?= strtoupper(substr($issue['reporter_name'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <span class="small"><?= e($issue['reporter_name']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Dates</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Created:</strong><br>
                        <span class="text-muted small"><?= format_date($issue['created_at']) ?></span>
                    </div>
                    <div class="mb-2">
                        <strong>Updated:</strong><br>
                        <span class="text-muted small"><?= format_date($issue['updated_at']) ?></span>
                    </div>
                    <?php if ($issue['due_date']): ?>
                    <div class="mb-2">
                        <strong>Due Date:</strong><br>
                        <span class="text-muted small"><?= format_date($issue['due_date']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($issue['resolved_at']): ?>
                    <div class="mb-2">
                        <strong>Resolved:</strong><br>
                        <span class="text-muted small"><?= format_date($issue['resolved_at']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
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
                <div class="modal-body">
                    <p>Transition issue to: <strong id="transition-status"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Transition</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Issue Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assign-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assignee" class="form-label">Assignee</label>
                        <select class="form-control" id="assignee" name="assignee_id" required>
                            <option value="">Select a user...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Link Issue Modal -->
<div class="modal fade" id="linkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Link Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="link-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="link-type-id" class="form-label">Link Type</label>
                        <select class="form-control" id="link-type-id" name="link_type_id" required>
                            <option value="">Select link type...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="target-issue-key" class="form-label">Issue Key</label>
                        <input type="text" class="form-control" id="target-issue-key" name="target_issue_key" placeholder="e.g., BP-1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                    <div class="mb-3">
                        <label for="time-spent" class="form-label">Time Spent (hours)</label>
                        <input type="number" class="form-control" id="time-spent" name="time_spent" step="0.5" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="started-at" class="form-label">Started At</label>
                        <input type="datetime-local" class="form-control" id="started-at" name="started_at" required>
                    </div>
                    <div class="mb-3">
                        <label for="work-description" class="form-label">Description</label>
                        <textarea class="form-control" id="work-description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Log Work</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Sticky Comment Form */
.sticky-comment-form {
    position: relative;
    background: white;
    border-radius: 0.375rem;
    z-index: 1;
}

/* Comments Container with Max Height */
.comments-container {
    max-height: 600px;
    overflow-y: auto;
    padding-right: 8px;
    transition: max-height 0.3s ease, overflow 0.3s ease;
    will-change: max-height;
    contain: layout style paint;
    position: relative;
    z-index: 0;
}

.comments-container::-webkit-scrollbar {
    width: 6px;
}

.comments-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.comments-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.comments-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Comment Item Animation */
.comment-item {
    animation: slideIn 0.3s ease-in-out;
    transition: all 0.2s ease;
}

.comment-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

/* Activity Section */
.activity-header {
    user-select: none;
}

.activity-header:hover {
    background-color: #f8f9fa;
}

.activity-body {
    transition: max-height 0.3s ease, overflow 0.3s ease, padding 0.3s ease, margin 0.3s ease;
    max-height: 400px;
    overflow-y: auto;
    will-change: max-height;
    contain: layout style paint;
}

.activity-body.collapsed {
    max-height: 0;
    overflow: hidden;
    padding: 0;
    margin: 0;
}

.activity-body::-webkit-scrollbar {
    width: 6px;
}

.activity-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.activity-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.activity-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.activity-item {
    border-left: 2px solid #e9ecef;
    padding-left: 12px;
    margin-left: 8px;
    transition: all 0.2s ease;
}

.activity-item:hover {
    border-left-color: #0d6efd;
    padding-left: 16px;
}

/* Scroll to Top Button */
#scroll-to-top {
    opacity: 0.8;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

#scroll-to-top:hover {
    opacity: 1;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

/* Comment Actions (Edit & Delete Buttons) */
.comment-actions {
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.comment:hover .comment-actions,
.comment-item:hover .comment-actions {
    opacity: 1;
}

.comment-actions .btn-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    text-decoration: none;
    transition: all 0.2s ease;
}

.comment-actions .btn-link:hover {
    transform: scale(1.2);
}

.edit-comment-btn {
    color: #0d6efd !important;
}

.edit-comment-btn:hover {
    color: #0b5ed7 !important;
}

.delete-comment-btn {
    color: #dc3545 !important;
}

.delete-comment-btn:hover {
    color: #bb2d3b !important;
}

/* Better load more button */
#load-more-comments {
    transition: all 0.2s ease;
}

#load-more-comments:hover {
    background-color: #e7f1ff;
    border-color: #0d6efd;
}

/* Collapse all button */
#toggle-all-comments {
    transition: all 0.2s ease;
    padding: 0.375rem 0.75rem;
    font-size: 0.85rem;
    font-weight: 500;
    white-space: nowrap;
    min-width: auto;
    flex-shrink: 0;
}

#toggle-all-comments:hover {
    background-color: #e7f1ff;
    border-color: #0d6efd;
    color: #0d6efd;
}

#toggle-all-comments i {
    transition: transform 0.3s ease;
}

#toggle-all-comments:active i {
    transform: rotate(180deg);
}

/* Comment badge styling */
.comment mb-4 p-3 {
    border-left: 3px solid #0d6efd;
}

/* Header icons animation */
.comment-header i,
.activity-toggle i {
    transition: transform 0.3s ease;
}
</style>

<script>
let currentTransitionStatusId = null;

function transitionIssue(statusId, statusName) {
    currentTransitionStatusId = statusId;
    document.getElementById('transition-status').textContent = statusName;
    new bootstrap.Modal(document.getElementById('transitionModal')).show();
}

document.getElementById('transition-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        // Get base URL from the current location
        const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
        const response = await fetch(`${baseUrl}/issue/${issueKey}/transition`, {
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
        
        const text = await response.text();
        console.log('Response status:', response.status);
        console.log('Response text:', text);
        
        if (!response.ok) {
            alert('Failed to transition issue (HTTP ' + response.status + '): ' + text);
            return;
        }
        
        const data = JSON.parse(text);
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to transition issue: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Transition error:', error);
        alert('Failed to transition issue: ' + error.message);
    }
});

function watchIssue(isWatching) {
    const action = isWatching ? 'unwatch' : 'watch';
    const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
    fetch(`${baseUrl}/issue/${issueKey}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({})
    }).then(() => window.location.reload())
      .catch(err => {
          console.error('Watch error:', err);
          alert('Failed to update watch status');
      });
}

function voteIssue(hasVoted) {
    const action = hasVoted ? 'unvote' : 'vote';
    const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
    fetch(`${baseUrl}/issue/${issueKey}/${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({})
    }).then(() => window.location.reload())
      .catch(err => {
          console.error('Vote error:', err);
          alert('Failed to update vote');
      });
}

document.getElementById('comment-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const commentBody = formData.get('body');
    
    if (!commentBody || !commentBody.trim()) {
        alert('Please enter a comment');
        return;
    }
    
    try {
        const response = await fetch(`${baseUrl}/issue/${issueKey}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ body: commentBody })
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            console.error('Comment error response:', response.status, errorData);
            alert(errorData.error || 'Failed to add comment');
            return;
        }
        
        window.location.reload();
    } catch (error) {
        console.error('Comment fetch error:', error);
        alert('Failed to add comment: ' + error.message);
    }
});

// Global variables for issue and project
const issueKey = '<?= $issue['issue_key'] ?>';
const projectKey = '<?= $issue['project_key'] ?>';
const projectId = '<?= $issue['project_id'] ?>';

// Helper function to show notifications
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

    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        alert.remove();
    }, 3000);
}

// ============== ASSIGN ISSUE FUNCTIONALITY ==============
function assignIssue() {
    const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
    
    // Load project members for assignee dropdown
    fetch(`${baseUrl}/api/v1/projects/${projectKey}/members`, {
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

// ============== LINK ISSUE FUNCTIONALITY ==============
function linkIssue() {
    const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
    
    // Load link types from API
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

// ============== LOG WORK FUNCTIONALITY ==============
function logWork() {
    // Set default started_at to now
    const now = new Date();
    const localDateTime = now.toISOString().slice(0, 16);
    document.getElementById('started-at').value = localDateTime;
    new bootstrap.Modal(document.getElementById('logWorkModal')).show();
}

// ============== ACTIVITY SECTION COLLAPSE/EXPAND ==============
document.addEventListener('DOMContentLoaded', function() {
    const activityHeader = document.querySelector('.activity-header');
    const activityBody = document.getElementById('activity-body');
    const activityToggle = document.querySelector('.activity-toggle i');
    
    if (activityHeader && activityBody && activityToggle) {
        activityHeader.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle collapsed state
            activityBody.classList.toggle('collapsed');
            
            // Update icon
            if (activityBody.classList.contains('collapsed')) {
                activityToggle.className = 'bi bi-chevron-down';
            } else {
                activityToggle.className = 'bi bi-chevron-up';
            }
            
            // Console log for debugging
            console.log('Activity toggled. Collapsed:', activityBody.classList.contains('collapsed'));
        });
    }

    // ============== LOAD MORE COMMENTS ==============
    const loadMoreBtn = document.getElementById('load-more-comments');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const commentsData = document.getElementById('comments-data');
            const container = document.getElementById('comments-container');
            
            if (commentsData) {
                // Move all hidden comments to the container
                const hiddenComments = commentsData.innerHTML;
                container.innerHTML += hiddenComments;
                
                // Animate new comments
                const newComments = container.querySelectorAll('.comment-item');
                newComments.forEach(comment => {
                    if (!comment.hasAttribute('data-loaded')) {
                        comment.setAttribute('data-loaded', 'true');
                    }
                });
                
                // Remove load more button and hidden data
                loadMoreBtn.parentElement.remove();
                commentsData.remove();
            }
        });
    }

    // ============== SCROLL TO TOP FUNCTIONALITY ==============
    const scrollTopBtn = document.getElementById('scroll-to-top');
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollTopBtn.style.display = 'block';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });

    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ============== TOGGLE ALL COMMENTS COLLAPSE ==============
    const toggleAllCommentsBtn = document.getElementById('toggle-all-comments');
    if (toggleAllCommentsBtn) {
        let commentsExpanded = true;  // Start expanded
        
        toggleAllCommentsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const container = document.getElementById('comments-container');
            commentsExpanded = !commentsExpanded;
            
            if (commentsExpanded) {
                // Expand all comments - calculate full height
                container.style.maxHeight = 'none';
                container.style.overflow = 'visible';
                toggleAllCommentsBtn.innerHTML = '<i class="bi bi-chevron-up me-1"></i>Collapse All';
                console.log('Comments expanded');
            } else {
                // Collapse to default height
                container.style.maxHeight = '600px';
                container.style.overflow = 'auto';
                toggleAllCommentsBtn.innerHTML = '<i class="bi bi-chevron-down me-1"></i>Expand All';
                console.log('Comments collapsed');
            }
        });
    }

    // ============== SMOOTH SCROLL ANCHOR LINKS ==============
    // Auto-scroll to comment if in URL hash
    if (window.location.hash) {
        setTimeout(function() {
            const element = document.querySelector(window.location.hash);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
                element.classList.add('highlight');
            }
        }, 100);
    }

    // ============== EDIT COMMENT FUNCTIONALITY ==============
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-comment-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.edit-comment-btn');
            const commentId = btn.dataset.commentId;
            const issueKey = btn.dataset.issueKey;
            const commentElement = document.getElementById('comment-' + commentId);
            const commentBody = commentElement.querySelector('.comment-body');
            const currentText = commentBody.innerText;

            // Create edit form
            const editForm = document.createElement('div');
            editForm.className = 'comment-edit-form mt-3';
            editForm.innerHTML = `
                <div class="form-group">
                    <textarea class="form-control" id="edit-comment-text-${commentId}" rows="4">${currentText}</textarea>
                </div>
                <div class="mt-2 d-flex gap-2">
                    <button class="btn btn-primary btn-sm save-edit-btn" data-comment-id="${commentId}" data-issue-key="${issueKey}">
                        <i class="bi bi-check-circle me-1"></i> Save
                    </button>
                    <button class="btn btn-secondary btn-sm cancel-edit-btn" data-comment-id="${commentId}">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            `;

            // Hide comment body and show edit form
            commentBody.style.display = 'none';
            commentElement.querySelector('.comment-actions').parentElement.insertAdjacentElement('afterend', editForm);

            // Cancel edit
            editForm.querySelector('.cancel-edit-btn').addEventListener('click', function() {
                commentBody.style.display = '';
                editForm.remove();
            });

            // Save edit
            editForm.querySelector('.save-edit-btn').addEventListener('click', function() {
                const newText = document.getElementById('edit-comment-text-' + commentId).value;

                if (!newText.trim()) {
                    alert('Comment cannot be empty');
                    return;
                }

                // Submit form via fetch
                 fetch(`/jira_clone_system/public/comments/${commentId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ body: newText })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response is not JSON');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        commentBody.innerHTML = newText.replace(/\n/g, '<br>');
                        commentBody.style.display = '';
                        editForm.remove();
                        showNotification('Comment updated successfully', 'success');
                    } else {
                        showNotification('Error: ' + (data.error || 'Failed to update comment'), 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error updating comment: ' + error.message, 'error');
                });
            });
        }
    });

    // ============== DELETE COMMENT FUNCTIONALITY ==============
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-comment-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.delete-comment-btn');
            const commentId = btn.dataset.commentId;
            const issueKey = btn.dataset.issueKey;

            if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
                return;
            }

            // Submit delete via fetch
            fetch(`/jira_clone_system/public/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const commentElement = document.getElementById('comment-' + commentId);
                    commentElement.style.transition = 'opacity 0.3s ease';
                    commentElement.style.opacity = '0';
                    setTimeout(() => {
                        commentElement.remove();
                        showNotification('Comment deleted successfully', 'success');
                    }, 300);
                } else {
                    showNotification('Error: ' + (data.error || 'Failed to delete comment'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting comment: ' + error.message, 'error');
            });
        }
    });



    document.getElementById('assign-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const assigneeId = document.getElementById('assignee').value;
        const baseUrl = window.location.pathname.split('/public/')[0] + '/public';
        
        if (!assigneeId) {
            showNotification('Please select an assignee', 'error');
            return;
        }

        try {
            const response = await fetch(`${baseUrl}/issue/${issueKey}/assign`, {
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
                window.location.reload();
            } else {
                showNotification(data.error || 'Failed to assign issue', 'error');
            }
        } catch (error) {
            console.error('Assign error:', error);
            showNotification('Error assigning issue: ' + error.message, 'error');
        }
    });



    document.getElementById('link-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const linkTypeId = document.getElementById('link-type-id').value;
        const targetIssueKey = document.getElementById('target-issue-key').value;
        const baseUrl = window.location.pathname.split('/public/')[0] + '/public';

        if (!linkTypeId || !targetIssueKey) {
            showNotification('Please fill in all fields', 'error');
            return;
        }

        try {
            const response = await fetch(`${baseUrl}/issue/${issueKey}/link`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    link_type_id: parseInt(linkTypeId),
                    target_issue_key: targetIssueKey
                })
            });

            const data = await response.json();
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('linkModal')).hide();
                showNotification('Issue linked successfully', 'success');
                window.location.reload();
            } else {
                showNotification(data.error || 'Failed to link issue', 'error');
            }
        } catch (error) {
            console.error('Link error:', error);
            showNotification('Error linking issue: ' + error.message, 'error');
        }
    });



    document.getElementById('logwork-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const timeSpent = document.getElementById('time-spent').value;
        const startedAt = document.getElementById('started-at').value;
        const description = document.getElementById('work-description').value;
        const baseUrl = window.location.pathname.split('/public/')[0] + '/public';

        if (!timeSpent || !startedAt) {
            showNotification('Please fill in required fields', 'error');
            return;
        }

        try {
            const response = await fetch(`${baseUrl}/issue/${issueKey}/logwork`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    time_spent: parseFloat(timeSpent) * 3600, // Convert hours to seconds
                    started_at: startedAt,
                    description: description
                })
            });

            const data = await response.json();
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('logWorkModal')).hide();
                showNotification('Work logged successfully', 'success');
                window.location.reload();
            } else {
                showNotification(data.error || 'Failed to log work', 'error');
            }
        } catch (error) {
            console.error('Log work error:', error);
            showNotification('Error logging work: ' + error.message, 'error');
        }
    });
});
</script>

<?php \App\Core\View::endSection(); ?>