<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="search-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <nav class="board-breadcrumb">
        <a href="<?= url('/dashboard') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">
            <i class="bi bi-search"></i> Search
        </span>
    </nav>

    <!-- Page Header -->
    <div class="page-header-section">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">Search Issues</h1>
                <p class="page-subtitle">Find and manage issues across all projects</p>
            </div>
            <div class="header-right">
                <a href="<?= url('/search/advanced') ?>" class="btn btn-secondary">
                    <i class="bi bi-sliders"></i> Advanced Search
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="search-main-container">
        <!-- Filters Sidebar -->
        <aside class="search-filters-sidebar">
            <!-- Quick Search -->
            <div class="filter-panel">
                <div class="filter-panel-header">
                    <h3 class="filter-panel-title">
                        <i class="bi bi-search"></i> Search
                    </h3>
                </div>
                <div class="filter-panel-body">
                    <form action="<?= url('/search') ?>" method="GET" id="searchForm">
                        <input type="hidden" name="q" value="<?= e($query ?? '') ?>">
                        
                        <div class="search-input-group">
                            <input type="text" name="q" class="search-input" placeholder="Search by summary, description, issue key..." 
                                   value="<?= e($query ?? '') ?>" aria-label="Search issues">
                            <button type="submit" class="search-submit" aria-label="Submit search">
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filter Groups -->
            <form action="<?= url('/search') ?>" method="GET" id="filterForm">
                <input type="hidden" name="q" value="<?= e($query ?? '') ?>">

                <!-- Project Filter -->
                <div class="filter-panel">
                    <div class="filter-panel-header">
                        <h4 class="filter-panel-label">
                            <i class="bi bi-folder"></i> Project
                        </h4>
                    </div>
                    <div class="filter-panel-body">
                        <select name="project" class="filter-input-select" onchange="document.getElementById('filterForm').submit();" aria-label="Filter by project">
                            <option value="">All Projects</option>
                            <?php foreach ($projects ?? [] as $proj): ?>
                            <option value="<?= $proj['key'] ?>" <?= ($filters['project'] ?? '') === $proj['key'] ? 'selected' : '' ?>>
                                <?= e($proj['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Type Filter -->
                <div class="filter-panel">
                    <div class="filter-panel-header">
                        <h4 class="filter-panel-label">
                            <i class="bi bi-tag"></i> Issue Type
                        </h4>
                    </div>
                    <div class="filter-panel-body">
                        <div class="filter-checkbox-group">
                            <?php foreach ($issueTypes ?? [] as $type): ?>
                            <div class="filter-checkbox-item">
                                <input class="filter-checkbox" type="checkbox" name="type[]" 
                                       value="<?= $type['id'] ?>" id="type-<?= $type['id'] ?>"
                                       <?= in_array($type['id'], $filters['type'] ?? []) ? 'checked' : '' ?>
                                       onchange="document.getElementById('filterForm').submit();">
                                <label class="filter-checkbox-label" for="type-<?= $type['id'] ?>">
                                    <span class="icon-badge" style="background-color: <?= e($type['color']) ?>20;">
                                        <i class="bi bi-<?= e($type['icon']) ?>" style="color: <?= e($type['color']) ?>"></i>
                                    </span>
                                    <span class="label-text"><?= e($type['name']) ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="filter-panel">
                    <div class="filter-panel-header">
                        <h4 class="filter-panel-label">
                            <i class="bi bi-circle-half"></i> Status
                        </h4>
                    </div>
                    <div class="filter-panel-body">
                        <div class="filter-checkbox-group">
                            <?php foreach ($statuses ?? [] as $status): ?>
                            <div class="filter-checkbox-item">
                                <input class="filter-checkbox" type="checkbox" name="status[]" 
                                       value="<?= $status['id'] ?>" id="status-<?= $status['id'] ?>"
                                       <?= in_array($status['id'], $filters['status'] ?? []) ? 'checked' : '' ?>
                                       onchange="document.getElementById('filterForm').submit();">
                                <label class="filter-checkbox-label" for="status-<?= $status['id'] ?>">
                                    <span class="status-dot" style="background-color: <?= e($status['color']) ?>"></span>
                                    <span class="label-text"><?= e($status['name']) ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Priority Filter -->
                <div class="filter-panel">
                    <div class="filter-panel-header">
                        <h4 class="filter-panel-label">
                            <i class="bi bi-exclamation-circle"></i> Priority
                        </h4>
                    </div>
                    <div class="filter-panel-body">
                        <div class="filter-checkbox-group">
                            <?php foreach ($priorities ?? [] as $priority): ?>
                            <div class="filter-checkbox-item">
                                <input class="filter-checkbox" type="checkbox" name="priority[]" 
                                       value="<?= $priority['id'] ?>" id="priority-<?= $priority['id'] ?>"
                                       <?= in_array($priority['id'], $filters['priority'] ?? []) ? 'checked' : '' ?>
                                       onchange="document.getElementById('filterForm').submit();">
                                <label class="filter-checkbox-label" for="priority-<?= $priority['id'] ?>">
                                    <span class="priority-dot" style="background-color: <?= e($priority['color']) ?>"></span>
                                    <span class="label-text"><?= e($priority['name']) ?></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Assignee Filter -->
                <div class="filter-panel">
                    <div class="filter-panel-header">
                        <h4 class="filter-panel-label">
                            <i class="bi bi-person"></i> Assignee
                        </h4>
                    </div>
                    <div class="filter-panel-body">
                        <select name="assignee" class="filter-input-select" onchange="document.getElementById('filterForm').submit();" aria-label="Filter by assignee">
                            <option value="">Anyone</option>
                            <option value="currentUser()" <?= ($filters['assignee'] ?? '') === 'currentUser()' ? 'selected' : '' ?>>
                                Current User
                            </option>
                            <option value="unassigned" <?= ($filters['assignee'] ?? '') === 'unassigned' ? 'selected' : '' ?>>
                                Unassigned
                            </option>
                            <?php foreach ($users ?? [] as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($filters['assignee'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                <?= e($u['display_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Reporter Filter -->
                <div class="filter-panel">
                    <div class="filter-panel-header">
                        <h4 class="filter-panel-label">
                            <i class="bi bi-card-text"></i> Reporter
                        </h4>
                    </div>
                    <div class="filter-panel-body">
                        <select name="reporter" class="filter-input-select" onchange="document.getElementById('filterForm').submit();" aria-label="Filter by reporter">
                            <option value="">Anyone</option>
                            <option value="currentUser()" <?= ($filters['reporter'] ?? '') === 'currentUser()' ? 'selected' : '' ?>>
                                Current User
                            </option>
                            <?php foreach ($users ?? [] as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($filters['reporter'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                <?= e($u['display_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Created Filter -->
                <div class="filter-panel">
                    <div class="filter-panel-header">
                        <h4 class="filter-panel-label">
                            <i class="bi bi-calendar"></i> Created
                        </h4>
                    </div>
                    <div class="filter-panel-body">
                        <select name="created" class="filter-input-select" onchange="document.getElementById('filterForm').submit();" aria-label="Filter by creation date">
                            <option value="">Any time</option>
                            <option value="today" <?= ($filters['created'] ?? '') === 'today' ? 'selected' : '' ?>>Today</option>
                            <option value="week" <?= ($filters['created'] ?? '') === 'week' ? 'selected' : '' ?>>This week</option>
                            <option value="month" <?= ($filters['created'] ?? '') === 'month' ? 'selected' : '' ?>>This month</option>
                        </select>
                    </div>
                </div>

                <!-- Clear Filters -->
                <div class="filter-actions">
                    <a href="<?= url('/search') ?>" class="btn btn-outline" title="Clear all filters">
                        <i class="bi bi-x-circle"></i> Clear Filters
                    </a>
                </div>
            </form>

            <!-- Saved Filters -->
            <div class="filter-panel">
                <div class="filter-panel-header filter-panel-header-action">
                    <h4 class="filter-panel-label">
                        <i class="bi bi-bookmark"></i> Saved Filters
                    </h4>
                    <button class="btn-icon-small" data-bs-toggle="modal" data-bs-target="#saveFilterModal" aria-label="Save current filter" title="Save filter">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div class="filter-panel-body">
                    <div class="saved-filters-list">
                        <?php if (!empty($savedFilters)): ?>
                            <?php foreach ($savedFilters as $filter): ?>
                            <a href="<?= url('/search?' . $filter['query']) ?>" class="saved-filter-item" title="<?= e($filter['name']) ?>">
                                <span class="saved-filter-name"><?= e($filter['name']) ?></span>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <p class="empty-saved-filters">No saved filters yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Results Area -->
        <main class="search-results-area">
            <!-- Results Header -->
            <div class="results-header">
                <div class="results-header-left">
                    <h2 class="results-title">
                        <?php if ($query): ?>
                            Search results for <strong>"<?= e($query) ?>"</strong>
                        <?php else: ?>
                            All Issues
                        <?php endif; ?>
                    </h2>
                    <p class="results-count">
                        <span class="count-number"><?= $totalResults ?? 0 ?></span>
                        <span class="count-label">result<?= ($totalResults ?? 0) !== 1 ? 's' : '' ?> found</span>
                    </p>
                </div>
                <div class="results-header-right">
                    <div class="results-controls">
                        <div class="control-group">
                            <label for="sortSelect" class="control-label">Sort by:</label>
                            <select class="control-select" id="sortSelect" onchange="changeSortOrder(this.value)" aria-label="Sort results">
                                <option value="updated_desc" <?= ($sort === 'updated_desc') ? 'selected' : '' ?>>Recently Updated</option>
                                <option value="created_desc" <?= ($sort === 'created_desc') ? 'selected' : '' ?>>Newest First</option>
                                <option value="created_asc" <?= ($sort === 'created_asc') ? 'selected' : '' ?>>Oldest First</option>
                                <option value="priority_desc" <?= ($sort === 'priority_desc') ? 'selected' : '' ?>>Priority</option>
                            </select>
                        </div>
                        <div class="view-toggle-group" role="group" aria-label="View mode">
                            <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'list'])) ?>" 
                               class="view-toggle-btn <?= ($view === 'list' || empty($view)) ? 'active' : '' ?>" 
                               title="List view" aria-label="List view">
                                <i class="bi bi-list-ul"></i>
                            </a>
                            <a href="?<?= http_build_query(array_merge($_GET, ['view' => 'detail'])) ?>" 
                               class="view-toggle-btn <?= ($view === 'detail') ? 'active' : '' ?>" 
                               title="Card view" aria-label="Card view">
                                <i class="bi bi-grid-3x2-gap"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Container -->
            <div class="results-container">
                <?php if (empty($issues)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon">🔍</div>
                    <h3 class="empty-state-title">No issues found</h3>
                    <p class="empty-state-message">Try adjusting your search terms or filters</p>
                </div>

                <?php elseif ($view === 'detail'): ?>
                <!-- Card/Detail View -->
                <div class="results-grid">
                    <?php foreach ($issues as $issue): ?>
                    <article class="result-card">
                        <div class="result-card-header">
                            <div class="result-type-badge" style="background-color: <?= e($issue['issue_type_color']) ?>20;">
                                <i class="bi bi-<?= e($issue['issue_type_icon']) ?>" style="color: <?= e($issue['issue_type_color']) ?>"></i>
                            </div>
                            <div class="result-key-info">
                                <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="result-key-link">
                                    <?= e($issue['issue_key']) ?>
                                </a>
                                <span class="result-priority-badge" style="background-color: <?= e($issue['priority_color']) ?>">
                                    <?= e($issue['priority_name']) ?>
                                </span>
                            </div>
                            <span class="result-status-badge" style="background-color: <?= e($issue['status_color']) ?>">
                                <?= e($issue['status_name']) ?>
                            </span>
                        </div>

                        <div class="result-card-body">
                            <h4 class="result-summary">
                                <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="result-summary-link">
                                    <?= e($issue['summary']) ?>
                                </a>
                            </h4>
                            
                            <?php if (!empty($issue['description'])): ?>
                            <p class="result-description">
                                <?= e(mb_substr(strip_tags($issue['description']), 0, 150)) ?>...
                            </p>
                            <?php endif; ?>

                            <div class="result-metadata">
                                <span class="metadata-item" title="Project">
                                    <i class="bi bi-folder"></i>
                                    <?= e($issue['project_name'] ?? $issue['project_key']) ?>
                                </span>
                                <span class="metadata-item" title="Assignee">
                                    <i class="bi bi-person"></i>
                                    <?= $issue['assignee_name'] ? e($issue['assignee_name']) : 'Unassigned' ?>
                                </span>
                                <span class="metadata-item" title="Updated">
                                    <i class="bi bi-clock"></i>
                                    <?= time_ago($issue['updated_at']) ?>
                                </span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <?php else: ?>
                <!-- List/Table View -->
                <div class="results-table-wrapper">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th class="col-icon"></th>
                                <th class="col-key">Key</th>
                                <th class="col-summary">Summary</th>
                                <th class="col-project">Project</th>
                                <th class="col-assignee">Assignee</th>
                                <th class="col-status">Status</th>
                                <th class="col-priority">Priority</th>
                                <th class="col-updated">Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($issues as $issue): ?>
                            <tr class="result-row">
                                <td class="col-icon">
                                    <span class="result-type-icon" title="<?= e($issue['issue_type_name']) ?>">
                                        <i class="bi bi-<?= e($issue['issue_type_icon']) ?>" style="color: <?= e($issue['issue_type_color']) ?>"></i>
                                    </span>
                                </td>
                                <td class="col-key">
                                    <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="key-link">
                                        <?= e($issue['issue_key']) ?>
                                    </a>
                                </td>
                                <td class="col-summary">
                                    <a href="<?= url('/issue/' . $issue['issue_key']) ?>" class="summary-link">
                                        <?= e($issue['summary']) ?>
                                    </a>
                                </td>
                                <td class="col-project">
                                    <span class="project-name"><?= e($issue['project_name'] ?? $issue['project_key']) ?></span>
                                </td>
                                <td class="col-assignee">
                                    <?php if ($issue['assignee_name']): ?>
                                    <span class="assignee-name"><?= e($issue['assignee_name']) ?></span>
                                    <?php else: ?>
                                    <span class="unassigned">Unassigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="col-status">
                                    <span class="status-badge-table" style="background-color: <?= e($issue['status_color']) ?>">
                                        <?= e($issue['status_name']) ?>
                                    </span>
                                </td>
                                <td class="col-priority">
                                    <span class="priority-badge-table" style="background-color: <?= e($issue['priority_color']) ?>">
                                        <?= e($issue['priority_name']) ?>
                                    </span>
                                </td>
                                <td class="col-updated">
                                    <span class="updated-time"><?= time_ago($issue['updated_at']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (($totalPages ?? 1) > 1): ?>
                <div class="results-pagination">
                    <nav aria-label="Search results pagination">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= ($totalPages ?? 1); $i++): ?>
                            <li class="page-item <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i, 'view' => $view ?? 'list'])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Save Filter Modal -->
<div class="modal fade" id="saveFilterModal" tabindex="-1" aria-labelledby="saveFilterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saveFilterTitle">Save Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('/filters') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="query" value="<?= e(http_build_query($_GET)) ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="filterName" class="form-label">Filter Name</label>
                        <input type="text" id="filterName" name="name" class="form-control" placeholder="e.g., My Open Issues" required>
                    </div>
                    <div class="form-group">
                        <label for="filterDescription" class="form-label">Description (optional)</label>
                        <textarea id="filterDescription" name="description" class="form-control" rows="2" placeholder="What is this filter for?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --jira-blue: #8B1956;
    --jira-dark: #6F123F;
    --jira-light-blue: rgba(139,25,86,0.1);
    --text-primary: #161B22;
    --text-secondary: #626F86;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    --border-color: #DFE1E6;
    --danger-color: #ED3C32;
    --success-color: #216E4E;
    --warning-color: #974F0C;
    --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.search-page-wrapper {
    background: transparent;
    min-height: auto;
    padding: 0;
    margin: 0;
}

/* Breadcrumb - Matches other pages */
.search-page-wrapper .board-breadcrumb {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 12px 20px;
    background: #FFFFFF;
    border-bottom: 1px solid var(--border-color);
    font-size: 12px;
    flex-shrink: 0;
    overflow-x: auto;
    white-space: nowrap;
    min-height: 30px;
}

.search-page-wrapper .breadcrumb-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--jira-blue);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition);
}

.search-page-wrapper .breadcrumb-link:hover {
    color: var(--jira-dark);
}

.search-page-wrapper .breadcrumb-separator {
    color: var(--text-secondary);
    opacity: 0.5;
    margin: 0 2px;
}

.search-page-wrapper .breadcrumb-current {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--text-secondary);
    font-weight: 500;
}

/* Page Header */
.page-header-section {
    background: var(--bg-primary);
    border-bottom: 0;
    box-shadow: none;
    padding: 16px 32px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    max-width: 1400px;
    margin: 0 auto;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 8px 0;
    letter-spacing: -0.3px;
}

.page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
}

.header-right {
    display: flex;
    gap: 12px;
}

/* Main Container */
.search-main-container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 24px;
    padding: 24px 32px;
    max-width: 1400px;
    margin: 0 auto;
}

/* Filters Sidebar */
.search-filters-sidebar {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.filter-panel {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.filter-panel:hover {
    box-shadow: var(--shadow-md);
}

.filter-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    gap: 8px;
}

.filter-panel-header-action {
    padding: 12px 16px;
}

.filter-panel-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-panel-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-secondary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: 0.3px;
}

.filter-panel-body {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Search Input */
.search-input-group {
    display: flex;
    gap: 0;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    overflow: hidden;
    transition: border-color var(--transition), box-shadow var(--transition);
}

.search-input-group:focus-within {
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

.search-input {
    flex: 1;
    background: transparent;
    border: none;
    padding: 10px 12px;
    font-size: 14px;
    color: var(--text-primary);
    outline: none;
}

.search-input::placeholder {
    color: var(--text-secondary);
}

.search-submit {
    background: transparent;
    border: none;
    border-left: 1px solid var(--border-color);
    padding: 10px 12px;
    cursor: pointer;
    color: var(--jira-blue);
    transition: background-color var(--transition), color var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-submit:hover {
    background: var(--jira-light-blue);
    color: var(--jira-dark);
}

/* Filter Inputs */
.filter-input-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 13px;
    color: var(--text-primary);
    background: var(--bg-primary);
    cursor: pointer;
    transition: border-color var(--transition), box-shadow var(--transition);
}

.filter-input-select:hover {
    border-color: var(--jira-blue);
}

.filter-input-select:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

/* Filter Checkboxes */
.filter-checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-checkbox-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--jira-blue);
    flex-shrink: 0;
}

.filter-checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
    flex: 1;
}

.icon-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    flex-shrink: 0;
    font-size: 12px;
}

.status-dot,
.priority-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    display: inline-block;
}

.label-text {
    font-size: 13px;
    color: var(--text-primary);
    font-weight: 500;
}

/* Filter Actions */
.filter-actions {
    display: flex;
    gap: 8px;
    padding: 4px 0;
}

.btn-icon-small {
    background: transparent;
    border: none;
    padding: 4px 8px;
    cursor: pointer;
    color: var(--text-secondary);
    transition: color var(--transition), background-color var(--transition);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.btn-icon-small:hover {
    color: var(--jira-blue);
    background: var(--jira-light-blue);
}

/* Saved Filters */
.saved-filters-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.saved-filter-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color var(--transition), color var(--transition);
    border: 1px solid transparent;
}

.saved-filter-item:hover {
    background: var(--jira-light-blue);
    color: var(--jira-dark);
}

.saved-filter-name {
    font-size: 13px;
    font-weight: 500;
    color: var(--jira-blue);
}

.empty-saved-filters {
    font-size: 12px;
    color: var(--text-secondary);
    text-align: center;
    padding: 12px 0;
    margin: 0;
}

/* Results Area */
.search-results-area {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Results Header */
.results-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 20px 24px;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    box-shadow: var(--shadow-sm);
}

.results-header-left {
    flex: 1;
}

.results-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 4px 0;
}

.results-count {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 0;
}

.count-number {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 14px;
}

.count-label {
    font-weight: 400;
}

.results-header-right {
    display: flex;
    gap: 16px;
    align-items: center;
}

.results-controls {
    display: flex;
    gap: 16px;
    align-items: center;
}

.control-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.control-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}

.control-select {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 13px;
    color: var(--text-primary);
    background: var(--bg-primary);
    cursor: pointer;
    transition: border-color var(--transition), box-shadow var(--transition);
    min-width: 160px;
}

.control-select:hover {
    border-color: var(--jira-blue);
}

.control-select:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

/* View Toggle */
.view-toggle-group {
    display: flex;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: var(--bg-secondary);
    gap: 0;
    overflow: hidden;
}

.view-toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    cursor: pointer;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all var(--transition);
    background: transparent;
    border: none;
    font-size: 16px;
}

.view-toggle-btn:hover {
    color: var(--jira-blue);
}

.view-toggle-btn.active {
    background: var(--bg-primary);
    color: var(--jira-blue);
    border: 1px solid var(--border-color);
    margin: -1px;
}

/* Results Container */
.results-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 32px;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    text-align: center;
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.6;
}

.empty-state-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 8px 0;
}

.empty-state-message {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
}

/* Results Grid (Card View) */
.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 20px;
}

.result-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition);
}

.result-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-3px);
}

.result-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
}

.result-type-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 4px;
    flex-shrink: 0;
    font-size: 14px;
}

.result-key-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
}

.result-key-link {
    font-size: 13px;
    font-weight: 600;
    color: var(--jira-blue);
    text-decoration: none;
    transition: color var(--transition);
}

.result-key-link:hover {
    color: var(--jira-dark);
    text-decoration: underline;
}

.result-priority-badge,
.result-status-badge {
    font-size: 11px;
    font-weight: 700;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    flex-shrink: 0;
}

.result-card-body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.result-summary {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.5;
}

.result-summary-link {
    color: var(--jira-blue);
    text-decoration: none;
    transition: color var(--transition);
}

.result-summary-link:hover {
    color: var(--jira-dark);
    text-decoration: underline;
}

.result-description {
    font-size: 13px;
    color: var(--text-secondary);
    margin: 0;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.result-metadata {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 8px;
    border-top: 1px solid var(--border-color);
}

.metadata-item {
    font-size: 12px;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.metadata-item i {
    font-size: 12px;
}

/* Results Table */
.results-table-wrapper {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.results-table {
    width: 100%;
    border-collapse: collapse;
}

.results-table thead {
    background: var(--bg-secondary);
}

.results-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-bottom: 1px solid var(--border-color);
}

.results-table tbody tr {
    border-bottom: 1px solid var(--border-color);
    transition: background-color var(--transition);
}

.results-table tbody tr:hover {
    background: var(--bg-secondary);
}

.results-table tbody tr:last-child {
    border-bottom: none;
}

.results-table td {
    padding: 14px 16px;
    font-size: 13px;
    color: var(--text-primary);
    vertical-align: middle;
}

.col-icon {
    width: 40px;
    padding: 14px 12px;
    text-align: center;
    flex-shrink: 0;
}

.result-type-icon {
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.col-key {
    width: 90px;
    flex-shrink: 0;
}

.col-summary {
    flex: 1;
    min-width: 250px;
}

.col-project {
    width: 120px;
    flex-shrink: 0;
}

.col-assignee {
    width: 130px;
    flex-shrink: 0;
}

.col-status,
.col-priority {
    width: 100px;
    flex-shrink: 0;
}

.col-updated {
    width: 110px;
    flex-shrink: 0;
}

.key-link,
.summary-link {
    color: var(--jira-blue);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition);
    display: inline-block;
}

.key-link:hover,
.summary-link:hover {
    color: var(--jira-dark);
    text-decoration: underline;
}

.project-name {
    color: var(--text-primary);
    font-weight: 500;
}

.assignee-name {
    color: var(--text-primary);
    font-weight: 500;
}

.unassigned {
    color: var(--text-secondary);
    font-style: italic;
}

.status-badge-table,
.priority-badge-table {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: inline-block;
    color: white;
    /* Add shadow/outline for better contrast on light backgrounds */
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.updated-time {
    color: var(--text-secondary);
    font-size: 12px;
}

/* Pagination */
.results-pagination {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 16px;
    text-align: center;
    box-shadow: var(--shadow-sm);
}

.pagination {
    display: flex;
    gap: 4px;
    justify-content: center;
    margin: 0;
    list-style: none;
    padding: 0;
    flex-wrap: wrap;
}

.page-item {
    display: inline-block;
}

.page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    color: var(--jira-blue);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all var(--transition);
}

.page-link:hover {
    background: var(--jira-light-blue);
    border-color: var(--jira-blue);
    color: var(--jira-dark);
}

.page-item.active .page-link {
    background: var(--jira-blue);
    border-color: var(--jira-blue);
    color: white;
}

/* Buttons */
.btn {
    padding: 10px 20px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    gap: 6px;
    white-space: nowrap;
}

.btn-primary {
    background: var(--jira-blue);
    color: white;
    border: 1px solid var(--jira-blue);
}

.btn-primary:hover {
    background: var(--jira-dark);
    border-color: var(--jira-dark);
}

.btn-secondary {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--border-color);
    border-color: var(--text-secondary);
}

.btn-outline {
    background: transparent;
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-outline:hover {
    background: var(--bg-secondary);
    border-color: var(--text-secondary);
}

/* Modal */
.modal-content {
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-lg);
    background: var(--bg-primary);
}

.modal-header {
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    padding: 16px 20px;
}

.modal-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    border-top: 1px solid var(--border-color);
    padding: 16px 20px;
    background: var(--bg-secondary);
}

.form-group {
    margin-bottom: 16px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 6px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    color: var(--text-primary);
    background: var(--bg-primary);
    transition: border-color var(--transition), box-shadow var(--transition);
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

.form-control::placeholder {
    color: var(--text-secondary);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .search-main-container {
        grid-template-columns: 260px 1fr;
        gap: 20px;
        padding: 20px 24px;
    }

    .page-header-section {
        padding: 20px 24px;
    }

    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .header-right {
        width: 100%;
    }

    .results-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .results-header-right {
        width: 100%;
        flex-wrap: wrap;
    }

    .results-controls {
        width: 100%;
        flex-wrap: wrap;
    }
}

@media (max-width: 1024px) {
    .search-main-container {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 16px 20px;
    }

    .search-filters-sidebar {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .filter-panel {
        margin: 0;
    }

    .results-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }

    .results-table {
        font-size: 12px;
    }

    .results-table th,
    .results-table td {
        padding: 10px 12px;
    }

    .col-project {
        display: none;
    }

    .results-table tbody td:nth-child(4) {
        display: none;
    }
}

@media (max-width: 768px) {
    .page-header-section {
        padding: 16px;
    }

    .breadcrumb-section {
        padding: 8px 16px;
    }

    .breadcrumb-nav {
        font-size: 12px;
    }

    .page-title {
        font-size: 22px;
    }

    .page-subtitle {
        font-size: 13px;
    }

    .search-main-container {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 16px;
    }

    .search-filters-sidebar {
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }

    .results-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
    }

    .results-header-right {
        width: 100%;
    }

    .results-controls {
        width: 100%;
        gap: 12px;
    }

    .control-group {
        flex: 1;
    }

    .control-label {
        display: none;
    }

    .control-select {
        width: 100%;
    }

    .results-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .results-table-wrapper {
        overflow-x: auto;
    }

    .results-table {
        min-width: 600px;
        font-size: 12px;
    }

    .results-table th,
    .results-table td {
        padding: 10px 12px;
    }

    .col-assignee,
    .col-project {
        display: none;
    }

    .results-table tbody td:nth-child(4),
    .results-table tbody td:nth-child(5) {
        display: none;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 18px;
    }

    .search-filters-sidebar {
        grid-template-columns: 1fr;
    }

    .results-title {
        font-size: 16px;
    }

    .results-count {
        font-size: 12px;
    }

    .view-toggle-group {
        flex-wrap: wrap;
    }

    .results-grid {
        grid-template-columns: 1fr;
    }

    .result-card-header {
        padding: 12px;
    }

    .result-card-body {
        padding: 12px;
    }

    .results-table {
        min-width: 500px;
        font-size: 11px;
    }

    .results-table th {
        padding: 8px 10px;
        font-size: 9px;
    }

    .results-table td {
        padding: 8px 10px;
    }

    .col-icon {
        width: 36px;
        padding: 8px 6px;
    }

    .col-key {
        width: 60px;
    }

    .col-summary {
        min-width: 180px;
    }

    .col-status,
    .col-priority {
        width: 80px;
    }

    .col-updated {
        width: 70px;
    }

    .empty-state {
        padding: 60px 16px;
    }

    .empty-state-icon {
        font-size: 48px;
    }

    .empty-state-title {
        font-size: 14px;
    }

    .empty-state-message {
        font-size: 12px;
    }
}
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
function changeSortOrder(sortValue) {
    const url = new URL(window.location);
    url.searchParams.set('sort', sortValue);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>
<?php \App\Core\View::endSection(); ?>
