<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </nav>
            <h2 class="mb-0">System Settings</h2>
        </div>
    </div>

    <div class="row">
        <!-- Settings Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="list-group shadow-sm settings-nav">
                <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                    <i class="bi bi-gear me-2"></i> General
                </a>
                <a href="#appearance" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                    <i class="bi bi-palette me-2"></i> Appearance
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

        <!-- Settings Content -->
        <div class="col-lg-9">
            <form action="<?= url('/admin/settings') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">
                
                <div class="tab-content">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">General Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Application Name</label>
                                    <input type="text" name="app_name" class="form-control" 
                                           value="<?= e($settings['app_name'] ?? 'Jira Clone') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Application URL</label>
                                    <input type="url" name="app_url" class="form-control" 
                                           value="<?= e($settings['app_url'] ?? '') ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Default Timezone</label>
                                    <select name="default_timezone" class="form-select">
                                        <?php foreach ($timezones ?? ['UTC'] as $tz): ?>
                                        <option value="<?= $tz ?>" <?= ($settings['default_timezone'] ?? 'UTC') === $tz ? 'selected' : '' ?>>
                                            <?= e($tz) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Default Language</label>
                                    <select name="default_language" class="form-select">
                                        <option value="en" <?= ($settings['default_language'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                                        <option value="es" <?= ($settings['default_language'] ?? '') === 'es' ? 'selected' : '' ?>>Spanish</option>
                                        <option value="fr" <?= ($settings['default_language'] ?? '') === 'fr' ? 'selected' : '' ?>>French</option>
                                        <option value="de" <?= ($settings['default_language'] ?? '') === 'de' ? 'selected' : '' ?>>German</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Date Format</label>
                                    <select name="date_format" class="form-select">
                                        <option value="Y-m-d" <?= ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>2024-01-15</option>
                                        <option value="d/m/Y" <?= ($settings['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' ?>>15/01/2024</option>
                                        <option value="m/d/Y" <?= ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' ?>>01/15/2024</option>
                                        <option value="d M Y" <?= ($settings['date_format'] ?? '') === 'd M Y' ? 'selected' : '' ?>>15 Jan 2024</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance Settings -->
                    <div class="tab-pane fade" id="appearance">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">Appearance Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <?php if ($settings['logo'] ?? null): ?>
                                    <div class="mt-2">
                                        <img src="<?= e($settings['logo']) ?>" height="40" alt="Logo">
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Favicon</label>
                                    <input type="file" name="favicon" class="form-control" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Primary Color</label>
                                    <div class="input-group" style="width: 200px;">
                                        <input type="color" id="primary_color" name="primary_color" class="form-control form-control-color" 
                                               value="<?= e($settings['primary_color'] ?? '#0d6efd') ?>" onchange="document.getElementById('primary_color_text').value = this.value">
                                        <input type="text" id="primary_color_text" class="form-control" value="<?= e($settings['primary_color'] ?? '#0d6efd') ?>" 
                                               pattern="^#[0-9A-Fa-f]{6}$" onchange="document.getElementById('primary_color').value = this.value" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Default Theme</label>
                                    <select name="default_theme" class="form-select">
                                        <option value="light" <?= ($settings['default_theme'] ?? 'light') === 'light' ? 'selected' : '' ?>>Light</option>
                                        <option value="dark" <?= ($settings['default_theme'] ?? '') === 'dark' ? 'selected' : '' ?>>Dark</option>
                                        <option value="auto" <?= ($settings['default_theme'] ?? '') === 'auto' ? 'selected' : '' ?>>Auto (System)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="tab-pane fade" id="email">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">Email Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Mail Driver</label>
                                    <select name="mail_driver" class="form-select">
                                        <option value="smtp" <?= ($settings['mail_driver'] ?? 'smtp') === 'smtp' ? 'selected' : '' ?>>SMTP</option>
                                        <option value="sendmail" <?= ($settings['mail_driver'] ?? '') === 'sendmail' ? 'selected' : '' ?>>Sendmail</option>
                                        <option value="log" <?= ($settings['mail_driver'] ?? '') === 'log' ? 'selected' : '' ?>>Log (Testing)</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">SMTP Host</label>
                                        <input type="text" name="smtp_host" class="form-control" 
                                               value="<?= e($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">SMTP Port</label>
                                        <input type="number" name="smtp_port" class="form-control" 
                                               value="<?= e($settings['smtp_port'] ?? '587') ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Encryption</label>
                                        <select name="smtp_encryption" class="form-select">
                                            <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                            <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                            <option value="none" <?= ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">SMTP Username</label>
                                        <input type="text" name="smtp_username" class="form-control" 
                                               value="<?= e($settings['smtp_username'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">SMTP Password</label>
                                        <input type="password" name="smtp_password" class="form-control" 
                                               placeholder="<?= ($settings['smtp_password'] ?? '') ? '••••••••' : '' ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">From Address</label>
                                        <input type="email" name="mail_from_address" class="form-control" 
                                               value="<?= e($settings['mail_from_address'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">From Name</label>
                                        <input type="text" name="mail_from_name" class="form-control" 
                                               value="<?= e($settings['mail_from_name'] ?? '') ?>">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-secondary" onclick="testEmail()">
                                    <i class="bi bi-send me-1"></i> Send Test Email
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings -->
                    <div class="tab-pane fade" id="security">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">Security Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="require_2fa" id="require2fa" value="1"
                                               <?= (($settings['require_2fa'] ?? '0') === '1') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="require2fa">
                                            <strong>Require Two-Factor Authentication</strong><br>
                                            <small class="text-muted">All users must enable 2FA</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Session Timeout (minutes)</label>
                                    <input type="number" name="session_timeout" class="form-control" style="width: 150px;"
                                           value="<?= e($settings['session_timeout'] ?? '120') ?>" min="5" max="1440">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password Minimum Length</label>
                                    <input type="number" name="password_min_length" class="form-control" style="width: 150px;"
                                           value="<?= e($settings['password_min_length'] ?? '8') ?>" min="6" max="32">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="password_require_special" id="pwSpecial" value="1"
                                               <?= (($settings['password_require_special'] ?? '1') === '1') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="pwSpecial">Require special characters in passwords</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Maximum Login Attempts</label>
                                    <input type="number" name="max_login_attempts" class="form-control" style="width: 150px;"
                                           value="<?= e($settings['max_login_attempts'] ?? '5') ?>" min="1" max="20">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Lockout Duration (minutes)</label>
                                    <input type="number" name="lockout_duration" class="form-control" style="width: 150px;"
                                           value="<?= e($settings['lockout_duration'] ?? '15') ?>" min="1" max="1440">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Integrations -->
                    <div class="tab-pane fade" id="integrations">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">Integrations</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Configure integrations with third-party services.
                                </div>
                                
                                <h6 class="mb-3">Slack Integration</h6>
                                <div class="mb-4">
                                    <label class="form-label">Webhook URL</label>
                                    <input type="text" name="slack_webhook" class="form-control" 
                                           value="<?= e($settings['slack_webhook'] ?? '') ?>"
                                           placeholder="https://hooks.slack.com/services/...">
                                    <small class="text-muted">Enter your Slack incoming webhook URL for notifications</small>
                                </div>
                                
                                <hr>
                                
                                <h6 class="mb-3">GitHub Integration</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Client ID</label>
                                        <input type="text" name="github_client_id" class="form-control" 
                                               value="<?= e($settings['github_client_id'] ?? '') ?>"
                                               placeholder="Enter GitHub OAuth Client ID">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Client Secret</label>
                                        <div class="input-group">
                                            <input type="password" name="github_client_secret" id="github_client_secret" class="form-control"
                                                   value="<?= e($settings['github_client_secret'] ?? '') ?>"
                                                   placeholder="Enter GitHub OAuth Client Secret">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleSecretBtn">
                                                <i class="bi bi-eye" id="toggleSecretIcon"></i>
                                            </button>
                                        </div>
                                        <script>
                                            document.getElementById('toggleSecretBtn').addEventListener('click', function() {
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
                                        <small class="text-muted">Leave empty to keep existing secret</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">Notification Settings</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_issue_assigned" id="notifyAssigned" value="1"
                                           <?= (($settings['notify_issue_assigned'] ?? '1') === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="notifyAssigned">Issue Assigned</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_issue_updated" id="notifyUpdated" value="1"
                                           <?= (($settings['notify_issue_updated'] ?? '1') === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="notifyUpdated">Issue Updated</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_comment_added" id="notifyComment" value="1"
                                           <?= (($settings['notify_comment_added'] ?? '1') === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="notifyComment">Comment Added</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="notify_mentioned" id="notifyMentioned" value="1"
                                           <?= (($settings['notify_mentioned'] ?? '1') === '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="notifyMentioned">Mentioned in Comment</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
