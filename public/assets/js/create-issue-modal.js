/**
 * Create Issue Modal - Global JavaScript Handler
 * ✅ Single modal for entire application (not per-page)
 * ✅ Deployment-aware URLs (works in any directory)
 * ✅ No hardcoded paths
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    console.log('📍 [CREATE-ISSUE-MODAL] Initializing global create issue modal...');

    // Get the modal element (defined in components/create-issue-modal.php)
    const createIssueModal = document.getElementById('createIssueModal');

    // Early return if modal doesn't exist
    if (!createIssueModal) {
        console.error('❌ Create Issue Modal (#createIssueModal) not found in DOM');
        return;
    }

    console.log('✅ Create Issue Modal found in DOM');

    // Initialize Bootstrap modal
    let modal = null;

    try {
        modal = new bootstrap.Modal(createIssueModal, {
            keyboard: true,
            backdrop: true,
            focus: true
        });
        console.log('✅ Bootstrap Modal instance created');
    } catch (error) {
        console.error('❌ Failed to create Bootstrap Modal instance:', error);
        return;
    }

    /**
     * Get deployment-aware base URL from meta tag
     */
    function getBasePath() {
        const meta = document.querySelector('meta[name="app-base-path"]');
        if (meta) {
            const path = meta.getAttribute('content');
            console.log('📍 Base path from meta tag:', path);
            return path;
        }

        // Fallback: calculate from current location
        const pathName = window.location.pathname;
        const match = pathName.match(/^(.+?)\/(?:projects|issues|dashboard|search|calendar|roadmap|admin|profile)/);
        const fallback = match ? match[1] : '';
        console.log('📍 Base path calculated from URL:', fallback);
        return fallback;
    }

    /**
     * Get deployment-aware API URL
     */
    function getApiUrl(endpoint) {
        const basePath = getBasePath();
        return basePath + endpoint;
    }

    /**
     * Get CSRF token from meta tag
     */
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Load dropdown data for modal (projects, issue types, users)
     */
    async function loadCreateIssueModalData() {
        console.log('🔄 [CREATE-ISSUE-MODAL] Loading modal data...');

        try {
            const basePath = getBasePath();
            const projectsUrl = getApiUrl('/projects/quick-create-list');
            const usersUrl = getApiUrl('/users/active');
            const issueTypesUrl = getApiUrl('/api/v1/issue-types');

            console.log('📍 API URLs:', { projectsUrl, usersUrl, issueTypesUrl });

            // Load projects
            console.log('🔄 Fetching projects...');
            try {
                const projectsResponse = await fetch(projectsUrl);
                if (projectsResponse.ok) {
                    const projects = await projectsResponse.json();
                    console.log('✅ Projects loaded:', projects);
                    populateProjectDropdown(projects);
                } else {
                    console.error('❌ Failed to load projects. Status:', projectsResponse.status);
                }
            } catch (error) {
                console.error('❌ Error fetching projects:', error);
            }

            // Load active users for assignee dropdown
            console.log('🔄 Fetching users...');
            try {
                const usersResponse = await fetch(usersUrl);
                if (usersResponse.ok) {
                    const users = await usersResponse.json();
                    console.log('✅ Users loaded:', users);
                    populateAssigneeDropdown(users);
                } else {
                    console.error('❌ Failed to load users. Status:', usersResponse.status);
                }
            } catch (error) {
                console.error('❌ Error fetching users:', error);
            }

            // Load issue types globally
            console.log('🔄 Loading issue types...');
            await loadIssueTypesForProject();

            // Load priorities
            console.log('🔄 Loading priorities...');
            await loadPriorityOptions();

            console.log('✅ Modal data loaded successfully');
        } catch (error) {
            console.error('❌ Error loading modal data:', error);
        }
    }

    /**
     * Populate project dropdown with options
     */
    function populateProjectDropdown(projects) {
        const projectSelect = document.getElementById('issueProject');
        if (!projectSelect) {
            console.warn('⚠️ Project dropdown (#issueProject) not found');
            return;
        }

        projectSelect.innerHTML = '<option value="">Select a project</option>';

        if (Array.isArray(projects)) {
            projects.forEach(project => {
                const option = document.createElement('option');
                option.value = project.id;
                option.dataset.projectKey = project.key;
                option.textContent = `${project.name} (${project.key})`;
                projectSelect.appendChild(option);
            });
            console.log(`✅ Populated ${projects.length} projects`);
        } else {
            console.warn('⚠️ Projects data is not an array');
        }
    }

    /**
     * Populate assignee dropdown with users
     */
    function populateAssigneeDropdown(users) {
        const assigneeSelect = document.getElementById('issueAssignee');
        if (!assigneeSelect) {
            console.warn('⚠️ Assignee dropdown (#issueAssignee) not found');
            return;
        }

        assigneeSelect.innerHTML = '<option value="">Automatic</option>';

        if (Array.isArray(users)) {
            users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.display_name || user.name;
                assigneeSelect.appendChild(option);
            });
            console.log(`✅ Populated ${users.length} users`);
        } else {
            console.warn('⚠️ Users data is not an array');
        }
    }

    /**
     * Load issue types globally
     */
    async function loadIssueTypesForProject() {
        const issueTypeSelect = document.getElementById('issueType');
        if (!issueTypeSelect) {
            console.warn('⚠️ Issue type dropdown (#issueType) not found');
            return;
        }

        try {
            const issueTypesUrl = getApiUrl('/api/v1/issue-types');
            console.log('🔄 Fetching issue types from:', issueTypesUrl);

            const response = await fetch(issueTypesUrl);
            if (response.ok) {
                const data = await response.json();
                console.log('✅ Issue types loaded:', data);
                const issueTypes = Array.isArray(data) ? data : (data.data || data.issue_types || []);
                populateIssueTypeDropdown(issueTypes);
            } else {
                console.error('❌ Failed to load issue types. Status:', response.status);
            }
        } catch (error) {
            console.error('❌ Error loading issue types:', error);
        }
    }

    /**
     * Populate issue type dropdown
     */
    function populateIssueTypeDropdown(issueTypes) {
        const issueTypeSelect = document.getElementById('issueType');
        if (!issueTypeSelect) return;

        issueTypeSelect.innerHTML = '<option value="">Select issue type</option>';

        if (Array.isArray(issueTypes) && issueTypes.length > 0) {
            issueTypes.forEach(type => {
                const option = document.createElement('option');
                option.value = type.id;
                option.textContent = type.name;
                issueTypeSelect.appendChild(option);
            });
            console.log(`✅ Populated ${issueTypes.length} issue types`);
        } else {
            console.warn('⚠️ No issue types available');
        }
    }

    /**
     * Load priority options
     */
    async function loadPriorityOptions() {
        const prioritySelect = document.getElementById('issuePriority');
        if (!prioritySelect) {
            console.warn('⚠️ Priority dropdown (#issuePriority) not found');
            return;
        }

        try {
            const prioritiesUrl = getApiUrl('/api/v1/priorities');
            console.log('🔄 Fetching priorities from:', prioritiesUrl);

            const response = await fetch(prioritiesUrl);
            if (response.ok) {
                const priorities = await response.json();
                populatePriorityDropdown(priorities);
            } else {
                console.error('❌ Failed to load priorities. Status:', response.status);
            }
        } catch (error) {
            console.error('❌ Error loading priorities:', error);
        }
    }

    /**
     * Populate priority dropdown
     */
    function populatePriorityDropdown(priorities) {
        const prioritySelect = document.getElementById('issuePriority');
        if (!prioritySelect) return;

        prioritySelect.innerHTML = '<option value="">Select priority</option>';

        if (Array.isArray(priorities)) {
            priorities.forEach(priority => {
                const option = document.createElement('option');
                option.value = priority.id;
                option.textContent = priority.name;
                prioritySelect.appendChild(option);
            });
            console.log(`✅ Populated ${priorities.length} priorities`);
        }
    }

    /**
     * Setup project change handler
     */
    function setupProjectChangeHandler() {
        const projectSelect = document.getElementById('issueProject');
        if (!projectSelect) return;

        projectSelect.addEventListener('change', function () {
            const projectId = this.value;
            console.log(`📁 Project changed to ID: ${projectId}`);

            if (projectId) {
                loadIssueTypesForProject();
            } else {
                const issueTypeSelect = document.getElementById('issueType');
                if (issueTypeSelect) {
                    issueTypeSelect.innerHTML = '<option value="">Select a project first</option>';
                }
            }
        });
    }

    /**
     * Submit issue form via AJAX
     */
    async function submitCreateIssueForm() {
        const form = document.getElementById('createIssueForm');
        if (!form) {
            console.error('❌ Create Issue Form not found');
            return;
        }

        try {
            // Get form values
            const projectSelect = document.getElementById('issueProject');
            const projectId = projectSelect.value;
            const projectKey = projectSelect.options[projectSelect.selectedIndex].dataset.projectKey;
            const issueTypeId = document.getElementById('issueType').value;
            const summary = document.getElementById('issueSummary').value;
            const description = document.getElementById('issueDescription').value || '';
            const assigneeId = document.getElementById('issueAssignee').value;
            const priorityId = document.getElementById('issuePriority').value;

            // Validate required fields
            if (!projectId || !issueTypeId || !summary.trim()) {
                alert('⚠️ Please fill in all required fields (Project, Issue Type, Summary)');
                console.warn('⚠️ Validation failed:', { projectId, issueTypeId, summary });
                return;
            }

            if (!projectKey) {
                alert('⚠️ Unable to determine project key');
                console.error('❌ No project key found in selected option');
                return;
            }

            const submitBtn = document.getElementById('createIssueBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
            }

            const basePath = getBasePath();
            // ✅ Use correct endpoint: POST /issues/store (not /projects/{key}/issues)
            const endpoint = basePath + '/issues/store';

            console.log('📤 [CREATE-ISSUE-MODAL] Submitting issue to:', endpoint);
            console.log('📋 [CREATE-ISSUE-MODAL] Form data:', { 
                projectId, 
                projectKey, 
                issueTypeId, 
                summary, 
                assigneeId, 
                priorityId 
            });

            const requestBody = {
                project_id: parseInt(projectId),
                issue_type_id: parseInt(issueTypeId),
                summary: summary.trim(),
                description: description,
                assignee_id: assigneeId ? parseInt(assigneeId) : null,
                priority_id: priorityId ? parseInt(priorityId) : null
            };

            console.log('📦 [CREATE-ISSUE-MODAL] Request body:', requestBody);

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify(requestBody),
                credentials: 'include'
            });

            console.log('📡 [CREATE-ISSUE-MODAL] Response status:', response.status);

            // Check content type before parsing JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const errorText = await response.text();
                console.error('❌ [CREATE-ISSUE-MODAL] Non-JSON response:', errorText.substring(0, 500));
                alert('❌ Server error. Please check console and try again.');
                return;
            }

            const result = await response.json();
            console.log('📊 [CREATE-ISSUE-MODAL] API Response:', result);

            if (response.ok && result.success) {
                console.log('✅ [CREATE-ISSUE-MODAL] Issue created successfully:', result);
                alert(`✅ Issue ${result.issue_key} created successfully!`);
                
                // Close modal
                modal.hide();
                
                // Reset form
                form.reset();
                
                // Reload page after delay
                setTimeout(() => {
                    window.location.href = basePath + '/projects/' + projectKey + '/board';
                }, 1000);
            } else {
                // Handle error response
                const errorMessage = result.error || result.message || 'Failed to create issue';
                console.error('❌ [CREATE-ISSUE-MODAL] API Error:', errorMessage);
                console.error('❌ [CREATE-ISSUE-MODAL] Full response:', result);
                alert('❌ ' + errorMessage);
            }

        } catch (error) {
            console.error('❌ [CREATE-ISSUE-MODAL] Error submitting form:', error);
            console.error('❌ [CREATE-ISSUE-MODAL] Stack trace:', error.stack);
            alert('❌ Network error. Please check console and try again.');
        } finally {
            const submitBtn = document.getElementById('createIssueBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create';
            }
        }
    }

    /**
     * Initialize all handlers
     */
    function initialize() {
        console.log('🔧 [CREATE-ISSUE-MODAL] Setting up handlers...');

        // ✅ CRITICAL: Attach click handler to navbar Create button
        const openModalBtn = document.getElementById('openCreateIssueModal');
        if (openModalBtn) {
            openModalBtn.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                console.log('🔘 [CREATE-ISSUE-MODAL] Navbar Create button clicked');
                modal.show();
                // Load data when modal opens
                loadCreateIssueModalData();
            });
            console.log('✅ Navbar Create button (#openCreateIssueModal) click handler attached');
        } else {
            console.error('❌ Navbar Create button (#openCreateIssueModal) not found');
        }

        // Setup project change handler
        setupProjectChangeHandler();

        // Attach submit button handler (inside modal)
        const createIssueBtn = document.getElementById('createIssueBtn');
        if (createIssueBtn) {
            createIssueBtn.addEventListener('click', function (event) {
                event.preventDefault();
                console.log('🔘 [CREATE-ISSUE-MODAL] Modal submit button clicked');
                submitCreateIssueForm();
            });
            console.log('✅ Modal submit button (#createIssueBtn) click handler attached');
        } else {
            console.error('❌ Modal submit button (#createIssueBtn) not found');
        }

        // Load data when modal opens
        createIssueModal.addEventListener('show.bs.modal', function () {
            console.log('📖 [CREATE-ISSUE-MODAL] Modal opening - loading data');
            loadCreateIssueModalData();
        });

        createIssueModal.addEventListener('shown.bs.modal', function () {
            console.log('✅ [CREATE-ISSUE-MODAL] Modal fully shown and ready');
        });

        // Expose globally for potential future use
        window.CreateIssueModal = {
            modal: modal,
            open: function () { 
                console.log('🔘 Opening modal programmatically');
                modal?.show(); 
            },
            close: function () { 
                console.log('❌ Closing modal programmatically');
                modal?.hide(); 
            },
            loadData: loadCreateIssueModalData,
            submit: submitCreateIssueForm,
            getBasePath: getBasePath
        };

        console.log('✅ [CREATE-ISSUE-MODAL] Create Issue Modal fully initialized');
        console.log('📍 [CREATE-ISSUE-MODAL] Base path:', getBasePath());
        console.log('📍 [CREATE-ISSUE-MODAL] Modal ID: createIssueModal');
        console.log('📍 [CREATE-ISSUE-MODAL] Open button ID: openCreateIssueModal');
    }

    // Run initialization
    initialize();
});
