<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-shield-lock"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">System Settings</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-icon-badge">
                <i class="bi bi-sliders"></i>
            </div>
            <div class="header-info">
                <h1 class="page-title">System Settings</h1>
                <p class="page-subtitle">Configure application defaults, appearance, and integrations.</p>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
        <!-- Settings Navigation (Left Sidebar) -->
        <div class="settings-sidebar">
            <div class="sidebar-card p-0">
                <div class="list-group list-group-flush settings-nav">
                    <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                        <i class="bi bi-gear me-2"></i> General
                    </a>

                    <a href="#email" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-envelope me-2"></i> Email
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-shield-lock me-2"></i> Security
                    </a>
                    <a href="#integrations" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-plug me-2"></i> Integrations
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="bi bi-bell me-2"></i> Notifications
                    </a>
                </div>
            </div>
        </div>

        <!-- Settings Content (Right Column) -->
        <div class="settings-main">
            <form action="<?= url('/admin/settings') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <div class="tab-content">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general">
                        <div class="enterprise-card">
                            <div class="card-header-bar">
                                <h2 class="card-title">General Settings</h2>
                            </div>
                            <div class="p-4">
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Application Name</label>
                                    <input type="text" name="app_name" class="form-control-enterprise"
                                        value="<?= e($settings['app_name'] ?? 'Jira Clone') ?>">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Application URL</label>
                                    <input type="url" name="app_url" class="form-control-enterprise"
                                        value="<?= e($settings['app_url'] ?? '') ?>">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Default Timezone</label>
                                    <select name="default_timezone" class="form-select-enterprise">
                                        <?php foreach ($timezones ?? ['UTC'] as $tz): ?>
                                            <option value="<?= $tz ?>" <?= ($settings['default_timezone'] ?? 'UTC') === $tz ? 'selected' : '' ?>>
                                                <?= e($tz) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Default Language</label>
                                    <select name="default_language" class="form-select-enterprise">
                                        <option value="en" <?= ($settings['default_language'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                                        <option value="es" <?= ($settings['default_language'] ?? '') === 'es' ? 'selected' : '' ?>>Spanish</option>
                                        <option value="fr" <?= ($settings['default_language'] ?? '') === 'fr' ? 'selected' : '' ?>>French</option>
                                        <option value="de" <?= ($settings['default_language'] ?? '') === 'de' ? 'selected' : '' ?>>German</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Date Format</label>
                                    <select name="date_format" class="form-select-enterprise">
                                        <option value="Y-m-d" <?= ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>2024-01-15</option>
                                        <option value="d/m/Y" <?= ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' ?>>15/01/2024</option>
                                        <option value="m/d/Y" <?= ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' ?>>01/15/2024</option>
                                        <option value="d M Y" <?= ($settings['date_format'] ?? '') === 'd M Y' ? 'selected' : '' ?>>15 Jan 2024</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Email Settings -->
                    <div class="tab-pane fade" id="email">
                        <div class="enterprise-card">
                            <div class="card-header-bar">
                                <h2 class="card-title">Email Settings</h2>
                            </div>
                            <div class="p-4">
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Mail Driver</label>
                                    <select name="mail_driver" class="form-select-enterprise">
                                        <option value="smtp" <?= ($settings['mail_driver'] ?? 'smtp') === 'smtp' ? 'selected' : '' ?>>SMTP</option>
                                        <option value="sendmail" <?= ($settings['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' ?>>Sendmail</option>
                                        <option value="log" <?= ($settings['mail_driver'] ?? '') === 'log' ? 'selected' : '' ?>>Log (Testing)</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 mb-4">
                                        <label class="form-label-enterprise">SMTP Host</label>
                                        <input type="text" name="smtp_host" class="form-control-enterprise"
                                            value="<?= e($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                                    </div>
                                    <div class="col-md-3 mb-4">
                                        <label class="form-label-enterprise">SMTP Port</label>
                                        <input type="number" name="smtp_port" class="form-control-enterprise"
                                            value="<?= e($settings['smtp_port'] ?? '587') ?>">
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <label class="form-label-enterprise">Encryption</label>
                                        <select name="smtp_encryption" class="form-select-enterprise">
                                            <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                            <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                            <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-enterprise">SMTP Username</label>
                                        <input type="text" name="smtp_username" class="form-control-enterprise"
                                            value="<?= e($settings['smtp_username'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-enterprise">SMTP Password</label>
                                        <input type="password" name="smtp_password" class="form-control-enterprise"
                                            placeholder="<?= ($settings['smtp_password'] ?? '') ? '••••••••' : '' ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-enterprise">From Address</label>
                                        <input type="email" name="mail_from_address" class="form-control-enterprise"
                                            value="<?= e($settings['mail_from_address'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-enterprise">From Name</label>
                                        <input type="text" name="mail_from_name" class="form-control-enterprise"
                                            value="<?= e($settings['mail_from_name'] ?? '') ?>">
                                    </div>
                                </div>
                                <button type="button" class="action-button small" onclick="testEmail()">
                                    <i class="bi bi-send me-1"></i> Send Test Email
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security">
                        <div class="enterprise-card">
                            <div class="card-header-bar">
                                <h2 class="card-title">Security Settings</h2>
                            </div>
                            <div class="p-4">
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="require_2fa"
                                            id="require2fa" value="1" <?= (($settings['require_2fa'] ?? '0') === '1') ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold ms-2" for="require2fa">Require Two-Factor
                                            Authentication</label>
                                        <div class="text-muted small ms-5">All users must enable 2FA to access the
                                            system</div>
                                    </div>
                                </div>
                                <hr class="my-4" style="border-color: var(--jira-border);">
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Session Timeout (minutes)</label>
                                    <input type="number" name="session_timeout" class="form-control-enterprise"
                                        style="width: 100%; max-width: 150px;"
                                        value="<?= e($settings['session_timeout'] ?? '120') ?>" min="5" max="1440">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Password Minimum Length</label>
                                    <input type="number" name="password_min_length" class="form-control-enterprise"
                                        style="width: 100%; max-width: 150px;"
                                        value="<?= e($settings['password_min_length'] ?? '8') ?>" min="6" max="32">
                                </div>
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="password_require_special"
                                            id="pwSpecial" value="1" <?= (($settings['password_require_special'] ?? '1') === '1') ? 'checked' : '' ?>>
                                        <label class="form-check-label ms-2" for="pwSpecial">Require special characters
                                            in passwords</label>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Maximum Login Attempts</label>
                                    <input type="number" name="max_login_attempts" class="form-control-enterprise"
                                        style="width: 100%; max-width: 150px;"
                                        value="<?= e($settings['max_login_attempts'] ?? '5') ?>" min="1" max="20">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Lockout Duration (minutes)</label>
                                    <input type="number" name="lockout_duration" class="form-control-enterprise"
                                        style="width: 100%; max-width: 150px;"
                                        value="<?= e($settings['lockout_duration'] ?? '15') ?>" min="1" max="1440">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Integrations -->
                    <div class="tab-pane fade" id="integrations">
                        <div class="enterprise-card">
                            <div class="card-header-bar">
                                <h2 class="card-title">Integrations</h2>
                            </div>
                            <div class="p-4">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="bi bi-info-circle me-3 fs-5"></i>
                                    <div>Configure integrations with third-party services.</div>
                                </div>

                                <h6 class="text-uppercase text-muted fw-bold small mb-3">Slack Integration</h6>
                                <div class="mb-4">
                                    <label class="form-label-enterprise">Webhook URL</label>
                                    <input type="text" name="slack_webhook" class="form-control-enterprise"
                                        value="<?= e($settings['slack_webhook'] ?? '') ?>"
                                        placeholder="https://hooks.slack.com/services/...">
                                    <small class="text-muted mt-1 d-block">Enter your Slack incoming webhook URL for
                                        notifications</small>
                                </div>

                                <hr class="my-4" style="border-color: var(--jira-border);">

                                <h6 class="text-uppercase text-muted fw-bold small mb-3">GitHub Integration</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-enterprise">Client ID</label>
                                        <input type="text" name="github_client_id" class="form-control-enterprise"
                                            value="<?= e($settings['github_client_id'] ?? '') ?>"
                                            placeholder="Enter GitHub OAuth Client ID">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-enterprise">Client Secret</label>
                                        <div class="input-group">
                                            <input type="password" name="github_client_secret" id="github_client_secret"
                                                class="form-control-enterprise"
                                                value="<?= e($settings['github_client_secret'] ?? '') ?>"
                                                placeholder="Enter GitHub OAuth Client Secret"
                                                style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleSecretBtn"
                                                style="border: 2px solid var(--jira-border); border-left: none;">
                                                <i class="bi bi-eye" id="toggleSecretIcon"></i>
                                            </button>
                                        </div>
                                        <script>
                                            document.getElementById('toggleSecretBtn').addEventListener('click', function () {
                                                var input = document.getElementById('github_client_secret');
                                                var icon = document.getElementById('toggleSecretIcon');
                                                if (input.type === 'password') {
                                                    input.type = 'text';
                                                    icon.className = 'bi bi-eye-slash';
                                                } else {
                                                    input.type = 'password';
                                                    icon.className = 'bi bi-eye';
                                                }
                                            });
                                        </script>
                                        <small class="text-muted mt-1 d-block">Leave empty to keep existing
                                            secret</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="enterprise-card">
                            <div class="card-header-bar">
                                <h2 class="card-title">Notification Settings</h2>
                            </div>
                            <div class="p-4">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_issue_assigned"
                                        id="notifyAssigned" value="1" <?= (($settings['notify_issue_assigned'] ?? '1') === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label ms-2" for="notifyAssigned">Notify when an Issue is
                                        Assigned</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_issue_updated"
                                        id="notifyUpdated" value="1" <?= (($settings['notify_issue_updated'] ?? '1') === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label ms-2" for="notifyUpdated">Notify when an Issue is
                                        Updated</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_comment_added"
                                        id="notifyComment" value="1" <?= (($settings['notify_comment_added'] ?? '1') === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label ms-2" for="notifyComment">Notify when a Comment is
                                        Added</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_mentioned"
                                        id="notifyMentioned" value="1" <?= (($settings['notify_mentioned'] ?? '1') === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label ms-2" for="notifyMentioned">Notify when Mentioned in
                                        Comment</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="action-button primary">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show success/error messages
        <?php if ($success = \App\Core\Session::getFlash('success')): ?>
            showAlert('<?= e($success) ?>', 'success');
        <?php endif; ?>

        <?php if ($error = \App\Core\Session::getFlash('error')): ?>
            showAlert('<?= e($error) ?>', 'danger');
        <?php endif; ?>
    });

    function showAlert(message, type = 'info') {
        const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
        const form = document.querySelector('form');
        form.insertAdjacentHTML('beforebegin', alertHtml);
    }

    async function testEmail() {
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Sending...';

        try {
            const response = await api('/admin/settings/test-email', { method: 'POST' });
            showAlert(response.message || 'Test email sent successfully!', 'success');
        } catch (error) {
            showAlert('Failed to send test email: ' + (error.message || 'Unknown error'), 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

</script>
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('styles'); ?>
<style>
    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-dark: #161B22 !important;
        --jira-gray: #626F86 !important;
        --jira-light: #F7F8FA !important;
        --jira-border: #DFE1E6 !important;
        --white: #FFFFFF;
        --jira-green: #216E4E;
        --jira-yellow: #FFAB00;
        --jira-red: #DE350B;
    }

    .page-wrapper {
        background: var(--jira-light);
        min-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }

    /* Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        background: var(--white);
        border-bottom: 1px solid var(--jira-border);
        font-size: 13px;
        margin: 0;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: var(--jira-gray);
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-weight: 600;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 24px 32px;
        background: var(--white);
        border-bottom: 1px solid var(--jira-border);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-icon-badge {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--jira-blue), var(--jira-blue-dark));
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.2);
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
        line-height: 1.2;
    }

    .page-subtitle {
        font-size: 14px;
        color: var(--jira-gray);
        margin: 4px 0 0 0;
    }

    /* Page Content & Sidebar */
    .page-content {
        display: flex;
        align-items: flex-start;
        gap: 24px;
        padding: 24px 32px;
        flex: 1;
    }

    .settings-sidebar {
        width: 240px;
        flex-shrink: 0;
        position: sticky;
        top: 24px;
    }

    .settings-main {
        flex: 1;
        min-width: 0;
    }

    /* Cards */
    .enterprise-card,
    .sidebar-card {
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        overflow: hidden;
    }

    .card-header-bar {
        padding: 16px 20px;
        border-bottom: 1px solid var(--jira-border);
        background: #F9FAFB;
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
    }

    /* Nav */
    .settings-nav .list-group-item {
        border: none;
        padding: 12px 20px;
        font-weight: 500;
        color: var(--jira-dark);
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }

    .settings-nav .list-group-item:hover {
        background-color: #F4F5F7;
        color: var(--jira-blue);
    }

    .settings-nav .list-group-item.active {
        background-color: #E9F2FF;
        color: var(--jira-blue);
        border-left-color: var(--jira-blue);
        font-weight: 600;
    }

    .settings-nav .list-group-item i {
        color: var(--jira-gray);
    }

    .settings-nav .list-group-item.active i {
        color: var(--jira-blue);
    }

    /* Forms */
    .form-label-enterprise {
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
    }

    .form-control-enterprise,
    .form-select-enterprise {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid var(--jira-border);
        border-radius: 4px;
        font-size: 14px;
        color: var(--jira-dark);
        background: #F4F5F7;
        transition: all 0.2s;
    }

    .form-control-enterprise:focus,
    .form-select-enterprise:focus {
        background: var(--white);
        border-color: var(--jira-blue);
        outline: none;
    }

    /* Buttons */
    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        color: var(--jira-dark);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
    }

    .action-button:hover {
        background: #F4F5F7;
        transform: translateY(-1px);
    }

    .action-button.primary {
        background: var(--jira-blue);
        color: #FFFFFF !important;
        border: none;
    }

    .action-button.primary:hover {
        background: var(--jira-blue-dark);
    }

    .action-button.small {
        padding: 6px 12px;
        font-size: 13px;
    }
</style>
<?php \App\Core\View::endSection(); ?>