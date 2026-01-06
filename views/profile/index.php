<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="profile-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="profile-breadcrumb-section">
        <div class="profile-breadcrumb">
            <a href="<?= url('/') ?>" class="breadcrumb-link">
                <i class="bi bi-house-door"></i> Home
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Profile</span>
        </div>
    </div>

    <!-- Page Header -->
    <div class="profile-page-header">
        <div class="profile-header-left">
            <h1 class="profile-page-title">Account Settings <span class="profile-page-subtitle">— Manage your profile,
                    security, and preferences</span></h1>
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
                        <?php if (($avatarUrl = avatar($user['avatar'] ?? null))): ?>
                            <img src="<?= e($avatarUrl) ?>" class="user-avatar-image" alt="Avatar">
                        <?php else: ?>
                            <div class="user-avatar-placeholder">
                                <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <button class="avatar-upload-btn" title="Change avatar" data-bs-toggle="modal"
                            data-bs-target="#avatarModal">
                            <i class="bi bi-camera-fill"></i>
                        </button>
                    </div>
                    <h3 class="user-display-name">
                        <?= e($user['display_name'] ?? $user['first_name'] . ' ' . $user['last_name']) ?></h3>
                    <p class="user-email-address"><?= e($user['email']) ?></p>
                </div>

                <!-- Navigation Items -->
                <nav class="profile-nav-items">
                    <a href="<?= url('/profile') ?>" class="profile-nav-item active" data-section="profile">
                        <i class="bi bi-person"></i>
                        <span>Profile</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/notifications') ?>" class="profile-nav-item"
                        data-section="notifications">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/security') ?>" class="profile-nav-item" data-section="security">
                        <i class="bi bi-shield-lock"></i>
                        <span>Security</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                    <a href="<?= url('/profile/settings') ?>" class="profile-nav-item" data-section="settings">
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
            <!-- Profile Information Section -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2 class="profile-section-title">Profile Information</h2>
                    <p class="profile-section-description">Update your basic account details</p>
                </div>
                <div class="profile-card-body">
                    <form action="<?= url('/profile') ?>" method="POST" class="profile-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-row-group">
                            <div class="form-field">
                                <label class="form-field-label">First Name</label>
                                <input type="text" name="first_name" class="form-field-input"
                                    value="<?= e($user['first_name'] ?? '') ?>" placeholder="Enter your first name"
                                    required>
                                <small class="form-field-hint">Your first name</small>
                            </div>
                            <div class="form-field">
                                <label class="form-field-label">Last Name</label>
                                <input type="text" name="last_name" class="form-field-input"
                                    value="<?= e($user['last_name'] ?? '') ?>" placeholder="Enter your last name"
                                    required>
                                <small class="form-field-hint">Your last name</small>
                            </div>
                        </div>

                        <div class="form-field">
                            <label class="form-field-label">Email Address</label>
                            <input type="email" name="email" class="form-field-input" value="<?= e($user['email']) ?>"
                                placeholder="Enter your email address" required>
                            <small class="form-field-hint">Used for login and notifications</small>
                        </div>

                        <div class="form-field">
                            <label class="form-field-label">Timezone</label>
                            <select name="timezone" class="form-field-select">
                                <?php foreach ($timezones ?? [] as $tz): ?>
                                    <option value="<?= e($tz) ?>" <?= ($user['timezone'] ?? 'UTC') === $tz ? 'selected' : '' ?>>
                                        <?= e($tz) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-field-hint">Your local timezone for timestamps</small>
                        </div>

                        <div class="profile-form-actions">
                            <button type="submit" class="btn-primary-action">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2 class="profile-section-title">Password</h2>
                    <p class="profile-section-description">Update your password regularly for security</p>
                </div>
                <div class="profile-card-body">
                    <form action="<?= url('/profile/password') ?>" method="POST" class="profile-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">

                        <div class="form-field">
                            <label class="form-field-label">Current Password</label>
                            <input type="password" name="current_password" class="form-field-input"
                                placeholder="Enter your current password" required>
                            <small class="form-field-hint">Required to change your password</small>
                        </div>

                        <div class="form-row-group">
                            <div class="form-field">
                                <label class="form-field-label">New Password</label>
                                <input type="password" name="new_password" class="form-field-input"
                                    placeholder="Enter a new password" required minlength="8">
                                <small class="form-field-hint">At least 8 characters</small>
                            </div>
                            <div class="form-field">
                                <label class="form-field-label">Confirm Password</label>
                                <input type="password" name="new_password_confirmation" class="form-field-input"
                                    placeholder="Confirm your password" required>
                                <small class="form-field-hint">Must match new password</small>
                            </div>
                        </div>

                        <div class="profile-form-actions">
                            <button type="submit" class="btn-secondary-action">
                                <i class="bi bi-lock"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Summary -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h2 class="profile-section-title">Activity Summary</h2>
                    <p class="profile-section-description">Overview of your account activity</p>
                </div>
                <div class="profile-card-body">
                    <div class="activity-stats-grid">
                        <div class="activity-stat-card">
                            <div class="activity-stat-icon"
                                style="background-color: rgba(139, 25, 86, 0.1); color: #8B1956;">
                                <i class="bi bi-list-check"></i>
                            </div>
                            <div class="activity-stat-content">
                                <div class="activity-stat-value"><?= e($stats['issues_assigned'] ?? 0) ?></div>
                                <div class="activity-stat-label">Issues Assigned</div>
                            </div>
                        </div>
                        <div class="activity-stat-card">
                            <div class="activity-stat-icon"
                                style="background-color: rgba(54, 179, 126, 0.1); color: #36B37E;">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div class="activity-stat-content">
                                <div class="activity-stat-value"><?= e($stats['issues_completed'] ?? 0) ?></div>
                                <div class="activity-stat-label">Issues Completed</div>
                            </div>
                        </div>
                        <div class="activity-stat-card">
                            <div class="activity-stat-icon"
                                style="background-color: rgba(255, 171, 0, 0.1); color: #FFAB00;">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <div class="activity-stat-content">
                                <div class="activity-stat-value"><?= e($stats['comments_made'] ?? 0) ?></div>
                                <div class="activity-stat-label">Comments Made</div>
                            </div>
                        </div>
                        <div class="activity-stat-card">
                            <div class="activity-stat-icon"
                                style="background-color: rgba(0, 184, 217, 0.1); color: #00B8D9;">
                                <i class="bi bi-folder"></i>
                            </div>
                            <div class="activity-stat-content">
                                <div class="activity-stat-value"><?= e($stats['projects_count'] ?? 0) ?></div>
                                <div class="activity-stat-label">Projects</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Step 1: Upload -->
            <div id="avatarStep1">
                <div class="modal-body">
                    <div class="avatar-upload-area">
                        <input type="file" id="avatarInput" class="avatar-file-input" accept="image/*" hidden>
                        <label for="avatarInput" class="avatar-upload-label">
                            <div class="upload-icon">
                                <i class="bi bi-cloud-upload"></i>
                            </div>
                            <h4 class="upload-title">Upload Image</h4>
                            <p class="upload-description">Drag and drop your image here or click to select</p>
                            <small class="upload-hint">PNG, JPG, GIF • Max 2MB</small>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="nextStep" disabled>Next</button>
                </div>
            </div>

            <!-- Step 2: Crop -->
            <div id="avatarStep2" style="display: none;">
                <div class="modal-body">
                    <p class="crop-instruction">Adjust your avatar in the circle below</p>
                    <div class="crop-container">
                        <div class="crop-circle-frame">
                            <img id="cropperImage" alt="Cropper" style="display: none;">
                        </div>
                    </div>
                    <div class="crop-zoom-control">
                        <label class="crop-zoom-label">Zoom</label>
                        <input type="range" id="zoomSlider" class="crop-zoom-slider" min="1" max="3" step="0.1"
                            value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" id="backStep">Back</button>
                    <button type="button" class="btn btn-primary" id="uploadAvatar">Upload Avatar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cropper.js Library -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

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

    .avatar-upload-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #8B1956;
        color: white;
        border: 2px solid var(--profile-bg-primary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all var(--profile-transition);
        box-shadow: var(--profile-shadow-md);
    }

    .avatar-upload-btn:hover {
        background-color: #6F123F;
        transform: scale(1.1);
        box-shadow: var(--profile-shadow-lg);
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
        padding: 24px;
        box-shadow: var(--profile-shadow-md);
    }

    .stats-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--profile-text-secondary);
        margin: 0 0 12px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .stat-item {
        text-align: center;
        padding: 12px;
        background-color: var(--profile-bg-secondary);
        border-radius: 6px;
        border: 1px solid var(--profile-border-color);
    }

    .stat-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--profile-text-primary);
    }

    .stat-label {
        font-size: 11px;
        color: var(--profile-text-secondary);
        margin-top: 4px;
    }

    /* Main Content */
    .profile-main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    /* Cards */
    .profile-card {
        background-color: var(--profile-bg-primary);
        border: 1px solid var(--profile-border-color);
        border-radius: 8px;
        box-shadow: var(--profile-shadow-md);
        overflow: hidden;
    }

    .profile-card-header {
        padding: 24px;
        border-bottom: 1px solid var(--profile-border-color);
        background-color: var(--profile-bg-secondary);
    }

    .profile-card:first-of-type .profile-card-header {
        padding: 32px 28px;
    }

    .profile-section-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--profile-text-primary);
        margin: 0 0 4px 0;
    }

    .profile-card:first-of-type .profile-section-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .profile-section-description {
        font-size: 12px;
        color: var(--profile-text-secondary);
        margin: 0;
    }

    .profile-card:first-of-type .profile-section-description {
        font-size: 14px;
        color: var(--profile-text-secondary);
    }

    .profile-card-body {
        padding: 32px;
    }

    /* Forms */
    .profile-form {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .form-row-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 28px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-field-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--profile-text-primary);
    }

    .form-field-input,
    .form-field-select {
        padding: 12px 14px;
        border: 1px solid var(--profile-border-color);
        border-radius: 6px;
        font-size: 14px;
        color: var(--profile-text-primary);
        background-color: var(--profile-bg-primary);
        transition: all var(--profile-transition);
        font-family: inherit;
    }

    .form-field-input:focus,
    .form-field-select:focus {
        outline: none;
        border-color: var(--profile-primary-color);
        box-shadow: 0 0 0 3px rgba(0, 82, 204, 0.1);
        background-color: var(--profile-bg-primary);
    }

    .form-field-input::placeholder {
        color: var(--profile-text-muted);
    }

    .form-field-hint {
        font-size: 12px;
        color: var(--profile-text-secondary);
    }

    .profile-form-actions {
        display: flex;
        gap: 12px;
        margin-top: 12px;
    }

    .btn-primary-action,
    .btn-secondary-action {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--profile-transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary-action {
        background-color: #8B1956;
        color: white;
    }

    .btn-primary-action:hover {
        background-color: #6F123F;
    }

    .btn-secondary-action {
        background-color: var(--profile-border-color);
        color: var(--profile-text-primary);
    }

    .btn-secondary-action:hover {
        background-color: var(--profile-bg-secondary);
    }

    /* Activity Stats Grid */
    .activity-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .activity-stat-card {
        display: flex;
        gap: 16px;
        padding: 16px;
        background-color: var(--profile-bg-secondary);
        border: 1px solid var(--profile-border-color);
        border-radius: 8px;
        transition: all var(--profile-transition);
    }

    .activity-stat-card:hover {
        background-color: var(--profile-bg-primary);
        box-shadow: var(--profile-shadow-md);
    }

    .activity-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .activity-stat-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .activity-stat-value {
        font-size: 18px;
        font-weight: 700;
        color: var(--profile-text-primary);
    }

    .activity-stat-label {
        font-size: 12px;
        color: var(--profile-text-secondary);
        margin-top: 2px;
    }

    /* Avatar Modal */
    .avatar-upload-area {
        border: 2px dashed var(--profile-border-color);
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all var(--profile-transition);
    }

    .avatar-upload-label {
        cursor: pointer;
        display: block;
    }

    .avatar-upload-area:hover {
        border-color: var(--profile-primary-color);
        background-color: var(--profile-primary-light);
    }

    .upload-icon {
        font-size: 48px;
        color: var(--profile-primary-color);
        margin-bottom: 12px;
    }

    .upload-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--profile-text-primary);
        margin: 0 0 4px 0;
    }

    .upload-description {
        font-size: 13px;
        color: var(--profile-text-secondary);
        margin: 0 0 8px 0;
    }

    .upload-hint {
        font-size: 11px;
        color: var(--profile-text-muted);
    }

    .crop-instruction {
        text-align: center;
        color: var(--profile-text-secondary);
        margin-bottom: 16px;
        font-size: 14px;
    }

    .crop-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .crop-circle-frame {
        position: relative;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid var(--profile-border-color);
        background-color: var(--profile-bg-secondary);
    }

    .crop-circle-frame img {
        width: 100%;
        height: 100%;
        display: block;
    }

    .crop-zoom-control {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .crop-zoom-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--profile-text-primary);
        white-space: nowrap;
    }

    .crop-zoom-slider {
        flex: 1;
        height: 4px;
        border-radius: 2px;
        background: linear-gradient(90deg, var(--profile-bg-secondary), var(--profile-border-color));
        outline: none;
        -webkit-appearance: none;
        appearance: none;
    }

    .crop-zoom-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: var(--profile-primary-color);
        cursor: pointer;
        transition: all var(--profile-transition);
    }

    .crop-zoom-slider::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: var(--profile-primary-color);
        cursor: pointer;
        border: none;
        transition: all var(--profile-transition);
    }

    .crop-zoom-slider::-webkit-slider-thumb:hover {
        background-color: var(--profile-primary-dark);
        box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
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

        .activity-stats-grid {
            grid-template-columns: repeat(2, 1fr);
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

        .form-field-input,
        .form-field-select {
            font-size: 16px;
        }

        .activity-stats-grid {
            grid-template-columns: 1fr;
        }

        .activity-stat-card {
            gap: 12px;
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

        .crop-circle-frame {
            width: 250px;
            height: 250px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let selectedFile = null;
        let cropper = null;

        // File input change event
        const avatarInput = document.getElementById('avatarInput');
        avatarInput.addEventListener('change', function (e) {
            selectedFile = e.target.files[0];
            const nextBtn = document.getElementById('nextStep');

            if (!selectedFile) {
                nextBtn.disabled = true;
                return;
            }

            // Validate file size (2MB)
            if (selectedFile.size > 2 * 1024 * 1024) {
                alert('File size exceeds 2MB limit');
                nextBtn.disabled = true;
                selectedFile = null;
                return;
            }

            nextBtn.disabled = false;
        });

        // Next Step - Show Cropper
        document.getElementById('nextStep').addEventListener('click', function () {
            if (!selectedFile) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.getElementById('cropperImage');
                img.src = e.target.result;
                img.style.display = 'block';

                // Destroy previous cropper
                if (cropper) {
                    cropper.destroy();
                }

                // Initialize Cropper with circular aspect
                cropper = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    restore: true,
                    guides: false,
                    center: true,
                    highlight: true,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                });
            };
            reader.readAsDataURL(selectedFile);

            // Show step 2, hide step 1
            document.getElementById('avatarStep1').style.display = 'none';
            document.getElementById('avatarStep2').style.display = 'block';
        });

        // Back button
        document.getElementById('backStep').addEventListener('click', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            document.getElementById('avatarStep1').style.display = 'block';
            document.getElementById('avatarStep2').style.display = 'none';
            document.getElementById('avatarInput').value = '';
        });

        // Zoom slider
        document.getElementById('zoomSlider').addEventListener('input', function (e) {
            if (cropper) {
                cropper.zoomTo(parseFloat(e.target.value));
            }
        });

        // Upload Avatar
        document.getElementById('uploadAvatar').addEventListener('click', function () {
            if (!cropper) return;

            const uploadBtn = this;
            const originalText = uploadBtn.innerText;
            uploadBtn.disabled = true;
            uploadBtn.innerText = 'Uploading...';

            const canvas = cropper.getCroppedCanvas({
                maxWidth: 400,
                maxHeight: 400,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            canvas.toBlob(function (blob) {
                const formData = new FormData();
                formData.append('avatar', blob, 'avatar.png');

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (csrfToken) {
                    formData.append('_csrf_token', csrfToken);
                }

                fetch('<?= url("/profile/avatar") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
                            modal.hide();
                            alert('Avatar uploaded successfully!');
                            setTimeout(() => location.reload(), 800);
                        } else {
                            throw new Error(data.error || 'Upload failed');
                        }
                    })
                    .catch(error => {
                        uploadBtn.disabled = false;
                        uploadBtn.innerText = originalText;
                        console.error('Upload error:', error);
                        alert('Error: ' + error.message);
                    });
            }, 'image/png');
        });

        // Reset modal on close
        document.getElementById('avatarModal').addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            document.getElementById('avatarStep1').style.display = 'block';
            document.getElementById('avatarStep2').style.display = 'none';
            document.getElementById('avatarInput').value = '';
            document.getElementById('zoomSlider').value = 1;
        });

        // Drag and drop for avatar upload
        const uploadArea = document.querySelector('.avatar-upload-area');
        if (uploadArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    uploadArea.style.borderColor = '#8B1956';
                    uploadArea.style.backgroundColor = '#f0dce5';
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, () => {
                    uploadArea.style.borderColor = '';
                    uploadArea.style.backgroundColor = '';
                });
            });

            uploadArea.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                avatarInput.files = files;
                const event = new Event('change', { bubbles: true });
                avatarInput.dispatchEvent(event);
            });
        }
    });
</script>

<?php \App\Core\View::endSection(); ?>