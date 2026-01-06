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
        const projectSelect = document.getElementById('global-modal-issueProject');
        if (!projectSelect) {
            console.warn('⚠️ Project dropdown (#global-modal-issueProject) not found');
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
        const assigneeSelect = document.getElementById('global-modal-issueAssignee');
        if (!assigneeSelect) {
            console.warn('⚠️ Assignee dropdown (#global-modal-issueAssignee) not found');
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
     * Populate reporter dropdown with users
     */


    /**
     * Load issue types globally
     */
    async function loadIssueTypesForProject() {
        const issueTypeSelect = document.getElementById('global-modal-issueType');
        if (!issueTypeSelect) {
            console.warn('⚠️ Issue type dropdown (#global-modal-issueType) not found');
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
        const issueTypeSelect = document.getElementById('global-modal-issueType');
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
        const prioritySelect = document.getElementById('global-modal-issuePriority');
        if (!prioritySelect) {
            console.warn('⚠️ Priority dropdown (#global-modal-issuePriority) not found');
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
        const prioritySelect = document.getElementById('global-modal-issuePriority');
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
        const projectSelect = document.getElementById('global-modal-issueProject');
        if (!projectSelect) return;

        projectSelect.addEventListener('change', function () {
            const projectId = this.value;
            console.log(`📁 Project changed to ID: ${projectId}`);

            if (projectId) {
                loadIssueTypesForProject();
            } else {
                const issueTypeSelect = document.getElementById('global-modal-issueType');
                if (issueTypeSelect) {
                    issueTypeSelect.innerHTML = '<option value="">Select a project first</option>';
                }
            }
        });
    }

    /**
     * Submit issue form via AJAX
     */
    /**
     * Submit issue form via AJAX
     */
    async function submitCreateIssueForm() {
        const form = document.getElementById('global-modal-createIssueForm');
        if (!form) {
            console.error('❌ Create Issue Form not found');
            return;
        }

        try {
            // Get form values
            const projectSelect = document.getElementById('global-modal-issueProject');
            const projectId = projectSelect.value;
            const projectKey = projectSelect.options[projectSelect.selectedIndex].dataset.projectKey;
            const issueTypeId = document.getElementById('global-modal-issueType').value;
            const summary = document.getElementById('global-modal-issueSummary').value;

            // Sync TinyMCE content to textarea
            if (typeof tinymce !== 'undefined' && tinymce.get('global-modal-issueDescription')) {
                tinymce.get('global-modal-issueDescription').save();
            }

            const description = document.getElementById('global-modal-issueDescription').value || '';
            const assigneeId = document.getElementById('global-modal-issueAssignee').value;
            const priorityId = document.getElementById('global-modal-issuePriority').value;
            const startDate = document.getElementById('global-modal-issueStartDate').value;
            const endDate = document.getElementById('global-modal-issueEndDate').value;

            // Validate dates
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                alert('⚠️ End Date cannot be before Start Date');
                return;
            }

            // Validate required fields
            if (!projectId || !issueTypeId || !summary.trim()) {
                alert('⚠️ Please fill in all required fields (Project, Issue Type, Summary)');
                console.warn('⚠️ Validation failed:', { projectId, issueTypeId, summary });
                return;
            }

            const submitBtn = document.getElementById('global-modal-createIssueBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }

            const basePath = getBasePath();
            const csrfToken = getCsrfToken();

            if (isEditMode) {
                // ---------------------------------------------------------
                // EDIT MODE (PUT /api/v1/issues/{key})
                // ---------------------------------------------------------
                const endpoint = getApiUrl(`/api/v1/issues/${currentIssueKey}`);
                console.log('✏️ Checkpoint: Updating issue via API:', endpoint);

                const payload = {
                    summary: summary.trim(),
                    description: description,
                    issue_type_id: parseInt(issueTypeId),
                    project_id: parseInt(projectId), // Shouldn't really change but required for validation sometimes
                    assignee_id: assigneeId ? parseInt(assigneeId) : null,
                    priority_id: priorityId ? parseInt(priorityId) : null,
                    start_date: startDate || null,
                    end_date: endDate || null
                };

                // Update Issue Details
                const response = await fetch(endpoint, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Failed to update issue');
                }

                console.log('✅ Issue updated successfully');

                // Handle Attachments (Upload separately)
                if (selectedFiles.size > 0) {
                    console.log(`📎 Uploading ${selectedFiles.size} new attachments...`);
                    const attachEndpoint = getApiUrl(`/api/v1/issues/${currentIssueKey}/attachments`);

                    for (const file of selectedFiles) {
                        const formData = new FormData();
                        formData.append('file', file);

                        await fetch(attachEndpoint, {
                            method: 'POST',
                            headers: { 'X-CSRF-Token': csrfToken },
                            body: formData
                        });
                    }
                }

                alert('✅ Issue updated successfully!');
                modal.hide();
                window.location.reload();

            } else {
                // ---------------------------------------------------------
                // CREATE MODE (POST /issues/store)
                // ---------------------------------------------------------
                // ✅ Use correct endpoint: POST /issues/store (not /projects/{key}/issues)
                const endpoint = basePath + '/issues/store';

                console.log('📤 [CREATE-ISSUE-MODAL] Submitting issue to:', endpoint);

                // Create FormData object to handle files + validation data
                const formData = new FormData();
                formData.append('project_id', parseInt(projectId));
                formData.append('issue_type_id', parseInt(issueTypeId));
                formData.append('summary', summary.trim());
                formData.append('description', description);

                if (assigneeId) formData.append('assignee_id', parseInt(assigneeId));
                if (priorityId) formData.append('priority_id', parseInt(priorityId));
                if (startDate) formData.append('start_date', startDate);
                if (endDate) formData.append('end_date', endDate);

                // Append input files
                if (selectedFiles.size > 0) {
                    console.log(`📎 Appending ${selectedFiles.size} files...`);
                    selectedFiles.forEach(file => {
                        formData.append('attachments[]', file);
                    });
                }

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-Token': csrfToken
                        // Content-Type must be undefined for FormData
                    },
                    body: formData,
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
                    selectedFiles.clear(); // Clear files
                    updateFilePreview();

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
            }

        } catch (error) {
            console.error('❌ [CREATE-ISSUE-MODAL] Error submitting form:', error);
            console.error('❌ [CREATE-ISSUE-MODAL] Stack trace:', error.stack);
            alert('❌ ' + (error.message || 'Network error. Please check console.'));
        } finally {
            const submitBtn = document.getElementById('global-modal-createIssueBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = isEditMode ? 'Update Issue' : 'Create';
            }
        }
    }


    // File storage for attachments
    const selectedFiles = new Set();

    /**
     * Initialize all handlers
     */
    function initialize() {
        console.log('🔧 [CREATE-ISSUE-MODAL] Setting up handlers...');

        // Setup Drag & Drop
        setupDragAndDrop();

        // ✅ CRITICAL: Attach click handler to ALL triggers (class-based)
        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('.open-create-issue-modal, #openCreateIssueModal');
            if (trigger) {
                event.preventDefault();
                event.stopPropagation();
                console.log('🔘 [CREATE-ISSUE-MODAL] Trigger button clicked');

                // Get pre-defined data from trigger
                const preProjectId = trigger.dataset.projectId;
                const preProjectKey = trigger.dataset.projectKey;

                modal.show();

                // Load data when modal opens
                loadCreateIssueModalData().then(() => {
                    // Pre-select project if specified
                    if (preProjectId || preProjectKey) {
                        const projectSelect = document.getElementById('global-modal-issueProject');
                        if (projectSelect) {
                            if (preProjectId) {
                                projectSelect.value = preProjectId;
                            } else if (preProjectKey) {
                                // Find option with matching key
                                const option = Array.from(projectSelect.options).find(opt => opt.dataset.projectKey === preProjectKey);
                                if (option) projectSelect.value = option.value;
                            }

                            // Trigger change event to load issue types
                            projectSelect.dispatchEvent(new Event('change'));
                        }
                    }
                });
            }
        });
        console.log('✅ Global click listener attached for .open-create-issue-modal');

        // Setup project change handler
        setupProjectChangeHandler();

        // Attach submit button handler (inside modal)
        const createIssueBtn = document.getElementById('global-modal-createIssueBtn');
        if (createIssueBtn) {
            createIssueBtn.addEventListener('click', function (event) {
                event.preventDefault();
                console.log('🔘 [CREATE-ISSUE-MODAL] Modal submit button clicked');
                submitCreateIssueForm();
            });
            console.log('✅ Modal submit button (#global-modal-createIssueBtn) click handler attached');
        } else {
            console.error('❌ Modal submit button (#global-modal-createIssueBtn) not found');
        }

        // Load data when modal opens
        createIssueModal.addEventListener('show.bs.modal', function () {
            console.log('📖 [CREATE-ISSUE-MODAL] Modal opening - loading data');
            loadCreateIssueModalData();

            // Clear selected files
            selectedFiles.clear();
            updateFilePreview();

            // Initialize TinyMCE
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#global-modal-issueDescription',
                    height: 300,
                    menubar: false,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                        'bold italic backcolor | alignleft aligncenter ' +
                        'alignright alignjustify | bullist numlist outdent indent | ' +
                        'removeformat | uploadimage uploadfile | help',
                    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',

                    // Enable local file uploads
                    image_title: true,
                    automatic_uploads: true,
                    file_picker_types: 'image',
                    file_picker_callback: function (cb, value, meta) {
                        var input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');

                        input.onchange = function () {
                            var file = this.files[0];

                            var reader = new FileReader();
                            reader.onload = function () {
                                var id = 'blobid' + (new Date()).getTime();
                                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                var base64 = reader.result.split(',')[1];
                                var blobInfo = blobCache.create(id, file, base64);
                                blobCache.add(blobInfo);

                                /* call the callback and populate the Title field with the file name */
                                cb(blobInfo.blobUri(), { title: file.name });
                            };
                            reader.readAsDataURL(file);
                        };

                        input.click();
                    },

                    setup: function (editor) {
                        editor.ui.registry.addButton('uploadfile', {
                            icon: 'document-properties', // Use a document icon
                            tooltip: 'Attach File',
                            onAction: function () {
                                var input = document.createElement('input');
                                input.setAttribute('type', 'file');
                                input.setAttribute('accept', '*/*'); // Accept all files

                                input.onchange = function () {
                                    var file = this.files[0];
                                    var reader = new FileReader();
                                    reader.onload = function () {
                                        var base64 = reader.result; // Full data URI

                                        // Insert link directly
                                        editor.insertContent('<a href="' + base64 + '" download="' + file.name + '" target="_blank">' + file.name + '</a>&nbsp;');
                                    };
                                    reader.readAsDataURL(file);
                                };
                                input.click();
                            }
                        });


                        editor.ui.registry.addButton('uploadimage', {
                            icon: 'image',
                            tooltip: 'Upload Image',
                            onAction: function () {
                                var input = document.createElement('input');
                                input.setAttribute('type', 'file');
                                input.setAttribute('accept', 'image/*');

                                input.onchange = function () {
                                    var file = this.files[0];
                                    var reader = new FileReader();
                                    reader.onload = function () {
                                        var id = 'blobid' + (new Date()).getTime();
                                        var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                                        var base64 = reader.result.split(',')[1];
                                        var blobInfo = blobCache.create(id, file, base64);
                                        blobCache.add(blobInfo);

                                        // Insert content directly
                                        editor.insertContent('<img src="' + blobInfo.blobUri() + '" alt="' + file.name + '"/>');
                                    };
                                    reader.readAsDataURL(file);
                                };
                                input.click();
                            }
                        });

                        editor.on('change', function () {
                            editor.save(); // Sync content to textarea
                        });
                    }
                });
            }
        });

        // Destroy TinyMCE when modal closes to prevent issues
        createIssueModal.addEventListener('hidden.bs.modal', function () {
            if (typeof tinymce !== 'undefined') {
                tinymce.remove('#global-modal-issueDescription');
            }
        });

        // Bootstrap modal focus fix for TinyMCE
        document.addEventListener('focusin', function (e) {
            if (e.target.closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root') !== null) {
                e.stopImmediatePropagation();
            }
        });

        createIssueModal.addEventListener('shown.bs.modal', function () {
            console.log('✅ [CREATE-ISSUE-MODAL] Modal fully shown and ready');
        });

        // Expose globally for potential future use
        // State variables
        let isEditMode = false;
        let currentIssueKey = null;
        let currentIssueId = null;

        window.CreateIssueModal = {
            modal: modal,
            open: function () {
                console.log('🔘 Opening modal programmatically');
                resetModal(); // Ensure clean state
                modal?.show();
            },
            openEdit: async function (issueKey) {
                console.log('✏️ Opening modal in EDIT mode for:', issueKey);
                resetModal();
                isEditMode = true;
                currentIssueKey = issueKey;

                // Show modal immediately with loading state if desired, or wait for data
                const modalTitle = document.getElementById('createIssueModalLabel');
                const submitBtn = document.getElementById('global-modal-createIssueBtn');

                if (modalTitle) modalTitle.textContent = `Edit Issue: ${issueKey}`;
                if (submitBtn) submitBtn.textContent = 'Update Issue';

                modal?.show();

                // Fetch and populate data
                await loadIssueData(issueKey);
            },
            close: function () {
                console.log('❌ Closing modal programmatically');
                modal?.hide();
            },
            loadData: loadCreateIssueModalData,
            submit: submitCreateIssueForm,
            getBasePath: getBasePath
        };

        /**
         * Reset modal state
         */
        function resetModal() {
            isEditMode = false;
            currentIssueKey = null;
            currentIssueId = null;

            const form = document.getElementById('global-modal-createIssueForm');
            if (form) form.reset();

            const modalTitle = document.getElementById('createIssueModalLabel');
            const submitBtn = document.getElementById('global-modal-createIssueBtn');

            if (modalTitle) modalTitle.textContent = 'Create Issue';
            if (submitBtn) submitBtn.textContent = 'Create';

            selectedFiles.clear();
            updateFilePreview();

            // Clear TinyMCE
            if (typeof tinymce !== 'undefined' && tinymce.get('global-modal-issueDescription')) {
                tinymce.get('global-modal-issueDescription').setContent('');
            }
        }

        /**
         * Load Issue Data for Editing
         */
        async function loadIssueData(issueKey) {
            const apiUrl = getApiUrl(`/api/v1/issues/${issueKey}`);
            try {
                const response = await fetch(apiUrl);
                if (!response.ok) throw new Error('Failed to fetch issue details');

                const data = await response.json();
                const issue = data.issue;
                currentIssueId = issue.id;

                console.log('📥 Loaded issue data:', issue);

                // Populate Fields

                // 1. Project (Wait for dropdown to load first if empty)
                const projectSelect = document.getElementById('global-modal-issueProject');
                if (projectSelect.options.length <= 1) {
                    await loadCreateIssueModalData();
                }
                projectSelect.value = issue.project_id;
                projectSelect.dispatchEvent(new Event('change')); // Trigger listener to load issue types

                // 2. Issue Type (Wait for issue types to load)
                // We need to wait a bit for the project change event to fetch issue types
                // Or we can manually trigger the fetch
                await loadIssueTypesForProject();

                // Small delay to ensure DOM update or promise resolution if loadIssueTypesForProject doesn't await DOM
                setTimeout(() => {
                    const typeSelect = document.getElementById('global-modal-issueType');
                    if (typeSelect) typeSelect.value = issue.issue_type_id;
                }, 500);

                // 3. Summary
                document.getElementById('global-modal-issueSummary').value = issue.summary;

                // 4. Description (TinyMCE)
                if (typeof tinymce !== 'undefined' && tinymce.get('global-modal-issueDescription')) {
                    tinymce.get('global-modal-issueDescription').setContent(issue.description || '');
                } else {
                    document.getElementById('global-modal-issueDescription').value = issue.description || '';
                }

                // 5. Assignee (Wait for users to load)
                // Checks if users are loaded, if not wait/reload
                const assigneeSelect = document.getElementById('global-modal-issueAssignee');
                if (assigneeSelect.options.length <= 1) {
                    // Assume loadCreateIssueModalData called fetchUsers
                }
                assigneeSelect.value = issue.assignee_id || '';

                // 6. Priority
                const prioritySelect = document.getElementById('global-modal-issuePriority');
                prioritySelect.value = issue.priority_id || '';

                // 7. Dates
                document.getElementById('global-modal-issueStartDate').value = issue.start_date || '';
                document.getElementById('global-modal-issueEndDate').value = issue.end_date || '';

            } catch (error) {
                console.error('❌ Error loading issue data:', error);
                alert('Failed to load issue details. Please try again.');
                modal.hide();
            }
        }


        console.log('✅ [CREATE-ISSUE-MODAL] Create Issue Modal fully initialized');
        console.log('📍 [CREATE-ISSUE-MODAL] Base path:', getBasePath());
        console.log('📍 [CREATE-ISSUE-MODAL] Modal ID: createIssueModal');
        console.log('📍 [CREATE-ISSUE-MODAL] Open button ID: openCreateIssueModal');
    }

    /**
     * Setup Drag & Drop Handlers
     */
    function setupDragAndDrop() {
        const dropZone = document.getElementById('global-modal-uploadZone');
        const fileInput = document.getElementById('global-modal-fileInput');

        if (!dropZone || !fileInput) {
            console.warn('⚠️ Drag & Drop zone not found');
            return;
        }

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        // Highlight drop zone when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        // Handle dropped files
        dropZone.addEventListener('drop', handleDrop, false);

        // Handle click to upload
        dropZone.addEventListener('click', () => fileInput.click());

        // Handle file input change
        fileInput.addEventListener('change', function () {
            handleFiles(this.files);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        function highlight() {
            dropZone.classList.add('dragover');
        }

        function unhighlight() {
            dropZone.classList.remove('dragover');
        }

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        console.log('✅ Drag & Drop handlers setup');
    }

    /**
     * Handle added files
     */
    function handleFiles(files) {
        ([...files]).forEach(file => {
            selectedFiles.add(file);
        });
        updateFilePreview();
    }

    /**
     * Update file preview list
     */
    function updateFilePreview() {
        const previewList = document.getElementById('global-modal-filePreviewList');
        if (!previewList) return;

        previewList.innerHTML = '';

        selectedFiles.forEach(file => {
            const item = document.createElement('div');
            item.className = 'file-preview-item';

            // File icon based on type
            let iconClass = 'file-earmark';
            if (file.type.includes('image')) iconClass = 'file-image';
            else if (file.type.includes('pdf')) iconClass = 'file-pdf';
            else if (file.type.includes('text')) iconClass = 'file-text';

            item.innerHTML = `
                <div class="file-preview-name" title="${file.name}">
                    <i class="bi bi-${iconClass}"></i>
                    ${file.name} <span class="text-muted">(${(file.size / 1024).toFixed(1)} KB)</span>
                </div>
                <button type="button" class="remove-file-btn" aria-label="Remove">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            `;

            // Remove handler
            item.querySelector('.remove-file-btn').addEventListener('click', (e) => {
                e.stopPropagation(); // Prevent triggering dropzone click
                selectedFiles.delete(file);
                updateFilePreview();
            });

            previewList.appendChild(item);
        });
    }

    /**
     * Submit issue form via AJAX
     */


    // Run initialization
    initialize();
});

