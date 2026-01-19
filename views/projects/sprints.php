<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-0">
    <!-- Breadcrumb Navigation -->
    <div class="project-breadcrumb">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">
            <?= e($project['name']) ?>
        </span>
    </div>

    <div class="px-5 py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-start mb-4" style="gap: 24px;">
            <div>
                <h1
                    style="font-size: 24px; font-weight: 700; color: #161B22; margin: 0 0 4px 0; letter-spacing: -0.2px;">
                    Sprints</h1>
                <p style="font-size: 15px; color: #626F86; margin: 0;">View and manage project sprints</p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <button id="createSprintBtn"
                    style="background-color: var(--jira-blue); color: white; border: none; padding: 10px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='var(--jira-blue-dark)'; this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.backgroundColor='var(--jira-blue)'; this.style.transform='translateY(0)'">
                    <i class="bi bi-plus-lg"></i> Create Sprint
                </button>
            </div>
        </div>

        <!-- Sprints List -->
        <?php if (empty($sprints)): ?>
            <div
                style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 60px 20px; text-align: center; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                <div style="font-size: 48px; margin-bottom: 16px;">⚡</div>
                <p style="font-size: 15px; color: #626F86; margin: 0;">No sprints have been created yet.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px;">
                <?php foreach ($sprints as $sprint): ?>
                    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13); transition: all 0.2s; cursor: pointer;"
                        onmouseover="this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.12)'; this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.boxShadow='0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)'; this.style.transform='translateY(0)'">

                        <!-- Header -->
                        <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                                <div>
                                    <h3
                                        style="font-size: 16px; font-weight: 600; color: #161B22; margin: 0; margin-bottom: 4px;">
                                        <?= e($sprint['name']) ?>
                                    </h3>
                                </div>
                                <span
                                    style="background-color: 
                        <?php
                        switch ($sprint['status']) {
                            case 'planning':
                                echo '#DEEAFE; color: var(--jira-blue);';
                                break;
                            case 'active':
                                echo '#DFFCF0; color: #216E4E;';
                                break;
                            case 'completed':
                                echo '#F3F0FF; color: #352C63;';
                                break;
                            default:
                                echo '#F1F2F4; color: #626F86;';
                        }
                        ?>; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?= ucfirst(str_replace('_', ' ', $sprint['status'])) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div style="padding: 20px;">
                            <!-- Status Detail -->
                            <div style="margin-bottom: 16px;">
                                <p
                                    style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">
                                    Status</p>
                                <p style="font-size: 14px; color: #161B22; margin: 0;">
                                    <?= ucfirst(str_replace('_', ' ', $sprint['status'])) ?>
                                </p>
                            </div>

                            <!-- Start Date -->
                            <?php if ($sprint['start_date']): ?>
                                <div style="margin-bottom: 16px;">
                                    <p
                                        style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">
                                        Start Date</p>
                                    <p
                                        style="font-size: 14px; color: #161B22; margin: 0; display: flex; align-items: center; gap: 6px;">
                                        <i class="bi bi-calendar3" style="color: var(--jira-blue);"></i>
                                        <?= date('M j, Y', strtotime($sprint['start_date'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- End Date -->
                            <?php if ($sprint['end_date']): ?>
                                <div style="margin-bottom: 16px;">
                                    <p
                                        style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">
                                        End Date</p>
                                    <p
                                        style="font-size: 14px; color: #161B22; margin: 0; display: flex; align-items: center; gap: 6px;">
                                        <i class="bi bi-calendar3-range" style="color: var(--jira-blue);"></i>
                                        <?= date('M j, Y', strtotime($sprint['end_date'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- Goal -->
                            <?php if ($sprint['goal']): ?>
                                <div style="margin-bottom: 16px;">
                                    <p
                                        style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">
                                        Goal</p>
                                    <p style="font-size: 14px; color: #161B22; margin: 0; line-height: 1.5;">
                                        <?= e($sprint['goal']) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Footer -->
                        <div
                            style="padding: 12px 20px; background-color: #F7F8FA; border-top: 1px solid #DFE1E6; display: flex; gap: 8px;">
                            <button type="button" onclick="window.location.href='<?= url("/projects/{$project['key']}/sprints/{$sprint['id']}/board") ?>'"
                                style="flex: 1; background-color: var(--jira-blue); color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: 500; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; text-decoration: none; transition: all 0.2s; min-width: 0;"
                                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                                <i class="bi bi-kanban" style="font-size: 14px; color: white;"></i> View Board
                            </button>
                            <a href="<?= url("/projects/{$project['key']}/sprints/{$sprint['id']}") ?>"
                                style="flex: 1; background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; padding: 8px 12px; border-radius: 4px; font-weight: 500; font-size: 13px; cursor: pointer; display: block; text-align: center; text-decoration: none; transition: all 0.2s;"
                                onmouseover="this.style.backgroundColor='#DEEAFE'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <i class="bi bi-gear"></i> Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Create Sprint Modal -->
    <div id="createSprintModal"
        style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1050; align-items: center; justify-content: center;">
        <div
            style="background: white; border-radius: 8px; padding: 32px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">
            <!-- Modal Header -->
            <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 24px; font-weight: 700; color: #161B22; margin: 0;">Create Sprint</h2>
                <button type="button" onclick="document.getElementById('createSprintModal').style.display='none'"
                    style="background: none; border: none; font-size: 24px; cursor: pointer; color: #626F86;">
                    ×
                </button>
            </div>

            <!-- Form -->
            <form id="createSprintForm" style="display: flex; flex-direction: column; gap: 16px;">
                <!-- Sprint Name -->
                <div>
                    <label
                        style="display: block; font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 6px;">
                        Sprint Name <span style="color: #AE2A19;">*</span>
                    </label>
                    <input type="text" name="name" id="sprintName"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit;"
                        placeholder="e.g., Sprint 5" required>
                </div>

                <!-- Sprint Goal -->
                <div>
                    <label
                        style="display: block; font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 6px;">
                        Sprint Goal
                    </label>
                    <textarea name="goal" id="sprintGoal"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit; resize: vertical; min-height: 100px;"
                        placeholder="What do you want to accomplish?"></textarea>
                </div>

                <!-- Start Date -->
                <div>
                    <label
                        style="display: block; font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 6px;">
                        Start Date
                    </label>
                    <input type="date" name="start_date" id="startDate"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit;">
                </div>

                <!-- End Date -->
                <div>
                    <label
                        style="display: block; font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 6px;">
                        End Date
                    </label>
                    <input type="date" name="end_date" id="endDate"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit;">
                </div>

                <!-- Error Message -->
                <div id="sprintError"
                    style="display: none; padding: 12px; background-color: #FFECEB; color: #AE2A19; border-radius: 4px; font-size: 13px;">
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 12px; margin-top: 16px;">
                    <button type="button" onclick="document.getElementById('createSprintModal').style.display='none'"
                        style="flex: 1; padding: 10px 16px; background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s;">
                        Cancel
                    </button>
                    <button type="submit" id="submitSprintBtn"
                        style="flex: 1; padding: 10px 16px; background-color: var(--jira-blue); color: white; border: none; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s;">
                        Create Sprint
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* ============================================
        BREADCRUMB NAVIGATION
        ============================================ */

        .project-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 32px;
            background: #FFFFFF;
            border-bottom: 1px solid #DFE1E6;
            font-size: 13px;
            flex-shrink: 0;
        }

        .breadcrumb-link {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #8B1956;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .breadcrumb-link:hover {
            color: #6F123F;
            text-decoration: underline;
        }

        .breadcrumb-separator {
            color: #626F86;
            font-weight: 300;
        }

        .breadcrumb-current {
            color: #161B22;
            font-weight: 600;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "/";
            color: #626F86;
            margin: 0 8px;
        }
    </style>

    <script>
        // Initialize sprint form when DOM is ready
        function initializeSprintForm() {
            console.log('[SPRINT-FORM] Initializing sprint form...');

            const createSprintBtn = document.getElementById('createSprintBtn');
            const createSprintModal = document.getElementById('createSprintModal');
            const createSprintForm = document.getElementById('createSprintForm');

            if (!createSprintBtn || !createSprintModal || !createSprintForm) {
                console.error('[SPRINT-FORM] Required elements not found');
                return;
            }

            // Open modal on button click
            createSprintBtn.addEventListener('click', function () {
                console.log('[SPRINT-FORM] Opening create sprint modal');
                createSprintModal.style.display = 'flex';
            });

            // Close modal on backdrop click
            createSprintModal.addEventListener('click', function (e) {
                if (e.target === this) {
                    console.log('[SPRINT-FORM] Closing create sprint modal');
                    this.style.display = 'none';
                }
            });

            // Handle form submission
            createSprintForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                console.log('[SPRINT-FORM] Form submitted');

                const errorDiv = document.getElementById('sprintError');
                errorDiv.style.display = 'none';

                const submitBtn = document.getElementById('submitSprintBtn');
                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creating...';

                const formData = {
                    name: document.getElementById('sprintName').value.trim(),
                    goal: document.getElementById('sprintGoal').value.trim() || null,
                    start_date: document.getElementById('startDate').value || null,
                    end_date: document.getElementById('endDate').value || null,
                };

                console.log('[SPRINT-FORM] Form data:', formData);

                // Validate sprint name
                if (!formData.name) {
                    errorDiv.textContent = 'Sprint name is required';
                    errorDiv.style.display = 'block';
                    console.error('[SPRINT-FORM] Sprint name is empty');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    return;
                }

                try {
                    const url = '<?= url("/projects/{$project['key']}/sprints") ?>';
                    console.log('[SPRINT-FORM] Posting to:', url);

                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify(formData),
                        credentials: 'include'
                    });

                    console.log('[SPRINT-FORM] Response status:', response.status);
                    console.log('[SPRINT-FORM] Response headers:', response.headers.get('content-type'));

                    // Parse response - always try to get JSON
                    let responseData;
                    const contentType = response.headers.get('content-type');

                    if (contentType && contentType.includes('application/json')) {
                        try {
                            responseData = await response.json();
                            console.log('[SPRINT-FORM] Response data:', responseData);
                        } catch (e) {
                            console.error('[SPRINT-FORM] Failed to parse JSON:', e);
                            errorDiv.textContent = 'Invalid response format from server';
                            errorDiv.style.display = 'block';
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                            return;
                        }
                    } else {
                        const text = await response.text();
                        console.error('[SPRINT-FORM] Non-JSON response (type: ' + contentType + '):', text.substring(0, 200));
                        errorDiv.textContent = 'Server returned invalid response format';
                        errorDiv.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                        return;
                    }

                    // Check BOTH response.ok AND response data for success
                    if (response.ok && responseData && responseData.success) {
                        console.log('[SPRINT-FORM] ✓ Sprint created successfully!');
                        console.log('[SPRINT-FORM] Sprint data:', responseData.sprint);
                        
                        // Success feedback
                        errorDiv.style.display = 'none';
                        
                        // Close modal
                        createSprintModal.style.display = 'none';
                        
                        // Reset form for next use
                        createSprintForm.reset();
                        
                        // Re-enable button
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                        
                        // Reload page after brief delay (gives visual feedback)
                        setTimeout(() => {
                            console.log('[SPRINT-FORM] Reloading page to show new sprint...');
                            location.reload();
                        }, 1000);
                    } else if (response.ok && responseData) {
                        // 200+ status but data indicates failure
                        console.error('[SPRINT-FORM] Response indicated failure:', responseData);
                        errorDiv.textContent = responseData.error || 'Failed to create sprint. Please try again.';
                        errorDiv.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    } else {
                        // Actual HTTP error
                        console.error('[SPRINT-FORM] HTTP error:', response.status, responseData);
                        
                        if (responseData && responseData.errors) {
                            const errorMessages = Object.values(responseData.errors).flat().join(', ');
                            errorDiv.textContent = errorMessages || 'Validation failed';
                        } else if (responseData && responseData.error) {
                            errorDiv.textContent = responseData.error;
                        } else {
                            errorDiv.textContent = 'Failed to create sprint (Status: ' + response.status + ')';
                        }
                        
                        errorDiv.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                } catch (error) {
                    console.error('[SPRINT-FORM] Exception:', error);
                    errorDiv.textContent = 'Error creating sprint: ' + error.message;
                    errorDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });

            console.log('[SPRINT-FORM] Sprint form initialized successfully');
        }

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSprintForm);
        } else {
            // DOM is already loaded
            initializeSprintForm();
        }
    </script>

    <?php \App\Core\View::endSection(); ?>