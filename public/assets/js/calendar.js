/**
 * Jira Clone Calendar System - Complete Implementation
 */

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('mainCalendar');
    
    if (!calendarEl || !window.JiraConfig) {
        console.error('Calendar element or JiraConfig missing');
        return;
    }

    // Elements
    const projectFilter = document.getElementById('projectFilter');
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const quickSearch = document.getElementById('quickSearch');
    const currentDateEl = document.getElementById('currentDate');

    // Navigation
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const todayBtn = document.getElementById('todayBtn');

    // Filter tabs
    const filterTabs = document.querySelectorAll('.filter-tab');
    const moreFiltersBtn = document.getElementById('moreFilters');
    const advancedFilters = document.getElementById('advancedFilters');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const applyFiltersBtn = document.getElementById('applyFilters');

    // Summary elements
    const totalIssuesEl = document.getElementById('totalIssues');
    const overdueIssuesEl = document.getElementById('overdueIssues');
    const dueTodayIssuesEl = document.getElementById('dueTodayIssues');
    const dueWeekIssuesEl = document.getElementById('dueWeekIssues');
    const myIssuesEl = document.getElementById('myIssues');

    // Sidebar elements
    const upcomingCountEl = document.getElementById('upcomingCount');
    const upcomingListEl = document.getElementById('upcomingList');
    const scheduleListEl = document.getElementById('scheduleList');
    const teamListEl = document.getElementById('teamList');

    // Modal elements
    const eventModal = document.getElementById('eventModal');
    const createEventModal = document.getElementById('createEventModal');
    const exportModal = document.getElementById('exportModal');

    // State
    let currentFilter = 'all';
    let calendar;
    
    // Global data cache
    window.calendarProjects = [];
    window.calendarStatuses = [];
    window.calendarPriorities = [];
    window.calendarIssueTypes = [];
    window.calendarUsers = [];

    // Initialize FullCalendar
    function initCalendar() {
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false, // Custom toolbar
            height: 'auto',
            editable: true,
            droppable: true,
            selectable: true,
            dayMaxEvents: true,
            eventMaxStack: 3,
            themeSystem: 'standard',

            // Data Source
            events: function (info, successCallback, failureCallback) {
                fetchEvents(info)
                    .then(events => {
                        updateSummaryStats(events);
                        successCallback(events);
                    })
                    .catch(error => failureCallback(error));
            },

            // Event Content Render
            eventContent: function (arg) {
                const priority = arg.event.extendedProps.priority?.toLowerCase() || 'medium';
                const key = arg.event.extendedProps.key || '';
                const title = arg.event.title;
                const displayTitle = title.includes(': ') ? title.split(': ')[1] : title;
                
                return {
                    html: `
                        <div class="fc-event-content" data-priority="${priority}">
                            <strong>${key}</strong> 
                            ${displayTitle}
                        </div>
                    `
                };
            },

            // Click Event (Show Details)
            eventClick: function (info) {
                info.jsEvent.preventDefault();
                info.jsEvent.stopPropagation();
                
                // Small delay to ensure event queue is processed
                setTimeout(() => {
                    showEventDetails(info.event);
                }, 10);
            },

            // Drag & Drop (Update Date)
            eventDrop: function (info) {
                updateEventDate(info.event, info.revert);
            },

            eventResize: function (info) {
                updateEventDate(info.event, info.revert);
            },

            // Date Select (Create Event)
            dateClick: function (info) {
                openCreateEventModal(info.dateStr);
            },

            // View Change
            datesSet: function (info) {
                updateCurrentDateDisplay();
                loadSidebarData();
            }
        });

        calendar.render();
        updateCurrentDateDisplay();
    }

    // ----------------------------------------------------
    // Event Fetching
    // ----------------------------------------------------

    function fetchEvents(info) {
        const params = new URLSearchParams({
            start: info.start.toISOString(),
            end: info.end.toISOString()
        });

        // Add project filter
        if (projectFilter && projectFilter.value) {
            params.append('project', projectFilter.value);
        }

        const url = `${window.JiraConfig.apiBase}/calendar/events?${params.toString()}`;

        return fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                return res.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.error || 'Failed to load events');
                }

                let events = data.data || [];

                // Apply client-side filters
                events = applyFilters(events);

                return events;
            });
    }

    function applyFilters(events) {
        // Status filter
        if (statusFilter && statusFilter.value) {
            events = events.filter(event => {
                const status = event.extendedProps.status?.toLowerCase().replace(' ', '_');
                return status === statusFilter.value;
            });
        }

        // Priority filter
        if (priorityFilter && priorityFilter.value) {
            events = events.filter(event => {
                const priority = event.extendedProps.priority?.toLowerCase();
                return priority === priorityFilter.value;
            });
        }

        // Quick search
        if (quickSearch && quickSearch.value.trim()) {
            const search = quickSearch.value.toLowerCase();
            events = events.filter(event => {
                const title = event.title.toLowerCase();
                const key = (event.extendedProps.key || '').toLowerCase();
                const project = (event.extendedProps.project || '').toLowerCase();
                return title.includes(search) || key.includes(search) || project.includes(search);
            });
        }

        // Tab filters
        const currentUser = window.JiraConfig.currentUser?.id;
        if (currentUser) {
            switch (currentFilter) {
                case 'assigned':
                    events = events.filter(event => 
                        event.extendedProps.assigneeId === currentUser
                    );
                    break;
                case 'overdue':
                    const today = new Date().toISOString().split('T')[0];
                    events = events.filter(event => 
                        event.start && event.start < today
                    );
                    break;
                case 'due-today':
                    const todayStr = new Date().toISOString().split('T')[0];
                    events = events.filter(event => 
                        event.start && event.start === todayStr
                    );
                    break;
                case 'due-week':
                    const weekFromNow = new Date();
                    weekFromNow.setDate(weekFromNow.getDate() + 7);
                    const weekStr = weekFromNow.toISOString().split('T')[0];
                    const todayStr2 = new Date().toISOString().split('T')[0];
                    events = events.filter(event => 
                        event.start && event.start >= todayStr2 && event.start <= weekStr
                    );
                    break;
            }
        }

        return events;
    }

    // ----------------------------------------------------
    // Event Operations
    // ----------------------------------------------------

    function updateEventDate(event, revertFunc) {
        const issueKey = event.extendedProps.key;

        const formatDate = (date) => {
            if (!date) return null;
            const offset = date.getTimezoneOffset() * 60000;
            const localDate = new Date(date.getTime() - offset);
            return localDate.toISOString().split('T')[0];
        };

        const payload = {
            start_date: formatDate(event.start),
            end_date: formatDate(event.end) || formatDate(event.start),
            due_date: formatDate(event.end) || formatDate(event.start)
        };

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
                    calendar.refetchEvents();
                }
            })
            .catch(err => {
                console.error('Event update error:', err);
                revertFunc();
            });
    }

    // ----------------------------------------------------
    // UI Functions
    // ----------------------------------------------------

    function showEventDetails(event) {
        const props = event.extendedProps;

        // Update modal content
        document.getElementById('eventModalTitle').textContent = `${props.key}: ${event.title.split(': ')[1] || event.title}`;
        document.getElementById('eventKey').textContent = props.key;
        document.getElementById('eventSummary').textContent = event.title.split(': ')[1] || event.title;
        document.getElementById('eventProject').textContent = props.project || '';
        document.getElementById('eventStatus').textContent = props.status || '';
        document.getElementById('eventPriority').textContent = props.priority || '';
        document.getElementById('eventDueDate').textContent = event.start ? formatDateDisplay(event.start) : '';
        document.getElementById('eventCreatedDate').textContent = formatDateDisplay(props.created) || '';
        document.getElementById('eventUpdatedDate').textContent = formatDateDisplay(props.updated) || '';
        document.getElementById('eventDescription').textContent = props.description || 'No description';
        document.getElementById('eventStoryPoints').textContent = props.storyPoints || '';

        // Update assignee info
        const assigneeAvatar = document.getElementById('assigneeAvatar');
        const assigneeName = document.getElementById('assigneeName');
        if (props.assigneeName) {
            assigneeAvatar.src = ''; // Could add avatar URL if available
            assigneeAvatar.alt = props.assigneeName;
            assigneeName.textContent = props.assigneeName;
        } else {
            assigneeAvatar.src = '';
            assigneeAvatar.alt = 'Unassigned';
            assigneeName.textContent = 'Unassigned';
        }

        // Update reporter info
        const reporterAvatar = document.getElementById('reporterAvatar');
        const reporterName = document.getElementById('reporterName');
        if (props.reporterName) {
            reporterAvatar.src = ''; // Could add avatar URL if available
            reporterAvatar.alt = props.reporterName;
            reporterName.textContent = props.reporterName;
        } else {
            reporterAvatar.src = '';
            reporterAvatar.alt = 'Unknown';
            reporterName.textContent = 'Unknown';
        }

        // Update labels (if any, for now empty)
        const labelsContainer = document.getElementById('eventLabels');
        labelsContainer.innerHTML = '<span class="label-tag">' + (props.issueType || 'Issue') + '</span>';

        // Update view button
        const viewBtn = document.getElementById('viewIssueBtn');
        viewBtn.href = `${window.JiraConfig.webBase}/projects/${props.projectKey}/issues/${props.key}`;

        // Show modal with proper handling
        if (eventModal) {
            // Use requestAnimationFrame to ensure proper rendering
            requestAnimationFrame(() => {
                eventModal.style.display = 'flex';
                eventModal.classList.add('open');
                eventModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
                document.body.style.position = 'fixed'; // Fixed position to prevent scroll
                document.body.style.width = '100%'; // Prevent layout shift
                document.body.style.top = `-${window.scrollY}px`; // Preserve scroll position

                // Focus trap for accessibility
                const focusableElements = eventModal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusableElements.length > 0) {
                    focusableElements[0].focus();
                }
            });
        }

        // Prevent event from bubbling to backdrop
        if (event && event.stopPropagation) {
            event.stopPropagation();
        }
    }

    function openCreateEventModal(dateStr) {
        if (createEventModal) {
            // Set default date
            const startDate = document.getElementById('eventStartDate');
            const endDate = document.getElementById('eventEndDate');
            if (startDate && !startDate.value) {
                startDate.value = dateStr + 'T09:00';
            }
            if (endDate && !endDate.value) {
                endDate.value = dateStr + 'T10:00';
            }
            
            createEventModal.style.display = 'flex';
            createEventModal.classList.add('open');
            createEventModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
            document.body.style.top = `-${window.scrollY}px`;
            
            // Focus trap for accessibility
            const focusableElements = createEventModal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusableElements.length > 0) {
                focusableElements[0].focus();
            }
        }
    }

    function updateCurrentDateDisplay() {
        if (currentDateEl && calendar) {
            const currentDate = calendar.getDate();
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];
            currentDateEl.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        }
    }

    function updateSummaryStats(events) {
        if (!events || events.length === 0) {
            if (totalIssuesEl) totalIssuesEl.textContent = '0';
            if (overdueIssuesEl) overdueIssuesEl.textContent = '0';
            if (dueTodayIssuesEl) dueTodayIssuesEl.textContent = '0';
            if (dueWeekIssuesEl) dueWeekIssuesEl.textContent = '0';
            if (myIssuesEl) myIssuesEl.textContent = '0';
            return;
        }

        const today = new Date().toISOString().split('T')[0];
        const weekFromNow = new Date();
        weekFromNow.setDate(weekFromNow.getDate() + 7);
        const weekStr = weekFromNow.toISOString().split('T')[0];
        const currentUser = window.JiraConfig.currentUser?.id;

        let overdue = 0;
        let dueToday = 0;
        let dueWeek = 0;
        let myIssues = 0;

        events.forEach(event => {
            const eventStart = event.start;
            
            if (eventStart < today) overdue++;
            if (eventStart === today) dueToday++;
            if (eventStart >= today && eventStart <= weekStr) dueWeek++;
            if (currentUser && event.extendedProps.assigneeId === currentUser) myIssues++;
        });

        if (totalIssuesEl) totalIssuesEl.textContent = events.length;
        if (overdueIssuesEl) overdueIssuesEl.textContent = overdue;
        if (dueTodayIssuesEl) dueTodayIssuesEl.textContent = dueToday;
        if (dueWeekIssuesEl) dueWeekIssuesEl.textContent = dueWeek;
        if (myIssuesEl) myIssuesEl.textContent = myIssues;
    }

    function loadSidebarData() {
        loadUpcomingIssues();
        loadMySchedule();
        loadTeamSchedule();
    }

    function loadUpcomingIssues() {
        fetch(`${window.JiraConfig.apiBase}/calendar/upcoming`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    renderUpcomingIssues(data.data);
                }
            })
            .catch(err => console.error('Failed to load upcoming issues:', err));
    }

    function renderUpcomingIssues(issues) {
        if (!upcomingListEl) return;
        
        if (upcomingCountEl) {
            upcomingCountEl.textContent = issues.length;
        }

        if (issues.length === 0) {
            upcomingListEl.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-calendar-x"></i>
                    <p>No upcoming issues</p>
                </div>
            `;
            return;
        }

        upcomingListEl.innerHTML = issues.map(issue => `
            <div class="upcoming-item">
                <div class="upcoming-date">${formatDateDisplay(issue.due_date)}</div>
                <div class="upcoming-info">
                    <div class="upcoming-title">${issue.issue_key}: ${issue.summary}</div>
                    <div class="upcoming-project">${issue.project_key}</div>
                </div>
            </div>
        `).join('');
    }

    function loadMySchedule() {
        if (!scheduleListEl) return;
        const currentUserId = window.JiraConfig.currentUser?.id;
        
        if (!currentUserId) {
            scheduleListEl.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-clock"></i>
                    <p>No scheduled items</p>
                </div>
            `;
            return;
        }
        
        // Fetch issues assigned to current user
        fetch(`${window.JiraConfig.apiBase}/calendar/events?start=${new Date().toISOString().split('T')[0]}&end=${new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0]}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                const myIssues = data.data.filter(issue => issue.extendedProps?.assigneeId === currentUserId);
                if (myIssues.length === 0) {
                    scheduleListEl.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-clock"></i>
                            <p>No scheduled items</p>
                        </div>
                    `;
                    return;
                }
                renderMySchedule(myIssues);
            }
        })
        .catch(err => console.error('Failed to load my schedule:', err));
    }
    
    function renderMySchedule(issues) {
        if (!scheduleListEl || !issues.length) return;
        
        scheduleListEl.innerHTML = issues.map(issue => `
            <div class="schedule-item">
                <div class="schedule-date">${formatDateDisplay(issue.start)}</div>
                <div class="schedule-info">
                    <div class="schedule-title">${issue.extendedProps?.key}: ${issue.title.split(': ')[1] || issue.title}</div>
                    <div class="schedule-status">
                        <span class="status-badge" style="background-color: ${issue.extendedProps?.statusColor || '#ccc'}">${issue.extendedProps?.status || ''}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function loadTeamSchedule() {
        if (!teamListEl) return;
        
        teamListEl.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-people"></i>
                <p>No team items</p>
            </div>
        `;
    }

    function loadProjects() {
        if (!projectFilter) return;
        
        fetch(`${window.JiraConfig.apiBase}/calendar/projects`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    data.data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id; // Use ID for API
                        opt.textContent = p.name;
                        projectFilter.appendChild(opt);
                    });
                }
            })
            .catch(err => console.error('Failed to load projects:', err));
    }

    function loadEventTypes() {
        fetch(`${window.JiraConfig.apiBase}/calendar-events/types`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                const eventTypeSelect = document.getElementById('eventType');
                if (eventTypeSelect) {
                    // Clear existing options except first
                    eventTypeSelect.innerHTML = '<option value="">Select event type...</option>';
                    data.data.forEach(type => {
                        const opt = document.createElement('option');
                        opt.value = type.value;
                        opt.textContent = type.label;
                        eventTypeSelect.appendChild(opt);
                    });
                }
            }
        })
        .catch(err => console.error('Failed to load event types:', err));
    }

    function loadPriorities() {
        fetch(`${window.JiraConfig.apiBase}/calendar-events/priorities`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                const prioritySelect = document.getElementById('eventPriority');
                if (prioritySelect) {
                    prioritySelect.innerHTML = '';
                    data.data.forEach(priority => {
                        const opt = document.createElement('option');
                        opt.value = priority.name.toLowerCase();
                        opt.textContent = priority.name;
                        opt.style.color = priority.color;
                        prioritySelect.appendChild(opt);
                    });
                }
            }
        })
        .catch(err => console.error('Failed to load priorities:', err));
    }

    function loadUsers() {
        fetch(`${window.JiraConfig.apiBase}/calendar-events/users`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                const attendeesInput = document.getElementById('eventAttendees');
                if (attendeesInput && data.data.length > 0) {
                    // Store users for autocomplete functionality
                    window.calendarUsers = data.data;
                    console.log('Loaded users for calendar:', data.data.length);
                }
            }
        })
        .catch(err => console.error('Failed to load users:', err));
    }

    function loadStatuses() {
        if (!statusFilter) return;

        fetch(`${window.JiraConfig.apiBase}/calendar/statuses`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                statusFilter.innerHTML = '';
                data.data.forEach(status => {
                    const opt = document.createElement('option');
                    opt.value = status.name.toLowerCase().replace(' ', '_');
                    opt.textContent = status.name;
                    statusFilter.appendChild(opt);
                });
            }
        })
        .catch(err => console.error('Failed to load statuses:', err));
    }

    function loadPriorities() {
        if (!priorityFilter) return;

        fetch(`${window.JiraConfig.apiBase}/calendar/priorities`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                priorityFilter.innerHTML = '';
                data.data.forEach(priority => {
                    const opt = document.createElement('option');
                    opt.value = priority.name.toLowerCase();
                    opt.textContent = priority.name;
                    priorityFilter.appendChild(opt);
                });
            }
        })
        .catch(err => console.error('Failed to load priorities:', err));
    }

    function loadIssueTypes() {
        if (!typeFilter) return;

        fetch(`${window.JiraConfig.apiBase}/calendar/issue-types`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                typeFilter.innerHTML = '';
                data.data.forEach(type => {
                    const opt = document.createElement('option');
                    opt.value = type.name.toLowerCase();
                    opt.textContent = type.name;
                    typeFilter.appendChild(opt);
                });
            }
        })
        .catch(err => console.error('Failed to load issue types:', err));
    }

    function loadAssigneeUsers() {
        if (!assigneeFilter) return;

        fetch(`${window.JiraConfig.apiBase}/calendar/users`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                // Keep the "Anyone" option
                assigneeFilter.innerHTML = '<option value="">Anyone</option>';
                data.data.forEach(user => {
                    const opt = document.createElement('option');
                    opt.value = user.id;
                    opt.textContent = user.name;
                    assigneeFilter.appendChild(opt);
                });
            }
        })
        .catch(err => console.error('Failed to load users:', err));
    }

    function formatDateDisplay(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    // ----------------------------------------------------
    // Modal Functions
    // ----------------------------------------------------

    // Handle backdrop clicks safely
    window.handleBackdropClick = function(event, modalType) {
        // Only close if click is directly on backdrop element, not modal content
        // Check if the clicked element is the backdrop itself
        const isBackdrop = event.target.classList.contains('jira-modal') || 
                           event.target.classList.contains('modal-backdrop');
        
        if (isBackdrop) {
            switch(modalType) {
                case 'create':
                    closeCreateModal();
                    break;
                case 'export':
                    closeExportModal();
                    break;
                default:
                    closeEventModal();
            }
        }
    };

    window.closeEventModal = function() {
        if (eventModal) {
            eventModal.style.display = 'none';
            eventModal.classList.remove('open');
            eventModal.setAttribute('aria-hidden', 'true');
            
            // Restore body scroll and position
            const scrollY = document.body.style.top;
            document.body.style.overflow = 'auto';
            document.body.style.position = 'static';
            document.body.style.width = 'auto';
            document.body.style.top = '';
            
            if (scrollY) {
                window.scrollTo(0, parseInt(scrollY || '0') * -1);
            }
        }
    };

    window.closeCreateModal = function() {
        if (createEventModal) {
            createEventModal.style.display = 'none';
            createEventModal.classList.remove('open');
            createEventModal.setAttribute('aria-hidden', 'true');
            
            // Restore body scroll and position
            const scrollY = document.body.style.top;
            document.body.style.overflow = 'auto';
            document.body.style.position = 'static';
            document.body.style.width = 'auto';
            document.body.style.top = '';
            
            if (scrollY) {
                window.scrollTo(0, parseInt(scrollY || '0') * -1);
            }
        }
    };

    window.closeExportModal = function() {
        if (exportModal) {
            exportModal.style.display = 'none';
            exportModal.classList.remove('open');
            exportModal.setAttribute('aria-hidden', 'true');
            
            // Restore body scroll and position
            const scrollY = document.body.style.top;
            document.body.style.overflow = 'auto';
            document.body.style.position = 'static';
            document.body.style.width = 'auto';
            document.body.style.top = '';
            
            if (scrollY) {
                window.scrollTo(0, parseInt(scrollY || '0') * -1);
            }
        }
    };

    window.saveEvent = function() {
        const form = document.getElementById('createEventForm');
        if (!form) {
            console.error('Create event form not found');
            return;
        }

        // Collect form data
        const formData = new FormData(form);
        const data = {
            event_type: formData.get('eventType'),
            project_id: formData.get('eventProject') || null,
            title: formData.get('eventTitle'),
            description: formData.get('eventDesc') || null,
            start_date: formData.get('eventStartDate'),
            end_date: formData.get('eventEndDate'),
            priority: formData.get('eventPriority'),
            attendees: formData.get('eventAttendees') || null,
            reminders: [],
            recurring_type: formData.get('recurringType') || 'none',
            recurring_interval: formData.get('recurringInterval') || null,
            recurring_ends: formData.get('recurringEnds') || null,
            recurring_end_date: null // Add date picker if needed
        };

        // Collect reminder checkboxes
        const reminder1 = document.getElementById('reminder1');
        const reminder2 = document.getElementById('reminder2');
        const reminder3 = document.getElementById('reminder3');
        
        if (reminder1 && reminder1.checked) {
            data.reminders.push({type: 'before', value: 15, unit: 'minutes'});
        }
        if (reminder2 && reminder2.checked) {
            data.reminders.push({type: 'before', value: 1, unit: 'hour'});
        }
        if (reminder3 && reminder3.checked) {
            data.reminders.push({type: 'before', value: 1, unit: 'day'});
        }

        // Validate required fields
        if (!data.title || !data.start_date || !data.end_date) {
            showStatus('Please fill in all required fields', 'error');
            return;
        }

        // Disable button and show loading
        const saveBtn = form.querySelector('button[onclick="saveEvent()"]');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating...';
        saveBtn.disabled = true;

        fetch(`${window.JiraConfig.apiBase}/calendar-events`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                showStatus('Calendar event created successfully!', 'success');
                closeCreateModal();
                calendar.refetchEvents(); // Refresh calendar
            } else {
                showStatus('Failed to create event: ' + response.error, 'error');
            }
        })
        .catch(error => {
            console.error('Error creating event:', error);
            showStatus('Error creating event. Please try again.', 'error');
        })
        .finally(() => {
            // Restore button
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    };

    window.previewEvent = function() {
        console.log('Preview event functionality to be implemented');
    };

    window.performExport = function() {
        console.log('Export functionality to be implemented');
    };

    window.watchIssue = function() {
        console.log('Watch issue functionality to be implemented');
    };

    window.shareIssue = function() {
        console.log('Share issue functionality to be implemented');
    };

    window.editIssue = function() {
        console.log('Edit issue functionality to be implemented');
    };

    // ----------------------------------------------------
    // Event Listeners
    // ----------------------------------------------------

    // Filter tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            filterTabs.forEach(t => t.classList.remove('active'));
            e.target.classList.add('active');
            currentFilter = e.target.dataset.filter;
            calendar.refetchEvents();
        });
    });

    // More filters toggle
    if (moreFiltersBtn && advancedFilters) {
        moreFiltersBtn.addEventListener('click', () => {
            advancedFilters.style.display = advancedFilters.style.display === 'none' ? 'block' : 'none';
        });
    }

    // Filter controls
    [projectFilter, statusFilter, priorityFilter, quickSearch].forEach(element => {
        if (element) {
            element.addEventListener('change', () => calendar.refetchEvents());
            if (element.type === 'text') {
                element.addEventListener('input', debounce(() => calendar.refetchEvents(), 300));
            }
        }
    });

    // Reset filters
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', () => {
            if (projectFilter) projectFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (priorityFilter) priorityFilter.value = '';
            if (quickSearch) quickSearch.value = '';
            
            // Reset to first tab
            filterTabs.forEach(t => t.classList.remove('active'));
            if (filterTabs[0]) filterTabs[0].classList.add('active');
            currentFilter = 'all';
            
            calendar.refetchEvents();
        });
    }

    // Apply filters
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', () => {
            calendar.refetchEvents();
            if (advancedFilters) {
                advancedFilters.style.display = 'none';
            }
        });
    }

    // Navigation
    if (prevBtn) {
        prevBtn.addEventListener('click', () => calendar.prev());
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', () => calendar.next());
    }
    if (todayBtn) {
        todayBtn.addEventListener('click', () => {
            calendar.today();
            updateCurrentDateDisplay();
        });
    }

    // Create event button
    const createEventBtn = document.getElementById('createEventBtn');
    if (createEventBtn) {
        createEventBtn.addEventListener('click', () => {
            openCreateEventModal(new Date().toISOString().split('T')[0]);
        });
    }

    // Attendee autocomplete functionality
    const attendeesInput = document.getElementById('eventAttendees');
    if (attendeesInput) {
        attendeesInput.addEventListener('input', debounce(function(e) {
            const value = e.target.value.trim();
            if (value.length < 2) return;
            
            if (window.calendarUsers) {
                const filteredUsers = window.calendarUsers.filter(user => 
                    user.name.toLowerCase().includes(value.toLowerCase()) ||
                    user.email.toLowerCase().includes(value.toLowerCase())
                );
                
                if (filteredUsers.length > 0) {
                    showUserSuggestions(filteredUsers, value);
                } else {
                    hideUserSuggestions();
                }
            }
        }, 300));
        
        attendeesInput.addEventListener('focus', function() {
            const value = attendeesInput.value.trim();
            if (value.length >= 2 && window.calendarUsers) {
                const filteredUsers = window.calendarUsers.filter(user => 
                    user.name.toLowerCase().includes(value.toLowerCase()) ||
                    user.email.toLowerCase().includes(value.toLowerCase())
                );
                
                if (filteredUsers.length > 0) {
                    showUserSuggestions(filteredUsers, value);
                }
            }
        });
    }

    function showUserSuggestions(users, searchValue) {
        // Hide existing suggestion box
        hideUserSuggestions();
        
        // Create suggestion box
        const suggestionBox = document.createElement('div');
        suggestionBox.className = 'user-suggestions';
        suggestionBox.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            margin-top: 4px;
        `;
        
        users.forEach(user => {
            const item = document.createElement('div');
            item.className = 'user-suggestion-item';
            item.style.cssText = `
                padding: 8px 12px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                border-bottom: 1px solid var(--border-color);
            `;
            
            item.innerHTML = `
                <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--jira-blue); color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 600;">
                    ${user.initials || user.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <div style="font-weight: 500; color: var(--text-primary);">${user.name}</div>
                    <div style="font-size: 12px; color: var(--text-secondary);">${user.email}</div>
                </div>
            `;
            
            item.addEventListener('click', () => {
                addUserToAttendees(user);
                hideUserSuggestions();
                attendeesInput.focus();
            });
            
            suggestionBox.appendChild(item);
        });
        
        attendeesInput.parentNode.style.position = 'relative';
        attendeesInput.parentNode.appendChild(suggestionBox);
    }

    function hideUserSuggestions() {
        const existingBox = document.querySelector('.user-suggestions');
        if (existingBox) {
            existingBox.remove();
        }
    }

    function addUserToAttendees(user) {
        const currentAttendees = attendeesInput.value.trim();
        const newAttendee = user.email;
        
        if (currentAttendees) {
            const attendeesList = currentAttendees.split(',').map(a => a.trim());
            if (!attendeesList.includes(newAttendee)) {
                attendeesList.push(newAttendee);
                attendeesInput.value = attendeesList.join(', ');
            }
        } else {
            attendeesInput.value = newAttendee;
        }
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.form-group') || !e.target.closest('#eventAttendees')) {
            hideUserSuggestions();
        }
    });

    // Export button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            if (exportModal) {
                exportModal.style.display = 'flex';
            }
        });
    }

    // Settings button
    const settingsBtn = document.getElementById('settingsBtn');
    if (settingsBtn) {
        settingsBtn.addEventListener('click', () => {
            console.log('Settings functionality to be implemented');
        });
    }

    // Layout options
    document.querySelectorAll('.layout-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.layout-btn').forEach(b => b.classList.remove('active'));
            e.currentTarget.classList.add('active');
            
            // Different layout styles could be implemented here
            const layout = e.currentTarget.dataset.layout;
            console.log('Layout changed to:', layout);
        });
    });

    // Utility function for debouncing
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // ----------------------------------------------------
    // Keyboard Handling
    // ----------------------------------------------------

    // Close modals on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const visibleModals = document.querySelectorAll('.jira-modal.open');
            visibleModals.forEach(modal => {
                modal.style.display = 'none';
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
            });
            
            // Restore body scroll and position
            const scrollY = document.body.style.top;
            document.body.style.overflow = 'auto';
            document.body.style.position = 'static';
            document.body.style.width = 'auto';
            document.body.style.top = '';
            
            if (scrollY) {
                window.scrollTo(0, parseInt(scrollY || '0') * -1);
            }
        }
    });

    // ----------------------------------------------------
    // Prevent modal content clicks from bubbling to backdrop
    document.addEventListener('click', function(event) {
        const modalDialog = event.target.closest('.modal-dialog');
        if (modalDialog) {
            event.stopPropagation();
        }
    });

    // ----------------------------------------------------
    function showStatus(message, type) {
        // Find or create status container in modal
        let statusDiv = document.querySelector('.create-event-status');
        
        if (!statusDiv) {
            statusDiv = document.createElement('div');
            statusDiv.className = 'create-event-status';
            statusDiv.style.cssText = `
                padding: 12px 16px;
                margin-bottom: 16px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 500;
            `;
            const form = document.getElementById('createEventForm');
            if (form) {
                form.insertBefore(statusDiv, form.firstChild);
            }
        }
        
        statusDiv.innerHTML = message;
        
        if (type === 'success') {
            statusDiv.style.background = '#d4edda';
            statusDiv.style.color = '#155724';
            statusDiv.style.border = '1px solid #c3e6cb';
        } else if (type === 'error') {
            statusDiv.style.background = '#f8d7da';
            statusDiv.style.color = '#721c24';
            statusDiv.style.border = '1px solid #f5c6cb';
        } else {
            statusDiv.style.background = '#d1ecf1';
            statusDiv.style.color = '#0c5460';
            statusDiv.style.border = '1px solid #bee5eb';
        }
        
        // Auto-hide success messages after 5 seconds, errors after 10 seconds
        const timeout = type === 'success' ? 5000 : 10000;
        setTimeout(() => {
            if (statusDiv && statusDiv.parentNode) {
                statusDiv.parentNode.removeChild(statusDiv);
            }
        }, timeout);
    }

    // Initialize
    // ----------------------------------------------------

    initCalendar();
    loadProjects();
    loadSidebarData();
    loadEventTypes();
    loadPriorities();
    loadUsers();
    loadEventTypes();
    loadPriorities();
    loadUsers();
});