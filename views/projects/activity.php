<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background-color: transparent; padding: 0; gap: 8px;">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>"
                    style="color: var(--jira-blue); text-decoration: none;">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>"
                    style="color: var(--jira-blue); text-decoration: none;">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>"
                    style="color: var(--jira-blue); text-decoration: none;"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active" style="color: #626F86;">Activity</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4" style="gap: 24px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin: 0 0 4px 0; letter-spacing: -0.2px;">
                Project Activity</h1>
            <p style="font-size: 15px; color: #626F86; margin: 0;">Real-time audit trail of all project changes</p>
        </div>
        <a href="<?= url("/projects/{$project['key']}") ?>"
            style="background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s;">
            <i class="bi bi-arrow-left"></i> Back to Project
        </a>
    </div>

    <!-- Activity Timeline -->
    <div
        style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <?php if (empty($activities)): ?>
            <!-- Empty State -->
            <div style="padding: 60px 20px; text-align: center;">
                <div style="font-size: 48px; margin-bottom: 16px;">📭</div>
                <p style="font-size: 15px; color: #626F86; margin: 0;">No activities recorded yet. Changes will appear here.
                </p>
            </div>
        <?php else: ?>
            <!-- Activity Feed -->
            <div style="position: relative;">
                <!-- Timeline line -->
                <div
                    style="position: absolute; left: 47px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, var(--jira-blue), transparent); opacity: 0.2;">
                </div>

                <?php foreach ($activities as $index => $activity): ?>
                    <div style="padding: 24px 20px; border-bottom: 1px solid #DFE1E6; display: flex; gap: 16px; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#F7F8FA'" onmouseout="this.style.backgroundColor=''">

                        <!-- Avatar with Timeline Dot -->
                        <div style="flex-shrink: 0; position: relative;">
                            <img src="<?= e(avatar($activity['user']['avatar'] ?? null) ?: '/images/default-avatar.png') ?>"
                                style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 0 0 2px var(--jira-blue);"
                                alt="<?= e($activity['user']['display_name']) ?>"
                                title="<?= e($activity['user']['display_name']) ?>">
                        </div>

                        <!-- Activity Content -->
                        <div style="flex: 1; min-width: 0;">
                            <!-- User and Action -->
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                                <span style="font-weight: 600; color: #161B22; font-size: 14px;">
                                    <?= e($activity['user']['display_name']) ?>
                                </span>

                                <!-- Activity Action Icon and Verb -->
                                <span
                                    style="display: inline-flex; align-items: center; gap: 4px; font-size: 13px; color: #626F86;">
                                    <?php
                                    $icon = 'arrow-repeat';
                                    $color = '#626F86';

                                    if (strpos($activity['action'], 'created') !== false) {
                                        $icon = 'plus-circle';
                                        $color = '#216E4E';
                                        $verb = 'created';
                                    } elseif (strpos($activity['action'], 'updated') !== false) {
                                        $icon = 'pencil';
                                        $color = '#8B1956';
                                        $verb = 'updated';
                                    } elseif (strpos($activity['action'], 'deleted') !== false) {
                                        $icon = 'trash';
                                        $color = '#AE2A19';
                                        $verb = 'deleted';
                                    } elseif (strpos($activity['action'], 'assigned') !== false) {
                                        $icon = 'person-check';
                                        $color = '#8B1956';
                                        $verb = 'assigned';
                                    } elseif (strpos($activity['action'], 'transitioned') !== false) {
                                        $icon = 'arrow-repeat';
                                        $color = '#974F0C';
                                        $verb = 'moved';
                                    } elseif (strpos($activity['action'], 'comment') !== false) {
                                        $icon = 'chat';
                                        $color = '#974F0C';
                                        $verb = 'commented on';
                                    } else {
                                        $icon = 'arrow-repeat';
                                        $color = '#626F86';
                                        $verb = 'updated';
                                    }
                                    ?>
                                    <i class="bi bi-<?= $icon ?>" style="color: <?= $color ?>;"></i>
                                    <span style="color: #626F86;"><?= $verb ?></span>
                                </span>

                                <!-- Issue Link -->
                                <?php if ($activity['issue']): ?>
                                    <a href="<?= url("/issue/{$activity['issue']['key']}") ?>"
                                        style="color: var(--jira-blue); text-decoration: none; font-weight: 600;">
                                        <?= e($activity['issue']['key']) ?>
                                    </a>
                                    <span style="color: #626F86; font-size: 13px;">
                                        <?= e(substr($activity['issue']['summary'], 0, 50)) ?>            <?= strlen($activity['issue']['summary']) > 50 ? '...' : '' ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Timestamp -->
                            <div style="font-size: 12px; color: #626F86; display: flex; align-items: center; gap: 4px;">
                                <i class="bi bi-clock"></i>
                                <?= time_ago($activity['created_at']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .breadcrumb-item+.breadcrumb-item::before {
        content: "/";
        color: #626F86;
        margin: 0 8px;
    }
</style>

<?php \App\Core\View::endSection(); ?>