/**
 * Jira Clone Calendar System - Fully Real-Time Implementation
 * All data fetched from database in real-time
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('📅 [CALENDAR] DOMContentLoaded event fired');

    const calendarEl = document.getElementById('mainCalendar');

    console.log('📅 [CALENDAR] Calendar element found:', !!calendarEl);
    console.log('📅 [CALENDAR] JiraConfig exists:', !!window.JiraConfig);

    if (!calendarEl || !window.JiraConfig) {
        console.error('❌ [CALENDAR] Calendar element or JiraConfig missing');
        return;
    }

    console.log('📅 [CALENDAR] Configuration:', window.JiraConfig);

    // =====================================================
    // UI ELEMENT REFERENCES
    // =====================================================

    const projectFilter = document.getElementById('projectFilter');
    const statusFilter = document.getElementById('statusFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const typeFilter = document.getElementById('typeFilter');
    const assigneeFilter = document.getElementById('assigneeFilter');
    const reporterFilter = document.getElementById('reporterFilter');
    const quickSearch = document.getElementById('quickSearch');
    const currentDateEl = document.getElementById('currentDate');

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const todayBtn = document.getElementById('todayBtn');

    const filterTabs = document.querySelectorAll('.filter-tab');
    const moreFiltersBtn = document.getElementById('moreFilters');
    const advancedFilters = document.getElementById('advancedFilters');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const applyFiltersBtn = document.getElementById('applyFilters');

    const totalIssuesEl = document.getElementById('totalIssues');
    const overdueIssuesEl = document.getElementById('overdueIssues');
    const dueTodayIssuesEl = document.getElementById('dueTodayIssues');
    const dueWeekIssuesEl = document.getElementById('dueWeekIssues');
    const myIssuesEl = document.getElementById('myIssues');

    const upcomingCountEl = document.getElementById('upcomingCount');
    const upcomingListEl = document.getElementById('upcomingList');
    const scheduleListEl = document.getElementById('scheduleList');
    const teamListEl = document.getElementById('teamList');

    const eventModal = document.getElementById('eventModal');
    const createEventModal = document.getElementById('createEventModal');
    const exportModal = document.getElementById('exportModal');

    // =====================================================
    // GLOBAL STATE & CACHE
    // =====================================================

    let currentFilter = 'all';
    let calendar;

    window.calendarProjects = [];
    window.calendarStatuses = [];
    window.calendarPriorities = [];
    window.calendarIssueTypes = [];
    window.calendarUsers = [];

    // =====================================================
    // INITIALIZATION
    // =====================================================

    function initCalendar() {
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false,
            height: 'auto',
            editable: true,
            droppable: true,
            selectable: true,
            dayMaxEvents: true,
            eventMaxStack: 3,
            themeSystem: 'standard',

            events: function (info, successCallback, failureCallback) {
                fetchEvents(info)
                    .then(events => {
                        updateSummaryStats(events);
                        successCallback(events);
                    })
                    .catch(error => failureCallback(error));
            },

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

            eventClick: function (info) {
                info.jsEvent.preventDefault();
                info.jsEvent.stopPropagation();

                setTimeout(() => {
                    showEventDetails(info.event);
                }, 10);
            },

            eventDrop: function (info) {
                updateEventDate(info.event, info.revert);
            },

            eventResize: function (info) {
                updateEventDate(info.event, info.revert);
            },

            dateClick: function (info) {
                openCreateEventModal(info.dateStr);
            },

            datesSet: function (info) {
                updateCurrentDateDisplay();
                loadSidebarData();
            }
        });

        calendar.render();
        updateCurrentDateDisplay();
    }

    // =====================================================
    // EVENT FETCHING & FILTERING
    // =====================================================

    function fetchEvents(info) {
        const params = new URLSearchParams({
            start: info.start.toISOString(),
            end: info.end.toISOString()
        });

        if (projectFilter && projectFilter.value) {
            params.append('project', projectFilter.value);
        }

        const url = `${window.JiraConfig.apiBase}/calendar/events?${params.toString()}`;

        console.log('📅 [CALENDAR] Fetching events from:', url);
        console.log('📅 [CALENDAR] Date range:', {
            start: info.start.toISOString(),
            end: info.end.toISOString()
        });

        return fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => {
                console.log('📅 [CALENDAR] API Response Status:', res.status, res.statusText);
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('📅 [CALENDAR] API Response Data:', data);

                if (!data.success) {
                    throw new Error(data.error || 'Failed to load events');
                }

                let events = data.data || [];
                console.log('📅 [CALENDAR] Events returned from API:', events.length);

                events = applyFilters(events);
                console.log('📅 [CALENDAR] Events after filters:', events.length);

                return events;
            })
            .catch(err => {
                console.error('❌ [CALENDAR] Error fetching events:', err);
                throw err;
            });
    }

    function applyFilters(events) {
        // Status filter
        if (statusFilter && statusFilter.value) {
            events = events.filter(event => {
                const status = event.extendedProps.status?.toLowerCase().replace(' ', '_');
                return statusFilter.value.includes(status);
            });
        }

        // Priority filter
        if (priorityFilter && priorityFilter.value) {
            events = events.filter(event => {
                const priority = event.extendedProps.priority?.toLowerCase();
                return priorityFilter.value.includes(priority);
            });
        }

        // Issue type filter
        if (typeFilter && typeFilter.value) {
            events = events.filter(event => {
                const type = event.extendedProps.issueType?.toLowerCase();
                return typeFilter.value.includes(type);
            });
        }

        // Assignee filter
        if (assigneeFilter && assigneeFilter.value) {
            events = events.filter(event => {
                return event.extendedProps.assigneeId == assigneeFilter.value;
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

    // =====================================================
    // EVENT DETAILS MODAL
    // =====================================================

    function showEventDetails(event) {
        const props = event.extendedProps;

        // Update all modal content with REAL data from database
        const titleParts = event.title.includes(': ') ? event.title.split(': ') : ['', event.title];
        const displayTitle = titleParts.length > 1 ? titleParts[1] : titleParts[0];

        document.getElementById('eventModalTitle').textContent = `${props.key}: ${displayTitle}`;
        document.getElementById('eventKey').textContent = props.key;
        document.getElementById('eventSummary').textContent = displayTitle;
        document.getElementById('eventProject').textContent = props.project || '';
        document.getElementById('eventStatus').textContent = props.status || '';
        document.getElementById('eventStatus').style.backgroundColor = props.statusColor || '#ccc';
        document.getElementById('eventPriority').textContent = props.priority || '';
        document.getElementById('eventDueDate').textContent = event.start ? formatDateDisplay(event.start) : '';
        document.getElementById('eventCreatedDate').textContent = formatDateDisplay(props.created) || '';
        document.getElementById('eventUpdatedDate').textContent = formatDateDisplay(props.updated) || '';
        document.getElementById('eventDescription').innerHTML = props.description || 'No description';
        document.getElementById('eventStoryPoints').textContent = props.storyPoints || '—';

        // Helper to get avatar URL
        const getAvatarUrl = (path) => {
            if (!path) return '';
            if (path.startsWith('http')) return path;
            return `${window.JiraConfig.webBase}/${path}`;
        };

        // Assignee
        const assigneeAvatar = document.getElementById('assigneeAvatar');
        const assigneeName = document.getElementById('assigneeName');
        if (props.assigneeName) {
            assigneeAvatar.src = getAvatarUrl(props.assigneeAvatar);
            assigneeAvatar.alt = props.assigneeName;
            assigneeName.textContent = props.assigneeName;
            assigneeAvatar.style.display = props.assigneeAvatar ? 'inline-block' : 'none';
        } else {
            assigneeAvatar.src = '';
            assigneeAvatar.alt = 'Unassigned';
            assigneeName.textContent = 'Unassigned';
            assigneeAvatar.style.display = 'none';
        }

        // Reporter
        const reporterAvatar = document.getElementById('reporterAvatar');
        const reporterName = document.getElementById('reporterName');
        if (props.reporterName) {
            reporterAvatar.src = getAvatarUrl(props.reporterAvatar);
            reporterAvatar.alt = props.reporterName;
            reporterName.textContent = props.reporterName;
            reporterAvatar.style.display = props.reporterAvatar ? 'inline-block' : 'none';
        } else {
            reporterAvatar.src = '';
            reporterAvatar.alt = 'Unknown';
            reporterName.textContent = 'Unknown';
            reporterAvatar.style.display = 'none';
        }

        // Labels/Tags (Dynamic)
        const labelsContainer = document.getElementById('eventLabels');
        labelsContainer.innerHTML = ''; // Clear static content
        const typeSpan = document.createElement('span');
        typeSpan.className = 'label-tag';
        typeSpan.textContent = props.issueType || 'Issue';
        labelsContainer.appendChild(typeSpan);

        // Priority Label
        if (props.priority) {
            const prioritySpan = document.createElement('span');
            prioritySpan.className = 'label-tag';
            prioritySpan.textContent = props.priority;
            labelsContainer.appendChild(prioritySpan);
        }

        // Load History dynamically
        loadIssueHistory(props.key);

        // Check Watch Status
        checkWatchStatus(props.key);

        // View button
        const viewBtn = document.getElementById('viewIssueBtn');
        viewBtn.onclick = () => {
            window.location.href = `${window.JiraConfig.webBase}/issues/${props.key}`;
        };

        // Show modal
        if (eventModal) {
            requestAnimationFrame(() => {
                eventModal.style.display = 'flex';
                eventModal.classList.add('open');
                eventModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                document.body.style.top = `-${window.scrollY}px`;

                const focusableElements = eventModal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusableElements.length > 0) {
                    focusableElements[0].focus();
                }
            });
        }

        if (event && event.stopPropagation) {
            event.stopPropagation();
        }
    }

    // =====================================================
    // ISSUE HISTORY LOADING
    // =====================================================

    function loadIssueHistory(issueKey) {
        const timelineContainer = document.getElementById('eventTimeline');
        if (!timelineContainer) return;

        timelineContainer.innerHTML = '<div class="loading-spinner"><i class="bi bi-arrow-repeat spin"></i> Loading activity...</div>';

        fetch(`${window.JiraConfig.apiBase}/issues/${issueKey}/history`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    renderTimeline(data.data);
                } else {
                    timelineContainer.innerHTML = '<div class="empty-state">No recent activity</div>';
                }
            })
            .catch(err => {
                console.error('Failed to load history:', err);
                timelineContainer.innerHTML = '<div class="error-state">Failed to load activity</div>';
            });
    }

    function renderTimeline(historyItems) {
        const timelineContainer = document.getElementById('eventTimeline');
        if (!timelineContainer) return;

        if (historyItems.length === 0) {
            timelineContainer.innerHTML = '<div class="empty-state">No recent activity</div>';
            return;
        }

        const html = historyItems.slice(0, 5).map(item => { // Show last 5 items
            const type = item.type || 'general'; // status, comment, etc
            const user = item.user_name || 'System';
            const time = timeAgo(item.created_at);

            let iconClass = 'bi-circle';
            let iconColor = 'gray';

            if (item.field === 'status') { iconClass = 'bi-arrow-left-right'; iconColor = 'blue'; }
            if (item.field === 'comment') { iconClass = 'bi-chat-text'; iconColor = 'green'; }
            if (item.field === 'priority') { iconClass = 'bi-flag'; iconColor = 'orange'; }

            return `
                <div class="timeline-item">
                    <div class="timeline-dot" style="color: ${iconColor}"><i class="bi ${iconClass}"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <strong>${user}</strong> ${formatAction(item)}
                            <span class="timeline-time">${time}</span>
                        </div>
                        <div class="timeline-detail">
                            ${formatHistoryDetail(item)}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        timelineContainer.innerHTML = html;
    }

    function formatAction(item) {
        if (item.field === 'status') return 'changed status';
        if (item.field === 'comment') return 'commented';
        if (item.field === 'assignee') return 'assigned issue';
        return `updated ${item.field}`;
    }

    function formatHistoryDetail(item) {
        if (item.field === 'status') {
            return `From <span class="old-value">${item.old_value || 'None'}</span> to <span class="new-value">${item.new_value}</span>`;
        }
        if (item.field === 'comment') {
            return `"${item.new_value}"`; // Assuming new_value contains the comment snippet
        }
        return `${item.new_value}`;
    }

    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " minutes ago";
        return Math.floor(seconds) + " seconds ago";
    }

    // =====================================================
    // EVENT DATE UPDATE
    // =====================================================

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

    // =====================================================
    // SIDEBAR DATA LOADING
    // =====================================================

    function loadSidebarData() {
        loadUpcomingIssues();
        loadMySchedule();
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

        const startDate = new Date().toISOString().split('T')[0];
        const endDate = new Date(new Date().setDate(new Date().getDate() + 30)).toISOString().split('T')[0];

        fetch(`${window.JiraConfig.apiBase}/calendar/events?start=${startDate}T00:00:00Z&end=${endDate}T23:59:59Z`, {
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

    // =====================================================
    // UI UPDATES
    // =====================================================

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

    // =====================================================
    // FILTER DATA LOADING
    // =====================================================

    function loadProjects() {
        fetch(`${window.JiraConfig.apiBase}/calendar/projects`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    window.calendarProjects = data.data;

                    if (projectFilter) {
                        projectFilter.innerHTML = '<option value="">All projects</option>';
                        data.data.forEach(project => {
                            const opt = document.createElement('option');
                            opt.value = project.key;
                            opt.textContent = project.name;
                            projectFilter.appendChild(opt);
                        });
                    }

                    const createModalProject = document.getElementById('eventProject');
                    if (createModalProject) {
                        createModalProject.innerHTML = '<option value="">Select project...</option>';
                        data.data.forEach(project => {
                            const opt = document.createElement('option');
                            opt.value = project.id;
                            opt.textContent = project.name;
                            createModalProject.appendChild(opt);
                        });
                    }
                }
            })
            .catch(err => console.error('Failed to load projects:', err));
    }

    function loadStatuses() {
        fetch(`${window.JiraConfig.apiBase}/calendar/statuses`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    window.calendarStatuses = data.data;

                    if (statusFilter) {
                        statusFilter.innerHTML = '';
                        data.data.forEach(status => {
                            const opt = document.createElement('option');
                            opt.value = status.name.toLowerCase().replace(' ', '_');
                            opt.textContent = status.name;
                            statusFilter.appendChild(opt);
                        });
                    }
                }
            })
            .catch(err => console.error('Failed to load statuses:', err));
    }

    function loadPriorities() {
        fetch(`${window.JiraConfig.apiBase}/calendar/priorities`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    window.calendarPriorities = data.data;

                    if (priorityFilter) {
                        priorityFilter.innerHTML = '';
                        data.data.forEach(priority => {
                            const opt = document.createElement('option');
                            opt.value = priority.name.toLowerCase();
                            opt.textContent = priority.name;
                            priorityFilter.appendChild(opt);
                        });
                    }

                    const createModalPriority = document.getElementById('eventPriority');
                    if (createModalPriority) {
                        createModalPriority.innerHTML = '';
                        data.data.forEach(priority => {
                            const opt = document.createElement('option');
                            opt.value = priority.id;
                            opt.textContent = priority.name;
                            createModalPriority.appendChild(opt);
                        });
                    }
                }
            })
            .catch(err => console.error('Failed to load priorities:', err));
    }

    function loadIssueTypes() {
        fetch(`${window.JiraConfig.apiBase}/calendar/issue-types`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    window.calendarIssueTypes = data.data;

                    if (typeFilter) {
                        typeFilter.innerHTML = '';
                        data.data.forEach(type => {
                            const opt = document.createElement('option');
                            opt.value = type.name.toLowerCase();
                            opt.textContent = type.name;
                            typeFilter.appendChild(opt);
                        });
                    }
                }
            })
            .catch(err => console.error('Failed to load issue types:', err));
    }

    function loadUsers() {
        fetch(`${window.JiraConfig.apiBase}/calendar/users`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data) {
                    window.calendarUsers = data.data;

                    if (assigneeFilter) {
                        assigneeFilter.innerHTML = '<option value="">Anyone</option>';
                        data.data.forEach(user => {
                            const opt = document.createElement('option');
                            opt.value = user.id;
                            opt.textContent = user.name;
                            assigneeFilter.appendChild(opt);
                        });
                    }

                    if (reporterFilter) {
                        reporterFilter.innerHTML = '<option value="">Anyone</option>';
                        data.data.forEach(user => {
                            const opt = document.createElement('option');
                            opt.value = user.id;
                            opt.textContent = user.name;
                            reporterFilter.appendChild(opt);
                        });
                    }
                }
            })
            .catch(err => console.error('Failed to load users:', err));
    }

    // =====================================================
    // MODAL FUNCTIONS
    // =====================================================

    function openCreateEventModal(dateStr) {
        if (createEventModal) {
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

            const focusableElements = createEventModal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusableElements.length > 0) {
                focusableElements[0].focus();
            }
        }
    }

    window.handleBackdropClick = function (event, modalType) {
        if (event.target === event.currentTarget) {
            if (modalType === 'create') {
                window.closeCreateModal();
            } else if (modalType === 'export') {
                window.closeExportModal();
            } else {
                window.closeEventModal();
            }
        }
    };

    window.closeEventModal = function () {
        if (eventModal) {
            eventModal.style.display = 'none';
            eventModal.classList.remove('open');
            eventModal.setAttribute('aria-hidden', 'true');

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

    window.closeCreateModal = function () {
        if (createEventModal) {
            createEventModal.style.display = 'none';
            createEventModal.classList.remove('open');
            createEventModal.setAttribute('aria-hidden', 'true');

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

    window.closeExportModal = function () {
        if (exportModal) {
            exportModal.style.display = 'none';
            exportModal.classList.remove('open');
            exportModal.setAttribute('aria-hidden', 'true');

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

    // =====================================================
    // UTILITY FUNCTIONS
    // =====================================================

    function formatDateDisplay(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

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

    // =====================================================
    // EVENT LISTENERS
    // =====================================================

    filterTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            filterTabs.forEach(t => t.classList.remove('active'));
            e.target.classList.add('active');
            currentFilter = e.target.dataset.filter;
            calendar.refetchEvents();
        });
    });

    if (moreFiltersBtn && advancedFilters) {
        moreFiltersBtn.addEventListener('click', () => {
            advancedFilters.style.display = advancedFilters.style.display === 'none' ? 'block' : 'none';
        });
    }

    [projectFilter, statusFilter, priorityFilter, typeFilter, assigneeFilter, quickSearch].forEach(element => {
        if (element) {
            element.addEventListener('change', () => calendar.refetchEvents());
            if (element.type === 'text') {
                element.addEventListener('input', debounce(() => calendar.refetchEvents(), 300));
            }
        }
    });

    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', () => {
            if (projectFilter) projectFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            if (priorityFilter) priorityFilter.value = '';
            if (typeFilter) typeFilter.value = '';
            if (assigneeFilter) assigneeFilter.value = '';
            if (quickSearch) quickSearch.value = '';

            filterTabs.forEach(t => t.classList.remove('active'));
            if (filterTabs[0]) filterTabs[0].classList.add('active');
            currentFilter = 'all';

            calendar.refetchEvents();
        });
    }

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', () => {
            calendar.refetchEvents();
            if (advancedFilters) {
                advancedFilters.style.display = 'none';
            }
        });
    }

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

    const createEventBtn = document.getElementById('createEventBtn');
    if (createEventBtn) {
        createEventBtn.addEventListener('click', () => {
            openCreateEventModal(new Date().toISOString().split('T')[0]);
        });
    }

    // =====================================================
    // KEYBOARD HANDLING
    // =====================================================

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            const visibleModals = document.querySelectorAll('.jira-modal.open');
            visibleModals.forEach(modal => {
                modal.style.display = 'none';
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
            });

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

    document.addEventListener('click', function (event) {
        const modalDialog = event.target.closest('.modal-dialog');
        if (modalDialog) {
            event.stopPropagation();
        }
    });

    // =====================================================
    // INITIALIZATION
    // =====================================================

    console.log('📅 [CALENDAR] Starting calendar initialization...');

    initCalendar();
    console.log('📅 [CALENDAR] Calendar initialized');

    loadProjects();
    loadStatuses();
    loadPriorities();
    loadIssueTypes();
    loadUsers();
    loadSidebarData();

    console.log('📅 [CALENDAR] All startup tasks completed');
});
// =====================================================
// WATCH & SHARE
// =====================================================

window.watchIssue = function () {
    const key = document.getElementById('eventKey').textContent;
    if (!key) return;

    const btn = document.querySelector('.modal-footer .footer-left button:first-child');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Watching...';
    btn.disabled = true;

    fetch(`${window.JiraConfig.apiBase}/issues/${key}/watch`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.JiraConfig.csrfToken
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateWatchButtonState(data.isWatching);
            } else {
                alert('Failed to update watch status');
                btn.innerHTML = originalHtml;
            }
        })
        .catch(err => {
            console.error(err);
            btn.innerHTML = originalHtml;
        })
        .finally(() => {
            btn.disabled = false;
        });
};

window.shareIssue = function () {
    const key = document.getElementById('eventKey').textContent;
    if (!key) return;

    const url = `${window.JiraConfig.webBase}/issues/${key}`;
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.querySelector('.modal-footer .footer-left button:last-child');
        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy', err);
        prompt('Copy this link:', url);
    });
};

function updateWatchButtonState(isWatching) {
    const btn = document.querySelector('.modal-footer .footer-left button:first-child');
    if (!btn) return;

    if (isWatching) {
        btn.innerHTML = '<i class="bi bi-eye-fill"></i> Unwatch';
        btn.classList.add('jira-btn-secondary');
        btn.classList.remove('jira-btn-ghost');
    } else {
        btn.innerHTML = '<i class="bi bi-eye"></i> Watch';
        btn.classList.add('jira-btn-ghost');
        btn.classList.remove('jira-btn-secondary');
    }
}

function checkWatchStatus(key) {
    fetch(`${window.JiraConfig.apiBase}/issues/${key}/watch`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': window.JiraConfig.csrfToken
        }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateWatchButtonState(data.isWatching);
            }
        })
        .catch(err => console.error(err));
}
