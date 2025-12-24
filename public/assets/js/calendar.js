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
        
        // Update view button
        const viewBtn = document.getElementById('viewIssueBtn');
        viewBtn.href = `${window.JiraConfig.webBase}/projects/${props.projectKey}/issues/${props.key}`;
        
        // Show modal with proper handling
        if (eventModal) {
            // Use requestAnimationFrame to ensure proper rendering
            requestAnimationFrame(() => {
                eventModal.style.display = 'flex';
                eventModal.classList.add('open');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
                
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
            document.body.style.overflow = 'hidden';
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
        
        scheduleListEl.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-clock"></i>
                <p>No scheduled items</p>
            </div>
        `;
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
                        opt.value = p.key;
                        opt.textContent = p.name;
                        projectFilter.appendChild(opt);
                    });
                }
            })
            .catch(err => console.error('Failed to load projects:', err));
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
        // Only close if click is directly on backdrop, not on modal content
        if (event.target === event.currentTarget) {
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
            document.body.style.overflow = 'auto'; // Restore scroll
        }
    };

    window.closeCreateModal = function() {
        if (createEventModal) {
            createEventModal.style.display = 'none';
            createEventModal.classList.remove('open');
            document.body.style.overflow = 'auto'; // Restore scroll
        }
    };

    window.closeExportModal = function() {
        if (exportModal) {
            exportModal.style.display = 'none';
            exportModal.classList.remove('open');
            document.body.style.overflow = 'auto'; // Restore scroll
        }
    };

    window.saveEvent = function() {
        // Implementation for creating new events
        console.log('Save event functionality to be implemented');
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
            });
            document.body.style.overflow = 'auto';
        }
    });

    // ----------------------------------------------------
    // Initialize
    // ----------------------------------------------------

    initCalendar();
    loadProjects();
    loadSidebarData();
});