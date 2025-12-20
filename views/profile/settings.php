<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="profile-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="profile-breadcrumb-section">
        <div class="profile-breadcrumb">
            <a href="<?= url('/') ?>" class="breadcrumb-link">
                <i class="bi bi-house-door"></i> Home
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url('/profile') ?>" class="breadcrumb-link">
                Profile
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Settings</span>
        </div>
    </div>

    <!-- Page Header -->
    <div class="profile-page-header">
        <div class="profile-header-left">
            <h1 class="profile-page-title">Settings <span class="profile-page-subtitle">— Customize your profile and preferences</span></h1>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="profile-content-container">
        <!-- Sidebar Navigation -->
        <div class="profile-sidebar">
            <div class="profile-sidebar-content">
                <!-- User Card -->
                <div class="profile-user-card">
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
                <nav class="profile-nav-items">
                    <a href="<?= url('/profile') ?>" class="profile-nav-item" data-section="profile">
                        <i class="bi bi-person"></i>
                        <span>Profile</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/notifications') ?>" class="profile-nav-item" data-section="notifications">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/security') ?>" class="profile-nav-item" data-section="security">
                        <i class="bi bi-shield-lock"></i>
                        <span>Security</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/settings') ?>" class="profile-nav-item active" data-section="settings">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/tokens') ?>" class="profile-nav-item" data-section="tokens">
                        <i class="bi bi-key"></i>
                        <span>API Tokens</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                </nav>

                <!-- Activity Stats (Hidden on small screens) -->
                <div class="profile-stats-section" style="display: none;">
                    <h4 class="stats-title">Activity</h4>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?= e($stats['issues_assigned'] ?? 0) ?></div>
                            <div class="stat-label">Assigned</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= e($stats['issues_completed'] ?? 0) ?></div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="profile-stats-section">
                    <h4 class="stats-title">Activity</h4>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?= e($stats['issues_assigned'] ?? 0) ?></div>
                            <div class="stat-label">Assigned</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= e($stats['issues_completed'] ?? 0) ?></div>
                            <div class="stat-label">Completed</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="profile-main-content">
            <!-- Preferences Section -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2 class="profile-section-title">Preferences</h2>
                    <p class="profile-section-description">Customize how the application works for you</p>
                </div>
                <div class="profile-card-body">
                    <form action="<?= url('/profile/settings') ?>" method="POST" class="profile-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="form-row-group">
                            <div class="form-field">
                                <label class="form-field-label">Language</label>
                                <select name="language" class="form-field-select">
                                    <option value="en" <?= ($userSettings['language'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="es" <?= ($userSettings['language'] ?? 'en') === 'es' ? 'selected' : '' ?>>Spanish</option>
                                    <option value="fr" <?= ($userSettings['language'] ?? 'en') === 'fr' ? 'selected' : '' ?>>French</option>
                                    <option value="de" <?= ($userSettings['language'] ?? 'en') === 'de' ? 'selected' : '' ?>>German</option>
                                </select>
                                <small class="form-field-hint">Select your preferred language</small>
                            </div>
                            <div class="form-field">
                                <label class="form-field-label">Items Per Page</label>
                                <select name="items_per_page" class="form-field-select">
                                    <option value="10" <?= ($userSettings['items_per_page'] ?? 25) == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= ($userSettings['items_per_page'] ?? 25) == 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= ($userSettings['items_per_page'] ?? 25) == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= ($userSettings['items_per_page'] ?? 25) == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <small class="form-field-hint">How many items to show per page in lists</small>
                            </div>
                        </div>

                        <div class="form-row-group">
                            <div class="form-field">
                                <div class="timezone-label-wrapper">
                                    <label class="form-field-label">Time Zone</label>
                                    <button type="button" class="timezone-detect-btn" id="timezoneDetectBtn" title="Auto-detect your timezone">
                                        <i class="bi bi-geo-alt"></i> Detect
                                    </button>
                                </div>
                                <select name="timezone" class="form-field-select" id="timezoneSelect">
                                    <option value="UTC" <?= ($userSettings['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                                    <option value="EST" <?= ($userSettings['timezone'] ?? 'UTC') === 'EST' ? 'selected' : '' ?>>Eastern Time (ET)</option>
                                    <option value="CST" <?= ($userSettings['timezone'] ?? 'UTC') === 'CST' ? 'selected' : '' ?>>Central Time (CT)</option>
                                    <option value="MST" <?= ($userSettings['timezone'] ?? 'UTC') === 'MST' ? 'selected' : '' ?>>Mountain Time (MT)</option>
                                    <option value="PST" <?= ($userSettings['timezone'] ?? 'UTC') === 'PST' ? 'selected' : '' ?>>Pacific Time (PT)</option>
                                    <option value="GMT" <?= ($userSettings['timezone'] ?? 'UTC') === 'GMT' ? 'selected' : '' ?>>Greenwich Mean Time (GMT)</option>
                                    <option value="CET" <?= ($userSettings['timezone'] ?? 'UTC') === 'CET' ? 'selected' : '' ?>>Central European Time (CET)</option>
                                    <option value="IST" <?= ($userSettings['timezone'] ?? 'UTC') === 'IST' ? 'selected' : '' ?>>Indian Standard Time (IST)</option>
                                </select>
                                <small class="form-field-hint">Automatically detected from your device or select manually</small>
                            </div>
                            <div class="form-field">
                                <label class="form-field-label">Date Format</label>
                                <select name="date_format" class="form-field-select">
                                    <option value="MM/DD/YYYY" <?= ($userSettings['date_format'] ?? 'MM/DD/YYYY') === 'MM/DD/YYYY' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                    <option value="DD/MM/YYYY" <?= ($userSettings['date_format'] ?? 'MM/DD/YYYY') === 'DD/MM/YYYY' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                    <option value="YYYY-MM-DD" <?= ($userSettings['date_format'] ?? 'MM/DD/YYYY') === 'YYYY-MM-DD' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                                    <option value="DD.MM.YYYY" <?= ($userSettings['date_format'] ?? 'MM/DD/YYYY') === 'DD.MM.YYYY' ? 'selected' : '' ?>>DD.MM.YYYY</option>
                                </select>
                                <small class="form-field-hint">Choose how dates are displayed</small>
                            </div>
                        </div>

                        <div class="form-field settings-checkbox-group">
                            <label class="form-field-label">
                                <input type="checkbox" name="auto_refresh" value="1" class="form-check-input" 
                                    <?= ($userSettings['auto_refresh'] ?? 0) ? 'checked' : '' ?>>
                                <span>Auto-Refresh Notifications</span>
                            </label>
                            <small class="form-field-hint">Enable automatic notification refresh every 30 seconds</small>
                        </div>

                        <div class="form-field settings-checkbox-group">
                            <label class="form-field-label">
                                <input type="checkbox" name="compact_view" value="1" class="form-check-input" 
                                    <?= ($userSettings['compact_view'] ?? 0) ? 'checked' : '' ?>>
                                <span>Compact View</span>
                            </label>
                            <small class="form-field-hint">Use compact layout with reduced padding and spacing</small>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Time Tracking Rates Section -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2 class="profile-section-title">Time Tracking Rates</h2>
                    <p class="profile-section-description">Configure your billing rates based on annual package</p>
                </div>
                <div class="profile-card-body">
                    <form action="<?= url('/profile/settings') ?>" method="POST" class="profile-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="rate-info-banner">
                            <i class="bi bi-info-circle"></i>
                            <span>Rates calculated based on 8.5 hours/day (9:30 AM - 6:00 PM), 5 days/week, excluding Sundays and public holidays</span>
                        </div>

                        <div class="form-row-group">
                            <div class="form-field">
                                <label class="form-field-label">Annual Package</label>
                                <div class="input-with-currency">
                                    <span class="currency-symbol" id="currencySymbolDisplay">$</span>
                                    <input type="number" name="annual_package" class="form-field-input" id="annualPackageInput" 
                                        placeholder="Enter your annual salary/package" step="100" min="0"
                                        value="<?= e($userSettings['annual_package'] ?? '') ?>">
                                </div>
                                <small class="form-field-hint" id="packageHint">Your annual salary or total package in USD</small>
                            </div>
                            <div class="form-field">
                                <label class="form-field-label">Currency</label>
                                <select name="rate_currency" class="form-field-select" id="currencySelect">
                                    <option value="USD" <?= ($userSettings['rate_currency'] ?? 'USD') === 'USD' ? 'selected' : '' ?>>USD ($)</option>
                                    <option value="EUR" <?= ($userSettings['rate_currency'] ?? 'USD') === 'EUR' ? 'selected' : '' ?>>EUR (€)</option>
                                    <option value="GBP" <?= ($userSettings['rate_currency'] ?? 'USD') === 'GBP' ? 'selected' : '' ?>>GBP (£)</option>
                                    <option value="INR" <?= ($userSettings['rate_currency'] ?? 'USD') === 'INR' ? 'selected' : '' ?>>INR (₹)</option>
                                </select>
                                <small class="form-field-hint">Select your billing currency</small>
                            </div>
                        </div>

                        <div class="rate-divider"></div>

                        <div class="rates-grid">
                            <div class="rate-card">
                                <div class="rate-label">Hourly Rate</div>
                                <div class="rate-value" id="hourlyRate">--</div>
                                <div class="rate-unit">per hour</div>
                            </div>
                            <div class="rate-card">
                                <div class="rate-label">Minute Rate</div>
                                <div class="rate-value" id="minuteRate">--</div>
                                <div class="rate-unit">per minute</div>
                            </div>
                            <div class="rate-card">
                                <div class="rate-label">Second Rate</div>
                                <div class="rate-value" id="secondRate">--</div>
                                <div class="rate-unit">per second</div>
                            </div>
                            <div class="rate-card">
                                <div class="rate-label">Daily Rate</div>
                                <div class="rate-value" id="dailyRate">--</div>
                                <div class="rate-unit">per day</div>
                            </div>
                        </div>

                        <div class="rate-calculation-info">
                            <h4>Calculation Details:</h4>
                            <ul>
                                <li><strong>Working Days:</strong> Monday - Friday (Sundays excluded)</li>
                                <li><strong>Working Hours:</strong> 9:30 AM to 6:00 PM (8.5 hours per day)</li>
                                <li><strong>Working Days/Year:</strong> ~260 days (52 weeks × 5 days)</li>
                                <li><strong>Working Hours/Year:</strong> ~2,210 hours (260 days × 8.5 hours)</li>
                                <li id="currencyInfo"><strong>Currency:</strong> USD</li>
                            </ul>
                        </div>

                        <div class="profile-form-actions">
                            <button type="submit" class="btn-primary-action">
                                <i class="bi bi-check-circle"></i> Save Time Tracking Rates
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Privacy Section -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2 class="profile-section-title">Privacy</h2>
                    <p class="profile-section-description">Control your visibility and data sharing</p>
                </div>
                <div class="profile-card-body">
                    <form action="<?= url('/profile/settings') ?>" method="POST" class="profile-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-field settings-checkbox-group">
                            <label class="form-field-label">
                                <input type="checkbox" name="show_profile" value="1" class="form-check-input" 
                                    <?= ($userSettings['show_profile'] ?? 1) ? 'checked' : '' ?>>
                                <span>Show Profile</span>
                            </label>
                            <small class="form-field-hint">Allow others to view your profile</small>
                        </div>

                        <div class="form-field settings-checkbox-group">
                            <label class="form-field-label">
                                <input type="checkbox" name="show_activity" value="1" class="form-check-input" 
                                    <?= ($userSettings['show_activity'] ?? 1) ? 'checked' : '' ?>>
                                <span>Show Activity</span>
                            </label>
                            <small class="form-field-hint">Show your activity in project timelines</small>
                        </div>

                        <div class="form-field settings-checkbox-group">
                            <label class="form-field-label">
                                <input type="checkbox" name="show_email" value="1" class="form-check-input" 
                                    <?= ($userSettings['show_email'] ?? 0) ? 'checked' : '' ?>>
                                <span>Show Email</span>
                            </label>
                            <small class="form-field-hint">Display your email address publicly</small>
                        </div>

                        <div class="profile-form-actions">
                            <button type="submit" class="btn-primary-action">
                                <i class="bi bi-check-circle"></i> Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>



            <!-- Accessibility Section -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2 class="profile-section-title">Accessibility</h2>
                    <p class="profile-section-description">Configure accessibility features for better usability</p>
                </div>
                <div class="profile-card-body">
                    <form action="<?= url('/profile/settings') ?>" method="POST" class="profile-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-field settings-checkbox-group">
                            <label class="form-field-label">
                                <input type="checkbox" name="high_contrast" value="1" class="form-check-input" 
                                    <?= ($userSettings['high_contrast'] ?? 0) ? 'checked' : '' ?>>
                                <span>High Contrast</span>
                            </label>
                            <small class="form-field-hint">Increase color contrast for better readability</small>
                        </div>

                        <div class="form-field settings-checkbox-group">
                            <label class="form-field-label">
                                <input type="checkbox" name="reduce_motion" value="1" class="form-check-input" 
                                    <?= ($userSettings['reduce_motion'] ?? 0) ? 'checked' : '' ?>>
                                <span>Reduce Motion</span>
                            </label>
                            <small class="form-field-hint">Minimize animations and transitions</small>
                        </div>

                        <div class="form-field settings-checkbox-group">
                            <label class="form-field-label">
                                <input type="checkbox" name="large_text" value="1" class="form-check-input" 
                                    <?= ($userSettings['large_text'] ?? 0) ? 'checked' : '' ?>>
                                <span>Large Text</span>
                            </label>
                            <small class="form-field-hint">Increase default font size throughout the application</small>
                        </div>

                        <div class="profile-form-actions">
                            <button type="submit" class="btn-primary-action">
                                <i class="bi bi-check-circle"></i> Save Accessibility Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --profile-primary-color: var(--jira-blue);
    --profile-primary-dark: var(--jira-blue-dark);
    --profile-primary-light: #E77817;
    --profile-text-primary: #161B22;
    --profile-text-secondary: #626F86;
    --profile-text-muted: #97A0AF;
    --profile-bg-primary: #FFFFFF;
    --profile-bg-secondary: #F7F8FA;
    --profile-bg-tertiary: #ECEDF0;
    --profile-border-color: #DFE1E6;
    --profile-shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --profile-shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --profile-shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --profile-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Page Wrapper */
.profile-page-wrapper {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 80px);
    background-color: var(--profile-bg-secondary);
    padding: 0 !important;
    margin: 0;
    width: 100% !important;
    box-sizing: border-box;
}

/* Breadcrumb Section */
.profile-breadcrumb-section {
    background-color: var(--profile-bg-primary);
    border-bottom: 1px solid var(--profile-border-color);
    padding: 0 32px;
    height: 48px;
    display: flex;
    align-items: center;
    box-shadow: var(--profile-shadow-sm);
    width: 100%;
    margin: 0;
}

.profile-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
}

a.breadcrumb-link,
.breadcrumb-link {
    color: var(--jira-blue) !important;
    text-decoration: none !important;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color var(--profile-transition);
}

a.breadcrumb-link:visited,
.breadcrumb-link:visited {
    color: var(--jira-blue) !important;
}

a.breadcrumb-link:hover,
.breadcrumb-link:hover {
    color: var(--jira-blue-dark) !important;
    text-decoration: none !important;
}

a.breadcrumb-link:active,
.breadcrumb-link:active {
    color: var(--jira-blue-dark) !important;
}

.breadcrumb-link i {
    font-size: 14px;
}

.breadcrumb-separator {
    color: var(--profile-text-muted);
}

.breadcrumb-current {
    color: var(--profile-text-primary);
    font-weight: 600;
}

/* Page Header */
.profile-page-header {
    background-color: var(--profile-bg-primary);
    border-bottom: 1px solid var(--profile-border-color);
    padding: 24px 32px;
    box-shadow: var(--profile-shadow-sm);
    width: 100%;
    margin: 0;
}

.profile-header-left {
    flex: 1;
}

.profile-page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--profile-text-primary);
    margin: 0 0 8px 0;
    letter-spacing: -0.3px;
}

.profile-page-subtitle {
    font-size: 14px;
    color: var(--profile-text-secondary);
    margin: 0;
    font-weight: 400;
    display: inline;
    margin-left: 8px;
}

/* Content Container */
.profile-content-container {
    display: flex;
    padding: 32px;
    gap: 32px;
    flex: 1;
    width: 100%;
    margin: 0;
    box-sizing: border-box;
}

/* Sidebar */
.profile-sidebar {
    flex-shrink: 0;
    width: 280px;
}

.profile-sidebar-content {
    position: sticky;
    top: 24px;
}

/* User Card */
.profile-user-card {
    background-color: var(--profile-bg-primary);
    border: 1px solid var(--profile-border-color);
    border-radius: 8px;
    padding: 28px 24px;
    text-align: center;
    box-shadow: var(--profile-shadow-md);
    margin-bottom: 8px;
}

.user-avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 16px;
    width: 120px;
    height: 120px;
}

.user-avatar-image {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3px solid var(--profile-border-color);
    object-fit: cover;
    display: block;
}

.user-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--profile-primary-color) 0%, var(--profile-primary-dark) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
    border: 3px solid var(--profile-border-color);
}

.user-display-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--profile-text-primary);
    margin: 0 0 4px 0;
}

.user-email-address {
    font-size: 12px;
    color: var(--profile-text-secondary);
    margin: 0;
    word-break: break-all;
}

/* Navigation Items */
.profile-nav-items {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 28px;
}

.profile-nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background-color: var(--profile-bg-primary);
    border: 1px solid var(--profile-border-color);
    border-radius: 6px;
    color: var(--profile-text-primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all var(--profile-transition);
    cursor: pointer;
}

.profile-nav-item:hover {
    background-color: var(--profile-bg-secondary);
    border-color: var(--jira-blue) !important;
    color: var(--jira-blue) !important;
}

.profile-nav-item.active {
    background-color: rgba(139, 25, 86, 0.08);
    border-color: var(--jira-blue) !important;
    color: var(--jira-blue) !important;
    box-shadow: inset 3px 0 0 var(--jira-blue);
}

.profile-nav-item i:first-child {
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
    transition: opacity var(--profile-transition);
}

.profile-nav-item:hover .nav-chevron {
    opacity: 1;
}

/* Stats Section */
.profile-stats-section {
    background-color: var(--profile-bg-primary);
    border: 1px solid var(--profile-border-color);
    border-radius: 8px;
    padding: 20px;
    box-shadow: var(--profile-shadow-md);
}

.stats-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--profile-text-secondary);
    margin: 0 0 16px 0;
    letter-spacing: 0.5px;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--profile-text-primary);
    display: block;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 11px;
    color: var(--profile-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Main Content */
.profile-main-content {
    flex: 1;
    min-width: 0;
}

/* Profile Card */
.profile-card {
    background-color: var(--profile-bg-primary);
    border: 1px solid var(--profile-border-color);
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: var(--profile-shadow-md);
    overflow: hidden;
}

.profile-card-header {
    padding: 20px 24px;
    background-color: var(--profile-bg-secondary);
    border-bottom: 1px solid var(--profile-border-color);
}

.profile-section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--profile-text-primary);
    margin: 0 0 4px 0;
}

.profile-section-description {
    font-size: 13px;
    color: var(--profile-text-secondary);
    margin: 0;
}

.profile-card-body {
    padding: 24px;
}

/* Form */
.profile-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-field-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--profile-text-primary);
}

.form-field-select {
    padding: 8px 12px;
    font-size: 13px;
    border: 1px solid var(--profile-border-color);
    border-radius: 4px;
    background-color: var(--profile-bg-primary);
    color: var(--profile-text-primary);
    transition: all var(--profile-transition);
}

.form-field-select:hover {
    border-color: var(--jira-blue) !important;
}

.form-field-select:focus {
    outline: none;
    border-color: var(--jira-blue) !important;
    box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
}

.form-field-hint {
    font-size: 12px;
    color: var(--profile-text-secondary);
    margin: 0;
}

/* Checkbox Group */
.settings-checkbox-group {
    gap: 8px;
}

.settings-checkbox-group .form-field-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 0;
    cursor: pointer;
    font-weight: 500;
}

.settings-checkbox-group .form-check-input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--profile-primary-color);
    flex-shrink: 0;
    margin: 0;
}

/* Form Actions */
.profile-form-actions {
    display: flex;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid var(--profile-border-color);
    margin-top: 8px;
}

.btn-primary-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    background-color: var(--profile-primary-color);
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--profile-transition);
}

.btn-primary-action:hover {
    background-color: var(--profile-primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--profile-shadow-lg);
}

.btn-secondary-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    background-color: transparent;
    color: var(--profile-primary-color);
    border: 1px solid var(--profile-border-color);
    border-radius: 4px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--profile-transition);
}

.btn-secondary-action:hover {
    background-color: var(--profile-bg-secondary);
    border-color: var(--profile-primary-color);
}

/* Responsive Design */
@media (max-width: 1199px) {
    .profile-content-container {
        gap: 28px;
        padding: 28px 40px;
    }

    .profile-sidebar {
        width: 260px;
    }
}

@media (max-width: 991px) {
    .profile-content-container {
        flex-direction: column;
        gap: 28px;
        padding: 24px 32px;
    }

    .profile-sidebar {
        width: 100%;
    }

    .profile-sidebar-content {
        position: static;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-row-group {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .profile-page-header {
        padding: 20px 24px;
    }

    .profile-page-title {
        font-size: 24px;
    }

    .profile-content-container {
        padding: 20px 24px;
        gap: 20px;
    }

    .profile-sidebar-content {
        grid-template-columns: 1fr;
    }

    .profile-card-body {
        padding: 24px;
    }

    .form-field-select {
        font-size: 16px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .profile-nav-items {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }

    .profile-nav-item {
        border-bottom: 1px solid var(--profile-border-color);
        border-right: 1px solid var(--profile-border-color);
    }

    .profile-nav-item:nth-child(2n) {
        border-right: none;
    }

    .profile-nav-item:nth-last-child(-n + 2) {
        border-bottom: none;
    }

    .nav-chevron {
        display: none;
    }
}

@media (max-width: 480px) {
    .profile-breadcrumb {
        padding: 0 16px;
        font-size: 12px;
    }

    .profile-page-header {
        padding: 16px;
    }

    .profile-page-title {
        font-size: 20px;
    }

    .profile-page-subtitle {
        font-size: 12px;
    }

    .profile-content-container {
        padding: 12px;
    }

    .profile-sidebar {
        width: 100%;
    }

    .profile-sidebar-content {
        position: static;
    }

    .profile-user-card {
        padding: 16px;
    }

    .user-avatar-wrapper {
        width: 100px;
        height: 100px;
    }

    .user-avatar-placeholder {
        font-size: 40px;
    }

    .user-display-name {
        font-size: 14px;
    }

    .profile-nav-items {
        grid-template-columns: 1fr;
    }

    .profile-nav-item {
        border-right: none;
        padding: 12px;
        font-size: 13px;
    }

    .profile-nav-item:nth-child(2n) {
        border-right: 1px solid var(--profile-border-color);
    }

    .profile-card-body {
        padding: 12px;
    }

    .form-row-group {
        grid-template-columns: 1fr;
    }

    .profile-form-actions {
        flex-direction: column;
    }

    .btn-primary-action,
    .btn-secondary-action {
        width: 100%;
    }
}

/* Timezone Detection Styles */
.timezone-label-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.timezone-detect-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background-color: rgba(139, 25, 86, 0.1);
    color: var(--jira-blue);
    border: 1px solid var(--jira-blue);
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.timezone-detect-btn:hover {
    background-color: rgba(139, 25, 86, 0.15);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(139, 25, 86, 0.15);
}

.timezone-detect-btn:active {
    transform: translateY(0);
}

.timezone-detect-btn i {
    font-size: 14px;
}

.timezone-detect-btn.detecting {
    opacity: 0.7;
    pointer-events: none;
}

.timezone-detect-btn.detected {
    background-color: #E8F5E9;
    color: #2E7D32;
    border-color: #2E7D32;
}

/* Time Tracking Rates Styles */
.rate-info-banner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background-color: #FEF5E7;
    border: 1px solid #F9E79F;
    border-radius: 6px;
    margin-bottom: 24px;
    font-size: 13px;
    color: #7D6608;
}

.rate-info-banner i {
    flex-shrink: 0;
    color: #F39C12;
    font-size: 16px;
    margin-top: 2px;
}

.input-with-currency {
    position: relative;
    display: flex;
    align-items: center;
}

.currency-symbol {
    position: absolute;
    left: 12px;
    font-size: 14px;
    font-weight: 600;
    color: var(--profile-text-secondary);
    pointer-events: none;
}

.form-field-input {
    width: 100%;
    padding: 8px 12px 8px 30px;
    font-size: 13px;
    border: 1px solid var(--profile-border-color);
    border-radius: 4px;
    background-color: var(--profile-bg-primary);
    color: var(--profile-text-primary);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-field-input:hover {
    border-color: var(--jira-blue);
}

.form-field-input:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
}

.rate-divider {
    height: 1px;
    background-color: var(--profile-border-color);
    margin: 24px 0;
}

.rates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.rate-card {
    padding: 20px;
    background: linear-gradient(135deg, rgba(139, 25, 86, 0.05) 0%, rgba(231, 120, 23, 0.05) 100%);
    border: 1px solid var(--profile-border-color);
    border-radius: 8px;
    text-align: center;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.rate-card:hover {
    border-color: var(--jira-blue);
    box-shadow: 0 2px 8px rgba(139, 25, 86, 0.1);
    transform: translateY(-2px);
}

.rate-label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--profile-text-secondary);
    margin-bottom: 8px;
    letter-spacing: 0.5px;
}

.rate-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--jira-blue);
    margin-bottom: 8px;
    font-family: 'Courier New', monospace;
    min-height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rate-unit {
    font-size: 12px;
    color: var(--profile-text-secondary);
}

.rate-calculation-info {
    padding: 16px;
    background-color: var(--profile-bg-secondary);
    border: 1px solid var(--profile-border-color);
    border-radius: 6px;
    margin-bottom: 24px;
}

.rate-calculation-info h4 {
    margin: 0 0 12px 0;
    font-size: 13px;
    font-weight: 700;
    color: var(--profile-text-primary);
}

.rate-calculation-info ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.rate-calculation-info li {
    font-size: 12px;
    color: var(--profile-text-secondary);
    padding: 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rate-calculation-info li:before {
    content: "•";
    color: var(--jira-blue);
    font-weight: bold;
}

@media (max-width: 768px) {
    .rates-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .rate-value {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .rates-grid {
        grid-template-columns: 1fr;
    }

    .rate-card {
        padding: 16px;
    }

    .rate-value {
        font-size: 18px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const detectBtn = document.getElementById('timezoneDetectBtn');
    const timezoneSelect = document.getElementById('timezoneSelect');

    // Timezone mapping: Intl timezone names to our options
    const timezoneMapping = {
        // North America - EST
        'America/New_York': 'EST',
        'America/Toronto': 'EST',
        'America/Jamaica': 'EST',
        'America/Puerto_Rico': 'EST',
        'America/Grenada': 'EST',

        // Central America - CST
        'America/Chicago': 'CST',
        'America/Mexico_City': 'CST',
        'America/Denver': 'MST',
        'America/Boise': 'MST',
        'America/Edmonton': 'MST',

        // Mountain/Pacific - PST/MST
        'America/Los_Angeles': 'PST',
        'America/Anchorage': 'PST',
        'America/Vancouver': 'PST',

        // UTC
        'UTC': 'UTC',
        'Etc/UTC': 'UTC',
        'Etc/GMT': 'UTC',
        'Etc/Zulu': 'UTC',

        // GMT - UK and West Africa
        'Europe/London': 'GMT',
        'Europe/Lisbon': 'GMT',
        'Africa/Casablanca': 'GMT',
        'Africa/Lagos': 'GMT',
        'Africa/Accra': 'GMT',

        // CET - Central Europe
        'Europe/Paris': 'CET',
        'Europe/Berlin': 'CET',
        'Europe/Madrid': 'CET',
        'Europe/Rome': 'CET',
        'Europe/Amsterdam': 'CET',
        'Europe/Brussels': 'CET',
        'Europe/Vienna': 'CET',
        'Europe/Prague': 'CET',
        'Europe/Budapest': 'CET',
        'Europe/Warsaw': 'CET',
        'Europe/Stockholm': 'CET',
        'Europe/Athens': 'CET',
        'Africa/Cairo': 'CET',
        'Africa/Johannesburg': 'CET',

        // IST - India
        'Asia/Kolkata': 'IST',
        'Asia/Colombo': 'IST',

        // Additional mappings
        'America/Havaii': 'PST',
        'Pacific/Auckland': 'UTC',
        'Australia/Sydney': 'UTC',
    };

    // Function to get device timezone
    function getDeviceTimezone() {
        try {
            // Get timezone from Intl API
            const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            return timeZone;
        } catch (e) {
            return null;
        }
    }

    // Function to map timezone to our options
    function mapTimezone(intlTimezone) {
        // Check direct mapping
        if (timezoneMapping[intlTimezone]) {
            return timezoneMapping[intlTimezone];
        }

        // Check partial matches
        for (const [key, value] of Object.entries(timezoneMapping)) {
            if (intlTimezone.startsWith(key.split('/')[0])) {
                return value;
            }
        }

        // Default to UTC if no match
        return 'UTC';
    }

    // Auto-detect on page load if timezone not already set
    function autoDetectTimezone() {
        const deviceTimezone = getDeviceTimezone();
        if (deviceTimezone) {
            const mappedTimezone = mapTimezone(deviceTimezone);
            timezoneSelect.value = mappedTimezone;
            detectBtn.classList.add('detected');
            console.log('Timezone auto-detected:', deviceTimezone, '→', mappedTimezone);
        }
    }

    // Detect button click handler
    detectBtn.addEventListener('click', function(e) {
        e.preventDefault();
        detectBtn.classList.add('detecting');
        
        setTimeout(() => {
            autoDetectTimezone();
            detectBtn.classList.remove('detecting');
        }, 300);
    });

    // Auto-detect on page load
    autoDetectTimezone();

    // ===== TIME TRACKING RATE CALCULATION =====
    const annualPackageInput = document.getElementById('annualPackageInput');
    const hourlyRateElement = document.getElementById('hourlyRate');
    const minuteRateElement = document.getElementById('minuteRate');
    const secondRateElement = document.getElementById('secondRate');
    const dailyRateElement = document.getElementById('dailyRate');
    const currencySelect = document.getElementById('currencySelect');
    const currencyInfoElement = document.getElementById('currencyInfo');
    const currencySymbolDisplay = document.getElementById('currencySymbolDisplay');
    const packageHint = document.getElementById('packageHint');

    // Currency information map
    const currencyInfo = {
        'USD': { symbol: '$', name: 'US Dollar', hint: 'Your annual salary or total package in USD' },
        'EUR': { symbol: '€', name: 'Euro', hint: 'Your annual salary or total package in EUR' },
        'GBP': { symbol: '£', name: 'British Pound', hint: 'Your annual salary or total package in GBP' },
        'INR': { symbol: '₹', name: 'Indian Rupee', hint: 'Your annual salary or total package in INR' }
    };

    // Time tracking constants
    const WORKING_HOURS_PER_DAY = 8.5; // 9:30 AM to 6:00 PM
    const WORKING_DAYS_PER_WEEK = 5; // Monday to Friday (excluding Sundays)
    const WEEKS_PER_YEAR = 52;
    const WORKING_DAYS_PER_YEAR = WORKING_DAYS_PER_WEEK * WEEKS_PER_YEAR; // 260 days
    const WORKING_HOURS_PER_YEAR = WORKING_DAYS_PER_YEAR * WORKING_HOURS_PER_DAY; // 2,210 hours

    // Function to update currency display
    function updateCurrencyDisplay() {
        const currency = currencySelect.value;
        const currencyData = currencyInfo[currency];
        
        // Update currency symbol in input
        currencySymbolDisplay.textContent = currencyData.symbol;
        
        // Update hint text
        packageHint.textContent = currencyData.hint;
        
        // Update currency info
        currencyInfoElement.innerHTML = `<strong>Currency:</strong> ${currency} (${currencyData.name})`;
    }

    // Function to calculate and display rates
    function calculateRates() {
        const annualPackage = parseFloat(annualPackageInput.value);
        const currency = currencySelect.value;
        const currencySymbol = currencyInfo[currency].symbol;

        if (!annualPackage || isNaN(annualPackage) || annualPackage <= 0) {
            // Show placeholder when no value
            hourlyRateElement.textContent = '--';
            minuteRateElement.textContent = '--';
            secondRateElement.textContent = '--';
            dailyRateElement.textContent = '--';
            return;
        }

        // Calculate rates
        const hourlyRate = annualPackage / WORKING_HOURS_PER_YEAR;
        const minuteRate = hourlyRate / 60;
        const secondRate = minuteRate / 60;
        const dailyRate = hourlyRate * WORKING_HOURS_PER_DAY;

        // Format and display
        hourlyRateElement.textContent = `${currencySymbol}${hourlyRate.toFixed(2)}`;
        minuteRateElement.textContent = `${currencySymbol}${minuteRate.toFixed(4)}`;
        secondRateElement.textContent = `${currencySymbol}${secondRate.toFixed(6)}`;
        dailyRateElement.textContent = `${currencySymbol}${dailyRate.toFixed(2)}`;

        // Log to console for verification
        console.log('⏱️ Time Tracking Rates Calculated (India Context):');
        console.log('Annual Package:', `${currencySymbol}${annualPackage.toLocaleString()}`);
        console.log('Currency:', currency);
        console.log('Hourly Rate:', `${currencySymbol}${hourlyRate.toFixed(2)}`);
        console.log('Minute Rate:', `${currencySymbol}${minuteRate.toFixed(4)}`);
        console.log('Second Rate:', `${currencySymbol}${secondRate.toFixed(6)}`);
        console.log('Daily Rate (8.5 hrs):', `${currencySymbol}${dailyRate.toFixed(2)}`);
        console.log('Working Hours/Year:', WORKING_HOURS_PER_YEAR);
        console.log('Working Days/Year:', WORKING_DAYS_PER_YEAR);
    }

    // Add event listeners
    annualPackageInput.addEventListener('input', calculateRates);
    annualPackageInput.addEventListener('change', calculateRates);
    currencySelect.addEventListener('change', function() {
        updateCurrencyDisplay();
        calculateRates();
    });

    // Initialize on page load
    updateCurrencyDisplay();
    calculateRates();
});
</script>

<?php \App\Core\View::endSection(); ?>
