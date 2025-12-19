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
            <a href="<?= url('/profile') ?>" class="breadcrumb-link">Profile</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">API Tokens</span>
        </div>
    </div>

    <!-- Page Header -->
    <div class="profile-page-header">
        <div class="profile-header-left">
            <h1 class="profile-page-title">API Tokens <span class="profile-page-subtitle">— Manage your personal API tokens for third-party integrations</span></h1>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="profile-content-container">
        <!-- Sidebar Navigation -->
        <div class="profile-sidebar">
            <div class="profile-sidebar-content">
                <!-- User Card -->
                <div class="profile-user-card">
                    <div class="user-avatar-wrapper" style="margin-bottom: 0;">
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
                    <a href="<?= url('/profile/tokens') ?>" class="profile-nav-item active" data-section="tokens">
                        <i class="bi bi-key"></i>
                        <span>API Tokens</span>
                        <i class="bi bi-chevron-right nav-chevron"></i>
                    </a>
                </nav>

                <!-- Activity Stats -->
                <div class="profile-stats-section">
                    <h4 class="stats-title">Activity</h4>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?= e(count($tokens ?? [])) ?></div>
                            <div class="stat-label">Active Tokens</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= e($stats['issues_assigned'] ?? 0) ?></div>
                            <div class="stat-label">Assigned</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="profile-main-content">
            <!-- API Tokens Section -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h2 class="profile-section-title">API Tokens</h2>
                            <p class="profile-section-description">Manage your personal API tokens for authentication</p>
                        </div>
                        <button class="btn-tokens-create" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                            <i class="bi bi-plus-lg"></i> Create Token
                        </button>
                    </div>
                </div>
                <div class="profile-card-body">
                    <div class="token-info-box">
                        <i class="bi bi-info-circle"></i>
                        <div>
                            <strong>Secure Your Tokens</strong>
                            <p>API tokens allow you to authenticate with the API without using your password. Treat them like passwords and keep them secure.</p>
                        </div>
                    </div>

                    <?php 
                    $successMsg = $flash['success'] ?? '';
                    $newToken = \App\Core\Session::getFlash('new_token');
                    if (!empty($successMsg) && !empty($newToken)): 
                    ?>
                    <div class="token-success-box">
                        <i class="bi bi-check-circle"></i>
                        <div>
                            <strong>Token Created Successfully</strong>
                            <p>Make sure to copy your new token now. You won't be able to see it again!</p>
                            <div class="token-copy-group">
                                <input type="text" class="token-copy-input" value="<?= e($newToken) ?>" readonly id="newTokenValue">
                                <button class="token-copy-btn" onclick="copyToken()">
                                    <i class="bi bi-clipboard"></i> Copy
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (empty($tokens)): ?>
                    <div class="tokens-empty-state">
                        <i class="bi bi-key"></i>
                        <h5>No API Tokens</h5>
                        <p>Create a token to authenticate with the API.</p>
                        <button class="btn-tokens-create-alt" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                            Create Your First Token
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="tokens-list">
                        <?php foreach ($tokens as $token): ?>
                        <div class="token-item">
                            <div class="token-item-header">
                                <div class="token-item-info">
                                    <h4 class="token-item-name"><?= e($token['name']) ?></h4>
                                    <code class="token-item-preview"><?= e(substr($token['token_preview'] ?? '****', 0, 12)) ?>...</code>
                                </div>
                                <div class="token-item-meta">
                                    <span class="token-badge">Full Access</span>
                                </div>
                            </div>
                            <div class="token-item-details">
                                <div class="token-detail">
                                    <span class="detail-label">Last Used</span>
                                    <span class="detail-value">
                                        <?php if ($token['last_used_at']): ?>
                                            <?= time_ago($token['last_used_at']) ?>
                                        <?php else: ?>
                                            Never
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="token-detail">
                                    <span class="detail-label">Created</span>
                                    <span class="detail-value"><?= format_date($token['created_at']) ?></span>
                                </div>
                                <div class="token-detail">
                                    <span class="detail-label">Expires</span>
                                    <span class="detail-value <?= (!empty($token['expires_at']) && strtotime($token['expires_at']) < time()) ? 'detail-expired' : '' ?>">
                                        <?php if (!empty($token['expires_at'])): ?>
                                            <?php if (strtotime($token['expires_at']) < time()): ?>
                                                Expired
                                            <?php else: ?>
                                                <?= format_date($token['expires_at']) ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Never
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="token-detail token-detail-action">
                                    <form action="<?= url('/profile/tokens/' . $token['id']) ?>" method="POST" 
                                          onsubmit="return confirm('Revoke this token? This action cannot be undone.')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn-token-revoke">
                                            <i class="bi bi-trash"></i> Revoke
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- API Documentation Section -->
            <div class="profile-card">
                <div class="profile-card-body">
                    <div class="documentation-box">
                        <div class="documentation-icon">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="documentation-content">
                            <h3 class="documentation-title">API Documentation</h3>
                            <p class="documentation-description">Learn how to use the API to integrate with your applications.</p>
                        </div>
                        <a href="<?= url('/api/docs') ?>" class="btn-documentation">View Docs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Token Modal -->
<div class="modal fade" id="createTokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create API Token</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= url('/profile/tokens') ?>" method="POST" class="token-form">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-field">
                        <label class="form-field-label">Token Name</label>
                        <input type="text" name="name" class="form-field-input" required 
                               placeholder="e.g., CI/CD Pipeline, Development">
                        <small class="form-field-hint">A descriptive name to identify this token</small>
                    </div>
                    
                    <div class="form-field">
                        <label class="form-field-label">Scopes</label>
                        <div class="scope-options">
                            <div class="scope-item">
                                <input class="form-check-input" type="checkbox" name="scopes[]" value="*" id="scope-all" checked>
                                <label class="form-check-label" for="scope-all">
                                    <strong>Full Access</strong>
                                    <small>Access to all API endpoints</small>
                                </label>
                            </div>
                            <hr class="scope-divider">
                            <div class="scope-item">
                                <input class="form-check-input" type="checkbox" name="scopes[]" value="read:issues" id="scope-read-issues">
                                <label class="form-check-label" for="scope-read-issues">Read Issues</label>
                            </div>
                            <div class="scope-item">
                                <input class="form-check-input" type="checkbox" name="scopes[]" value="write:issues" id="scope-write-issues">
                                <label class="form-check-label" for="scope-write-issues">Write Issues</label>
                            </div>
                            <div class="scope-item">
                                <input class="form-check-input" type="checkbox" name="scopes[]" value="read:projects" id="scope-read-projects">
                                <label class="form-check-label" for="scope-read-projects">Read Projects</label>
                            </div>
                            <div class="scope-item">
                                <input class="form-check-input" type="checkbox" name="scopes[]" value="write:projects" id="scope-write-projects">
                                <label class="form-check-label" for="scope-write-projects">Write Projects</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-field-label">Expiration</label>
                        <select name="expires" class="form-field-select">
                            <option value="">Never expires</option>
                            <option value="7">7 days</option>
                            <option value="30" selected>30 days</option>
                            <option value="90">90 days</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-create">Create Token</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --tokens-primary-color: #8B1956;
    --tokens-primary-dark: #6F123F;
    --tokens-primary-light: #E77817;
    --tokens-text-primary: #161B22;
    --tokens-text-secondary: #626F86;
    --tokens-text-muted: #97A0AF;
    --tokens-bg-primary: #FFFFFF;
    --tokens-bg-secondary: #F7F8FA;
    --tokens-bg-tertiary: #ECEDF0;
    --tokens-border-color: #DFE1E6;
    --tokens-shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --tokens-shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --tokens-shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --tokens-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Page Wrapper */
.profile-page-wrapper {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 80px);
    background-color: var(--tokens-bg-secondary);
}

/* Breadcrumb Section */
.profile-breadcrumb-section {
    background-color: var(--tokens-bg-primary);
    border-bottom: 1px solid var(--tokens-border-color);
    padding: 0 32px;
    height: 48px;
    display: flex;
    align-items: center;
    box-shadow: var(--tokens-shadow-sm);
}

.profile-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
}

a.breadcrumb-link,
.breadcrumb-link {
    color: var(--tokens-primary-color) !important;
    text-decoration: none !important;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color var(--tokens-transition);
}

a.breadcrumb-link:hover,
.breadcrumb-link:hover {
    color: var(--tokens-primary-dark) !important;
    text-decoration: none !important;
}

.breadcrumb-separator {
    color: var(--tokens-text-muted);
}

.breadcrumb-current {
    color: var(--tokens-text-primary);
    font-weight: 600;
}

/* Page Header */
.profile-page-header {
    background-color: var(--tokens-bg-primary);
    border-bottom: 1px solid var(--tokens-border-color);
    padding: 24px 32px;
    box-shadow: var(--tokens-shadow-sm);
}

.profile-page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--tokens-text-primary);
    margin: 0 0 8px 0;
    letter-spacing: -0.3px;
}

.profile-page-subtitle {
    font-size: 14px;
    color: var(--tokens-text-secondary);
    margin: 0;
    font-weight: 400;
    display: inline;
    margin-left: 8px;
}

/* Content Container */
.profile-content-container {
    display: flex;
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 48px;
    gap: 32px;
    flex: 1;
    width: 100%;
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
    background-color: var(--tokens-bg-primary);
    border: 1px solid var(--tokens-border-color);
    border-radius: 8px;
    padding: 28px 24px;
    text-align: center;
    box-shadow: var(--tokens-shadow-md);
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
    border: 3px solid var(--tokens-border-color);
    object-fit: cover;
    display: block;
}

.user-avatar-placeholder {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--tokens-primary-color) 0%, var(--tokens-primary-dark) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
    border: 3px solid var(--tokens-border-color);
}

.user-display-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--tokens-text-primary);
    margin: 0 0 4px 0;
}

.user-email-address {
    font-size: 12px;
    color: var(--tokens-text-secondary);
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
    background-color: var(--tokens-bg-primary);
    border: 1px solid var(--tokens-border-color);
    border-radius: 6px;
    color: var(--tokens-text-primary);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all var(--tokens-transition);
    cursor: pointer;
}

.profile-nav-item:hover {
    background-color: var(--tokens-bg-secondary);
    border-color: var(--tokens-primary-color) !important;
    color: var(--tokens-primary-color) !important;
}

.profile-nav-item.active {
    background-color: rgba(139, 25, 86, 0.08);
    border-color: var(--tokens-primary-color) !important;
    color: var(--tokens-primary-color) !important;
    box-shadow: inset 3px 0 0 var(--tokens-primary-color);
}

.nav-chevron {
    margin-left: auto;
    flex-shrink: 0;
    opacity: 0.6;
}

/* Stats Section */
.profile-stats-section {
    background-color: var(--tokens-bg-primary);
    border: 1px solid var(--tokens-border-color);
    border-radius: 8px;
    padding: 24px;
    box-shadow: var(--tokens-shadow-md);
}

.stats-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--tokens-text-secondary);
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
    background-color: var(--tokens-bg-secondary);
    border-radius: 6px;
    border: 1px solid var(--tokens-border-color);
}

.stat-value {
    font-size: 18px;
    font-weight: 700;
    color: var(--tokens-text-primary);
}

.stat-label {
    font-size: 11px;
    color: var(--tokens-text-secondary);
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
    background-color: var(--tokens-bg-primary);
    border: 1px solid var(--tokens-border-color);
    border-radius: 8px;
    box-shadow: var(--tokens-shadow-md);
    overflow: hidden;
}

.profile-card-header {
    padding: 24px;
    border-bottom: 1px solid var(--tokens-border-color);
    background-color: var(--tokens-bg-secondary);
}

.profile-section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--tokens-text-primary);
    margin: 0 0 4px 0;
}

.profile-card:first-of-type .profile-section-title {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 8px;
}

.profile-section-description {
    font-size: 12px;
    color: var(--tokens-text-secondary);
    margin: 0;
}

.profile-card-body {
    padding: 32px;
}

/* Create Token Button */
.btn-tokens-create {
    background-color: var(--tokens-primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--tokens-transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-tokens-create:hover {
    background-color: var(--tokens-primary-dark);
    color: white;
}

.btn-tokens-create-alt {
    background-color: var(--tokens-primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--tokens-transition);
    margin-top: 12px;
}

.btn-tokens-create-alt:hover {
    background-color: var(--tokens-primary-dark);
    color: white;
}

/* Info Box */
.token-info-box {
    display: flex;
    gap: 16px;
    padding: 16px 20px;
    background-color: #E8F4FD;
    border-left: 4px solid #0052CC;
    border-radius: 4px;
    margin-bottom: 24px;
}

.token-info-box i {
    flex-shrink: 0;
    color: #0052CC;
    font-size: 20px;
    margin-top: 2px;
}

.token-info-box strong {
    color: var(--tokens-text-primary);
    display: block;
    margin-bottom: 4px;
}

.token-info-box p {
    margin: 0;
    color: var(--tokens-text-secondary);
    font-size: 13px;
    line-height: 1.5;
}

/* Success Box */
.token-success-box {
    display: flex;
    gap: 16px;
    padding: 16px 20px;
    background-color: #DFFCF0;
    border-left: 4px solid #36B37E;
    border-radius: 4px;
    margin-bottom: 24px;
}

.token-success-box i {
    flex-shrink: 0;
    color: #36B37E;
    font-size: 20px;
    margin-top: 2px;
}

.token-success-box strong {
    color: var(--tokens-text-primary);
    display: block;
    margin-bottom: 8px;
}

.token-success-box p {
    margin: 0 0 12px 0;
    color: var(--tokens-text-secondary);
    font-size: 13px;
}

.token-copy-group {
    display: flex;
    gap: 8px;
}

.token-copy-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid var(--tokens-border-color);
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
    background-color: white;
}

.token-copy-btn {
    background-color: var(--tokens-primary-color);
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 12px;
    cursor: pointer;
    transition: all var(--tokens-transition);
    display: flex;
    align-items: center;
    gap: 6px;
}

.token-copy-btn:hover {
    background-color: var(--tokens-primary-dark);
}

/* Empty State */
.tokens-empty-state {
    text-align: center;
    padding: 60px 32px;
}

.tokens-empty-state i {
    font-size: 64px;
    color: var(--tokens-text-muted);
    display: block;
    margin-bottom: 16px;
}

.tokens-empty-state h5 {
    font-size: 18px;
    font-weight: 700;
    color: var(--tokens-text-primary);
    margin: 0 0 8px 0;
}

.tokens-empty-state p {
    color: var(--tokens-text-secondary);
    margin: 0 0 24px 0;
}

/* Token List */
.tokens-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.token-item {
    border: 1px solid var(--tokens-border-color);
    border-radius: 6px;
    padding: 20px;
    background-color: var(--tokens-bg-primary);
    transition: all var(--tokens-transition);
}

.token-item:hover {
    border-color: var(--tokens-primary-color);
    box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.05);
}

.token-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--tokens-border-color);
}

.token-item-info {
    flex: 1;
}

.token-item-name {
    font-size: 16px;
    font-weight: 700;
    color: var(--tokens-text-primary);
    margin: 0 0 6px 0;
}

.token-item-preview {
    font-size: 12px;
    color: var(--tokens-text-muted);
    background-color: var(--tokens-bg-secondary);
    padding: 4px 8px;
    border-radius: 4px;
}

.token-item-meta {
    display: flex;
    gap: 8px;
}

.token-badge {
    background-color: rgba(139, 25, 86, 0.1);
    color: var(--tokens-primary-color);
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.token-item-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 16px;
    align-items: center;
}

.token-detail {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-label {
    font-size: 11px;
    color: var(--tokens-text-secondary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.detail-value {
    font-size: 13px;
    color: var(--tokens-text-primary);
}

.detail-expired {
    color: #AE2A19;
    font-weight: 600;
}

.token-detail-action {
    margin-top: 12px;
}

.btn-token-revoke {
    background-color: #F87462;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--tokens-transition);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-token-revoke:hover {
    background-color: #AE2A19;
}

/* Documentation Box */
.documentation-box {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 0;
}

.documentation-icon {
    font-size: 48px;
    color: var(--tokens-primary-color);
    flex-shrink: 0;
}

.documentation-content {
    flex: 1;
}

.documentation-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--tokens-text-primary);
    margin: 0 0 4px 0;
}

.documentation-description {
    margin: 0;
    color: var(--tokens-text-secondary);
    font-size: 14px;
}

.btn-documentation {
    background-color: var(--tokens-primary-color) !important;
    color: white !important;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--tokens-transition);
    text-decoration: none !important;
    white-space: nowrap;
    flex-shrink: 0;
    display: inline-block;
}

.btn-documentation:hover {
    background-color: var(--tokens-primary-dark) !important;
    color: white !important;
    text-decoration: none !important;
}

.btn-documentation:visited {
    color: white !important;
}

/* Form Styling */
.form-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 20px;
}

.form-field-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--tokens-text-primary);
}

.form-field-input,
.form-field-select {
    padding: 10px 12px;
    border: 1px solid var(--tokens-border-color);
    border-radius: 6px;
    font-size: 14px;
    transition: all var(--tokens-transition);
    font-family: inherit;
}

.form-field-input:focus,
.form-field-select:focus {
    outline: none;
    border-color: var(--tokens-primary-color);
    box-shadow: 0 0 0 4px rgba(139, 25, 86, 0.1);
}

.form-field-hint {
    font-size: 12px;
    color: var(--tokens-text-secondary);
}

/* Scope Options */
.scope-options {
    border: 1px solid var(--tokens-border-color);
    border-radius: 6px;
    padding: 16px;
    background-color: var(--tokens-bg-secondary);
}

.scope-item {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
}

.scope-item:last-of-type {
    margin-bottom: 0;
}

.scope-item input[type="checkbox"] {
    margin-top: 4px;
    flex-shrink: 0;
    cursor: pointer;
    accent-color: var(--tokens-primary-color);
}

.form-check-label {
    font-size: 13px;
    cursor: pointer;
    color: var(--tokens-text-primary);
}

.form-check-label strong {
    font-weight: 600;
}

.form-check-label small {
    display: block;
    font-size: 11px;
    color: var(--tokens-text-secondary);
    margin-top: 2px;
}

.scope-divider {
    margin: 12px 0;
    border: none;
    border-top: 1px solid var(--tokens-border-color);
}

/* Modal Styling */
.modal-header {
    border-bottom: 1px solid var(--tokens-border-color);
    padding: 24px;
}

.modal-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--tokens-text-primary);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    border-top: 1px solid var(--tokens-border-color);
    padding: 16px 24px;
    gap: 12px;
}

.btn-modal-cancel {
    background-color: var(--tokens-border-color);
    color: var(--tokens-text-primary);
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--tokens-transition);
}

.btn-modal-cancel:hover {
    background-color: var(--tokens-bg-secondary);
}

.btn-modal-create {
    background-color: var(--tokens-primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--tokens-transition);
}

.btn-modal-create:hover {
    background-color: var(--tokens-primary-dark);
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

    .documentation-box {
        flex-direction: column;
        text-align: center;
    }

    .btn-documentation {
        width: 100%;
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

    .token-item-details {
        grid-template-columns: 1fr;
    }

    .token-item-header {
        flex-direction: column;
    }

    .profile-nav-items {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }

    .profile-nav-item {
        border-bottom: 1px solid var(--tokens-border-color);
        border-right: 1px solid var(--tokens-border-color);
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
        grid-template-columns: 1fr;
    }

    .profile-user-card {
        padding: 16px;
    }

    .user-avatar-wrapper {
        width: 100px;
        height: 100px;
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
        border-right: 1px solid var(--tokens-border-color);
    }

    .profile-card-body {
        padding: 16px;
    }

    .token-item {
        padding: 16px;
    }

    .token-copy-group {
        flex-direction: column;
    }

    .btn-documentation {
        width: 100%;
    }
}
</style>

<script>
function copyToken() {
    const input = document.getElementById('newTokenValue');
    input.select();
    document.execCommand('copy');
    
    const btn = input.nextElementSibling;
    btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
    setTimeout(() => {
        btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy';
    }, 2000);
}

// Handle scope checkbox logic
document.addEventListener('DOMContentLoaded', function() {
    const scopeAll = document.getElementById('scope-all');
    if (scopeAll) {
        scopeAll.addEventListener('change', function() {
            document.querySelectorAll('input[name="scopes[]"]:not(#scope-all)').forEach(cb => {
                cb.checked = false;
                cb.disabled = this.checked;
            });
        });
    }
});
</script>

<?php \App\Core\View::endSection(); ?>
