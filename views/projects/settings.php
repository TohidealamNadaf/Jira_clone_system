<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background-color: transparent; padding: 0; gap: 8px;">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>" style="color: var(--jira-blue); text-decoration: none;">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>" style="color: var(--jira-blue); text-decoration: none;">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>" style="color: var(--jira-blue); text-decoration: none;"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active" style="color: #626F86;">Settings</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin: 0 0 24px 0; letter-spacing: -0.2px;">Project Settings</h1>

    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 24px;">
        <!-- Sidebar Navigation -->
        <div>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13); position: sticky; top: 100px;">
                <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                    <h6 style="font-size: 13px; font-weight: 600; color: #161B22; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Navigation</h6>
                </div>
                <nav style="display: flex; flex-direction: column;">
                    <a href="#details" style="padding: 12px 16px; border-left: 3px solid transparent; color: #161B22; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 14px; transition: all 0.2s; border-bottom: 1px solid #DFE1E6; cursor: pointer;"
                       onclick="showTab('details'); return false;"
                       onmouseover="this.style.backgroundColor='#F7F8FA'"
                       onmouseout="this.style.backgroundColor=''">
                        <i class="bi bi-info-circle" style="color: var(--jira-blue);"></i> Details
                    </a>
                    <a href="#access" style="padding: 12px 16px; border-left: 3px solid transparent; color: #161B22; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 14px; transition: all 0.2s; border-bottom: 1px solid #DFE1E6; cursor: pointer;"
                       onclick="showTab('access'); return false;"
                       onmouseover="this.style.backgroundColor='#F7F8FA'"
                       onmouseout="this.style.backgroundColor=''">
                        <i class="bi bi-shield-lock" style="color: var(--jira-blue);"></i> Access
                    </a>
                    <a href="#notifications" style="padding: 12px 16px; border-left: 3px solid transparent; color: #161B22; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 14px; transition: all 0.2s; border-bottom: 1px solid #DFE1E6; cursor: pointer;"
                       onclick="showTab('notifications'); return false;"
                       onmouseover="this.style.backgroundColor='#F7F8FA'"
                       onmouseout="this.style.backgroundColor=''">
                        <i class="bi bi-bell" style="color: var(--jira-blue);"></i> Notifications
                    </a>
                    <a href="#workflows" style="padding: 12px 16px; border-left: 3px solid transparent; color: #161B22; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 14px; transition: all 0.2s; border-bottom: 1px solid #DFE1E6; cursor: pointer;"
                       onclick="showTab('workflows'); return false;"
                       onmouseover="this.style.backgroundColor='#F7F8FA'"
                       onmouseout="this.style.backgroundColor=''">
                        <i class="bi bi-diagram-3" style="color: var(--jira-blue);"></i> Workflows
                    </a>
                    <?php if (can('delete-project', $project['id'])): ?>
                    <a href="#danger" style="padding: 12px 16px; border-left: 3px solid transparent; color: #AE2A19; text-decoration: none; display: flex; align-items: center; gap: 10px; font-size: 14px; transition: all 0.2s; cursor: pointer;"
                       onclick="showTab('danger'); return false;"
                       onmouseover="this.style.backgroundColor='#FFECEB'"
                       onmouseout="this.style.backgroundColor=''">
                        <i class="bi bi-exclamation-triangle"></i> Danger Zone
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>

        <!-- Settings Content -->
        <div>
            <!-- Details Tab -->
            <div id="details-tab" style="display: block;">
                <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                    <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                        <h3 style="font-size: 16px; font-weight: 600; color: #161B22; margin: 0;">Project Details</h3>
                    </div>
                    <div style="padding: 24px;">
                        <form action="<?= url("/projects/{$project['key']}/settings") ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <!-- Project Avatar & Name -->
                            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 24px; margin-bottom: 24px;">
                                <div style="text-align: center;">
                                    <?php if ($project['avatar'] ?? null): ?>
                                    <img src="<?= e($project['avatar']) ?>" style="width: 96px; height: 96px; border-radius: 8px; object-fit: cover; margin-bottom: 12px;" alt="">
                                    <?php else: ?>
                                    <div style="width: 96px; height: 96px; border-radius: 8px; background: linear-gradient(135deg, var(--jira-blue), #003D99); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 700; margin-bottom: 12px;">
                                        <?= strtoupper(substr($project['key'], 0, 1)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" style="display: block; width: 100%; padding: 8px 4px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 12px;" name="avatar" accept="image/*">
                                </div>
                                <div>
                                    <div style="margin-bottom: 16px;">
                                        <label style="display: block; font-weight: 600; color: #161B22; font-size: 14px; margin-bottom: 6px;">Project Name</label>
                                        <input type="text" style="width: 100%; padding: 8px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; <?= has_error('name') ? 'border-color: #AE2A19;' : '' ?>" 
                                               name="name" value="<?= e($project['name']) ?>" required>
                                        <?php if (has_error('name')): ?>
                                        <div style="color: #AE2A19; font-size: 12px; margin-top: 4px;"><?= e(error('name')) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label style="display: block; font-weight: 600; color: #161B22; font-size: 14px; margin-bottom: 6px;">Project Key</label>
                                        <input type="text" style="width: 100%; padding: 8px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; background-color: #F7F8FA; color: #626F86;" 
                                               value="<?= e($project['key']) ?>" disabled>
                                        <div style="color: #626F86; font-size: 12px; margin-top: 4px;">Key cannot be changed</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div style="margin-bottom: 16px;">
                                <label style="display: block; font-weight: 600; color: #161B22; font-size: 14px; margin-bottom: 6px;">Description</label>
                                <textarea style="width: 100%; padding: 8px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; font-family: inherit; resize: vertical; min-height: 100px;" 
                                          name="description"><?= e($project['description']) ?></textarea>
                            </div>

                            <!-- Category & Lead -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                                <div>
                                    <label style="display: block; font-weight: 600; color: #161B22; font-size: 14px; margin-bottom: 6px;">Category</label>
                                    <select style="width: 100%; padding: 8px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px;">
                                        <option value="">None</option>
                                        <?php foreach ($categories ?? [] as $category): ?>
                                        <option value="<?= e($category['id']) ?>" <?= $project['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                            <?= e($category['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-weight: 600; color: #161B22; font-size: 14px; margin-bottom: 6px;">Project Lead</label>
                                    <select style="width: 100%; padding: 8px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px;" name="lead_id">
                                        <option value="">Unassigned</option>
                                        <?php foreach ($users ?? [] as $u): ?>
                                        <option value="<?= e($u['id']) ?>" <?= $project['lead_id'] == $u['id'] ? 'selected' : '' ?>>
                                            <?= e($u['display_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Project URL -->
                            <div style="margin-bottom: 24px;">
                                <label style="display: block; font-weight: 600; color: #161B22; font-size: 14px; margin-bottom: 6px;">Project URL</label>
                                <input type="url" style="width: 100%; padding: 8px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px;" 
                                       name="url" value="<?= e($project['url'] ?? '') ?>" placeholder="https://example.com/project">
                            </div>

                            <!-- Save Button -->
                            <button type="submit" style="background-color: var(--jira-blue); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                    onmouseover="this.style.opacity='0.9'"
                                    onmouseout="this.style.opacity='1'">
                                <i class="bi bi-check-lg"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Access Tab -->
            <div id="access-tab" style="display: none;">
                <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                    <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                        <h3 style="font-size: 16px; font-weight: 600; color: #161B22; margin: 0;">Access Settings</h3>
                    </div>
                    <div style="padding: 24px;">
                        <form action="<?= url("/projects/{$project['key']}/settings/access") ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <!-- Visibility -->
                            <div style="margin-bottom: 24px;">
                                <h4 style="font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 12px;">Project Visibility</h4>
                                <label style="display: flex; align-items: start; gap: 12px; margin-bottom: 12px; cursor: pointer;">
                                    <input type="radio" name="is_private" value="0" <?= !$project['is_private'] ? 'checked' : '' ?> style="margin-top: 4px;">
                                    <div>
                                        <div style="font-weight: 500; color: #161B22; font-size: 14px;">🌐 Public</div>
                                        <div style="color: #626F86; font-size: 13px;">Anyone in the organization can view</div>
                                    </div>
                                </label>
                                <label style="display: flex; align-items: start; gap: 12px; cursor: pointer;">
                                    <input type="radio" name="is_private" value="1" <?= $project['is_private'] ? 'checked' : '' ?> style="margin-top: 4px;">
                                    <div>
                                        <div style="font-weight: 500; color: #161B22; font-size: 14px;">🔒 Private</div>
                                        <div style="color: #626F86; font-size: 13px;">Only members can view</div>
                                    </div>
                                </label>
                            </div>

                            <!-- Default Assignee -->
                            <div style="margin-bottom: 24px;">
                                <label style="display: block; font-weight: 600; color: #161B22; font-size: 14px; margin-bottom: 6px;">Default Assignee</label>
                                <select style="width: 100%; max-width: 300px; padding: 8px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px;" name="default_assignee">
                                    <option value="unassigned" <?= ($project['default_assignee'] ?? '') === 'unassigned' ? 'selected' : '' ?>>Unassigned</option>
                                    <option value="project_lead" <?= ($project['default_assignee'] ?? '') === 'project_lead' ? 'selected' : '' ?>>Project Lead</option>
                                </select>
                            </div>

                            <!-- Save Button -->
                            <button type="submit" style="background-color: var(--jira-blue); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                    onmouseover="this.style.opacity='0.9'"
                                    onmouseout="this.style.opacity='1'">
                                <i class="bi bi-check-lg"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div id="notifications-tab" style="display: none;">
                <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                    <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                        <h3 style="font-size: 16px; font-weight: 600; color: #161B22; margin: 0;">Notification Settings</h3>
                    </div>
                    <div style="padding: 24px;">
                        <form action="<?= url("/projects/{$project['key']}/settings/notifications") ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <h4 style="font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 16px;">Email Notifications</h4>

                            <label style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; cursor: pointer;">
                                <input type="checkbox" name="notify_assignee" value="1" <?= $project['settings']['notify_assignee'] ?? true ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span style="font-size: 14px; color: #161B22;">Notify assignee on issue assignment</span>
                            </label>

                            <label style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; cursor: pointer;">
                                <input type="checkbox" name="notify_reporter" value="1" <?= $project['settings']['notify_reporter'] ?? true ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span style="font-size: 14px; color: #161B22;">Notify reporter on updates</span>
                            </label>

                            <label style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; cursor: pointer;">
                                <input type="checkbox" name="notify_watchers" value="1" <?= $project['settings']['notify_watchers'] ?? true ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                                <span style="font-size: 14px; color: #161B22;">Notify watchers on updates</span>
                            </label>

                            <!-- Save Button -->
                            <button type="submit" style="background-color: var(--jira-blue); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;"
                                    onmouseover="this.style.opacity='0.9'"
                                    onmouseout="this.style.opacity='1'">
                                <i class="bi bi-check-lg"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Workflows Tab -->
            <div id="workflows-tab" style="display: none;">
                <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                    <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                        <h3 style="font-size: 16px; font-weight: 600; color: #161B22; margin: 0;">Workflows</h3>
                    </div>
                    <div style="padding: 24px;">
                        <p style="color: #626F86; margin-bottom: 16px;">Configure issue workflows and status transitions.</p>
                        <a href="<?= url("/projects/{$project['key']}/workflows") ?>" 
                           style="background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s;"
                           onmouseover="this.style.backgroundColor='#DEEAFE'"
                           onmouseout="this.style.backgroundColor='transparent'">
                            <i class="bi bi-diagram-3"></i> Manage Workflows
                        </a>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Tab -->
            <?php if (can('delete-project', $project['id'])): ?>
            <div id="danger-tab" style="display: none;">
                <div style="background: white; border: 1px solid #FFECEB; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                    <div style="padding: 16px 20px; background-color: #FFECEB; border-bottom: 1px solid #FFDBDA;">
                        <h3 style="font-size: 16px; font-weight: 600; color: #AE2A19; margin: 0;">⚠️ Danger Zone</h3>
                    </div>
                    <div style="padding: 24px;">
                        <div style="margin-bottom: 24px;">
                            <h4 style="font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 8px;">Archive Project</h4>
                            <p style="color: #626F86; font-size: 13px; margin-bottom: 16px;">Archived projects are read-only and hidden from the main list.</p>
                            <?php if ($project['is_archived'] ?? false): ?>
                            <form action="<?= url("/projects/{$project['key']}/unarchive") ?>" method="POST" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" style="background-color: #216E4E; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s;"
                                        onmouseover="this.style.opacity='0.9'"
                                        onmouseout="this.style.opacity='1'">
                                    <i class="bi bi-archive"></i> Unarchive Project
                                </button>
                            </form>
                            <?php else: ?>
                            <form action="<?= url("/projects/{$project['key']}/archive") ?>" method="POST" style="display: inline;">
                                <?= csrf_field() ?>
                                <button type="submit" style="background-color: #974F0C; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s;"
                                        onclick="return confirm('Archive this project?')"
                                        onmouseover="this.style.opacity='0.9'"
                                        onmouseout="this.style.opacity='1'">
                                    <i class="bi bi-archive"></i> Archive Project
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>

                        <hr style="border: none; border-top: 1px solid #DFE1E6; margin: 24px 0;">

                        <div>
                            <h4 style="font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 8px;">Delete Project</h4>
                            <p style="color: #626F86; font-size: 13px; margin-bottom: 16px;">Once deleted, all data will be permanently removed. This cannot be undone.</p>
                            <button type="button" onclick="openDeleteModal()" style="background-color: #AE2A19; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.opacity='0.9'"
                                    onmouseout="this.style.opacity='1'">
                                <i class="bi bi-trash"></i> Delete Project
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Project Modal -->
<div id="deleteProjectModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 8px; width: 90%; max-width: 400px; overflow: hidden;">
        <div style="padding: 20px; border-bottom: 1px solid #DFE1E6;">
            <h4 style="font-size: 16px; font-weight: 600; color: #AE2A19; margin: 0;">Delete Project</h4>
        </div>
        <div style="padding: 20px;">
            <div style="background: #FFECEB; border: 1px solid #FFDBDA; border-radius: 4px; padding: 12px; margin-bottom: 16px; color: #AE2A19; font-size: 13px;">
                <i class="bi bi-exclamation-triangle"></i> This cannot be undone. All data will be permanently deleted.
            </div>
            <p style="color: #161B22; font-size: 13px; margin-bottom: 12px;">Type <strong><?= e($project['key']) ?></strong> to confirm:</p>
            <form action="<?= url("/projects/{$project['key']}") ?>" method="POST" id="deleteProjectForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <input type="text" id="confirmProjectKey" placeholder="Project key" style="width: 100%; padding: 8px 12px; border: 1px solid #DFE1E6; border-radius: 4px; font-size: 14px; margin-bottom: 16px;" required>
            </form>
        </div>
        <div style="padding: 12px 20px; background-color: #F7F8FA; border-top: 1px solid #DFE1E6; display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" onclick="closeDeleteModal()" style="background-color: transparent; border: 1px solid #DFE1E6; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer;">
                Cancel
            </button>
            <button type="submit" form="deleteProjectForm" id="confirmDeleteBtn" disabled style="background-color: #AE2A19; color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; opacity: 0.6;">
                Delete Project
            </button>
        </div>
    </div>
</div>

<style>
    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #626F86;
        margin: 0 8px;
    }

    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<script>
    function showTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('[id$="-tab"]').forEach(tab => {
            tab.style.display = 'none';
        });
        // Show selected tab
        document.getElementById(tabName + '-tab').style.display = 'block';
    }

    function openDeleteModal() {
        document.getElementById('deleteProjectModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteProjectModal').style.display = 'none';
    }

    // Enable delete button only when correct key is typed
    document.getElementById('confirmProjectKey')?.addEventListener('input', function() {
        const btn = document.getElementById('confirmDeleteBtn');
        const isCorrect = this.value === '<?= e($project['key']) ?>';
        btn.disabled = !isCorrect;
        btn.style.opacity = isCorrect ? '1' : '0.6';
    });

    // Close modal when clicking outside
    document.getElementById('deleteProjectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>

<?php \App\Core\View::endSection(); ?>
