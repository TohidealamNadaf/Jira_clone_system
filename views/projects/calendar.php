<?php
/**
 * Project Calendar View - Enterprise Jira-style Calendar
 * Displays all issues with dates for a specific project
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
                    <li class="breadcrumb-item active">Calendar</li>
                </ol>
            </nav>
            <h1 class="h3 mt-2 mb-0">Project Calendar</h1>
            <p class="text-muted mb-0">Track issue due dates and timelines for <?= htmlspecialchars($projectKey ?? '') ?></p>
        </div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary" id="prevMonth">
                <i class="bi bi-chevron-left"></i> Previous
            </button>
            <button type="button" class="btn btn-outline-primary" id="todayBtn">Today</button>
            <button type="button" class="btn btn-outline-primary" id="nextMonth">
                Next <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select id="statusFilter" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="closed">Closed</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Priority</label>
            <select id="priorityFilter" class="form-select form-select-sm">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-sm btn-secondary" id="clearFilters">Clear Filters</button>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="card">
        <div class="card-body p-0">
            <div id="calendar" style="min-height: 600px;"></div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Issue Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventModalBody">
                <!-- Event details will be loaded here -->
            </div>
            <div class="modal-footer">
                <a href="#" id="eventModalLink" class="btn btn-primary" target="_blank">View Full Issue</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .fc {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
    }
    
    .fc .fc-button-primary {
        background-color: #8B1956;
        border-color: #8B1956;
    }
    
    .fc .fc-button-primary:hover,
    .fc .fc-button-primary:focus {
        background-color: #6F123F;
        border-color: #6F123F;
    }
    
    .fc .fc-button-primary.fc-button-active {
        background-color: #6F123F;
        border-color: #6F123F;
    }
    
    .fc .fc-daygrid-day:hover {
        background-color: #f8f9fa;
    }
    
    .fc .fc-event {
        cursor: pointer;
        border-radius: 4px;
    }
    
    .fc .fc-event:hover {
        opacity: 0.9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .fc-event-title {
        font-weight: 500;
        font-size: 12px;
    }
    
    .fc-col-header-cell {
        background-color: #f8f9fa;
        color: #161B22;
        border-color: #e5e5e5;
        font-weight: 600;
    }
    
    .fc-daygrid-day-frame {
        min-height: 100px;
    }
    
    .today-indicator {
        background-color: #fff3cd !important;
    }
    
    .event-modal-status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: 500;
        margin-top: 8px;
    }
    
    .event-modal-meta {
        font-size: 13px;
        color: #626F86;
        margin: 8px 0;
    }
    
    .event-modal-dates {
        background-color: #f5f5f5;
        padding: 12px;
        border-radius: 4px;
        margin: 12px 0;
        font-size: 13px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectKey = <?= json_encode($projectKey ?? '') ?>;
    const calendarEl = document.getElementById('calendar');
    
    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: '',
            center: 'title',
            right: ''
        },
        plugins: ['dayGrid', 'interaction'],
        height: 'auto',
        contentHeight: 'auto',
        events: function(info, successCallback, failureCallback) {
            loadCalendarEvents(info.startStr, info.endStr, successCallback, failureCallback);
        },
        eventClick: function(info) {
            showEventModal(info.event.extendedProps);
        },
        dateClick: function(info) {
            // Optional: handle date click
        },
        datesSet: function(dateInfo) {
            // Called when dates change
        },
        dayCellClassNames: function(arg) {
            const today = new Date();
            if (arg.date.toDateString() === today.toDateString()) {
                return ['today-indicator'];
            }
            return [];
        }
    });
    
    calendar.render();
    
    // Navigation buttons
    document.getElementById('prevMonth').addEventListener('click', function() {
        calendar.prev();
    });
    
    document.getElementById('nextMonth').addEventListener('click', function() {
        calendar.next();
    });
    
    document.getElementById('todayBtn').addEventListener('click', function() {
        calendar.today();
    });
    
    // Filter handling
    document.getElementById('statusFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    document.getElementById('priorityFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    document.getElementById('clearFilters').addEventListener('click', function() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('priorityFilter').value = '';
        calendar.refetchEvents();
    });
    
    // Load calendar events
    function loadCalendarEvents(startDate, endDate, successCallback, failureCallback) {
        const statusFilter = document.getElementById('statusFilter').value;
        const priorityFilter = document.getElementById('priorityFilter').value;
        
        let url = `/api/v1/calendar/events?project=${projectKey}&start=${startDate}&end=${endDate}`;
        
        if (statusFilter) {
            url += `&status=${statusFilter}`;
        }
        if (priorityFilter) {
            url += `&priority=${priorityFilter}`;
        }
        
        fetch(url, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                successCallback(data.data);
            } else {
                failureCallback(new Error('Invalid response format'));
            }
        })
        .catch(error => {
            console.error('Error loading calendar events:', error);
            failureCallback(error);
        });
    }
    
    // Show event modal
    function showEventModal(eventData) {
        const modal = document.getElementById('eventModal');
        const title = document.getElementById('eventModalTitle');
        const body = document.getElementById('eventModalBody');
        const link = document.getElementById('eventModalLink');
        
        // Format dates
        const startDate = eventData.startDate ? new Date(eventData.startDate).toLocaleDateString() : 'Not set';
        const endDate = eventData.endDate ? new Date(eventData.endDate).toLocaleDateString() : 'Not set';
        const dueDate = eventData.dueDate ? new Date(eventData.dueDate).toLocaleDateString() : 'Not set';
        
        // Build modal content
        let html = `
            <div class="mb-3">
                <h6 class="text-muted mb-2">Issue</h6>
                <p class="mb-0"><strong>${htmlEscape(eventData.key)}</strong></p>
                <p class="text-muted small">${htmlEscape(eventData.summary)}</p>
            </div>
            
            <div class="mb-3">
                <h6 class="text-muted mb-2">Project</h6>
                <p class="mb-0">${htmlEscape(eventData.project)}</p>
            </div>
            
            <div class="event-modal-dates">
                <div class="mb-2"><strong>Start Date:</strong> ${startDate}</div>
                <div class="mb-2"><strong>End Date:</strong> ${endDate}</div>
                <div><strong>Due Date:</strong> ${dueDate}</div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Status</h6>
                    <div class="event-modal-status" style="background-color: ${eventData.statusColor || '#ddd'}; color: white;">
                        ${htmlEscape(eventData.status)}
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Priority</h6>
                    <p class="mb-0">${htmlEscape(eventData.priority)}</p>
                </div>
            </div>
        `;
        
        title.textContent = `${eventData.key} - ${eventData.summary}`;
        body.innerHTML = html;
        link.href = `<?= url('/issues/') ?>${eventData.key}`;
        
        new bootstrap.Modal(modal).show();
    }
    
    // Helper function to escape HTML
    function htmlEscape(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
