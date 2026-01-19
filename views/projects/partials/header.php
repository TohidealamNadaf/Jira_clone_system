<?php
/**
 * Project Header Partial
 * 
 * Includes Breadcrumbs, Project Meta, and Navigation Tabs.
 * Expects $project to be available.
 */

/**
 * Helper to check active route
 */
if (!function_exists('is_active')) {
    function is_active($path)
    {
        $current = $_SERVER['REQUEST_URI'];
        return strpos($current, $path) !== false;
    }
}
?>

<!-- Breadcrumb Navigation -->
<div class="project-breadcrumb">
    <a href="<?= url('/projects') ?>" class="breadcrumb-link">
        <i class="bi bi-house-door"></i> Projects
    </a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">
        <?= e($project['name']) ?>
    </span>
</div>

<!-- Project Header Section -->
<div class="project-header">
    <!-- Left: Project Avatar + Info -->
    <div class="project-header-left">
        <div class="project-avatar-wrapper">
            <?php if ($project['avatar'] ?? null): ?>
                <img src="<?= e(url($project['avatar'])) ?>" class="project-avatar" alt="<?= e($project['name']) ?>">
            <?php else: ?>
                <div class="project-avatar project-avatar-initials">
                    <?= strtoupper(substr($project['key'], 0, 2)) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="project-info">
            <h1 class="project-title">
                <?= e($project['name']) ?>
            </h1>
            <div class="project-meta">
                <span class="project-key">
                    <?= e($project['key']) ?>
                </span>
                <?php if ($project['category_name'] ?? null): ?>
                    <span class="project-category">
                        <?= e($project['category_name']) ?>
                    </span>
                <?php endif; ?>
                <?php if ($project['is_archived'] ?? false): ?>
                    <span class="project-badge archived">Archived</span>
                <?php endif; ?>
            </div>
            <?php if ($project['description'] ?? null): ?>
                <p class="project-description">
                    <?= e($project['description']) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right: Navigation Buttons -->
    <div class="project-header-actions">


        <!-- Kanban Board -->
        <a href="<?= url("/projects/{$project['key']}/board") ?>" class="action-button
            <?= is_active("projects/{$project['key']}/board") ? 'active' : '' ?>">
            <i class="bi bi-kanban"></i>
            <span>Kanban Board</span>
        </a>

        <!-- Scrum Board -->
        <?php
        $scrumBoardIdHeader = \App\Core\Database::selectValue(
            "SELECT id FROM boards WHERE project_id = ? AND type = 'scrum' ORDER BY id ASC LIMIT 1",
            [$project['id']]
        );
        ?>
        <?php if ($scrumBoardIdHeader): ?>
            <?php
            // robust check for active sprint
            try {
                $activeSprintHeader = \App\Core\Database::selectOne(
                    "SELECT id FROM sprints WHERE board_id = ? AND status = 'active' LIMIT 1",
                    [$scrumBoardIdHeader]
                );
            } catch (\Exception $e) {
                $activeSprintHeader = null;
            }
            $scrumBoardUrl = $activeSprintHeader
                ? url("/boards/{$scrumBoardIdHeader}?sprint_id={$activeSprintHeader['id']}")
                : url("/boards/{$scrumBoardIdHeader}");
            ?>
            <a href="<?= $scrumBoardUrl ?>" class="action-button
                <?= is_active("boards/{$scrumBoardIdHeader}") ? 'active' : '' ?>">
                <i class="bi bi-layout-three-columns"></i>
                <span>Scrum Board</span>
            </a>
        <?php endif; ?>

        <!-- Issues List -->
        <a href="<?= url("/projects/{$project['key']}/issues") ?>" class="action-button
            <?= is_active("projects/{$project['key']}/issues") ? 'active' : '' ?>">
            <i class="bi bi-list-ul"></i>
            <span>Issues</span>
        </a>

        <!-- Backlog (Scrum Only) -->
        <?php
        $scrumBoardId = \App\Core\Database::selectValue(
            "SELECT id FROM boards WHERE project_id = ? AND type = 'scrum' ORDER BY id ASC LIMIT 1",
            [$project['id']]
        );
        ?>
        <a href="<?= $scrumBoardId ? url("/boards/{$scrumBoardId}/backlog") : url("/projects/{$project['key']}/backlog") ?>"
            class="action-button
            <?= is_active("boards/{$scrumBoardId}/backlog") || is_active("projects/{$project['key']}/backlog") ? 'active' : '' ?>">
            <i class="bi bi-inbox"></i>
            <span>Backlog</span>
        </a>

        <!-- Sprints List -->
        <a href="<?= url("/projects/{$project['key']}/sprints") ?>" class="action-button
            <?= is_active("projects/{$project['key']}/sprints") ? 'active' : '' ?>">
            <i class="bi bi-lightning-charge"></i>
            <span>Sprints</span>
        </a>

        <!-- Reports -->
        <a href="<?= url("/projects/{$project['key']}/reports") ?>" class="action-button
            <?= is_active("projects/{$project['key']}/reports") ? 'active' : '' ?>">
            <i class="bi bi-bar-chart"></i>
            <span>Reports</span>
        </a>

        <!-- Documentation -->
        <a href="<?= url("/projects/{$project['key']}/documentation") ?>" class="action-button
            <?= is_active("projects/{$project['key']}/documentation") ? 'active' : '' ?>">
            <i class="bi bi-folder-fill"></i>
            <span>Docs</span>
        </a>

        <!-- Roadmap -->
        <a href="<?= url("/projects/{$project['key']}/roadmap") ?>" class="action-button
            <?= is_active("projects/{$project['key']}/roadmap") ? 'active' : '' ?>">
            <i class="bi bi-signpost-2"></i>
            <span>Roadmap</span>
        </a>

        <!-- Settings -->
        <?php if (can('edit-project', $project['id'])): ?>
            <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="action-button
            <?= is_active("projects/{$project['key']}/settings") ? 'active' : '' ?>">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<style>
    /* Project Header Styles */
    .project-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        background: #FFFFFF;
        border-bottom: 1px solid #DFE1E6;
        /* var(--jira-border) */
        font-size: 13px;
        flex-shrink: 0;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #8B1956;
        /* var(--jira-blue) */
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .breadcrumb-link:hover {
        color: #6F123F;
        /* var(--jira-blue-dark) */
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: #626F86;
        /* var(--jira-gray) */
        font-weight: 300;
    }

    .breadcrumb-current {
        color: #161B22;
        /* var(--jira-dark) */
        font-weight: 600;
    }

    .project-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 32px;
        padding: 32px;
        background: #FFFFFF;
        border-bottom: 1px solid #DFE1E6;
        flex-shrink: 0;
        flex-wrap: wrap;
        /* responsive */
    }

    .project-header-left {
        display: flex;
        align-items: center;
        gap: 24px;
        flex: 1;
        min-width: 280px;
    }

    .project-avatar {
        width: 64px;
        height: 64px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .project-avatar-initials {
        width: 64px;
        height: 64px;
        border-radius: 8px;
        background: linear-gradient(135deg, #8B1956, #6F123F);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .project-info {
        flex: 1;
    }

    .project-title {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #161B22;
        letter-spacing: -0.3px;
    }

    .project-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 6px;
    }

    .project-key {
        display: inline-block;
        padding: 2px 8px;
        background: #F7F8FA;
        border: 1px solid #DFE1E6;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        color: #626F86;
        text-transform: uppercase;
    }

    .project-category {
        font-size: 13px;
        color: #626F86;
    }

    .project-description {
        margin: 8px 0 0 0;
        font-size: 14px;
        color: #626F86;
        line-height: 1.5;
        max-width: 600px;
    }

    .project-header-actions {
        display: grid !important;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        flex-shrink: 0;
        /* Ensure it doesn't wrap unexpectedly if something overrides grid */
        flex-wrap: nowrap !important;
        max-width: 100%;
    }

    .action-button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 16px;
        background: #FFFFFF;
        border: 1px solid #DFE1E6;
        border-radius: 6px;
        color: #161B22;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .action-button:hover {
        background: #F7F8FA;
        border-color: #B6C2CF;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .action-button.active {
        background: #DEEAFE;
        /* Light Blue/Primary Light */
        color: #8B1956;
        border-color: #8B1956;
        font-weight: 600;
    }
</style>

</style>