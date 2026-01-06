<?php \App\Core\View:: extends('layouts.app'); ?>
<?php \App\Core\View::section('content'); ?>

<?php
$authUser = auth();
$currentUserId = $authUser['id'] ?? null;

$issue = $issue ?? [];
$links = $links ?? ['inward' => [], 'outward' => []];
$history = $history ?? [];
$transitions = $transitions ?? [];
$isWatching = $isWatching ?? false;
$hasVoted = $hasVoted ?? false;
?>

<div class="issue-detail-wrapper">

    <!-- ========================= BREADCRUMB ========================= -->
    <div class="breadcrumb-section">
        <div class="breadcrumb">
            <a href="<?= url('/') ?>" class="breadcrumb-link"><i class="bi bi-house-door"></i> Home</a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url("/projects/{$issue['project_key']}") ?>" class="breadcrumb-link">
                <?= e($issue['project_name'] ?? '') ?>
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current"><?= e($issue['issue_key'] ?? '') ?></span>
        </div>
    </div>

    <!-- ========================= MAIN LAYOUT ========================= -->
    <div class="issue-main-container">

        <!-- ========================= LEFT PANEL ========================= -->
        <div class="issue-left-panel">

            <!-- ========================= ISSUE HEADER ========================= -->
            <div class="issue-header-card">
                <div class="issue-header-top">

                    <div class="issue-key-group">
                        <div class="issue-type-icon" style="background: <?= e($issue['issue_type_color'] ?? '#ccc') ?>">
                            <i class="bi bi-<?= e($issue['issue_type_icon'] ?? 'bug') ?>"></i>
                        </div>

                        <h1 class="issue-key"><?= e($issue['issue_key'] ?? '') ?></h1>

                        <span class="issue-status-badge"
                            style="background: <?= e($issue['status_color'] ?? '#999') ?>;">
                            <?= e($issue['status_name'] ?? '') ?>
                        </span>
                    </div>

                    <div class="issue-actions-group">

                        <?php if (can('issues.edit', $issue['project_id'] ?? null)): ?>
                            <a href="#" onclick="CreateIssueModal.openEdit('<?= e($issue['issue_key']) ?>');return false;"
                                class="btn btn-sm btn-outline">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        <?php endif; ?>

                        <button class="btn btn-sm btn-outline" data-bs-toggle="modal"
                            data-bs-target="#attachmentsModal">
                            <i class="bi bi-paperclip"></i> Attachments
                            <?php if (!empty($issue['attachments'])): ?>
                                <span class="badge bg-secondary ms-1"><?= count($issue['attachments']) ?></span>
                            <?php endif; ?>
                        </button>

                        <div class="dropdown-container">
                            <button class="btn btn-sm btn-outline dropdown-toggle-btn">
                                <i class="bi bi-three-dots"></i>
                            </button>

                            <div class="dropdown-menu d-none">
                                <?php if (can('issues.assign', $issue['project_id'] ?? null)): ?>
                                    <a href="#" onclick="assignIssue();return false;" class="dropdown-item">
                                        <i class="bi bi-person"></i> Assign
                                    </a>
                                <?php endif; ?>

                                <a href="#" onclick="watchIssue(<?= $isWatching ? 'true' : 'false' ?>);return false;"
                                    class="dropdown-item">
                                    <i class="bi bi-eye<?= $isWatching ? '-slash' : '' ?>"></i>
                                    <?= $isWatching ? 'Unwatch' : 'Watch' ?>
                                </a>

                                <?php if (($issue['reporter_id'] ?? null) !== user_id()): ?>
                                    <a href="#" onclick="voteIssue(<?= $hasVoted ? 'true' : 'false' ?>);return false;"
                                        class="dropdown-item">
                                        <i class="bi bi-hand-thumbs-<?= $hasVoted ? 'down' : 'up' ?>"></i>
                                        <?= $hasVoted ? 'Remove Vote' : 'Vote' ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="issue-summary"><?= e($issue['summary'] ?? '') ?></h2>

                <?php if (!empty($issue['description'])): ?>
                    <div class="description-section">
                        <h3 class="section-label">Description</h3>
                        <div class="description-content"><?= $issue['description'] ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ========================= COMMENTS ========================= -->
            <div class="section-card">
                <div class="section-header">
                    <h3 class="section-title"><i class="bi bi-chat-left-text"></i> Comments</h3>
                </div>

                <div class="section-content">
                    <?php if (can('issues.comment', $issue['project_id'] ?? null)): ?>
                        <form id="comment-form">
                            <textarea class="form-control" name="body" rows="3" required></textarea>
                            <div class="form-actions mt-2">
                                <button class="btn btn-primary">Post</button>
                            </div>
                        </form>
                        <hr>
                    <?php endif; ?>

                    <div id="comments-container">
                        <?php foreach (($issue['comments'] ?? []) as $comment): ?>
                            <div class="comment-item" id="comment-<?= $comment['id'] ?>">
                                <strong><?= e($comment['user']['display_name'] ?? 'User') ?></strong>
                                <div class="comment-body"><?= nl2br(e($comment['body'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div><!-- LEFT PANEL END -->

        <!-- ========================= RIGHT PANEL ========================= -->
        <div class="issue-right-panel">

            <?php if (!empty($transitions)): ?>
                <div class="sidebar-card">
                    <div class="sidebar-header">
                        <h4>Status</h4>
                    </div>
                    <div class="sidebar-content">
                        <?php foreach ($transitions as $t): ?>
                            <button class="btn btn-primary w-100 mb-2"
                                onclick="transitionIssue(<?= (int) $t['status_id'] ?>,'<?= e($t['status_name']) ?>')">
                                <?= e($t['status_name']) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div><!-- RIGHT PANEL END -->
    </div><!-- MAIN END -->
</div><!-- WRAPPER END -->

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
    (() => {

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const baseUrl = location.origin;

        document.querySelectorAll('.dropdown-toggle-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                const menu = btn.nextElementSibling;
                menu.classList.toggle('d-none');
                document.addEventListener('click', () => menu.classList.add('d-none'), { once: true });
            });
        });

        window.transitionIssue = (id, name) => {
            const modal = new bootstrap.Modal(document.getElementById('transitionModal'));
            document.getElementById('transition-status').textContent = name;
            modal.show();
        };

        window.assignIssue = async () => {
            try {
                const res = await fetch(`${baseUrl}/api/v1/projects/<?= e($issue['project_key']) ?>/members`, {
                    headers: { 'X-CSRF-TOKEN': csrf }
                });
                const data = await res.json();
                const select = document.getElementById('assignee');
                select.innerHTML = '<option value="">Select</option>';
                (data.members || []).forEach(m => {
                    const o = document.createElement('option');
                    o.value = m.id; o.textContent = m.display_name;
                    select.appendChild(o);
                });
                new bootstrap.Modal(assignModal).show();
            } catch (e) {
                console.error(e);
            }
        };

        window.watchIssue = async watching => {
            const action = watching ? 'unwatch' : 'watch';
            await fetch(`${baseUrl}/issue/<?= e($issue['issue_key']) ?>/${action}`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }
            });
            location.reload();
        };

        window.voteIssue = async voted => {
            const action = voted ? 'unvote' : 'vote';
            await fetch(`${baseUrl}/issue/<?= e($issue['issue_key']) ?>/${action}`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }
            });
            location.reload();
        };

    })();
</script>
<?php \App\Core\View::endSection(); ?>