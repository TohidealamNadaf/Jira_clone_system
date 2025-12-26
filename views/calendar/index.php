<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="calendar-wrapper">
    <!-- Jira-style Header -->
    <div class="calendar-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="calendar-title">Calendar</h1>
                <p class="calendar-subtitle">Track due dates, sprints, and milestones across all projects</p>
            </div>
            <div class="header-actions">
                <button class="jira-btn jira-btn-secondary" id="createEventBtn">
                    <i class="bi bi-plus-lg"></i>
                    Create
                </button>
                <div class="view-switcher">
                    <button class="view-btn active" data-view="month">Month</button>
                    <button class="view-btn" data-view="week">Week</button>
                    <button class="view-btn" data-view="day">Day</button>
                    <button class="view-btn" data-view="list">List</button>
                </div>
                <button class="jira-btn jira-btn-secondary" id="todayBtn">
                    Today
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Filters Bar -->
    <div class="quick-filters">
        <div class="filter-tabs">
            <button class="filter-tab active" data-filter="all">All issues</button>
            <button class="filter-tab" data-filter="assigned">Assigned to me</button>
            <button class="filter-tab" data-filter="overdue">Overdue</button>
            <button class="filter-tab" data-filter="due-today">Due today</button>
            <button class="filter-tab" data-filter="due-week">Due this week</button>
        </div>
        <div class="filter-controls">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="quickSearch" placeholder="Search issues...">
            </div>
            <div class="project-filter">
                <select id="projectFilter" class="jira-select">
                    <option value="">All projects</option>
                </select>
            </div>
            <button class="jira-btn jira-btn-ghost" id="moreFilters">
                <i class="bi bi-funnel"></i>
                More filters
            </button>
        </div>
    </div>

    <!-- Advanced Filters Panel (Hidden by default) -->
    <div class="advanced-filters" id="advancedFilters" style="display: none;">
        <div class="filters-grid">
            <div class="filter-group">
                <label>Status</label>
                <select id="statusFilter" class="jira-select" multiple>
                    <!-- Loaded dynamically -->
                </select>
            </div>
            <div class="filter-group">
                <label>Priority</label>
                <select id="priorityFilter" class="jira-select" multiple>
                    <!-- Loaded dynamically -->
                </select>
            </div>
            <div class="filter-group">
                <label>Issue Type</label>
                <select id="typeFilter" class="jira-select" multiple>
                    <!-- Loaded dynamically -->
                </select>
            </div>
            <div class="filter-group">
                <label>Assignee</label>
                <select id="assigneeFilter" class="jira-select">
                    <option value="">Anyone</option>
                    <!-- Loaded dynamically -->
                </select>
            </div>
            <div class="filter-group">
                <label>Reporter</label>
                <select id="reporterFilter" class="jira-select">
                    <option value="">Anyone</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Created Date</label>
                <div class="date-range">
                    <input type="date" id="createdFrom" class="jira-input">
                    <span>to</span>
                    <input type="date" id="createdTo" class="jira-input">
                </div>
            </div>
            <div class="filter-group">
                <label>Updated Date</label>
                <div class="date-range">
                    <input type="date" id="updatedFrom" class="jira-input">
                    <span>to</span>
                    <input type="date" id="updatedTo" class="jira-input">
                </div>
            </div>
            <div class="filter-actions">
                <button class="jira-btn jira-btn-secondary" id="resetFilters">Reset</button>
                <button class="jira-btn jira-btn-primary" id="applyFilters">Apply filters</button>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="calendar-main">
        <div class="calendar-sidebar">
            <!-- Unscheduled Issues -->
            <div class="unscheduled-section">
                <h3 class="sidebar-title">
                    <i class="bi bi-calendar-x"></i>
                    Unscheduled Issues
                    <span class="unscheduled-count" id="unscheduledCount">0</span>
                </h3>
                <div class="unscheduled-list" id="unscheduledList">
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>All issues scheduled</p>
                    </div>
                </div>
            </div>



            <!-- Unscheduled Issues -->
            <div class="unscheduled-section">
                <h3 class="sidebar-title">
                    <i class="bi bi-calendar-x"></i>
                    Unscheduled Issues
                    <span class="unscheduled-count" id="unscheduledCount">0</span>
                </h3>
                <div class="unscheduled-list" id="unscheduledList">
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>All issues scheduled</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="calendar-content">
            <div class="calendar-toolbar">
                <div class="toolbar-left">
                    <button class="nav-btn" id="prevBtn">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="nav-btn" id="nextBtn">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    <h2 class="current-date" id="currentDate">December 2025</h2>
                </div>
                <div class="toolbar-right">
                    <div class="layout-options">
                        <button class="layout-btn active" data-layout="standard" title="Standard view">
                            <i class="bi bi-calendar3"></i>
                        </button>
                        <button class="layout-btn" data-layout="compact" title="Compact view">
                            <i class="bi bi-calendar-week"></i>
                        </button>
                        <button class="layout-btn" data-layout="detailed" title="Detailed view">
                            <i class="bi bi-calendar-range"></i>
                        </button>
                    </div>
                    <button class="jira-btn jira-btn-ghost" id="exportBtn">
                        <i class="bi bi-download"></i>
                        Export
                    </button>
                <button class="jira-btn jira-btn-ghost" id="settingsBtn">
                    <i class="bi bi-gear"></i>
                    Settings
                </button>
                <button class="jira-btn jira-btn-ghost" id="debugDropBtn" onclick="debugCalendarDrop()">
                    <i class="bi bi-bug"></i>
                    Debug Drop
                </button>
                <button class="jira-btn jira-btn-ghost" id="testDragBtn" onclick="testDragData()">
                    <i class="bi bi-cursor"></i>
                    Test Drag
                </button>
                </div>
            </div>

            <div class="calendar-grid">
                <div id="mainCalendar" class="main-calendar"></div>
            </div>
        </div>
    </div>

    <!-- Summary Bar -->
    <div class="summary-bar">
        <div class="summary-item">
            <span class="label">Total Issues:</span>
            <span class="value" id="totalIssues">0</span>
        </div>
        <div class="summary-item">
            <span class="label">Overdue:</span>
            <span class="value overdue" id="overdueIssues">0</span>
        </div>
        <div class="summary-item">
            <span class="label">Due Today:</span>
            <span class="value due-today" id="dueTodayIssues">0</span>
        </div>
        <div class="summary-item">
            <span class="label">Due This Week:</span>
            <span class="value due-week" id="dueWeekIssues">0</span>
        </div>
        <div class="summary-item">
            <span class="label">My Issues:</span>
            <span class="value" id="myIssues">0</span>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="jira-modal" id="eventModal" onclick="handleBackdropClick(event)" aria-hidden="true">
    <div class="modal-dialog modal-standard">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="eventModalTitle">Event Details</h2>
                <button class="modal-close" onclick="closeEventModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body-scroll">
                <div class="event-info">
                    <div class="event-type-badge" id="eventTypeBadge">
                        <i class="bi bi-bug"></i>
                        Bug
                    </div>
                    <div class="event-basic">
                        <h3 class="event-key" id="eventKey">PROJ-123</h3>
                        <h4 class="event-summary" id="eventSummary">Fix calendar loading issue</h4>
                    </div>
                </div> 

                <div class="event-details-grid">
                    <div class="detail-row">
                        <label>Project</label>
                        <span id="eventProject">Project Alpha</span>
                    </div>
                    <div class="detail-row">
                        <label>Status</label>
                        <span class="status-badge" id="eventStatus">In Progress</span>
                    </div>
                    <div class="detail-row">
                        <label>Priority</label>
                        <span class="priority-badge urgent" id="eventPriority">Urgent</span>
                    </div>
                    <div class="detail-row">
                        <label>Assignee</label>
                        <div class="assignee-info">
                            <img class="assignee-avatar" id="assigneeAvatar" src="" alt="">
                            <span id="assigneeName">John Doe</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <label>Reporter</label>
                        <div class="reporter-info">
                            <img class="reporter-avatar" id="reporterAvatar" src="" alt="">
                            <span id="reporterName">Jane Smith</span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <label>Due Date</label>
                        <span id="eventDueDate">Dec 24, 2025</span>
                    </div>
                    <div class="detail-row">
                        <label>Created</label>
                        <span id="eventCreatedDate">Dec 20, 2025</span>
                    </div>
                    <div class="detail-row">
                        <label>Updated</label>
                        <span id="eventUpdatedDate">Dec 22, 2025</span>
                    </div>
                    <div class="detail-row">
                        <label>Story Points</label>
                        <span id="eventStoryPoints">5</span>
                    </div>
                    <div class="detail-row">
                        <label>Labels</label>
                        <div class="labels-container" id="eventLabels">
                            <span class="label-tag">backend</span>
                            <span class="label-tag">urgent</span>
                        </div>
                    </div>
                </div> 

                <div class="event-description">
                    <h4>Description</h4>
                    <div class="description-content" id="eventDescription">
                        The calendar page is not loading properly and shows no styling. Need to investigate the CSS
                        loading issue and fix the design.
                    </div>
                </div> 

                <div class="event-timeline">
                    <h4>Recent Activity</h4>
                    <div class="timeline-items" id="eventTimeline">
                        <div class="timeline-item">
                            <div class="timeline-dot status"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <strong>Status changed</strong>
                                    <span class="timeline-time">2 hours ago</span>
                                </div>
                                <div class="timeline-detail">
                                    From <span class="old-value">To Do</span> to <span class="new-value">In
                                        Progress</span>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot comment"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <strong>Comment added</strong>
                                    <span class="timeline-time">5 hours ago</span>
                                </div>
                                <div class="timeline-detail">
                                    Working on CSS fix now. Should be ready by EOD.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="footer-left">
                    <button class="jira-btn jira-btn-ghost" onclick="watchIssue()">
                        <i class="bi bi-eye"></i>
                        Watch
                    </button>
                    <button class="jira-btn jira-btn-ghost" onclick="shareIssue()">
                        <i class="bi bi-share"></i>
                        Share
                    </button>
                </div>
                <div class="footer-right">
                    <button class="jira-btn jira-btn-secondary" onclick="editIssue()">
                        <i class="bi bi-pencil"></i>
                        Edit
                    </button>
                    <button class="jira-btn jira-btn-primary" id="viewIssueBtn">
                        <i class="bi bi-box-arrow-up-right"></i>
                        View Issue
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Event Modal -->
<div class="jira-modal" id="createEventModal" onclick="handleBackdropClick(event, 'create')" aria-hidden="true">
    <div class="modal-dialog modal-standard">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Create Calendar Event</h2>
                <button class="modal-close" onclick="closeCreateModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body-scroll">
                <form id="createEventForm">
                    <div class="form-section">
                        <h3>Event Details</h3>
                        <div class="form-row">
                            <div class="form-group half">
                                <label>Event Type</label>
                                <select id="eventType" class="jira-select">
                                    <option value="issue">Issue Due Date</option>
                                    <option value="sprint">Sprint Start/End</option>
                                    <option value="milestone">Milestone</option>
                                    <option value="reminder">Reminder</option>
                                    <option value="meeting">Meeting</option>
                                </select>
                            </div>
                            <div class="form-group half">
                                <label>Project</label>
                                <select id="eventProject" class="jira-select">
                                    <option value="">Select project...</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" id="eventTitle" class="jira-input" placeholder="Event title..." required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="eventDesc" class="jira-textarea" rows="2"
                                placeholder="Event description..."></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group half">
                                <label>Start Date</label>
                                <input type="datetime-local" id="eventStartDate" class="jira-input" required>
                            </div>
                            <div class="form-group half">
                                <label>End Date</label>
                                <input type="datetime-local" id="eventEndDate" class="jira-input" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group half">
                                <label>Priority</label>
                                <select id="eventPriority" class="jira-select">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="form-group half">
                                <label>Attendees</label>
                                <input type="text" id="eventAttendees" class="jira-input"
                                    placeholder="Add attendees...">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Reminders</h3>
                        <div class="reminder-settings">
                            <div class="reminder-item">
                                <input type="checkbox" id="reminder1" checked>
                                <label for="reminder1">15 minutes before</label>
                            </div>
                            <div class="reminder-item">
                                <input type="checkbox" id="reminder2">
                                <label for="reminder2">1 hour before</label>
                            </div>
                            <div class="reminder-item">
                                <input type="checkbox" id="reminder3">
                                <label for="reminder3">1 day before</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Recurring</h3>
                        <div class="recurring-settings">
                            <div class="form-group">
                                <select id="recurringType" class="jira-select">
                                    <option value="none">Does not repeat</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="custom">Custom...</option>
                                </select>
                            </div>
                            <div class="recurring-options" id="recurringOptions" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group half">
                                        <label>Every</label>
                                        <input type="number" id="recurringInterval" class="jira-input" value="1"
                                            min="1">
                                    </div>
                                    <div class="form-group half">
                                        <label>Ends</label>
                                        <select id="recurringEnds" class="jira-select">
                                            <option value="never">Never</option>
                                            <option value="after">After</option>
                                            <option value="on">On</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="footer-left">
                    <button class="jira-btn jira-btn-ghost" onclick="previewEvent()">
                        <i class="bi bi-eye"></i>
                        Preview
                    </button>
                </div>
                <div class="footer-right">
                    <button class="jira-btn jira-btn-secondary" onclick="closeCreateModal()">Cancel</button>
                    <button class="jira-btn jira-btn-primary" onclick="saveEvent()">
                        <i class="bi bi-check-lg"></i>
                        Create Event
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="jira-modal" id="exportModal" onclick="handleBackdropClick(event, 'export')" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Export Calendar</h2>
                <button class="modal-close" onclick="closeExportModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="export-options">
                    <div class="export-option">
                        <input type="radio" id="exportIcs" name="exportFormat" value="ics" checked>
                        <label for="exportIcs">
                            <i class="bi bi-calendar-check"></i>
                            <div class="option-content">
                                <strong>iCalendar (.ics)</strong>
                                <p>Import into Google Calendar, Outlook, Apple Calendar</p>
                            </div>
                        </label>
                    </div>
                    <div class="export-option">
                        <input type="radio" id="exportCsv" name="exportFormat" value="csv">
                        <label for="exportCsv">
                            <i class="bi bi-file-earmark-spreadsheet"></i>
                            <div class="option-content">
                                <strong>CSV</strong>
                                <p>Import into Excel, Google Sheets, data analysis</p>
                            </div>
                        </label>
                    </div>
                    <div class="export-option">
                        <input type="radio" id="exportPdf" name="exportFormat" value="pdf">
                        <label for="exportPdf">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <div class="option-content">
                                <strong>PDF</strong>
                                <p>Print or share as document</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="export-filters">
                    <h4>Export Options</h4>
                    <div class="form-row">
                        <div class="form-group half">
                            <label>Date Range</label>
                            <select id="exportDateRange" class="jira-select">
                                <option value="current-month">Current Month</option>
                                <option value="next-month">Next Month</option>
                                <option value="current-quarter">Current Quarter</option>
                                <option value="current-year">Current Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="form-group half">
                            <label>Include</label>
                            <select id="exportInclude" class="jira-select" multiple>
                                <option value="issues" selected>Issues</option>
                                <option value="sprints">Sprints</option>
                                <option value="milestones">Milestones</option>
                                <option value="meetings">Meetings</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="footer-right">
                    <button class="jira-btn jira-btn-secondary" onclick="closeExportModal()">Cancel</button>
                    <button class="jira-btn jira-btn-primary" onclick="performExport()">
                        <i class="bi bi-download"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Issue Modal -->
<div class="jira-modal" id="scheduleIssueModal" onclick="handleBackdropClick(event, 'schedule')" aria-hidden="true">
    <div class="modal-dialog modal-standard">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Schedule Issue</h2>
                <button class="modal-close" onclick="closeScheduleModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body-scroll">
                <div class="issue-preview" id="issuePreview">
                    <div class="issue-type-badge" id="scheduleIssueType">
                        <i class="bi bi-bug"></i>
                        Bug
                    </div>
                    <div class="issue-basic">
                        <h3 class="issue-key" id="scheduleIssueKey">PROJ-123</h3>
                        <h4 class="issue-summary" id="scheduleIssueSummary">Fix calendar loading issue</h4>
                    </div>
                </div>

                <form id="scheduleIssueForm">
                    <input type="hidden" id="scheduleIssueId" name="issue_id">
                    
                    <div class="form-section">
                        <h3>Schedule Details</h3>
                        <div class="form-group">
                            <label for="scheduleDueDate">Due Date *</label>
                            <input type="date" id="scheduleDueDate" name="due_date" class="jira-input" required>
                            <small class="form-help">The date you dropped this issue on will be set as the due date</small>
                        </div>
                        <div class="form-group">
                            <label for="scheduleStartDate">Start Date</label>
                            <input type="date" id="scheduleStartDate" name="start_date" class="jira-input">
                            <small class="form-help">Optional: When work should begin on this issue</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Issue Information</h3>
                        <div class="info-grid">
                            <div class="info-row">
                                <label>Project</label>
                                <span id="scheduleProjectName">Project Alpha</span>
                            </div>
                            <div class="info-row">
                                <label>Priority</label>
                                <span class="priority-badge" id="schedulePriority">Medium</span>
                            </div>
                            <div class="info-row">
                                <label>Assignee</label>
                                <div class="assignee-info" id="scheduleAssignee">
                                    <img class="assignee-avatar" src="" alt="">
                                    <span>John Doe</span>
                                </div>
                            </div>
                            <div class="info-row">
                                <label>Created</label>
                                <span id="scheduleCreatedDate">Dec 20, 2025</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="footer-left">
                    <button class="jira-btn jira-btn-ghost" onclick="viewIssueDetails()">
                        <i class="bi bi-box-arrow-up-right"></i>
                        View Issue
                    </button>
                </div>
                <div class="footer-right">
                    <button class="jira-btn jira-btn-secondary" onclick="closeScheduleModal()">Cancel</button>
                    <button class="jira-btn jira-btn-primary" onclick="saveScheduledIssue()">
                        <i class="bi bi-check-lg"></i>
                        Schedule Issue
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

 <?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('styles'); ?>
<!-- Calendar styles are included in app.css -->
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<!-- Config Setup -->
<script>
    window.JiraConfig = {
        apiBase: "<?= url('/api/v1') ?>",
        webBase: "<?= url('/') ?>",
        csrfToken: "<?= csrf_token() ?>",
        currentUser: <?= json_encode([
            'id' => \App\Core\Session::get('user_id'),
            'name' => \App\Core\Session::get('user_name'),
            'email' => \App\Core\Session::get('user_email')
        ]) ?>
    };
</script>

<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="<?= asset('js/calendar-realtime.js') ?>"></script>
<?php \App\Core\View::endSection(); ?>