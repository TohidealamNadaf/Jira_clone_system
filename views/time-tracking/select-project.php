<?php

declare(strict_types=1);

\App\Core\View::extends('layouts.app');

\App\Core\View::section('content');

$projects = $projects ?? [];
$user = $user ?? [];

?>

<style>
    .tt-wrapper {
        background-color: #F7F8FA;
        min-height: calc(100vh - 80px);
        padding: 0;
    }

    .tt-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .tt-header {
        background-color: white;
        padding: 32px 40px;
        border-radius: 8px;
        margin-bottom: 32px;
        box-shadow: 0 1px 3px rgba(9, 30, 66, 0.13);
    }

    .tt-title {
        font-size: 28px;
        font-weight: 700;
        color: #161B22;
        margin: 0 0 8px 0;
    }

    .tt-subtitle {
        font-size: 14px;
        color: #626F86;
        margin: 0;
    }

    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .project-card {
        background-color: white;
        border: 1px solid #DFE1E6;
        border-radius: 8px;
        padding: 20px;
        cursor: pointer;
        transition: 0.2s;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .project-card:hover {
        border-color: #8B1956;
        box-shadow: 0 4px 12px rgba(9, 30, 66, 0.15);
        transform: translateY(-2px);
    }

    .project-avatar {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        background: linear-gradient(135deg, #8B1956, #E77817);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
    }

    .project-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 4px;
        object-fit: cover;
    }

    .project-info {
        flex: 1;
    }

    .project-key {
        font-size: 12px;
        color: #626F86;
        font-weight: 600;
        text-transform: uppercase;
    }

    .project-name {
        font-size: 16px;
        font-weight: 600;
        color: #161B22;
        word-break: break-word;
    }

    .project-description {
        font-size: 13px;
        color: #626F86;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .project-meta {
        display: flex;
        gap: 12px;
        font-size: 12px;
        color: #626F86;
        padding-top: 12px;
        border-top: 1px solid #DFE1E6;
    }

    .project-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .empty-state-text {
        font-size: 14px;
        color: #626F86;
    }

    @media (max-width: 768px) {
        .tt-container {
            padding: 20px 16px;
        }

        .tt-header {
            padding: 20px;
            margin-bottom: 20px;
        }

        .tt-title {
            font-size: 20px;
        }

        .projects-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }
</style>

<div class="tt-wrapper">
    <div class="tt-container">
        <div class="tt-header">
            <h1 class="tt-title">Time Tracking</h1>
            <p class="tt-subtitle">Select a project to view time tracking analytics and reports</p>
        </div>

        <div class="projects-grid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <a href="<?= url('/time-tracking/project/' . (int)$project['id']) ?>" class="project-card">
                        <div style="display: flex; gap: 12px;">
                            <div class="project-avatar">
                                <?php if (!empty($project['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($project['avatar']) ?>" alt="">
                                <?php else: ?>
                                    <?= substr($project['name'] ?? 'P', 0, 1) ?>
                                <?php endif; ?>
                            </div>
                            <div class="project-info" style="flex: 1;">
                                <div class="project-key"><?= htmlspecialchars($project['key'] ?? '') ?></div>
                                <div class="project-name"><?= htmlspecialchars($project['name'] ?? 'Untitled Project') ?></div>
                            </div>
                        </div>

                        <?php if (!empty($project['description'])): ?>
                            <div class="project-description">
                                <?= htmlspecialchars(substr($project['description'], 0, 80)) ?>...
                            </div>
                        <?php endif; ?>

                        <div class="project-meta">
                            <div class="project-meta-item">
                                👤 <?= htmlspecialchars($project['role_name'] ?? 'Member') ?>
                            </div>
                            <?php if (!empty($project['issue_count'])): ?>
                                <div class="project-meta-item">
                                    📋 <?= (int)$project['issue_count'] ?> issues
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p class="empty-state-text">No projects available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection();
