<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="calendar-page-wrapper">
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
                <li class="breadcrumb-current">Calendar</li>
            </ol>
        </nav>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Calendar View</h1>
            <p class="page-subtitle">Track issue due dates, start dates, and end dates across all projects</p>
        </div>
        <div class="header-actions">
            <!-- Navigation controls moved inside FullCalendar toolbar usually, but we keep custom -->
            <button type="button" class="action-button" id="prevMonth" title="Previous">
                <i class="bi bi-chevron-left"></i> Previous
            </button>
            <button type="button" class="action-button action-button-primary" id="todayBtn" title="Go to today">
                <i class="bi bi-calendar-week"></i> Today
            </button>
            <button type="button" class="action-button" id="nextMonth" title="Next">
                Next <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="filters-section">
        <div class="filters-container">
            <div class="filter-group">
                <label class="filter-label" for="projectFilter">Project</label>
                <select id="projectFilter" class="filter-select">
                    <option value="">All Projects</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="statusFilter">Status</label>
                <select id="statusFilter" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="priorityFilter">Priority</label>
                <select id="priorityFilter" class="filter-select">
                    <option value="">All Priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="viewSelect">View</label>
                <select id="viewSelect" class="filter-select">
                    <option value="month">Month</option>
                    <option value="week">Week</option>
                    <option value="day">Day</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="page-content">
        <!-- Calendar Container -->
        <div class="calendar-card">
            <div class="card-body">
                <div id="calendar" class="calendar-container"></div>
            </div>
        </div>

        <!-- Legend -->
        <div class="legend-card">
            <div class="card-header">
                <h3 class="card-title">Legend</h3>
            </div>
            <div class="card-body">
                <div class="legend-grid">
                    <div class="legend-section">
                        <h4 class="legend-section-title">Priority</h4>
                        <div class="legend-items">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #d9534f;"></div><span>Urgent</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #f0ad4e;"></div><span>High</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #5bc0de;"></div><span>Medium</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #5cb85c;"></div><span>Low</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Issue Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="eventDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="viewIssueBtn" class="btn btn-primary" target="_blank">View Issue</a>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('styles'); ?>
<style>
    /* Clean styles re-embedded here for completeness */
    :root {
        --jira-bg: #F4F5F7;
        --jira-border: #DFE1E6;
        --jira-text: #172B4D;
        --jira-text-sec: #6B778C;
        --jira-blue: #0052CC;
    }

    .calendar-page-wrapper {
        background: var(--jira-bg);
        min-height: 100vh;
        padding-bottom: 40px;
    }

    /* Filters */
    .filters-section {
        background: #fff;
        padding: 15px 32px;
        border-bottom: 1px solid var(--jira-border);
    }

    .filters-container {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filter-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--jira-text-sec);
    }

    .filter-select {
        padding: 6px 10px;
        border: 1px solid var(--jira-border);
        border-radius: 3px;
        background: #FAFBFC;
        min-width: 150px;
    }

    /* Page Content */
    .page-content {
        padding: 20px 32px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .calendar-card,
    .legend-card {
        background: #fff;
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        margin-bottom: 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .card-body {
        padding: 20px;
    }

    /* FullCalendar Overrides */
    .fc-event {
        cursor: pointer;
        border: none;
        border-radius: 3px;
        font-size: 12px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }

    .fc-event-content {
        padding: 2px 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #fff;
    }

    .page-header {
        background: #fff;
        padding: 24px 32px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        margin: 0;
        font-size: 24px;
        color: var(--jira-text);
    }

    .action-button {
        background: #FAFBFC;
        border: 1px solid var(--jira-border);
        padding: 8px 12px;
        border-radius: 3px;
        cursor: pointer;
    }

    .action-button:hover {
        background: #EBECF0;
    }

    .action-button-primary {
        background: var(--jira-blue);
        color: #fff;
        border: none;
    }

    .action-button-primary:hover {
        background: #0065FF;
    }

    /* Legend */
    .legend-items {
        display: flex;
        gap: 15px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }
</style>
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<!-- Config Setup -->
<script>
    window.JiraConfig = {
        apiBase: "<?= url('/api/v1') ?>",
        webBase: "<?= url('/') ?>",
        csrfToken: "<?= csrf_token() ?>"
    };
</script>

<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="<?= asset('assets/js/calendar.js') ?>"></script>
<?php \App\Core\View::endSection(); ?>