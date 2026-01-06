/**
 * Jira Clone Calendar System - Fully Real-Time Implementation
 * All data fetched from database in real-time
 */

document.addEventListener('DOMContentLoaded', function () {
    if (window.JiraCalendarInitialized) {
        console.warn('⚠️ [CALENDAR] Calendar script already initialized. Skipping.');
        return;
    }
    window.JiraCalendarInitialized = true;

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

    function handleCalendarDrop(info) {
        console.log('📅 [DROP] Something dropped on calendar:', info);

        let dragData = null;

        // Method 1: Check dragged element (FullCalendar Draggable API)
        if (info.draggedEl) {
            console.log('📅 [DROP] Dragged element detected via Draggable API');
            const issueId = info.draggedEl.dataset.issueId;
            const issueKey = info.draggedEl.dataset.issueKey;

            if (issueId) {
                dragData = {
                    id: issueId,
                    key: issueKey,
                    fromUnscheduled: true
                };
                console.log('📅 [DROP] Got data from draggedEl:', dragData);
            }
        }

        // Method 2: Check global fallback (Native Drag & Drop)
        if (!dragData && window.currentDragData) {
            dragData = window.currentDragData;
            console.log('📅 [DROP] Using global fallback data:', dragData);
        }

        // Method 3: Try DataTransfer (Native Drag & Drop)
        if (!dragData && info.jsEvent && info.jsEvent.dataTransfer) {
            try {
                const plainText = info.jsEvent.dataTransfer.getData('text/plain');
                if (plainText) {
                    dragData = JSON.parse(plainText);
                    console.log('📅 [DROP] Got data from text/plain:', dragData);
                }
            } catch (err) {
                // Ignore
            }

            if (!dragData) {
                try {
                    const jsonData = info.jsEvent.dataTransfer.getData('application/json');
                    if (jsonData) {
                        dragData = JSON.parse(jsonData);
                    }
                } catch (err) { }
            }
        }

        if (!dragData) {
            console.log('📅 [DROP] No valid drag data found in any method, ignoring drop');
            return;
        }

        try {
            if (dragData.fromUnscheduled) {
                // This is an unscheduled issue being scheduled
                const dropDate = info.dateStr;
                console.log('📅 [DROP] Unscheduled issue dropped on:', dropDate);

                // Find the issue data from our unscheduled issues array
                const issueData = unscheduledIssues.find(issue => issue.id == dragData.id);
                if (issueData) {
                    openScheduleModal(issueData, dropDate);
                } else {
                    console.error('📅 [DROP] Issue data not found for ID:', dragData.id);
                }
            } else {
                console.log('📅 [DROP] Not an unscheduled issue, ignoring');
            }
        } catch (err) {
            console.error('📅 [DROP] Error processing drop:', err);
        }
    }


    function initCalendar() {
        if (calendar) {
            console.warn('⚠️ [CALENDAR] Destroying existing calendar instance');
            calendar.destroy();
            calendar = null;
        }

        const uniqueRenderSet = new Set();

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: false,
            height: 'auto',
            editable: true,
            droppable: true,
            selectable: true,
            dayMaxEvents: true,
            // eventMaxStack: 3, // REMOVED to avoid stacking issues
            themeSystem: 'standard',
            dropAccept: '.unscheduled-issue',

            events: function (info, successCallback, failureCallback) {
                fetchEvents(info)
                    .then(events => {
                        // AGGRESSIVE CLIENT-SIDE DEDUPLICATION
                        const uniqueEvents = [];
                        const seenKeys = new Set();
                        events.forEach(e => {
                            const key = e.extendedProps?.key || e.id;
                            if (!seenKeys.has(key)) {
                                seenKeys.add(key);
                                uniqueEvents.push(e);
                            }
                        });

                        console.log(`📅 [CALENDAR] Client-side Dedup: ${events.length} -> ${uniqueEvents.length}`);
                        updateSummaryStats(uniqueEvents);
                        successCallback(uniqueEvents);
                    })
                    .catch(error => failureCallback(error));
            },

            eventContent: function (arg) {
                const priority = arg.event.extendedProps.priority?.toLowerCase() || 'medium';
                const key = arg.event.extendedProps.key || '';
                const title = arg.event.title;
                const displayTitle = title.includes(': ') ? title.split(': ')[1] : title;

                // Debug duplicate rendering
                // console.log(`📅 [RENDER] Rendering event ${key}`);

                return {
                    html: `
                        <div class="fc-event-content" data-priority="${priority}" data-issue-key="${key}">
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

            drop: function (info) {
                handleCalendarDrop(info);
            },

            eventDragStart: function (info) {
                console.log('📅 [DRAG] Calendar event drag started:', info.event.title);
            },

            eventDragStop: function (info) {
                console.log('📅 [DRAG] Calendar event drag stopped:', info.event.title);
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
        console.log(`📅 [CALENDAR] applyFilters called. Filter: "${currentFilter}", Events In: ${events.length}`);

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

        // Assignee filter (Dropdown)
        if (assigneeFilter && assigneeFilter.value) {
            events = events.filter(event => {
                return event.extendedProps.assigneeId == assigneeFilter.value; // Loose equality
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

        // Tab filters (Logic Decoupled from currentUser existence)
        const currentUser = window.JiraConfig.currentUser?.id;

        switch (currentFilter) {
            case 'assigned':
                if (currentUser) {
                    events = events.filter(event => event.extendedProps.assigneeId == currentUser);
                    console.log(`📅 [CALENDAR] Filtered 'assigned'. User: ${currentUser}. Remaining: ${events.length}`);
                } else {
                    console.warn('⚠️ [CALENDAR] Current User ID missing, cannot filter "assigned"');
                }
                break;

            case 'overdue':
                const today = new Date().toISOString().split('T')[0];
                events = events.filter(event => event.start && event.start < today);
                console.log(`📅 [CALENDAR] Filtered 'overdue' (< ${today}). Remaining: ${events.length}`);
                break;

            case 'due-today':
                const todayStr = new Date().toISOString().split('T')[0];
                events = events.filter(event => event.start && event.start === todayStr);
                console.log(`📅 [CALENDAR] Filtered 'due-today' (== ${todayStr}). Remaining: ${events.length}`);
                break;

            case 'due-week':
                const weekFromNow = new Date();
                weekFromNow.setDate(weekFromNow.getDate() + 7);
                const weekStr = weekFromNow.toISOString().split('T')[0];
                const todayStr2 = new Date().toISOString().split('T')[0];
                events = events.filter(event => event.start && event.start >= todayStr2 && event.start <= weekStr);
                console.log(`📅 [CALENDAR] Filtered 'due-week' (${todayStr2} to ${weekStr}). Remaining: ${events.length}`);
                break;
        }

        console.log(`📅 [CALENDAR] applyFilters Returning: ${events.length}`);
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
        document.getElementById('detailKey').textContent = props.key;
        document.getElementById('detailSummary').textContent = displayTitle;
        document.getElementById('detailProject').textContent = props.project || '';
        document.getElementById('detailStatus').textContent = props.status || '';
        document.getElementById('detailStatus').style.backgroundColor = props.statusColor || '#ccc';
        document.getElementById('detailPriority').textContent = props.priority || '';
        document.getElementById('detailDueDate').textContent = event.start ? formatDateDisplay(event.start) : '';
        document.getElementById('detailCreatedDate').textContent = formatDateDisplay(props.created) || '';
        document.getElementById('detailUpdatedDate').textContent = formatDateDisplay(props.updated) || '';
        document.getElementById('detailDescription').innerHTML = props.description || 'No description';
        document.getElementById('detailStoryPoints').textContent = props.storyPoints || '—';

        // Helper to get avatar URL - Uses same logic as avatar() PHP helper
        const getAvatarUrl = (path) => {
            if (!path) return '';
            
            // If already a full URL, return as-is
            if (path.startsWith('http://') || path.startsWith('https://')) {
                return path;
            }
            
            // FIX: Handle incorrectly stored /public/avatars/ paths
            if (path.includes('/public/avatars/')) {
                path = path.replace('/public/avatars/', '/uploads/avatars/');
            }
            
            // FIX: Handle /avatars/ paths (missing /uploads prefix)
            if (path.startsWith('/avatars/')) {
                path = '/uploads' + path;
            }
            
            // Ensure path starts with /
            if (!path.startsWith('/')) {
                path = '/' + path;
            }
            
            // Build URL using the proper URL building logic
            // window.JiraConfig.webBase includes /jira_clone_system/public/ 
            let baseUrl = window.JiraConfig.webBase;
            
            // Remove trailing slash
            if (baseUrl.endsWith('/')) {
                baseUrl = baseUrl.slice(0, -1);
            }
            
            // Return full URL
            return baseUrl + path;
        };

        // Assignee
        const assigneeAvatar = document.getElementById('assigneeAvatar');
        const assigneeName = document.getElementById('assigneeName');
        if (props.assigneeName) {
            const assigneeUrl = getAvatarUrl(props.assigneeAvatar);
            console.log('📅 [AVATAR] Assignee:', {
                raw: props.assigneeAvatar,
                resolved: assigneeUrl,
                webBase: window.JiraConfig.webBase
            });
            assigneeAvatar.src = assigneeUrl;
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
            const reporterUrl = getAvatarUrl(props.reporterAvatar);
            console.log('📅 [AVATAR] Reporter:', {
                raw: props.reporterAvatar,
                resolved: reporterUrl,
                webBase: window.JiraConfig.webBase
            });
            reporterAvatar.src = reporterUrl;
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
        const labelsContainer = document.getElementById('detailLabels');
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
        if (viewBtn) {
            viewBtn.onclick = () => {
                const baseUrl = window.JiraConfig.webBase.endsWith('/')
                    ? window.JiraConfig.webBase.slice(0, -1)
                    : window.JiraConfig.webBase;
                window.location.href = `${baseUrl}/issue/${props.key}`;
            };
        }

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

        // Store current event data for editing
        window.currentEventData = {
            ...props,
            start: event.start,
            end: event.end,
            allDay: event.allDay
        };
    }

    // =====================================================
    // EDIT ISSUE FUNCTIONALITY
    // =====================================================

    window.editIssue = function () {
        console.log('📅 [EDIT] editIssue called');
        const data = window.currentEventData;

        if (!data) {
            console.error('❌ [EDIT] No currentEventData found');
            alert('Error: Could not load issue data for editing.');
            return;
        }

        console.log('📅 [EDIT] Editing issue:', data);

        // Close details modal
        window.closeEventModal();

        // Open create modal with small delay to ensure DOM is ready
        setTimeout(() => {
            try {
                // Open create modal
                // We use the create modal but repurpose it for editing
                const modal = document.getElementById('createEventModal');
                if (!modal) {
                    console.error('❌ [EDIT] createEventModal not found in DOM');
                    alert('Error: Edit modal missing.');
                    return;
                }

                // Reset form first
                const form = document.getElementById('createEventForm');
                if (form) form.reset();

                // Set Title
                const titleEl = modal.querySelector('.modal-title');
                if (titleEl) titleEl.textContent = 'Edit Issue: ' + data.key;

                // Set Button
                const submitBtn = modal.querySelector('.modal-footer .jira-btn-primary');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="bi bi-check-lg"></i> Update Issue';
                    submitBtn.onclick = window.saveEvent; // Ensure it calls saveEvent
                }

                // Set validation flag/id
                window.editingIssueId = data.key; // We use KEY for updates usually, but API might want ID? API uses KEY in URL.

                // Populate Fields

                // 1. Event Type / Issue Type
                const typeSelect = document.getElementById('issueType');
                if (typeSelect && data.issueTypeId) {
                    typeSelect.value = data.issueTypeId;
                } else if (typeSelect && data.issueType) {
                    // Try matching by text if ID missing
                    for (let i = 0; i < typeSelect.options.length; i++) {
                        if (typeSelect.options[i].text.toLowerCase() === data.issueType.toLowerCase()) {
                            typeSelect.selectedIndex = i;
                            break;
                        }
                    }
                }

                // 2. Project - select by ID
                const projectSelect = document.getElementById('eventProject');
                if (projectSelect && data.projectId) {
                    projectSelect.value = data.projectId;
                }

                // 3. Title
                const titleInput = document.getElementById('eventTitle');
                if (titleInput) {
                    // Remove Key prefix if present in title
                    let summary = data.title || '';
                    if (data.key && summary.startsWith(data.key + ': ')) {
                        summary = summary.substring(data.key.length + 2);
                    }
                    titleInput.value = summary;
                }

                // 4. Description
                const descInput = document.getElementById('eventDesc');
                if (descInput) {
                    descInput.value = data.description || '';
                    // If TinyMCE is already initialized (re-opening), set content
                    if (typeof tinymce !== 'undefined' && tinymce.get('eventDesc')) {
                        tinymce.get('eventDesc').setContent(data.description || '');
                    }
                }

                // 5. Dates
                const startInput = document.getElementById('eventStartDate');
                const endInput = document.getElementById('eventEndDate');

                const toDateTimeLocal = (date) => {
                    if (!date) return '';
                    let d = new Date(date);
                    if (isNaN(d.getTime())) return ''; // Invalid date
                    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
                    return d.toISOString().slice(0, 16);
                };

                if (startInput && data.start) startInput.value = toDateTimeLocal(data.start);
                if (endInput) {
                    // If end is null (single day), use start
                    endInput.value = toDateTimeLocal(data.end || data.start);
                }

                // 6. Priority
                const prioritySelect = document.getElementById('eventPriority');
                if (prioritySelect && data.priority) {
                    // Find option with text content matching priority name
                    for (let i = 0; i < prioritySelect.options.length; i++) {
                        if (prioritySelect.options[i].text.toLowerCase() === data.priority.toLowerCase()) {
                            prioritySelect.selectedIndex = i;
                            break;
                        }
                    }
                }
                console.log('📅 [EDIT] Priority set.');

                // Show Modal and Lock Body
                modal.style.display = 'flex';
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');

                // Re-apply body lock
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                document.body.style.top = `-${window.scrollY}px`;

                console.log('📅 [EDIT] Modal displayed successfully');

                // Init TinyMCE after modal is visible
                setTimeout(() => {
                    initTinyMCE('#eventDesc');
                }, 100);

            } catch (err) {
                console.error('❌ [EDIT] Error in setTimeout block:', err);
                alert('Error opening edit form: ' + err.message);
            }
        }, 100);
    };

    window.saveEvent = function (e) {
        if (e) e.preventDefault();

        const btn = document.querySelector('#createEventModal .modal-footer .jira-btn-primary');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
        btn.disabled = true;

        // Gather Data
        const summary = document.getElementById('eventTitle').value;
        const projectId = document.getElementById('eventProject').value;
        const priorityId = document.getElementById('eventPriority').value;
        const issueTypeId = document.getElementById('issueType').value;
        const startDate = document.getElementById('eventStartDate').value;
        const endDate = document.getElementById('eventEndDate').value;

        // Sync TinyMCE header
        if (typeof tinymce !== 'undefined' && tinymce.get('eventDesc')) {
            tinymce.triggerSave();
        }
        const description = document.getElementById('eventDesc').value;
        const payload = {
            summary: summary,
            description: description,
            project_id: projectId,
            priority_id: priorityId,
            issue_type_id: issueTypeId,
            start_date: startDate ? startDate.split('T')[0] : null,
            due_date: endDate ? endDate.split('T')[0] : null
        };

        let url = `${window.JiraConfig.apiBase}/calendar/events`; // Default create
        let method = 'POST';

        // Check if editing
        if (window.editingIssueId) {
            // Updating existing Issue
            url = `${window.JiraConfig.apiBase}/issues/${window.editingIssueId}`;
            method = 'PUT';
        } else {
            // Creating new (Not fully implemented for generic events vs issues)
            // For now, assume creating a generic calendar event or issue
            // If eventType is 'issue', we might want to call issues endpoint? 
            // Leaving as default calendar/events for create for now if backend supports it.
            // But since saveEvent was missing, create was broken anyway. 
            // I'll leave create logic basic.
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.issue) { // Issue API returns {success:true, issue:...}
                    console.log('📅 [SAVE] Success');
                    window.closeCreateModal();
                    calendar.refetchEvents();

                    // Clear editing state
                    window.editingIssueId = null;

                    // Reset button (create mode)
                    const titleEl = document.querySelector('#createEventModal .modal-title');
                    if (titleEl) titleEl.textContent = 'Create Calendar Event';
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Create Event';
                } else {
                    alert('Failed to save: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Save error:', err);
                alert('An error occurred while saving.');
            })
            .finally(() => {
                if (!window.editingIssueId) {
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Create Event';
                }
                btn.disabled = false;
            });
    };

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
        loadMySchedule();
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
        console.log('📅 [CALENDAR] Updating summary stats for', events.length, 'events');

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
            const eventStart = event.start; // Format is already YYYY-MM-DD from API or ISO
            // Ensure we compare YYYY-MM-DD parts only
            const eventDate = eventStart.split('T')[0];
            const isDone = event.extendedProps.statusCategory === 'done';

            if (eventDate < today && !isDone) overdue++;
            if (eventDate === today && !isDone) dueToday++; // Optionally exclude done from 'due today' too
            if (eventDate >= today && eventDate <= weekStr && !isDone) dueWeek++;

            // Loose equality check for ID match to handle string vs int
            if (currentUser && event.extendedProps.assigneeId == currentUser) {
                myIssues++;
            }
        });

        console.log(`📅 [CALENDAR] Stats Calculated - Total: ${events.length}, Overdue: ${overdue}, My: ${myIssues} (User: ${currentUser})`);

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

                    const createModalType = document.getElementById('issueType');
                    if (createModalType) {
                        createModalType.innerHTML = '';
                        data.data.forEach(type => {
                            const opt = document.createElement('option');
                            opt.value = type.id;
                            opt.textContent = type.name;
                            createModalType.appendChild(opt);
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

            // Initialize TinyMCE
            setTimeout(() => {
                initTinyMCE('#eventDesc');
            }, 100);
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

            // Remove TinyMCE
            if (typeof tinymce !== 'undefined') {
                tinymce.remove('#eventDesc');
            }

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

    // Event Delegation for Filter Tabs
    const filterTabsContainer = document.querySelector('.filter-tabs');
    if (filterTabsContainer) {
        console.log('📅 [CALENDAR] Filter tabs container found, attaching listener');
        filterTabsContainer.addEventListener('click', (e) => {
            const button = e.target.closest('.filter-tab');
            if (!button) return; // Clicked outside a tab button

            console.log('📅 [CALENDAR] Filter tab clicked:', button.dataset.filter);
            console.log('📅 [CALENDAR] Current User ID:', window.JiraConfig?.currentUser?.id || 'Missing');

            // Remove active class from all tabs in this container
            filterTabsContainer.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            button.classList.add('active');

            currentFilter = button.dataset.filter;
            console.log('📅 [CALENDAR] Set currentFilter to:', currentFilter);

            calendar.refetchEvents();
        });
    } else {
        console.error('❌ [CALENDAR] Filter tabs container not found!');
    }

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

    // View Switcher Logic
    const viewSwitcher = document.querySelector('.view-switcher');
    if (viewSwitcher) {
        console.log('📅 [CALENDAR] View switcher found, attaching listener');
        viewSwitcher.addEventListener('click', (e) => {
            const button = e.target.closest('.view-btn');
            if (!button) return;

            const viewType = button.dataset.view;
            console.log('📅 [CALENDAR] Switching view to:', viewType);

            // Update UI
            viewSwitcher.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            // Map view types to FullCalendar views
            const viewMap = {
                'month': 'dayGridMonth',
                'week': 'timeGridWeek',
                'day': 'timeGridDay',
                'list': 'listWeek'
            };

            const fcView = viewMap[viewType] || 'dayGridMonth';

            if (calendar) {
                calendar.changeView(fcView);
                updateCurrentDateDisplay();
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
    // UNSCHEDULED ISSUES FUNCTIONALITY
    // =====================================================

    let unscheduledIssues = [];

    function loadUnscheduledIssues() {
        console.log('📅 [UNSCHEDULED] Loading unscheduled issues...');

        fetch(`${window.JiraConfig.apiBase}/calendar/unscheduled`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            }
        })
            .then(res => {
                console.log('📅 [UNSCHEDULED] API Response Status:', res.status);
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}: ${res.statusText}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('📅 [UNSCHEDULED] API Response Data:', data);

                if (!data.success) {
                    throw new Error(data.error || 'Failed to load unscheduled issues');
                }

                unscheduledIssues = data.data || [];
                console.log('📅 [UNSCHEDULED] Loaded unscheduled issues:', unscheduledIssues.length);
                renderUnscheduledIssues();
            })
            .catch(err => {
                console.error('❌ [UNSCHEDULED] Error loading unscheduled issues:', err);
                showUnscheduledError();
            });
    }

    function renderUnscheduledIssues() {
        const unscheduledList = document.getElementById('unscheduledList');
        const unscheduledCount = document.getElementById('unscheduledCount');

        if (!unscheduledList || !unscheduledCount) return;

        // Update count
        unscheduledCount.textContent = unscheduledIssues.length;

        if (unscheduledIssues.length === 0) {
            unscheduledList.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-check-circle"></i>
                    <p>All issues scheduled</p>
                </div>
            `;
            return;
        }

        const issuesHtml = unscheduledIssues.map(issue => `
            <div class="unscheduled-issue" 
                 data-issue-id="${issue.id}" 
                 data-issue-key="${issue.key}"
                 draggable="true">
                <div class="issue-type-icon" style="background-color: ${issue.issue_type_color || '#6b7280'}">
                    <i class="bi ${issue.issue_type_icon || 'bi-bug'}"></i>
                </div>
                <div class="issue-details">
                    <div class="issue-key">${issue.key}</div>
                    <div class="issue-summary">${issue.summary}</div>
                    <div class="issue-meta">
                        <span class="project-name">${issue.project_key}</span>
                        <span class="priority-badge ${issue.priority_name?.toLowerCase()}">${issue.priority_name}</span>
                    </div>
                </div>
                <div class="issue-assignee">
                    ${issue.assignee_avatar ?
                `<img src="${issue.assignee_avatar}" alt="${issue.assignee_name}" title="${issue.assignee_name}">` :
                `<div class="assignee-initials" title="Unassigned">${issue.assignee_name?.charAt(0) || 'U'}</div>`
            }
                </div>
            </div>
        `).join('');

        unscheduledList.innerHTML = issuesHtml;

        // Add drag event listeners
        setupUnscheduledDragEvents();
    }

    function setupUnscheduledDragEvents() {
        // No longer needed - handled by FullCalendar.Draggable
        // kept empty to prevent errors if called
    }

    function handleDragStart(e) {
        const issueElement = e.target.closest('.unscheduled-issue');
        if (!issueElement) return;

        const issueId = issueElement.dataset.issueId;
        const issueKey = issueElement.dataset.issueKey;

        const dragData = {
            id: issueId,
            key: issueKey,
            fromUnscheduled: true
        };

        const dragDataJson = JSON.stringify(dragData);

        // Set multiple formats for maximum compatibility
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', dragDataJson);
        e.dataTransfer.setData('application/json', dragDataJson);

        // Store in global variable as fallback
        window.currentDragData = dragData;

        issueElement.style.opacity = '0.5';
        issueElement.classList.add('dragging');
        console.log('📅 [DRAG] Started dragging unscheduled issue:', issueKey);
        console.log('📅 [DRAG] Drag data set:', dragDataJson);
        console.log('📅 [DRAG] Data types available:', e.dataTransfer.types);
    }

    function handleDragEnd(e) {
        const issueElement = e.target.closest('.unscheduled-issue');
        if (issueElement) {
            issueElement.style.opacity = '';
            issueElement.classList.remove('dragging');
        }

        // Clear global drag data
        window.currentDragData = null;

        console.log('📅 [DRAG] Ended dragging');
    }

    function showUnscheduledError() {
        const unscheduledList = document.getElementById('unscheduledList');
        if (!unscheduledList) return;

        unscheduledList.innerHTML = `
            <div class="error-state">
                <i class="bi bi-exclamation-triangle"></i>
                <p>Failed to load unscheduled issues</p>
                <button class="jira-btn jira-btn-ghost small" onclick="loadUnscheduledIssues()">
                    <i class="bi bi-arrow-clockwise"></i>
                    Retry
                </button>
            </div>
        `;
    }

    // =====================================================
    // SCHEDULE ISSUE MODAL
    // =====================================================

    window.openScheduleModal = function (issueData, dropDate) {
        const modal = document.getElementById('scheduleIssueModal');
        if (!modal) return;

        console.log('📅 [SCHEDULE] Opening schedule modal for:', issueData.key, 'on date:', dropDate);

        // Populate modal with issue data
        document.getElementById('scheduleIssueId').value = issueData.id;
        document.getElementById('scheduleIssueKey').textContent = issueData.key;
        document.getElementById('scheduleIssueSummary').textContent = issueData.summary;
        document.getElementById('scheduleDueDate').value = dropDate;
        document.getElementById('scheduleStartDate').value = '';
        document.getElementById('scheduleProjectName').textContent = issueData.project_key;

        // Set issue type
        const issueTypeElement = document.getElementById('scheduleIssueType');
        issueTypeElement.innerHTML = `
            <i class="bi ${issueData.issue_type_icon || 'bi-bug'}"></i>
            ${issueData.issue_type || 'Issue'}
        `;
        issueTypeElement.style.backgroundColor = issueData.issue_type_color || '#6b7280';

        // Set priority
        const priorityElement = document.getElementById('schedulePriority');
        priorityElement.textContent = issueData.priority_name;
        priorityElement.className = `priority-badge ${issueData.priority_name?.toLowerCase()}`;

        // Set assignee
        const assigneeElement = document.getElementById('scheduleAssignee');
        if (issueData.assignee_avatar && issueData.assignee_name) {
            assigneeElement.innerHTML = `
                <img class="assignee-avatar" src="${issueData.assignee_avatar}" alt="${issueData.assignee_name}">
                <span>${issueData.assignee_name}</span>
            `;
        } else {
            assigneeElement.innerHTML = `
                <div class="assignee-initials">U</div>
                <span>Unassigned</span>
            `;
        }

        // Set created date
        if (issueData.created_at) {
            const createdDate = new Date(issueData.created_at);
            document.getElementById('scheduleCreatedDate').textContent = createdDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        // Show modal
        modal.style.display = 'flex';
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        document.body.style.top = `-${window.scrollY}px`;
    };

    window.closeScheduleModal = function () {
        const modal = document.getElementById('scheduleIssueModal');
        if (!modal) return;

        modal.style.display = 'none';
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');

        // Restore body scroll
        const scrollY = document.body.style.top;
        document.body.style.overflow = 'auto';
        document.body.style.position = 'static';
        document.body.style.width = 'auto';
        document.body.style.top = '';

        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }
    };

    window.saveScheduledIssue = function () {
        const issueId = document.getElementById('scheduleIssueId').value;
        const dueDate = document.getElementById('scheduleDueDate').value;
        const startDate = document.getElementById('scheduleStartDate').value;

        if (!issueId || !dueDate) {
            alert('Due date is required');
            return;
        }

        const btn = document.querySelector('#scheduleIssueModal .modal-footer .jira-btn-primary');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Scheduling...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('issue_id', issueId);
        formData.append('due_date', dueDate);
        if (startDate) {
            formData.append('start_date', startDate);
        }

        fetch(`${window.JiraConfig.apiBase}/calendar/schedule-issue`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.JiraConfig.csrfToken
            },
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('📅 [SCHEDULE] Issue scheduled successfully');
                    closeScheduleModal();

                    // Remove from unscheduled list
                    unscheduledIssues = unscheduledIssues.filter(issue => issue.id != issueId);
                    renderUnscheduledIssues();

                    // Refresh calendar
                    if (calendar) {
                        calendar.refetchEvents();
                    }

                    // Show success message
                    showNotification('Issue scheduled successfully!', 'success');
                } else {
                    alert(data.error || 'Failed to schedule issue');
                }
            })
            .catch(err => {
                console.error('❌ [SCHEDULE] Error scheduling issue:', err);
                alert('Failed to schedule issue. Please try again.');
            })
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
    };

    window.viewIssueDetails = function () {
        const issueKey = document.getElementById('scheduleIssueKey').textContent;
        if (!issueKey) return;

        window.open(`${window.JiraConfig.webBase}/issues/${issueKey}`, '_blank');
    };

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-info-circle'}"></i>
            ${message}
        `;

        // Add to page
        document.body.appendChild(notification);

        // Show animation
        setTimeout(() => notification.classList.add('show'), 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // =====================================================
    // CALENDAR DEBUG FUNCTIONS
    // =====================================================

    window.debugCalendarDrop = function () {
        console.log('📅 [DEBUG] Calendar Debug Info:');
        console.log('📅 [DEBUG] Calendar object exists:', !!calendar);
        if (calendar) {
            console.log('📅 [DEBUG] Calendar options:', calendar.getOption('droppable'));
            console.log('📅 [DEBUG] Calendar element:', calendar.el);
        }
        console.log('📅 [DEBUG] Unscheduled issues:', unscheduledIssues.length);

        // Test drop directly
        if (calendar) {
            const testDragData = {
                fromUnscheduled: true,
                id: '37',
                key: 'DEVOPS-4'
            };
            const mockDropEvent = {
                dateStr: '2025-12-30',
                jsEvent: {
                    dataTransfer: {
                        getData: (type) => {
                            const data = type === 'text/plain' ? JSON.stringify(testDragData) : null;
                            console.log('📅 [DEBUG] getData called for type:', type, 'returning:', data);
                            return data;
                        },
                        types: ['text/plain', 'application/json']
                    }
                }
            };

            console.log('📅 [DEBUG] Simulating drop event...');
            try {
                handleCalendarDrop(mockDropEvent);
                console.log('📅 [DEBUG] Drop event triggered successfully');
            } catch (err) {
                console.error('📅 [DEBUG] Error triggering drop:', err);
            }
        }
    };

    // Test drag data setting
    window.testDragData = function () {
        console.log('📅 [TEST] Testing drag data setup...');

        const unscheduledElement = document.querySelector('.unscheduled-issue');
        if (unscheduledElement) {
            console.log('📅 [TEST] Found unscheduled issue element, simulating drag start...');

            // Simulate dragstart event
            const dragStartEvent = new DragEvent('dragstart', {
                bubbles: true,
                cancelable: true,
                dataTransfer: new DataTransfer()
            });

            // Set drag data
            const testData = {
                id: '37',
                key: 'DEVOPS-4',
                fromUnscheduled: true
            };

            dragStartEvent.dataTransfer.setData('text/plain', JSON.stringify(testData));
            dragStartEvent.dataTransfer.setData('application/json', JSON.stringify(testData));

            // Store globally
            window.currentDragData = testData;

            // Trigger event
            unscheduledElement.dispatchEvent(dragStartEvent);

            console.log('📅 [TEST] Drag start simulated, data should be set');
            console.log('📅 [TEST] Global drag data:', window.currentDragData);
        } else {
            console.log('📅 [TEST] No unscheduled issue element found');
        }
    };


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
    loadUnscheduledIssues();
    initExternalDraggable();

    console.log('📅 [CALENDAR] All startup tasks completed');
});

// =====================================================
// EXTERNAL DRAGGABLE INIT
// =====================================================

function initExternalDraggable() {
    const containerEl = document.getElementById('unscheduledList');
    if (!containerEl) {
        console.log('📅 [DRAG] Unscheduled list container not found');
        return;
    }

    if (containerEl._fcDraggable) return;

    console.log('📅 [DRAG] Initializing FullCalendar Draggable...');

    try {
        new FullCalendar.Draggable(containerEl, {
            itemSelector: '.unscheduled-issue',
            eventData: function (eventEl) {
                return {
                    title: eventEl.querySelector('.issue-key')?.innerText || 'Issue',
                };
            }
        });
        containerEl._fcDraggable = true;
        console.log('📅 [DRAG] Draggable initialized successfully');
    } catch (err) {
        console.error('❌ [DRAG] Failed to initialize FullCalendar Draggable:', err);
    }
}
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
