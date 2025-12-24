/**
 * Jira Clone Calendar System - Next Gen
 */

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    // Elements
    const projectFilter = document.getElementById('projectFilter');
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const viewSelect = document.getElementById('viewSelect');

    // Custom Navigation
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    const todayBtn = document.getElementById('todayBtn');

    if (!calendarEl || !window.JiraConfig) {
        console.error('Calendar element or JiraConfig missing');
        return;
    }

    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: false, // Custom toolbar
        height: 'auto',
        editable: true,
        droppable: true,
        selectable: true,
        dayMaxEvents: true,

        // Data Source
        events: function (info, successCallback, failureCallback) {
            fetchEvents(info)
                .then(events => successCallback(events))
                .catch(error => failureCallback(error));
        },

        // Event Content Render
        eventContent: function (arg) {
            return {
                html: `
                    <div class="fc-event-content">
                        <strong>${arg.event.extendedProps.key}</strong> 
                        ${arg.event.title.split(': ')[1] || arg.event.title}
                    </div>
                `
            };
        },

        // Click Event (Show Details)
        eventClick: function (info) {
            showEventDetails(info.event);
        },

        // Drag & Drop (Update Date)
        eventDrop: function (info) {
            updateEventDate(info.event, info.revert);
        },

        eventResize: function (info) {
            updateEventDate(info.event, info.revert);
        }
    });

    calendar.render();
    loadProjects();

    // ----------------------------------------------------
    // Functions
    // ----------------------------------------------------

    function fetchEvents(info) {
        const params = new URLSearchParams({
            start: info.start.toISOString(),
            end: info.end.toISOString(),
            project: projectFilter.value
        });

        const url = `${window.JiraConfig.apiBase}/calendar/events?${params.toString()}`;

        return fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data.success) throw new Error(data.error);

                // Client-side filtering for Status & Priority
                // (Optional: move to backend if large dataset)
                return data.data.filter(event => {
                    let match = true;
                    if (statusFilter.value) {
                        const s = event.extendedProps.status.toLowerCase().replace(' ', '_');
                        if (s !== statusFilter.value) match = false;
                    }
                    if (priorityFilter.value) {
                        const p = event.extendedProps.priority.toLowerCase();
                        if (p !== priorityFilter.value) match = false;
                    }
                    return match;
                });
            });
    }

    function updateEventDate(event, revertFunc) {
        const issueKey = event.extendedProps.key;

        // Calculate dates (adjusting for timezone offset is tricky in JS Dates)
        // FullCalendar returns local dates by default but check config.
        // Simplest: use YYYY-MM-DD from the event object directly if possible, 
        // or format with simple local string logic.

        const formatDate = (date) => {
            if (!date) return null;
            const offset = date.getTimezoneOffset() * 60000;
            const localDate = new Date(date.getTime() - offset);
            return localDate.toISOString().split('T')[0];
        };

        const payload = {
            start_date: formatDate(event.start),
            // If end is null (happens on single day drop), use start
            end_date: formatDate(event.end) || formatDate(event.start)
        };

        // Sync Due Date logic (optional, but good for Jira likeness)
        payload.due_date = payload.end_date;

        fetch(`${window.JiraConfig.apiBase}/issues/${issueKey}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert('Update failed: ' + data.error);
                    revertFunc();
                } else {
                    // Optional: success toast
                }
            })
            .catch(err => {
                console.error(err);
                revertFunc();
            });
    }

    function showEventDetails(event) {
        const props = event.extendedProps;
        const modalEl = document.getElementById('eventModal');
        const modalTitle = document.getElementById('eventModalTitle');
        const modalBody = document.getElementById('eventDetails');
        const viewBtn = document.getElementById('viewIssueBtn');

        modalTitle.innerText = `${props.key}: ${event.title.split(': ')[1] || event.title}`;

        modalBody.innerHTML = `
            <div class="row mb-2">
                <div class="col-sm-4 text-muted">Project</div>
                <div class="col-sm-8">${props.project}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted">Type</div>
                <div class="col-sm-8">${props.issueType}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted">Status</div>
                <div class="col-sm-8"><span class="badge" style="background:${props.statusColor}">${props.status}</span></div>
            </div>
             <div class="row mb-2">
                <div class="col-sm-4 text-muted">Priority</div>
                <div class="col-sm-8">${props.priority}</div>
            </div>
            <hr>
            <div>
                <strong>Description</strong>
                <p class="mt-1">${props.description || 'No description'}</p>
            </div>
        `;

        viewBtn.href = `${window.JiraConfig.webBase}/projects/${props.projectKey}/issues/${props.key}`;

        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    function loadProjects() {
        fetch(`${window.JiraConfig.apiBase}/calendar/projects`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    res.data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.key;
                        opt.textContent = p.name;
                        projectFilter.appendChild(opt);
                    });
                }
            });
    }

    // ----------------------------------------------------
    // Event Listeners
    // ----------------------------------------------------

    // Refresh calendar when filters change
    [projectFilter, statusFilter, priorityFilter].forEach(filter => {
        filter.addEventListener('change', () => calendar.refetchEvents());
    });

    // View Switching
    viewSelect.addEventListener('change', (e) => {
        const viewMap = {
            'month': 'dayGridMonth',
            'week': 'dayGridWeek',
            'day': 'timeGridDay'
        };
        calendar.changeView(viewMap[e.target.value]);
    });

    // Navigation
    prevBtn.addEventListener('click', () => calendar.prev());
    nextBtn.addEventListener('click', () => calendar.next());
    todayBtn.addEventListener('click', () => calendar.today());
});
