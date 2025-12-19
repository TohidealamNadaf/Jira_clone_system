<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background-color: transparent; padding: 0; gap: 8px;">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>" style="color: var(--jira-blue); text-decoration: none;">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>" style="color: var(--jira-blue); text-decoration: none;">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>" style="color: var(--jira-blue); text-decoration: none;"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active" style="color: #626F86;">Backlog</li>
        </ol>
    </nav>

    <!-- Project Navigation Tabs -->
    <div class="project-nav-tabs">
        <a href="<?= url("/projects/{$project['key']}/board") ?>" class="nav-tab">
            <i class="bi bi-kanban"></i>
            <span>Board</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/issues") ?>" class="nav-tab">
            <i class="bi bi-list-ul"></i>
            <span>Issues</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/backlog") ?>" class="nav-tab active">
            <i class="bi bi-inbox"></i>
            <span>Backlog</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/sprints") ?>" class="nav-tab">
            <i class="bi bi-lightning-charge"></i>
            <span>Sprints</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/reports") ?>" class="nav-tab">
            <i class="bi bi-bar-chart"></i>
            <span>Reports</span>
        </a>
        <a href="<?= url("/time-tracking/project/{$project['id']}") ?>" class="nav-tab">
            <i class="bi bi-hourglass-split"></i>
            <span>Time Tracking</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/calendar") ?>" class="nav-tab">
            <i class="bi bi-calendar-event"></i>
            <span>Calendar</span>
        </a>
        <a href="<?= url("/projects/{$project['key']}/roadmap") ?>" class="nav-tab">
            <i class="bi bi-signpost-2"></i>
            <span>Roadmap</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4" style="gap: 24px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin: 0 0 4px 0; letter-spacing: -0.2px;">Backlog</h1>
            <p style="font-size: 15px; color: #626F86; margin: 0;">Manage and prioritize issues for upcoming sprints</p>
        </div>
        <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" 
           class="btn" 
           style="background-color: var(--jira-blue); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s;">
            <i class="bi bi-plus-lg"></i> Create Issue
        </a>
    </div>

    <!-- Backlog Section -->
    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <!-- Header -->
        <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 15px; font-weight: 600; color: #161B22; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">
                Backlog Items
                <span style="background: var(--jira-blue-lighter); color: var(--jira-blue); padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: 500; margin-left: 8px; display: inline-block;">
                    <?= count($backlogIssues) ?>
                </span>
            </h2>
        </div>

        <!-- Content -->
        <?php if (empty($backlogIssues)): ?>
        <div style="padding: 60px 20px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
            <p style="font-size: 15px; color: #626F86; margin-bottom: 20px;">No backlog items yet. Create your first issue to get started.</p>
            <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" 
               class="btn" 
               style="background-color: var(--jira-blue); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; text-decoration: none;">
                Create First Issue
            </a>
        </div>
        <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin: 0;">
                <thead>
                    <tr style="background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                        <th style="padding: 12px 20px; font-size: 13px; font-weight: 600; color: #626F86; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; width: 100px;">Key</th>
                        <th style="padding: 12px 20px; font-size: 13px; font-weight: 600; color: #626F86; text-align: left; text-transform: uppercase; letter-spacing: 0.5px;">Summary</th>
                        <th style="padding: 12px 20px; font-size: 13px; font-weight: 600; color: #626F86; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; width: 120px;">Type</th>
                        <th style="padding: 12px 20px; font-size: 13px; font-weight: 600; color: #626F86; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; width: 120px;">Status</th>
                        <th style="padding: 12px 20px; font-size: 13px; font-weight: 600; color: #626F86; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; width: 100px;">Priority</th>
                        <th style="padding: 12px 20px; font-size: 13px; font-weight: 600; color: #626F86; text-align: left; text-transform: uppercase; letter-spacing: 0.5px; width: 120px;">Assignee</th>
                        <th style="padding: 12px 20px; font-size: 13px; font-weight: 600; color: #626F86; text-align: center; text-transform: uppercase; letter-spacing: 0.5px; width: 60px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backlogIssues as $issue): ?>
                    <tr style="border-bottom: 1px solid #DFE1E6; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#F7F8FA'" onmouseout="this.style.backgroundColor=''">
                        <!-- Key -->
                        <td style="padding: 12px 20px; font-size: 14px;">
                            <a href="<?= url("/issue/{$issue['issue_key']}") ?>" 
                               style="color: var(--jira-blue); text-decoration: none; font-weight: 600;">
                                <?= e($issue['issue_key']) ?>
                            </a>
                        </td>

                        <!-- Summary -->
                        <td style="padding: 12px 20px; font-size: 14px;">
                            <a href="<?= url("/issue/{$issue['issue_key']}") ?>" 
                               style="color: #161B22; text-decoration: none; display: block;">
                                <?= e(substr($issue['summary'], 0, 80)) ?><?= strlen($issue['summary']) > 80 ? '...' : '' ?>
                            </a>
                        </td>

                        <!-- Type Badge -->
                        <td style="padding: 12px 20px; font-size: 13px;">
                            <span style="background-color: <?= e($issue['issue_type_color']) ?>; color: white; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; font-weight: 500;">
                                <i class="bi bi-<?= e($issue['issue_type_icon']) ?>" style="font-size: 12px;"></i>
                                <?= e($issue['issue_type_name']) ?>
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td style="padding: 12px 20px; font-size: 13px;">
                            <span style="background-color: <?= e($issue['status_color']) ?>; color: white; padding: 4px 8px; border-radius: 4px; display: inline-block; font-weight: 500;">
                                <?= e($issue['status_name']) ?>
                            </span>
                        </td>

                        <!-- Priority Badge -->
                        <td style="padding: 12px 20px; font-size: 13px;">
                            <span style="background-color: <?= e($issue['priority_color']) ?>; color: white; padding: 4px 8px; border-radius: 4px; display: inline-block; font-weight: 500;">
                                <?= e($issue['priority_name']) ?>
                            </span>
                        </td>

                        <!-- Assignee -->
                        <td style="padding: 12px 20px; font-size: 13px;">
                            <?php if ($issue['assignee_name']): ?>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <img src="<?= e($issue['assignee_avatar'] ?? '/images/default-avatar.png') ?>" 
                                     style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;"
                                     title="<?= e($issue['assignee_name']) ?>" 
                                     alt="<?= e($issue['assignee_name']) ?>">
                                <span style="color: #161B22;"><?= e($issue['assignee_name']) ?></span>
                            </div>
                            <?php else: ?>
                            <span style="color: #626F86;">Unassigned</span>
                            <?php endif; ?>
                        </td>

                        <!-- Action -->
                        <td style="padding: 12px 20px; text-align: center;">
                            <a href="<?= url("/issue/{$issue['issue_key']}") ?>" 
                               style="color: var(--jira-blue); text-decoration: none; font-size: 16px; display: inline-block; padding: 4px; transition: transform 0.2s;"
                               onmouseover="this.style.transform='scale(1.15)'"
                               onmouseout="this.style.transform='scale(1)'">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #626F86;
        margin: 0 8px;
    }
    
    a:hover {
        opacity: 0.8;
    }
</style>

<?php \App\Core\View::endSection(); ?>
