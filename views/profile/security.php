<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="security-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="security-breadcrumb-section">
        <div class="security-breadcrumb">
            <a href="<?= url('/') ?>" class="breadcrumb-link">
                <i class="bi bi-house-door"></i> Home
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url('/profile') ?>" class="breadcrumb-link">
                Profile
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Security</span>
        </div>
    </div>

    <!-- Page Header -->
    <div class="security-page-header">
        <div class="security-header-left">
            <h1 class="security-page-title">Security</h1>
            <p class="security-page-subtitle">Manage your password, sessions, and login activity</p>
        </div>
    </div>

    <!-- Main Content Container (2-Column Layout) -->
    <div class="security-content-container">
        <!-- Sidebar Navigation -->
        <div class="security-sidebar">
            <div class="security-sidebar-content">
                <!-- User Card -->
                <div class="security-user-card">
                    <div class="user-avatar-wrapper">
                        <?php if ($user['avatar'] ?? null): ?>
                            <img src="<?= e($user['avatar']) ?>" class="user-avatar-image" alt="Avatar">
                        <?php else: ?>
                            <div class="user-avatar-placeholder">
                                <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h3 class="user-display-name"><?= e($user['display_name'] ?? $user['first_name'] . ' ' . $user['last_name']) ?></h3>
                    <p class="user-email-address"><?= e($user['email']) ?></p>
                </div>

                <!-- Navigation Items -->
                <nav class="security-nav-items">
                    <a href="<?= url('/profile') ?>" class="security-nav-item" data-section="profile">
                        <i class="bi bi-person"></i>
                        <span>Profile</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/notifications') ?>" class="security-nav-item" data-section="notifications">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/security') ?>" class="security-nav-item active" data-section="security">
                        <i class="bi bi-shield-lock"></i>
                        <span>Security</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/tokens') ?>" class="security-nav-item" data-section="tokens">
                        <i class="bi bi-key"></i>
                        <span>API Tokens</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="security-main-content">
            <!-- Change Password Section -->
            <div class="security-card">
                <div class="security-card-header">
                    <div class="security-header-icon">
                        <i class="bi bi-lock"></i>
                    </div>
                    <div class="security-header-text">
                        <h2 class="security-section-title">Change Password</h2>
                        <p class="security-section-description">Update your password regularly to keep your account secure</p>
                    </div>
                </div>
                <div class="security-card-divider"></div>
                <div class="security-card-body">
                    <form action="<?= url('/profile/password') ?>" method="POST" class="security-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <!-- Current Password -->
                        <div class="security-form-group">
                            <label for="current_password" class="security-form-label">
                                <span class="required-indicator">*</span> Current Password
                            </label>
                            <div class="security-input-wrapper">
                                <input type="password" 
                                       id="current_password" 
                                       name="current_password" 
                                       class="security-input" 
                                       placeholder="Enter your current password"
                                       required>
                                <button type="button" class="password-toggle-btn" title="Show/Hide" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <p class="security-help-text">For security reasons, we need your current password to confirm this change</p>
                        </div>

                        <!-- New Password -->
                        <div class="security-form-group">
                            <label for="new_password" class="security-form-label">
                                <span class="required-indicator">*</span> New Password
                            </label>
                            <div class="security-input-wrapper">
                                <input type="password" 
                                       id="new_password" 
                                       name="new_password" 
                                       class="security-input" 
                                       placeholder="Enter a strong new password"
                                       minlength="8"
                                       required>
                                <button type="button" class="password-toggle-btn" title="Show/Hide" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength-indicator">
                                <div class="strength-bar">
                                    <div class="strength-fill"></div>
                                </div>
                                <span class="strength-text">Password strength: <strong id="strengthLabel">Weak</strong></span>
                            </div>
                            <p class="security-help-text">Use at least 8 characters with a mix of uppercase, lowercase, numbers, and symbols</p>
                        </div>

                        <!-- Confirm Password -->
                        <div class="security-form-group">
                            <label for="new_password_confirmation" class="security-form-label">
                                <span class="required-indicator">*</span> Confirm New Password
                            </label>
                            <div class="security-input-wrapper">
                                <input type="password" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       class="security-input" 
                                       placeholder="Re-enter your new password"
                                       required>
                                <button type="button" class="password-toggle-btn" title="Show/Hide" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div id="passwordMatch" class="password-match-indicator" style="display: none;">
                                <span class="match-text">Passwords match ✓</span>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="security-form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg me-2"></i> Update Password
                            </button>
                            <a href="<?= url('/profile') ?>" class="btn btn-secondary btn-lg">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Sessions Section -->
            <div class="security-card">
                <div class="security-card-header">
                    <div class="security-header-icon">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <div class="security-header-text">
                        <h2 class="security-section-title">Active Sessions</h2>
                        <p class="security-section-description">Manage your active login sessions</p>
                    </div>
                </div>
                <div class="security-card-divider"></div>
                <div class="security-card-body">
                    <div class="session-alert">
                        <div class="session-alert-icon">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="session-alert-content">
                            <h4 class="session-alert-title">Current Session</h4>
                            <p class="session-alert-text">You are currently logged in from this device</p>
                            <p class="session-last-login">
                                <strong>Last login:</strong> 
                                <?= $user['last_login_at'] ? format_datetime($user['last_login_at']) : 'Unknown' ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Login Activity Section -->
            <div class="security-card">
                <div class="security-card-header">
                    <div class="security-header-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="security-header-text">
                        <h2 class="security-section-title">Recent Login Activity</h2>
                        <p class="security-section-description">Review your recent login history</p>
                    </div>
                </div>
                <div class="security-card-divider"></div>
                <div class="security-card-body">
                    <?php if (empty($loginActivity)): ?>
                    <!-- Empty State -->
                    <div class="empty-state-container">
                        <div class="empty-state-icon">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h4 class="empty-state-title">No Recent Activity</h4>
                        <p class="empty-state-description">No recent login activity recorded. Your login history will appear here.</p>
                    </div>
                    <?php else: ?>
                    <!-- Activity Table -->
                    <div class="security-activity-table">
                        <div class="activity-header">
                            <div class="activity-col-action">Action</div>
                            <div class="activity-col-ip">IP Address</div>
                            <div class="activity-col-date">Date & Time</div>
                        </div>
                        <div class="activity-rows">
                            <?php foreach ($loginActivity as $activity): ?>
                            <div class="activity-row">
                                <div class="activity-col-action">
                                    <div class="activity-badge">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                        <span><?= e(ucwords(str_replace('_', ' ', $activity['action']))) ?></span>
                                    </div>
                                </div>
                                <div class="activity-col-ip">
                                    <code class="ip-address"><?= e($activity['ip_address'] ?? 'Unknown') ?></code>
                                </div>
                                <div class="activity-col-date">
                                    <time class="activity-time"><?= format_datetime($activity['created_at']) ?></time>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Two-Factor Authentication (Placeholder for Future) -->
            <div class="security-card security-card-coming-soon">
                <div class="security-card-header">
                    <div class="security-header-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="security-header-text">
                        <h2 class="security-section-title">Two-Factor Authentication</h2>
                        <p class="security-section-description">Add an extra layer of security to your account</p>
                    </div>
                    <div class="coming-soon-badge">
                        <span>Coming Soon</span>
                    </div>
                </div>
                <div class="security-card-divider"></div>
                <div class="security-card-body">
                    <div class="feature-disabled-container">
                        <p class="feature-disabled-text">Two-factor authentication will be available soon. This feature adds an extra layer of security to your account by requiring a second form of verification.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* =============================================
       Security Page - Enterprise Jira-like Design
       ============================================= */

    .security-page-wrapper {
        display: flex;
        flex-direction: column;
        min-height: calc(100vh - 80px);
        background-color: var(--bg-secondary);
    }

    /* Breadcrumb Navigation */
    .security-breadcrumb-section {
        background-color: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        padding: 0 32px;
        height: 48px;
        display: flex;
        align-items: center;
        box-shadow: var(--shadow-sm);
    }

    .security-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.875rem;
    }

    .breadcrumb-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: var(--jira-blue) !important;
        text-decoration: none;
        transition: color var(--transition-base);
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark) !important;
    }

    .breadcrumb-separator {
        color: var(--text-tertiary);
        margin: 0 4px;
    }

    .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 500;
    }

    /* Page Header */
    .security-page-header {
        background-color: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        padding: 24px 32px;
        box-shadow: var(--shadow-sm);
    }

    .security-header-left {
        flex: 1;
    }

    .security-page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.3px;
    }

    .security-page-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 4px 0 0 0;
        font-weight: 400;
    }

    /* Content Container */
    .security-content-container {
        display: flex;
        flex: 1;
        gap: 24px;
        padding: 24px 32px;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* Sidebar */
    .security-sidebar {
        flex-shrink: 0;
        width: 280px;
    }

    .security-sidebar-content {
        position: sticky;
        top: 24px;
    }

    /* User Card */
    .security-user-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
    }

    .user-avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 16px;
    }

    .user-avatar-image,
    .user-avatar-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 600;
        background: linear-gradient(135deg, var(--primary-main), var(--primary-hover));
        color: white;
    }

    .user-avatar-image {
        object-fit: cover;
    }

    .user-display-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 12px 0 4px 0;
    }

    .user-email-address {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0;
        word-break: break-all;
    }

    /* Navigation Items */
    .security-nav-items {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 20px;
    }

    .security-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all var(--transition-base);
        cursor: pointer;
    }

    .security-nav-item:hover {
        background-color: var(--bg-secondary);
        border-color: var(--jira-blue) !important;
        color: var(--jira-blue) !important;
    }

    .security-nav-item.active {
        background-color: rgba(139, 25, 86, 0.08);
        border-color: var(--jira-blue) !important;
        color: var(--jira-blue) !important;
        box-shadow: inset 3px 0 0 var(--jira-blue);
    }

    .security-nav-item i:first-child {
        flex-shrink: 0;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-chevron {
        margin-left: auto;
        flex-shrink: 0;
        opacity: 0.6;
        transition: opacity var(--transition-base);
    }

    .security-nav-item:hover .nav-chevron {
        opacity: 1;
    }

    /* Main Content Area */
    .security-main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Security Card */
    .security-card {
        background-color: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        transition: all var(--transition-base);
    }

    .security-card:hover:not(.security-card-coming-soon) {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .security-card-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 24px;
        background-color: var(--bg-primary);
        position: relative;
    }

    .security-header-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background-color: #f0dce5;
        color: #8B1956 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .security-header-text {
        flex: 1;
    }

    .security-section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .security-section-description {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 4px 0 0 0;
    }

    .coming-soon-badge {
        position: absolute;
        top: 24px;
        right: 24px;
        background-color: var(--color-warning);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Divider */
    .security-card-divider {
        height: 1px;
        background-color: var(--border-color);
    }

    /* Card Body */
    .security-card-body {
        padding: 24px;
    }

    /* Form Styling */
    .security-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .security-form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .security-form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .required-indicator {
        color: var(--color-error);
        margin-right: 2px;
    }

    .security-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .security-input {
        flex: 1;
        padding: 10px 12px;
        font-size: 14px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background-color: var(--bg-primary);
        color: var(--text-primary);
        transition: all var(--transition-base);
        font-family: inherit;
        width: 100%;
    }

    .security-input:focus {
        outline: none;
        border-color: #8B1956 !important;
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
        background-color: var(--bg-primary);
    }

    .security-input::placeholder {
        color: var(--text-tertiary);
    }

    .password-toggle-btn {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-secondary);
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color var(--transition-base);
        font-size: 14px;
    }

    .password-toggle-btn:hover {
        color: var(--jira-blue) !important;
    }

    /* Password Strength Indicator */
    .password-strength-indicator {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .strength-bar {
        height: 4px;
        background-color: var(--bg-tertiary);
        border-radius: 2px;
        overflow: hidden;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        background-color: var(--color-error);
        transition: width 200ms ease, background-color 200ms ease;
    }

    .strength-text {
        font-size: 12px;
        color: var(--text-secondary);
    }

    /* Password Match Indicator */
    .password-match-indicator {
        padding: 8px 12px;
        background-color: var(--bg-success);
        border-radius: 4px;
    }

    .match-text {
        font-size: 12px;
        color: var(--color-success);
        font-weight: 500;
    }

    /* Help Text */
    .security-help-text {
        font-size: 12px;
        color: var(--text-tertiary);
        margin: 0;
    }

    /* Form Actions */
    .security-form-actions {
        display: flex;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--border-color);
        margin-top: 12px;
    }

    .btn-lg {
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all var(--transition-base);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--jira-blue) !important;
        color: white !important;
    }

    .btn-primary:hover {
        background-color: var(--jira-blue-dark) !important;
    }

    .btn-secondary {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background-color: var(--bg-secondary);
        border-color: var(--text-secondary);
    }

    /* Session Alert */
    .session-alert {
        display: flex;
        gap: 16px;
        padding: 16px;
        background-color: var(--color-warning-light);
        border: 1px solid var(--color-warning);
        border-radius: 6px;
    }

    .session-alert-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 6px;
        background-color: var(--color-warning);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .session-alert-content {
        flex: 1;
    }

    .session-alert-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 4px 0;
    }

    .session-alert-text {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0 0 8px 0;
    }

    .session-last-login {
        font-size: 12px;
        color: var(--text-tertiary);
        margin: 0;
    }

    /* Activity Table */
    .security-activity-table {
        display: flex;
        flex-direction: column;
        gap: 1px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        overflow: hidden;
    }

    .activity-header {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1.2fr;
        gap: 16px;
        padding: 12px 16px;
        background-color: var(--bg-secondary);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .activity-rows {
        display: flex;
        flex-direction: column;
    }

    .activity-row {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1.2fr;
        gap: 16px;
        padding: 16px;
        border-top: 1px solid var(--border-color);
        align-items: center;
        transition: background-color var(--transition-base);
    }

    .activity-row:hover {
        background-color: var(--bg-secondary);
    }

    .activity-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background-color: var(--color-success-light);
        color: var(--color-success);
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        width: fit-content;
    }

    .activity-badge i {
        font-size: 12px;
    }

    .ip-address {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        padding: 4px 8px;
        background-color: var(--bg-secondary);
        border-radius: 4px;
        color: var(--text-primary);
    }

    .activity-time {
        font-size: 13px;
        color: var(--text-secondary);
    }

    /* Empty State */
    .empty-state-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 48px;
        opacity: 0.5;
        margin-bottom: 16px;
    }

    .empty-state-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 8px 0;
    }

    .empty-state-description {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
        max-width: 300px;
    }

    /* Coming Soon Card */
    .security-card-coming-soon {
        opacity: 0.7;
    }

    .feature-disabled-container {
        padding: 40px 20px;
        text-align: center;
    }

    .feature-disabled-text {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .security-content-container {
            flex-direction: column;
            gap: 16px;
            padding: 16px 20px;
        }

        .security-sidebar {
            width: 100%;
        }

        .security-sidebar-content {
            position: static;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .security-nav-items {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .activity-header,
        .activity-row {
            grid-template-columns: 1fr 1fr;
        }

        .activity-col-date {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 768px) {
        .security-page-header {
            padding: 16px 16px;
        }

        .security-page-title {
            font-size: 24px;
        }

        .security-breadcrumb-section {
            padding: 0 16px;
        }

        .security-sidebar {
            width: 100%;
        }

        .security-nav-items {
            grid-template-columns: repeat(2, 1fr);
        }

        .security-card-header {
            flex-direction: column;
        }

        .coming-soon-badge {
            position: static;
            margin-top: 8px;
        }

        .security-input {
            font-size: 16px;
        }

        .security-form-actions {
            flex-direction: column;
        }

        .btn-lg {
            width: 100%;
        }

        .activity-header,
        .activity-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .session-alert {
            flex-direction: column;
        }

        .password-strength-indicator {
            margin-top: 8px;
        }
    }

    @media (max-width: 480px) {
        .security-content-container {
            padding: 12px 12px;
            gap: 12px;
        }

        .security-breadcrumb {
            font-size: 12px;
        }

        .security-page-title {
            font-size: 20px;
        }

        .security-page-subtitle {
            font-size: 12px;
        }

        .security-form-group {
            gap: 6px;
        }

        .security-card-body {
            padding: 16px;
        }

        .security-user-card {
            padding: 16px;
            margin-bottom: 12px;
        }

        .btn-lg {
            font-size: 13px;
            padding: 10px 16px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password Toggle Functionality
        const toggleButtons = document.querySelectorAll('.password-toggle-btn');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        });

        // Password Strength Indicator
        const newPasswordInput = document.getElementById('new_password');
        const strengthFill = document.querySelector('.strength-fill');
        const strengthLabel = document.getElementById('strengthLabel');
        
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function() {
                const strength = calculatePasswordStrength(this.value);
                updateStrengthIndicator(strength, strengthFill, strengthLabel);
            });
        }

        // Password Match Indicator
        const confirmPasswordInput = document.getElementById('new_password_confirmation');
        const passwordMatchIndicator = document.getElementById('passwordMatch');
        
        if (confirmPasswordInput && newPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                const match = this.value === newPasswordInput.value && this.value.length > 0;
                passwordMatchIndicator.style.display = match ? 'block' : 'none';
            });
            
            newPasswordInput.addEventListener('input', function() {
                if (confirmPasswordInput.value.length > 0) {
                    const match = this.value === confirmPasswordInput.value;
                    passwordMatchIndicator.style.display = match ? 'block' : 'none';
                }
            });
        }

        // Calculate password strength
        function calculatePasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            return strength;
        }

        // Update strength indicator display
        function updateStrengthIndicator(strength, fill, label) {
            const width = (strength / 6) * 100;
            let color = '#FF5630';
            let labelText = 'Weak';
            
            if (strength >= 5) {
                color = '#36B37E';
                labelText = 'Strong';
            } else if (strength >= 3) {
                color = '#e77817';
                labelText = 'Fair';
            }
            
            fill.style.width = width + '%';
            fill.style.backgroundColor = color;
            label.textContent = labelText;
        }
    });
</script>

<?php \App\Core\View::endSection(); ?>
