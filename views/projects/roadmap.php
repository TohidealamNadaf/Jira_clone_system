<?php
/**
 * Project Roadmap View - Enterprise Jira-style Roadmap
 * Displays epics and versions with timeline visualization for a specific project
 */

declare(strict_types=1);

\App\Core\View::extends('layouts.app');
?>

<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?= url('/') ?>">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= url('/projects') ?>">Projects</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= url('/projects/' . ($projectKey ?? '')) ?>">
                            <?= htmlspecialchars($projectKey ?? '') ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Roadmap</li>
                </ol>
            </nav>
            <h1 class="h3 mt-2 mb-0">Project Roadmap</h1>
            <p class="text-muted mb-0">Epics and releases planned for <?= htmlspecialchars($projectKey ?? '') ?></p>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="alert alert-info">
        <i class="bi bi-hourglass-split"></i> Loading roadmap data...
    </div>

    <!-- Roadmap Container -->
    <div id="roadmapContainer" style="display: none;">
        <!-- Stats Cards -->
        <div class="row mb-4" id="statsContainer"></div>

        <!-- Timeline Visualization -->
        <div class="card mb-4" id="timelineCard">
            <div class="card-header bg-light">
                <h6 class="mb-0">Timeline View</h6>
            </div>
            <div class="card-body" id="timelineContainer"></div>
        </div>

        <!-- Epics Section -->
        <div class="card mb-4" id="epicsCard">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-lightning-fill"></i> Epics
                    <span class="badge bg-secondary ms-2" id="epicCount">0</span>
                </h6>
            </div>
            <div class="card-body" id="epicsContainer"></div>
        </div>

        <!-- Versions Section -->
        <div class="card" id="versionsCard">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-tag-fill"></i> Versions / Releases
                    <span class="badge bg-secondary ms-2" id="versionCount">0</span>
                </h6>
            </div>
            <div class="card-body" id="versionsContainer"></div>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="alert alert-info" style="display: none;">
        <i class="bi bi-info-circle"></i> No epics or versions found for this project.
    </div>
</div>

<style>
    .roadmap-item {
        padding: 16px;
        border-left: 4px solid #8B1956;
        background: #f8f9fa;
        margin-bottom: 12px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .roadmap-item:hover {
        background: #f0f0f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .roadmap-item-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 8px;
    }
    
    .roadmap-item-title {
        font-size: 14px;
        font-weight: 600;
        color: #161B22;
    }
    
    .roadmap-item-key {
        font-size: 12px;
        color: #626F86;
        font-family: monospace;
        background: white;
        padding: 2px 6px;
        border-radius: 3px;
    }
    
    .roadmap-progress-bar {
        height: 24px;
        background: #e5e5e5;
        border-radius: 4px;
        overflow: hidden;
        margin: 8px 0;
    }
    
    .roadmap-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #8B1956, #A0245B);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: 600;
        transition: width 0.3s ease;
    }
    
    .roadmap-dates {
        font-size: 12px;
        color: #626F86;
        margin-top: 8px;
    }
    
    .roadmap-meta {
        display: flex;
        gap: 16px;
        font-size: 12px;
        margin-top: 12px;
        flex-wrap: wrap;
    }
    
    .roadmap-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-active { background: #d4edda; color: #155724; }
    .status-released { background: #cce5ff; color: #004085; }
    .status-archived { background: #e2e3e5; color: #383d41; }
    
    .stats-card {
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
    
    .stats-card-value {
        font-size: 32px;
        font-weight: 700;
        color: #8B1956;
        margin: 8px 0;
    }
    
    .stats-card-label {
        font-size: 13px;
        color: #626F86;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .timeline-bar {
        height: 40px;
        background: linear-gradient(90deg, #8B1956, #A0245B);
        border-radius: 4px;
        display: flex;
        align-items: center;
        padding: 0 8px;
        color: white;
        font-size: 12px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .timeline-row {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
        gap: 12px;
    }
    
    .timeline-label {
        min-width: 150px;
        font-size: 13px;
        font-weight: 600;
        color: #161B22;
    }
    
    .timeline-track {
        flex: 1;
        height: 40px;
        background: #f0f0f0;
        border-radius: 4px;
        position: relative;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectKey = <?= json_encode($projectKey ?? '') ?>;
    const roadmapContainer = document.getElementById('roadmapContainer');
    const emptyState = document.getElementById('emptyState');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    if (!projectKey) {
        loadingIndicator.style.display = 'none';
        emptyState.textContent = '❌ No project key provided';
        emptyState.style.display = 'block';
        return;
    }
    
    // Load roadmap data
    loadRoadmap();
    
    function loadRoadmap() {
        fetch(`/api/v1/roadmap/project?project=${projectKey}`, {
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
                const roadmapData = data.data;
                
                // Check if there's any data to display
                const hasEpics = roadmapData.epics && roadmapData.epics.length > 0;
                const hasVersions = roadmapData.versions && roadmapData.versions.length > 0;
                
                if (!hasEpics && !hasVersions) {
                    emptyState.style.display = 'block';
                    return;
                }
                
                renderRoadmap(roadmapData);
                roadmapContainer.style.display = 'block';
            } else {
                emptyState.textContent = '❌ Error loading roadmap. Please try again.';
                emptyState.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading roadmap:', error);
            loadingIndicator.style.display = 'none';
            emptyState.textContent = '❌ Error loading roadmap. Please check console.';
            emptyState.style.display = 'block';
        });
    }
    
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
            card.className = 'col-md-3 mb-3';
            card.innerHTML = `
                <div class="stats-card">
                    <div class="stats-card-label">${stat.label}</div>
                    <div class="stats-card-value">${stat.value}</div>
                </div>
            `;
            statsContainer.appendChild(card);
        });
    }
    
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
            container.innerHTML = '<p class="text-muted">No items to display in timeline</p>';
            return;
        }
        
        // Calculate date range
        const dates = allItems
            .filter(i => i.start && i.end)
            .flatMap(i => [new Date(i.start), new Date(i.end)])
            .sort((a, b) => a - b);
        
        if (dates.length === 0) {
            container.innerHTML = '<p class="text-muted">No dates available for timeline</p>';
            return;
        }
        
        const minDate = dates[0];
        const maxDate = dates[dates.length - 1];
        const totalDays = (maxDate - minDate) / (1000 * 60 * 60 * 24) || 1;
        
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
                    <span class="roadmap-item-key">${htmlEscape(item.key)}</span>
                </div>
                <div class="timeline-track">
                    <div class="timeline-bar" style="margin-left: ${Math.max(0, startOffset)}%; width: ${Math.max(2, width)}%; min-width: 2px;">
                        ${width > 15 ? htmlEscape(item.title) : ''}
                    </div>
                </div>
            `;
            container.appendChild(row);
        });
    }
    
    function renderEpics(epics) {
        const container = document.getElementById('epicsContainer');
        const countBadge = document.getElementById('epicCount');
        
        container.innerHTML = '';
        countBadge.textContent = epics.length;
        
        if (epics.length === 0) {
            container.innerHTML = '<p class="text-muted">No epics found for this project</p>';
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
                    <div>
                        <div class="roadmap-item-title">${htmlEscape(epic.summary)}</div>
                        <div class="roadmap-item-key">${htmlEscape(epic.key)}</div>
                    </div>
                    <span class="status-badge status-${epic.status_name ? epic.status_name.toLowerCase() : 'active'}">
                        ${htmlEscape(epic.status_name || 'Active')}
                    </span>
                </div>
                
                <div class="roadmap-progress-bar">
                    <div class="roadmap-progress-fill" style="width: ${progress}%;">
                        ${progress}%
                    </div>
                </div>
                
                <div class="roadmap-dates">
                    <i class="bi bi-calendar"></i> ${startDate} — ${endDate}
                </div>
                
                <div class="roadmap-meta">
                    <div class="roadmap-meta-item">
                        <i class="bi bi-list-check"></i>
                        <span>${epic.issue_count || 0} issues</span>
                    </div>
                    <div class="roadmap-meta-item">
                        <i class="bi bi-checkmark-circle"></i>
                        <span>${epic.completed_count || 0} completed</span>
                    </div>
                    <div class="roadmap-meta-item">
                        <i class="bi bi-exclamation-circle"></i>
                        <span>Priority: ${htmlEscape(epic.priority || 'Medium')}</span>
                    </div>
                </div>
            `;
            container.appendChild(item);
        });
    }
    
    function renderVersions(versions) {
        const container = document.getElementById('versionsContainer');
        const countBadge = document.getElementById('versionCount');
        
        container.innerHTML = '';
        countBadge.textContent = versions.length;
        
        if (versions.length === 0) {
            container.innerHTML = '<p class="text-muted">No versions found for this project</p>';
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
                    <div>
                        <div class="roadmap-item-title">${htmlEscape(version.name)}</div>
                    </div>
                    <span class="status-badge status-${version.status ? version.status.toLowerCase() : 'active'}">
                        ${htmlEscape(version.status || 'Active')}
                    </span>
                </div>
                
                <div class="roadmap-progress-bar">
                    <div class="roadmap-progress-fill" style="width: ${progress}%;">
                        ${progress}%
                    </div>
                </div>
                
                <div class="roadmap-dates">
                    <i class="bi bi-calendar"></i> ${startDate} — ${releaseDate}
                </div>
                
                <div class="roadmap-meta">
                    <div class="roadmap-meta-item">
                        <i class="bi bi-list-check"></i>
                        <span>${version.issue_count || 0} issues</span>
                    </div>
                    <div class="roadmap-meta-item">
                        <i class="bi bi-checkmark-circle"></i>
                        <span>${version.completed_count || 0} completed</span>
                    </div>
                </div>
            `;
            container.appendChild(item);
        });
    }
    
    function htmlEscape(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
