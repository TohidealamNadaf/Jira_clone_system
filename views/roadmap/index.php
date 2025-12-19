<?php
/**
 * Global Roadmap View - Enterprise Jira-style Roadmap
 * Displays epics and versions with timeline visualization
 */

declare(strict_types=1);

\App\Core\View::extends('layouts.app');
\App\Core\View::section('content');
?>

<div class="roadmap-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-section">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item">
                    <a href="<?= url('/') ?>" class="breadcrumb-link">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-separator">/</li>
                <li class="breadcrumb-current">Roadmap</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Product Roadmap</h1>
            <p class="page-subtitle">Long-term planning with epics and releases across projects</p>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="quick-actions-bar">
        <div class="actions-container">
            <div class="action-group">
                <label class="action-label" for="projectSelector">Select Project:</label>
                <select id="projectSelector" class="action-select">
                    <option value="">Choose a project...</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="page-content">
        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="loading-card">
            <div class="loading-spinner">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <p class="loading-text">Loading roadmap data...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="empty-state-card">
            <div class="empty-state-icon">📋</div>
            <h3 class="empty-state-title">Select a Project</h3>
            <p class="empty-state-text">Choose a project from the dropdown above to view the roadmap with epics and releases.</p>
        </div>

        <!-- Roadmap Container -->
        <div id="roadmapContainer" style="display: none;">
            <!-- Stats Grid -->
            <div class="stats-grid" id="statsContainer"></div>

            <!-- Timeline Card -->
            <div class="timeline-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-calendar3"></i> Timeline View
                    </h3>
                </div>
                <div class="card-body" id="timelineContainer"></div>
            </div>

            <!-- Epics Section -->
            <div class="section-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-lightning-fill"></i> Epics
                        <span class="section-badge" id="epicCount">0</span>
                    </h3>
                </div>
                <div class="card-body" id="epicsContainer"></div>
            </div>

            <!-- Versions Section -->
            <div class="section-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-tag-fill"></i> Versions / Releases
                        <span class="section-badge" id="versionCount">0</span>
                    </h3>
                </div>
                <div class="card-body" id="versionsContainer"></div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ============================================
       CSS VARIABLES & ROOT
       ============================================ */
    :root {
        --jira-blue: #8B1956;
        --jira-blue-dark: #6F123F;
        --jira-dark: #161B22;
        --jira-gray: #626F86;
        --jira-light: #F7F8FA;
        --jira-border: #DFE1E6;
        --transition-base: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ============================================
       LAYOUT & PAGE STRUCTURE
       ============================================ */
    .roadmap-page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        background-color: var(--jira-light);
    }

    /* Breadcrumb Section */
    .breadcrumb-section {
        background-color: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        padding: 12px 32px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .breadcrumb-list {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .breadcrumb-item a {
        color: var(--jira-blue);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color var(--transition-base);
    }

    .breadcrumb-item a:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-item a i {
        font-size: 14px;
    }

    .breadcrumb-separator {
        color: var(--jira-gray);
        font-size: 12px;
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-size: 13px;
        font-weight: 600;
    }

    /* Page Header */
    .page-header {
        background-color: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        padding: 32px;
    }

    .header-left {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
        letter-spacing: -0.2px;
    }

    .page-subtitle {
        font-size: 15px;
        color: var(--jira-gray);
        margin: 0;
        line-height: 1.5;
        max-width: 600px;
    }

    /* Quick Actions Bar */
    .quick-actions-bar {
        background-color: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        padding: 20px 32px;
    }

    .actions-container {
        display: flex;
        align-items: flex-end;
        gap: 20px;
        flex-wrap: wrap;
    }

    .action-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 280px;
    }

    .action-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .action-select {
        padding: 10px 12px;
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        font-size: 14px;
        color: var(--jira-dark);
        background-color: #FFFFFF;
        cursor: pointer;
        transition: all var(--transition-base);
        font-family: inherit;
    }

    .action-select:hover {
        border-color: var(--jira-gray);
        background-color: var(--jira-light);
    }

    .action-select:focus {
        outline: none;
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
    }

    /* Main Content */
    .page-content {
        flex: 1;
        padding: 32px;
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    /* Loading State */
    .loading-card {
        background-color: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13);
    }

    .loading-spinner {
        font-size: 48px;
        margin-bottom: 16px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .loading-text {
        font-size: 15px;
        color: var(--jira-gray);
        margin: 0;
    }

    /* Empty State */
    .empty-state-card {
        background-color: #FFFFFF;
        border: 2px dashed var(--jira-border);
        border-radius: 8px;
        padding: 60px 40px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    .empty-state-icon {
        font-size: 64px;
        opacity: 0.3;
    }

    .empty-state-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
        letter-spacing: -0.2px;
    }

    .empty-state-text {
        font-size: 14px;
        color: var(--jira-gray);
        margin: 0;
        max-width: 400px;
        line-height: 1.6;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .stats-card {
        background-color: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        transition: all var(--transition-base);
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13);
    }

    .stats-card:hover {
        border-color: var(--jira-gray);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .stats-card-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .stats-card-value {
        font-size: 36px;
        font-weight: 700;
        color: var(--jira-blue);
        margin: 0;
        letter-spacing: -0.3px;
    }

    /* Card Components */
    .timeline-card,
    .section-card {
        background-color: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13);
        overflow: hidden;
    }

    .card-header {
        border-bottom: 1px solid var(--jira-border);
        padding: 20px 24px;
        background-color: #FAFBFC;
    }

    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.2px;
    }

    .card-title i {
        font-size: 18px;
        opacity: 0.8;
    }

    .section-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--jira-blue);
        color: #FFFFFF;
        border-radius: 12px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 700;
        margin-left: auto;
    }

    .card-body {
        padding: 24px;
    }

    /* ============================================
       TIMELINE VISUALIZATION
       ============================================ */
    .timeline-row {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .timeline-label {
        min-width: 160px;
        flex-shrink: 0;
    }

    .timeline-item-key {
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-dark);
        background-color: var(--jira-light);
        padding: 4px 8px;
        border-radius: 4px;
        font-family: monospace;
        display: inline-block;
    }

    .timeline-track {
        flex: 1;
        height: 40px;
        background-color: var(--jira-light);
        border-radius: 6px;
        position: relative;
        border: 1px solid var(--jira-border);
        overflow: hidden;
    }

    .timeline-bar {
        position: absolute;
        top: 0;
        height: 100%;
        background: linear-gradient(90deg, var(--jira-blue), var(--jira-blue-dark));
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 0 8px;
        transition: all var(--transition-base);
        min-height: 2px;
    }

    .timeline-track:hover .timeline-bar {
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
    }

    /* ============================================
       ROADMAP ITEMS
       ============================================ */
    .roadmap-item {
        padding: 20px 24px;
        border-left: 4px solid var(--jira-blue);
        background-color: #FFFFFF;
        margin-bottom: 16px;
        border-radius: 6px;
        border: 1px solid var(--jira-border);
        border-left-width: 4px;
        cursor: pointer;
        transition: all var(--transition-base);
    }

    .roadmap-item:hover {
        background-color: var(--jira-light);
        border-color: var(--jira-gray);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .roadmap-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
    }

    .roadmap-item-content {
        flex: 1;
    }

    .roadmap-item-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0 0 6px 0;
        letter-spacing: -0.2px;
    }

    .roadmap-item-key {
        font-size: 12px;
        color: var(--jira-gray);
        font-family: monospace;
        background-color: var(--jira-light);
        padding: 2px 6px;
        border-radius: 3px;
        display: inline-block;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .status-active {
        background-color: #D4EDDA;
        color: #155724;
        border: 1px solid #C3E6CB;
    }

    .status-released {
        background-color: #CCE5FF;
        color: #004085;
        border: 1px solid #B8DAFF;
    }

    .status-archived {
        background-color: #E2E3E5;
        color: #383D41;
        border: 1px solid #D6D8DB;
    }

    .status-in-progress {
        background-color: #FFF3CD;
        color: #856404;
        border: 1px solid #FFEEBA;
    }

    /* Progress Bar */
    .roadmap-progress-bar {
        height: 28px;
        background-color: var(--jira-light);
        border-radius: 6px;
        overflow: hidden;
        margin: 16px 0;
        border: 1px solid var(--jira-border);
    }

    .roadmap-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--jira-blue), var(--jira-blue-dark));
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 12px;
        font-weight: 700;
        transition: width var(--transition-base);
        min-width: 30px;
    }

    /* Dates */
    .roadmap-dates {
        font-size: 12px;
        color: var(--jira-gray);
        margin: 12px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .roadmap-dates i {
        font-size: 13px;
    }

    /* Metadata */
    .roadmap-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        font-size: 13px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--jira-border);
    }

    .roadmap-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--jira-gray);
    }

    .roadmap-meta-item i {
        font-size: 14px;
        color: var(--jira-blue);
    }

    .roadmap-meta-item strong {
        color: var(--jira-dark);
    }

    /* ============================================
       RESPONSIVE DESIGN
       ============================================ */

    @media (max-width: 1024px) {
        .page-header {
            padding: 24px;
        }

        .quick-actions-bar {
            padding: 16px 24px;
        }

        .page-content {
            padding: 24px;
            gap: 24px;
        }

        .action-group {
            min-width: 240px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .roadmap-meta {
            flex-direction: column;
            gap: 12px;
        }
    }

    @media (max-width: 768px) {
        .breadcrumb-section {
            padding: 10px 16px;
        }

        .page-header {
            padding: 20px 16px;
        }

        .quick-actions-bar {
            padding: 16px;
        }

        .page-content {
            padding: 16px;
            gap: 16px;
        }

        .page-title {
            font-size: 24px;
        }

        .page-subtitle {
            font-size: 13px;
        }

        .action-group {
            min-width: 100%;
        }

        .action-select {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .card-body {
            padding: 16px;
        }

        .card-header {
            padding: 16px;
        }

        .card-title {
            font-size: 14px;
        }

        .timeline-row {
            flex-direction: column;
            gap: 12px;
        }

        .timeline-label {
            min-width: auto;
            width: 100%;
        }

        .timeline-track {
            width: 100%;
        }

        .roadmap-item {
            padding: 16px;
        }

        .roadmap-item-header {
            flex-direction: column;
            gap: 12px;
        }

        .status-badge {
            align-self: flex-start;
        }

        .roadmap-meta {
            gap: 8px;
        }

        .breadcrumb-list {
            gap: 4px;
        }

        .breadcrumb-item a,
        .breadcrumb-current {
            font-size: 12px;
        }

        .breadcrumb-item a i {
            display: none;
        }
    }

    @media (max-width: 480px) {
        .breadcrumb-section {
            padding: 8px 12px;
        }

        .page-header {
            padding: 16px 12px;
        }

        .quick-actions-bar {
            padding: 12px;
        }

        .page-content {
            padding: 12px;
            gap: 12px;
        }

        .page-title {
            font-size: 20px;
        }

        .page-subtitle {
            font-size: 12px;
        }

        .loading-card,
        .empty-state-card {
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 48px;
        }

        .empty-state-title {
            font-size: 16px;
        }

        .empty-state-text {
            font-size: 12px;
        }

        .stats-card {
            padding: 16px;
        }

        .stats-card-value {
            font-size: 28px;
        }

        .stats-card-label {
            font-size: 11px;
        }

        .roadmap-item-title {
            font-size: 13px;
        }

        .roadmap-dates {
            font-size: 11px;
        }

        .roadmap-meta-item {
            font-size: 12px;
        }

        .timeline-item-key {
            font-size: 11px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelector = document.getElementById('projectSelector');
    const roadmapContainer = document.getElementById('roadmapContainer');
    const emptyState = document.getElementById('emptyState');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    let currentProject = null;
    
    // Initialize
    loadingIndicator.style.display = 'none';
    emptyState.style.display = 'block';
    
    // Load projects for selector
    loadProjects();
    
    // Project selector change handler
    projectSelector.addEventListener('change', function() {
        currentProject = this.value;
        if (currentProject) {
            loadRoadmap();
        } else {
            roadmapContainer.style.display = 'none';
            emptyState.style.display = 'block';
            loadingIndicator.style.display = 'none';
        }
    });
    
    // Load projects
    function loadProjects() {
        fetch('<?= url('/api/v1/roadmap/projects') ?>', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                data.data.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.key;
                    option.textContent = `${project.name} (${project.key})`;
                    projectSelector.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading projects:', error));
    }
    
    // Load roadmap data
    function loadRoadmap() {
        emptyState.style.display = 'none';
        loadingIndicator.style.display = 'block';
        roadmapContainer.style.display = 'none';
        
        fetch(`<?= url('/api/v1/roadmap/project') ?>?project=${currentProject}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingIndicator.style.display = 'none';
            
            if (data.success) {
                renderRoadmap(data.data);
                roadmapContainer.style.display = 'block';
            } else {
                emptyState.innerHTML = `
                    <div class="empty-state-icon">⚠️</div>
                    <h3 class="empty-state-title">Unable to Load Roadmap</h3>
                    <p class="empty-state-text">There was an error loading the roadmap. Please try selecting another project.</p>
                `;
                emptyState.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading roadmap:', error);
            loadingIndicator.style.display = 'none';
            emptyState.innerHTML = `
                <div class="empty-state-icon">❌</div>
                <h3 class="empty-state-title">Error Loading Roadmap</h3>
                <p class="empty-state-text">An unexpected error occurred. Please check the console and try again.</p>
            `;
            emptyState.style.display = 'block';
        });
    }
    
    // Render roadmap
    function renderRoadmap(data) {
        const epics = data.epics || [];
        const versions = data.versions || [];
        const stats = data.stats || {};
        
        // Render stats
        renderStats(stats);
        
        // Render timeline
        renderTimeline(epics, versions);
        
        // Render epics
        renderEpics(epics);
        
        // Render versions
        renderVersions(versions);
    }
    
    // Render stats cards
    function renderStats(stats) {
        const statsContainer = document.getElementById('statsContainer');
        statsContainer.innerHTML = '';
        
        const statCards = [
            { label: 'Total Issues', value: stats.total_issues || 0 },
            { label: 'Completed', value: stats.completed_issues || 0 },
            { label: 'In Progress', value: stats.in_progress_issues || 0 },
            { label: 'Not Started', value: stats.not_started_issues || 0 }
        ];
        
        statCards.forEach(stat => {
            const card = document.createElement('div');
            card.className = 'stats-card';
            card.innerHTML = `
                <div class="stats-card-label">${htmlEscape(stat.label)}</div>
                <div class="stats-card-value">${stat.value}</div>
            `;
            statsContainer.appendChild(card);
        });
    }
    
    // Render timeline
    function renderTimeline(epics, versions) {
        const container = document.getElementById('timelineContainer');
        container.innerHTML = '';
        
        const allItems = [
            ...epics.map(e => ({
                type: 'epic',
                key: e.key,
                title: e.summary,
                start: e.start_date,
                end: e.end_date || e.start_date
            })),
            ...versions.map(v => ({
                type: 'version',
                key: v.name,
                title: v.name,
                start: v.start_date,
                end: v.release_date || v.start_date
            }))
        ];
        
        if (allItems.length === 0) {
            container.innerHTML = '<p class="text-muted" style="margin: 0; padding: 20px; text-align: center;">No items to display in timeline</p>';
            return;
        }
        
        // Calculate date range
        const dates = allItems
            .filter(i => i.start && i.end)
            .flatMap(i => [new Date(i.start), new Date(i.end)])
            .sort((a, b) => a - b);
        
        if (dates.length === 0) {
            container.innerHTML = '<p class="text-muted" style="margin: 0; padding: 20px; text-align: center;">No dates available for timeline</p>';
            return;
        }
        
        const minDate = dates[0];
        const maxDate = dates[dates.length - 1];
        const totalDays = (maxDate - minDate) / (1000 * 60 * 60 * 24);
        
        // Render items
        allItems.forEach(item => {
            if (!item.start || !item.end) return;
            
            const itemStart = new Date(item.start);
            const itemEnd = new Date(item.end);
            
            const startOffset = ((itemStart - minDate) / (1000 * 60 * 60 * 24)) / totalDays * 100;
            const width = ((itemEnd - itemStart) / (1000 * 60 * 60 * 24)) / totalDays * 100;
            
            const row = document.createElement('div');
            row.className = 'timeline-row';
            row.innerHTML = `
                <div class="timeline-label">
                    <span class="timeline-item-key">${htmlEscape(item.key)}</span>
                </div>
                <div class="timeline-track">
                    <div class="timeline-bar" style="margin-left: ${startOffset}%; width: ${Math.max(width, 1)}%;">
                        ${width > 15 ? htmlEscape(item.title) : ''}
                    </div>
                </div>
            `;
            container.appendChild(row);
        });
    }
    
    // Render epics
    function renderEpics(epics) {
        const container = document.getElementById('epicsContainer');
        const countBadge = document.getElementById('epicCount');
        
        container.innerHTML = '';
        countBadge.textContent = epics.length;
        
        if (epics.length === 0) {
            container.innerHTML = '<p style="margin: 0; text-align: center; color: #626F86; padding: 20px;">No epics found for this project</p>';
            return;
        }
        
        epics.forEach(epic => {
            const progress = epic.progress || 0;
            const startDate = epic.start_date ? new Date(epic.start_date).toLocaleDateString() : 'Not set';
            const endDate = epic.end_date ? new Date(epic.end_date).toLocaleDateString() : 'Not set';
            
            const item = document.createElement('div');
            item.className = 'roadmap-item';
            item.innerHTML = `
                <div class="roadmap-item-header">
                    <div class="roadmap-item-content">
                        <h4 class="roadmap-item-title">${htmlEscape(epic.summary)}</h4>
                        <span class="roadmap-item-key">${htmlEscape(epic.key)}</span>
                    </div>
                    <span class="status-badge status-${epic.status_name ? epic.status_name.toLowerCase().replace(/\s+/g, '-') : 'active'}">
                        ${htmlEscape(epic.status_name || 'Active')}
                    </span>
                </div>
                
                <div class="roadmap-progress-bar">
                    <div class="roadmap-progress-fill" style="width: ${progress}%;">
                        ${progress}%
                    </div>
                </div>
                
                <div class="roadmap-dates">
                    <i class="bi bi-calendar-event"></i> ${startDate} — ${endDate}
                </div>
                
                <div class="roadmap-meta">
                    <div class="roadmap-meta-item">
                        <i class="bi bi-list-check"></i>
                        <span><strong>${epic.issue_count || 0}</strong> issues</span>
                    </div>
                    <div class="roadmap-meta-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><strong>${epic.completed_count || 0}</strong> completed</span>
                    </div>
                    <div class="roadmap-meta-item">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>Priority: <strong>${htmlEscape(epic.priority || 'Medium')}</strong></span>
                    </div>
                </div>
            `;
            container.appendChild(item);
        });
    }
    
    // Render versions
    function renderVersions(versions) {
        const container = document.getElementById('versionsContainer');
        const countBadge = document.getElementById('versionCount');
        
        container.innerHTML = '';
        countBadge.textContent = versions.length;
        
        if (versions.length === 0) {
            container.innerHTML = '<p style="margin: 0; text-align: center; color: #626F86; padding: 20px;">No versions found for this project</p>';
            return;
        }
        
        versions.forEach(version => {
            const progress = version.progress || 0;
            const startDate = version.start_date ? new Date(version.start_date).toLocaleDateString() : 'Not set';
            const releaseDate = version.release_date ? new Date(version.release_date).toLocaleDateString() : 'Not set';
            
            const item = document.createElement('div');
            item.className = 'roadmap-item';
            item.innerHTML = `
                <div class="roadmap-item-header">
                    <div class="roadmap-item-content">
                        <h4 class="roadmap-item-title">${htmlEscape(version.name)}</h4>
                    </div>
                    <span class="status-badge status-${version.status ? version.status.toLowerCase().replace(/\s+/g, '-') : 'active'}">
                        ${htmlEscape(version.status || 'Active')}
                    </span>
                </div>
                
                <div class="roadmap-progress-bar">
                    <div class="roadmap-progress-fill" style="width: ${progress}%;">
                        ${progress}%
                    </div>
                </div>
                
                <div class="roadmap-dates">
                    <i class="bi bi-calendar-event"></i> ${startDate} — ${releaseDate}
                </div>
                
                <div class="roadmap-meta">
                    <div class="roadmap-meta-item">
                        <i class="bi bi-list-check"></i>
                        <span><strong>${version.issue_count || 0}</strong> issues</span>
                    </div>
                    <div class="roadmap-meta-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><strong>${version.completed_count || 0}</strong> completed</span>
                    </div>
                </div>
            `;
            container.appendChild(item);
        });
    }
    
    // Helper function
    function htmlEscape(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>

<?php \App\Core\View::endSection(); ?>
