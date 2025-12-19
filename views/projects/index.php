<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="projects-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="board-breadcrumb">
        <a href="<?= url('/dashboard') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">
            <i class="bi bi-folder"></i> Projects
        </span>
    </div>

    <!-- Page Header -->
    <div class="board-header">
        <div class="board-header-left">
            <h1 class="board-title">Projects</h1>
            <p class="board-subtitle">Manage and organize your work across projects</p>
        </div>
        <div class="board-header-right">
            <?php if (can('create-projects')): ?>
            <a href="<?= url('/projects/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Create Project
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="projects-filters">
        <form method="GET" action="<?= url('/projects') ?>" class="filters-form">
            <div class="filter-group">
                <label for="search-input" class="filter-label">Search</label>
                <div class="search-input-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" id="search-input" name="search" 
                           value="<?= e($filters['search'] ?? '') ?>" 
                           placeholder="Find projects..."
                           class="filter-input">
                </div>
            </div>

            <div class="filter-group">
                <label for="category-select" class="filter-label">Category</label>
                <select id="category-select" name="category" class="filter-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories ?? [] as $category): ?>
                    <option value="<?= e($category['id']) ?>" <?= ($filters['category'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="status-select" class="filter-label">Status</label>
                <select id="status-select" name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="archived" <?= ($filters['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary filter-btn">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </form>
    </div>

    <!-- Projects Grid -->
    <?php if (empty($projects['items'] ?? [])): ?>
    <div class="empty-state-container">
        <i class="empty-state-icon bi bi-inbox"></i>
        <h3 class="empty-state-title">No projects found</h3>
        <p class="empty-state-description">
            <?php if (can('create-projects')): ?>
            Create your first project to get started
            <?php else: ?>
            No projects are available
            <?php endif; ?>
        </p>
        <?php if (can('create-projects')): ?>
        <a href="<?= url('/projects/create') ?>" class="btn btn-primary empty-state-btn">
            <i class="bi bi-plus-lg"></i> Create First Project
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="projects-grid">
        <?php foreach ($projects['items'] as $project): ?>
        <div class="project-card">
            
            <!-- Project Header -->
            <div class="project-card-header">
                <div class="project-header-left">
                    <!-- Project Avatar -->
                    <div class="project-avatar">
                        <?php if ($project['avatar'] ?? null): ?>
                        <img src="<?= e($project['avatar']) ?>" 
                             alt="<?= e($project['name']) ?>"
                             class="avatar-image">
                        <?php else: ?>
                        <div class="avatar-initials">
                            <?= strtoupper(substr($project['key'], 0, 2)) ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Project Info -->
                    <div class="project-info">
                        <h3 class="project-name">
                            <a href="<?= url("/projects/{$project['key']}") ?>" class="project-link">
                                <?= e($project['name']) ?>
                            </a>
                        </h3>
                        <div class="project-badges">
                            <span class="badge badge-primary"><?= e($project['key']) ?></span>
                            <?php if ($project['is_archived'] ?? false): ?>
                            <span class="badge badge-warning">Archived</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Three-dot Menu -->
                <div class="project-menu">
                    <button class="project-menu-btn" onclick="toggleProjectMenu(event)" title="More actions">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <div class="project-dropdown-menu">
                        <a href="<?= url("/projects/{$project['key']}") ?>" class="menu-item">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="<?= url("/projects/{$project['key']}/board") ?>" class="menu-item">
                            <i class="bi bi-kanban"></i> Board
                        </a>
                        <?php if (can('edit-project', $project['id'])): ?>
                        <div class="menu-divider"></div>
                        <a href="<?= url("/projects/{$project['key']}/settings") ?>" class="menu-item">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Project Description -->
            <?php if ($project['description'] ?? null): ?>
            <div class="project-description">
                <p><?= e(substr($project['description'], 0, 100)) ?><?= strlen($project['description']) > 100 ? '...' : '' ?></p>
            </div>
            <?php endif; ?>

            <!-- Project Stats -->
            <div class="project-stats">
                <div class="stat-item">
                    <span class="stat-label">
                        <i class="bi bi-list-task"></i> Issues
                    </span>
                    <span class="stat-value"><?= e($project['issue_count'] ?? 0) ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">
                        <i class="bi bi-people"></i> Members
                    </span>
                    <span class="stat-value"><?= e($project['member_count'] ?? 0) ?></span>
                </div>
            </div>

            <!-- Project Footer -->
            <div class="project-footer">
                <div class="project-lead">
                    <span class="lead-label">Lead:</span>
                    <?php if ($project['lead'] ?? null): ?>
                    <img src="<?= e($project['lead']['avatar'] ?? '') ?>" 
                         alt="<?= e($project['lead']['display_name'] ?? 'Lead') ?>"
                         class="lead-avatar"
                         title="<?= e($project['lead']['display_name'] ?? 'Lead') ?>">
                    <?php else: ?>
                    <span class="lead-placeholder">Unassigned</span>
                    <?php endif; ?>
                </div>
                <a href="<?= url("/projects/{$project['key']}/issues/create") ?>" class="btn btn-outline-primary">
                    <i class="bi bi-plus"></i> New Issue
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($projects['last_page']) && $projects['last_page'] > 1): ?>
    <nav class="pagination-wrapper">
        <ul class="pagination">
            <!-- Previous Button -->
            <li>
                <a href="<?= $projects['current_page'] <= 1 ? '#' : url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $projects['current_page'] - 1]))) ?>" 
                   class="pagination-link <?= $projects['current_page'] <= 1 ? 'disabled' : '' ?>">
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
            </li>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $projects['last_page']; $i++): ?>
            <li>
                <a href="<?= url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $i]))) ?>" 
                   class="pagination-link <?= $projects['current_page'] == $i ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            </li>
            <?php endfor; ?>

            <!-- Next Button -->
            <li>
                <a href="<?= $projects['current_page'] >= $projects['last_page'] ? '#' : url('/projects?' . http_build_query(array_merge($filters ?? [], ['page' => $projects['current_page'] + 1]))) ?>" 
                   class="pagination-link <?= $projects['current_page'] >= $projects['last_page'] ? 'disabled' : '' ?>">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Styles -->
<style>
    /* Ensure Theme Variables Are Available */
    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-blue-light: #E77817 !important;
        --jira-blue-lighter: #f0dce5 !important;
        --bg-secondary: #F7F8FA !important;
        --bg-primary: #FFFFFF !important;
        --border-color: #DFE1E6 !important;
        --text-primary: #161B22 !important;
        --text-secondary: #57606A !important;
        --text-tertiary: #738496 !important;
    }

    /* Page Wrapper */
    .projects-page-wrapper {
        background: var(--bg-secondary);
        margin: 0;
        padding: 0;
    }

    /* Page Header */
    .board-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px 32px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        margin: 0;
        gap: 16px;
    }

    .board-header-left {
        flex: 1;
    }

    .board-header-right {
        flex-shrink: 0;
    }

    .board-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 6px 0;
        letter-spacing: -0.3px;
        line-height: 1.3;
    }

    .board-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
        font-weight: 400;
    }

    /* Breadcrumb */
    .board-breadcrumb {
        padding: 12px 32px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
    }

    .breadcrumb-link {
        color: var(--jira-blue);
        text-decoration: none;
        transition: color var(--transition-fast);
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 500;
    }

    .breadcrumb-separator {
        margin: 0 0.5rem;
        color: var(--text-tertiary);
    }

    /* Filters */
    .projects-filters {
        padding: 16px 32px;
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 0;
    }

    .filters-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 12px;
        color: var(--text-secondary);
        font-size: 14px;
        pointer-events: none;
    }

    .filter-input,
    .filter-select {
        padding: 8px 12px;
        padding-left: 32px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 13px;
        font-family: inherit;
        background: white;
        color: var(--text-primary);
        transition: all 0.2s ease;
        height: auto;
        min-height: 32px;
        line-height: 1.5;
    }

    .filter-select {
        padding-left: 12px;
        padding-right: 28px;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
        outline: none;
    }

    .filter-input:hover,
    .filter-select:hover {
        border-color: #B6BEC7;
    }

    .filter-btn {
        padding: 8px 16px;
        height: 32px;
    }

    /* Projects Grid */
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        background: var(--bg-secondary);
    }

    /* Project Card */
    .project-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        overflow: hidden;
        transition: all var(--transition-base);
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .project-card:hover {
        box-shadow: 0 4px 12px rgba(9, 30, 66, 0.15);
        border-color: var(--border-color);
        transform: translateY(-3px);
    }

    /* Card Header */
    .project-card-header {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 8px;
    }

    .project-header-left {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }

    .project-avatar {
        flex-shrink: 0;
    }

    .avatar-image,
    .avatar-initials {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        background: linear-gradient(135deg, var(--jira-blue) 0%, var(--jira-blue-dark) 100%);
        color: white;
        object-fit: cover;
    }

    .project-info {
        flex: 1;
        min-width: 0;
    }

    .project-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 4px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.4;
    }

    .project-link {
        color: var(--jira-blue);
        text-decoration: none;
        transition: color var(--transition-fast);
    }

    .project-link:hover {
        color: var(--jira-blue-dark);
    }

    .project-badges {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-primary {
        background: var(--jira-blue-lighter);
        color: var(--jira-blue);
    }

    .badge-warning {
        background: #FEE2E2;
        color: #974F0C;
    }

    /* Project Menu */
    .project-menu {
        flex-shrink: 0;
        position: relative;
    }

    .project-menu-btn {
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 4px;
        border-radius: 3px;
        transition: all var(--transition-fast);
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
    }

    .project-menu-btn:hover {
        background: var(--bg-secondary);
        color: var(--text-primary);
    }

    .project-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(9, 30, 66, 0.15);
        min-width: 140px;
        margin-top: 4px;
        z-index: 100;
        overflow: hidden;
    }

    .project-dropdown-menu.show {
        display: block;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        color: var(--text-primary);
        text-decoration: none;
        font-size: 13px;
        transition: all var(--transition-fast);
        border: none;
        background: none;
        cursor: pointer;
        width: 100%;
        text-align: left;
    }

    .menu-item:hover {
        background: var(--bg-secondary);
        color: var(--jira-blue);
    }

    .menu-item i {
        font-size: 14px;
        width: 16px;
        flex-shrink: 0;
    }

    .menu-divider {
        height: 1px;
        background: var(--border-color);
        margin: 4px 0;
    }

    /* Card Description */
    .project-description {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        display: none;
    }

    .project-description p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 13px;
        line-height: 1.4;
    }

    /* Stats */
    .project-stats {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border-color);
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .stat-label i {
        font-size: 13px;
    }

    .stat-value {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }

    /* Footer */
    .project-footer {
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: auto;
    }

    .project-lead {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        flex: 1;
        min-width: 0;
    }

    .lead-label {
        font-weight: 600;
        color: var(--text-secondary);
        white-space: nowrap;
    }

    .lead-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .lead-placeholder {
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* Empty State */
    .empty-state-container {
        text-align: center;
        padding: 60px 20px;
        background: var(--bg-primary);
        border: 2px dashed var(--border-color);
        border-radius: 6px;
        margin: 16px 32px;
    }

    .empty-state-icon {
        font-size: 56px;
        color: var(--text-secondary);
        display: block;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .empty-state-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 8px 0;
    }

    .empty-state-description {
        color: var(--text-secondary);
        margin: 0 0 16px 0;
        font-size: 13px;
    }

    .empty-state-btn {
        margin: 0;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
        font-family: inherit;
    }

    .btn-primary {
        background: var(--jira-blue) !important;
        color: white !important;
        border-color: var(--jira-blue) !important;
    }

    .btn-primary:hover {
        background: var(--jira-blue-dark) !important;
        border-color: var(--jira-blue-dark) !important;
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.2) !important;
        color: white !important;
    }

    .btn-primary:focus,
    .btn-primary:active {
        background: var(--jira-blue-dark) !important;
        border-color: var(--jira-blue-dark) !important;
        color: white !important;
    }

    .btn-secondary {
        background: white;
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-secondary);
        color: var(--text-primary);
    }

    .btn-outline-primary {
        background: white;
        color: var(--jira-blue);
        border: 1px solid var(--border-color);
        font-size: 13px;
        padding: 6px 12px;
    }

    .btn-outline-primary:hover {
        background: rgba(139, 25, 86, 0.1);
        border-color: var(--jira-blue);
        color: var(--jira-blue-dark);
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        padding: 20px 32px;
        margin-bottom: 2rem;
    }

    .pagination {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 4px;
    }

    .pagination-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 8px 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        text-decoration: none;
        color: var(--jira-blue);
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
        background: white;
    }

    .pagination-link:hover:not(.disabled):not(.active) {
        border-color: var(--jira-blue);
        background: rgba(139, 25, 86, 0.1);
        color: var(--jira-blue-dark);
    }

    .pagination-link.active {
        background: var(--jira-blue);
        color: white;
        border-color: var(--jira-blue);
    }

    .pagination-link.disabled {
        color: var(--text-secondary);
        border-color: var(--border-color);
        cursor: not-allowed;
        background: #F7F8FA;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .projects-grid {
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        }

        .board-title {
            font-size: 24px;
        }

        .board-header {
            padding: 16px 24px;
        }

        .projects-filters {
            padding: 12px 24px;
        }

        .projects-grid {
            padding: 12px 24px;
        }
    }

    @media (max-width: 768px) {
        .projects-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
            padding: 12px 16px;
        }

        .board-header {
            flex-direction: column;
            gap: 12px;
            padding: 16px;
        }

        .board-header-right {
            width: 100%;
        }

        .board-header-right .btn {
            width: 100%;
            justify-content: center;
        }

        .board-title {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .board-breadcrumb {
            padding: 8px 16px;
            font-size: 12px;
        }

        .projects-filters {
            padding: 12px 16px;
        }

        .filters-form {
            grid-template-columns: 1fr;
        }

        .project-stats {
            grid-template-columns: 1fr;
        }

        .filter-btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .projects-grid {
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 10px;
        }

        .board-header {
            padding: 12px;
            gap: 8px;
        }

        .board-title {
            font-size: 18px;
        }

        .board-header-right .btn {
            padding: 6px 10px;
            font-size: 12px;
        }

        .project-card-header {
            padding: 10px 12px;
        }

        .project-stats {
            padding: 10px 12px;
            gap: 10px;
        }

        .project-footer {
            padding: 10px 12px;
            flex-direction: column;
            align-items: stretch;
        }

        .btn-outline-primary {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    function toggleProjectMenu(event) {
        event.stopPropagation();
        const btn = event.currentTarget;
        const menu = btn.nextElementSibling;
        const allMenus = document.querySelectorAll('.project-dropdown-menu');
        
        allMenus.forEach(m => {
            if (m !== menu) m.classList.remove('show');
        });
        
        menu.classList.toggle('show');
    }

    document.addEventListener('click', function() {
        document.querySelectorAll('.project-dropdown-menu').forEach(m => m.classList.remove('show'));
    });
</script>

<?php \App\Core\View::endSection(); ?>
