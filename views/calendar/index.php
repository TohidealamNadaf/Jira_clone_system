<?php
/**
 * Calendar View - Enterprise Jira-style Calendar
 * Displays all issues with dates across organization
 */

declare(strict_types=1);

\App\Core\View::extends('layouts.app');
\App\Core\View::section('content');
?>

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
            <button type="button" class="action-button" id="prevMonth" title="Previous month">
                <i class="bi bi-chevron-left"></i> Previous
            </button>
            <button type="button" class="action-button action-button-primary" id="todayBtn" title="Go to today">
                <i class="bi bi-calendar-week"></i> Today
            </button>
            <button type="button" class="action-button" id="nextMonth" title="Next month">
                Next <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Quick Filters Bar -->
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
        <!-- Calendar Card -->
        <div class="calendar-card">
            <div class="card-body">
                <div id="calendar" class="calendar-container"></div>
            </div>
        </div>

        <!-- Legend Card -->
        <div class="legend-card">
            <div class="card-header">
                <h3 class="card-title">Legend & Information</h3>
            </div>
            <div class="card-body">
                <div class="legend-grid">
                    <div class="legend-section">
                        <h4 class="legend-section-title">Priority Colors</h4>
                        <div class="legend-items">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #CD5C5C;"></div>
                                <span class="legend-label">Urgent</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #FF6B6B;"></div>
                                <span class="legend-label">High</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #FFD93D;"></div>
                                <span class="legend-label">Medium</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: #6BCB77;"></div>
                                <span class="legend-label">Low</span>
                            </div>
                        </div>
                    </div>

                    <div class="legend-section">
                        <h4 class="legend-section-title">Date Types</h4>
                        <ul class="legend-list">
                            <li><strong>Due Date</strong> — Primary deadline for issue completion</li>
                            <li><strong>Start Date</strong> — When work is planned to begin</li>
                            <li><strong>End Date</strong> — Expected completion date</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="eventDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="viewIssueBtn" class="btn btn-primary">
                    <i class="bi bi-arrow-up-right"></i> View Issue
                </a>
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
    .calendar-page-wrapper {
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
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 32px;
    }

    .header-left {
        flex: 1;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0 0 8px 0;
        letter-spacing: -0.2px;
    }

    .page-subtitle {
        font-size: 15px;
        color: var(--jira-gray);
        margin: 0;
        line-height: 1.5;
        max-width: 600px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .action-button {
        background-color: #FFFFFF;
        border: 1px solid var(--jira-border);
        color: var(--jira-dark);
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all var(--transition-base);
        white-space: nowrap;
    }

    .action-button:hover {
        background-color: var(--jira-light);
        border-color: var(--jira-gray);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        text-decoration: none;
        color: var(--jira-dark);
    }

    .action-button i {
        font-size: 14px;
    }

    .action-button-primary {
        background-color: var(--jira-blue);
        border-color: var(--jira-blue);
        color: #FFFFFF;
    }

    .action-button-primary:hover {
        background-color: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
        color: #FFFFFF;
    }

    /* Filters Section */
    .filters-section {
        background-color: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        padding: 20px 32px;
    }

    .filters-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        padding: 8px 12px;
        border: 1px solid var(--jira-border);
        border-radius: 6px;
        font-size: 13px;
        color: var(--jira-dark);
        background-color: #FFFFFF;
        cursor: pointer;
        transition: all var(--transition-base);
        font-family: inherit;
    }

    .filter-select:hover {
        border-color: var(--jira-gray);
        background-color: var(--jira-light);
    }

    .filter-select:focus {
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

    /* Calendar Card */
    .calendar-card {
        background-color: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        overflow: hidden;
    }

    .calendar-card .card-body {
        padding: 0;
    }

    .calendar-container {
        min-height: 600px;
        width: 100%;
    }

    /* Legend Card */
    .legend-card {
        background-color: #FFFFFF;
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
        overflow: hidden;
    }

    .card-header {
        border-bottom: 1px solid var(--jira-border);
        padding: 20px 24px;
        background-color: #FAFBFC;
    }

    .card-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
        letter-spacing: -0.2px;
    }

    .card-body {
        padding: 24px;
    }

    .legend-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 32px;
    }

    .legend-section {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .legend-section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .legend-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        flex-shrink: 0;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .legend-label {
        font-size: 13px;
        color: var(--jira-dark);
        font-weight: 500;
    }

    .legend-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .legend-list li {
        font-size: 13px;
        color: var(--jira-dark);
        line-height: 1.5;
    }

    .legend-list strong {
        color: var(--jira-dark);
        font-weight: 600;
    }

    /* ============================================
       FULLCALENDAR STYLING
       ============================================ */
    #calendar {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        letter-spacing: -0.2px;
    }

    .fc {
        font-size: 0.95rem;
    }

    .fc .fc-button-primary {
        background-color: var(--jira-blue);
        border-color: var(--jira-blue);
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):hover {
        background-color: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
    }

    .fc .fc-button-primary:disabled {
        background-color: #ccc;
    }

    .fc .fc-col-header-cell {
        padding: 12px 4px;
        background-color: #f8f9fa;
        font-weight: 600;
        color: var(--jira-dark);
        border-color: var(--jira-border);
        font-size: 13px;
    }

    .fc .fc-daygrid-day {
        border-color: var(--jira-border);
        background-color: #FFFFFF;
    }

    .fc .fc-daygrid-day:hover {
        background-color: var(--jira-light);
    }

    .fc .fc-daygrid-day-number {
        padding: 8px 4px;
        color: var(--jira-dark);
        font-weight: 600;
        font-size: 14px;
    }

    .fc .fc-daygrid-day.fc-day-other .fc-daygrid-day-number {
        color: var(--jira-gray);
    }

    .fc .fc-event {
        border: none;
        padding: 2px 4px;
        cursor: pointer;
        transition: all var(--transition-base);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }

    .fc .fc-event-title {
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #FFFFFF;
    }

    .fc .fc-event:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .fc .fc-daygrid-day-frame {
        position: relative;
    }

    .fc .fc-daygrid-block-event:hover {
        opacity: 0.95;
    }

    /* ============================================
       MODAL STYLING
       ============================================ */
    .modal-content {
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        box-shadow: 0 20px 25px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        background-color: #FFFFFF;
        border-bottom: 1px solid var(--jira-border);
        padding: 20px 24px;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--jira-dark);
        letter-spacing: -0.2px;
    }

    .modal-body {
        padding: 24px;
        color: var(--jira-dark);
    }

    .modal-footer {
        background-color: #FAFBFC;
        border-top: 1px solid var(--jira-border);
        padding: 16px 24px;
    }

    .btn-primary {
        background-color: var(--jira-blue);
        border-color: var(--jira-blue);
        color: #FFFFFF;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all var(--transition-base);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary:hover {
        background-color: var(--jira-blue-dark);
        border-color: var(--jira-blue-dark);
        color: #FFFFFF;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
    }

    .btn-secondary {
        background-color: #FFFFFF;
        border-color: var(--jira-border);
        color: var(--jira-dark);
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all var(--transition-base);
    }

    .btn-secondary:hover {
        background-color: var(--jira-light);
        border-color: var(--jira-gray);
        color: var(--jira-dark);
    }

    .btn-close {
        width: 24px;
        height: 24px;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23626F86'%3e%3cpath d='M.293.293a1 1 0 111.414 1.414L1.414 2.12l.293-.293a1 1 0 111.414 1.414l-.293-.293.293.293a1 1 0 11-1.414 1.414l-.293-.293-.293.293a1 1 0 11-1.414-1.414l.293.293L.293 2.707A1 1 0 01.293.293z'/%3e%3c/svg%3e");
        opacity: 0.5;
        transition: opacity var(--transition-base);
    }

    .btn-close:hover {
        opacity: 1;
    }

    /* ============================================
       RESPONSIVE DESIGN
       ============================================ */

    @media (max-width: 1024px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .filters-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .legend-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .breadcrumb-section {
            padding: 12px 16px;
        }

        .page-header {
            padding: 20px 16px;
        }

        .page-content {
            padding: 20px 16px;
            gap: 20px;
        }

        .filters-section {
            padding: 16px;
        }

        .filters-container {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .filter-group {
            gap: 6px;
        }

        .filter-select {
            padding: 10px;
            font-size: 14px;
        }

        .page-title {
            font-size: 24px;
        }

        .page-subtitle {
            font-size: 13px;
        }

        .header-actions {
            flex-direction: column;
            width: 100%;
        }

        .action-button {
            width: 100%;
            justify-content: center;
        }

        .calendar-container {
            min-height: 400px;
        }

        .fc .fc-col-header-cell {
            padding: 8px 2px;
            font-size: 0.85rem;
        }

        .fc .fc-daygrid-day-number {
            padding: 6px 2px;
            font-size: 12px;
        }

        .fc .fc-event-title {
            font-size: 0.75rem;
        }

        .legend-items {
            gap: 8px;
        }

        .legend-item {
            gap: 8px;
        }

        .legend-color {
            width: 16px;
            height: 16px;
        }

        .card-body {
            padding: 16px;
        }

        .card-header {
            padding: 16px;
        }
    }

    @media (max-width: 480px) {
        .breadcrumb-section {
            padding: 10px 12px;
        }

        .page-header {
            padding: 16px 12px;
        }

        .page-content {
            padding: 16px 12px;
        }

        .filters-section {
            padding: 12px;
        }

        .page-title {
            font-size: 20px;
        }

        .page-subtitle {
            font-size: 12px;
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

        .calendar-container {
            min-height: 300px;
        }

        .fc .fc-button {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }

        .legend-grid {
            gap: 20px;
        }

        .legend-item {
            gap: 8px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🗓️ Calendar page loaded, initializing FullCalendar...');
    
    let calendar;
    const calendarEl = document.getElementById('calendar');
    const currentDate = new Date();
    
    if (!calendarEl) {
        console.error('❌ Calendar element not found!');
        return;
    }
    
    console.log('✓ Calendar element found, creating FullCalendar instance');
    
    // Initialize FullCalendar
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: currentDate,
        headerToolbar: {
            left: '',
            center: 'title',
            right: ''
        },
        events: function(info, successCallback, failureCallback) {
            console.log('📡 FullCalendar requesting events for:', info.start, '-', info.end);
            fetchEvents(info.start, info.end, successCallback, failureCallback);
        },
        eventClick: function(info) {
            showEventModal(info.event);
        },
        height: '100%',
        contentHeight: 'auto',
        selectable: true,
        selectConstraint: 'businessHours',
        editable: false,
        eventDisplay: 'auto',
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        datesSet: function(info) {
            console.log('📅 Calendar view changed, displaying:', info.start, '-', info.end);
        },
    });
    
    console.log('✓ FullCalendar instance created, rendering...');
    calendar.render();
    console.log('✓ FullCalendar rendered successfully');
    
    // Fetch events from API
    function fetchEvents(startDate, endDate, successCallback, failureCallback) {
        const projectFilter = document.getElementById('projectFilter').value;
        
        const params = new URLSearchParams({
            start: startDate.toISOString().split('T')[0],
            end: endDate.toISOString().split('T')[0],
        });
        
        if (projectFilter) {
            params.append('project', projectFilter);
        }
        
        const apiUrl = `<?= url('/api/v1/calendar/events') ?>?` + params.toString();
        console.log('🔗 Fetching events from:', apiUrl);
        
        fetch(apiUrl, {
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('📦 API response status:', response.status);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('✓ Events data received:', data);
            if (data.success) {
                console.log(`📌 Loaded ${data.data.length} events`);
                successCallback(data.data);
            } else {
                console.error('❌ API returned error:', data.error);
                failureCallback(new Error(data.error || 'Failed to fetch events'));
            }
        })
        .catch(error => {
            console.error('❌ Calendar API error:', error);
            failureCallback(error);
        });
    }
    
    // Show event detail modal
    function showEventModal(event) {
        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
        const props = event.extendedProps;
        
        document.getElementById('eventModalTitle').textContent = props.key;
        
        let detailsHtml = `
            <div class="mb-3">
                <span class="badge bg-light text-dark me-2">${props.issueType}</span>
                <span class="badge" style="background-color: ${event.backgroundColor}; color: white;">
                    ${props.priority}
                </span>
            </div>
            <h6 class="mb-2">${props.summary}</h6>
            <dl class="row small">
                <dt class="col-sm-4"><strong>Project:</strong></dt>
                <dd class="col-sm-8">${props.project}</dd>
                <dt class="col-sm-4"><strong>Status:</strong></dt>
                <dd class="col-sm-8">${props.status}</dd>
                <dt class="col-sm-4"><strong>Priority:</strong></dt>
                <dd class="col-sm-8">${props.priority}</dd>
                <dt class="col-sm-4"><strong>Start Date:</strong></dt>
                <dd class="col-sm-8">${props.startDate || 'N/A'}</dd>
                <dt class="col-sm-4"><strong>Due Date:</strong></dt>
                <dd class="col-sm-8">${props.dueDate || 'N/A'}</dd>
                <dt class="col-sm-4"><strong>End Date:</strong></dt>
                <dd class="col-sm-8">${props.endDate || 'N/A'}</dd>
            </dl>
        `;
        
        document.getElementById('eventDetails').innerHTML = detailsHtml;
        document.getElementById('viewIssueBtn').href = `<?= url('/issues/') ?>${props.key}`;
        
        modal.show();
    }
    
    // Month navigation
    document.getElementById('prevMonth').addEventListener('click', () => {
        calendar.prev();
    });
    
    document.getElementById('nextMonth').addEventListener('click', () => {
        calendar.next();
    });
    
    document.getElementById('todayBtn').addEventListener('click', () => {
        calendar.today();
    });
    
    // View selection
    document.getElementById('viewSelect').addEventListener('change', (e) => {
        const view = e.target.value;
        switch(view) {
            case 'week':
                calendar.changeView('dayGridWeek');
                break;
            case 'day':
                calendar.changeView('dayGridDay');
                break;
            default:
                calendar.changeView('dayGridMonth');
        }
    });
    
    // Filters
    document.getElementById('projectFilter').addEventListener('change', () => {
        calendar.refetchEvents();
    });
    
    // Load projects for filter
    loadProjectsForFilter();
    
    function loadProjectsForFilter() {
        console.log('📂 Loading projects for filter...');
        
        fetch(`<?= url('/api/v1/calendar/projects') ?>`, {
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Projects API status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Projects data:', data);
            if (data.success && data.data) {
                const select = document.getElementById('projectFilter');
                console.log(`✓ Adding ${data.data.length} projects to filter`);
                data.data.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.key;
                    option.textContent = `${project.key} - ${project.name}`;
                    select.appendChild(option);
                });
            } else {
                console.warn('⚠️ No projects returned from API');
            }
        })
        .catch(error => console.error('❌ Error loading projects:', error));
    }
});
</script>

<?php \App\Core\View::endSection(); ?>
