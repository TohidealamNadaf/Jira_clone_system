<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="api-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="api-breadcrumb-section">
        <div class="api-breadcrumb">
            <a href="<?= url('/') ?>" class="breadcrumb-link">
                <i class="bi bi-house-door"></i> Home
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">API Documentation</span>
        </div>
    </div>

    <!-- Page Header -->
    <div class="api-page-header">
        <div class="api-header-content">
            <h1 class="api-page-title">API Documentation <span class="api-page-subtitle">— Build integrations with REST API</span></h1>
            <p class="api-header-description">Comprehensive guide to authenticate and integrate with the Jira Clone API</p>
        </div>
    </div>

    <!-- Content Container -->
    <div class="api-content-container">
        <!-- Sidebar Navigation -->
        <div class="api-sidebar">
            <div class="api-sidebar-content">
                <div class="api-sidebar-title">
                    <i class="bi bi-book"></i> Documentation
                </div>
                <nav class="api-nav-items">
                    <a href="#overview" class="api-nav-link active">
                        <i class="bi bi-diagram-3"></i> Overview
                    </a>
                    <a href="#authentication" class="api-nav-link">
                        <i class="bi bi-shield-lock"></i> Authentication
                    </a>
                    <a href="#projects" class="api-nav-link">
                        <i class="bi bi-folder"></i> Projects
                    </a>
                    <a href="#issues" class="api-nav-link">
                        <i class="bi bi-exclamation-circle"></i> Issues
                    </a>
                    <a href="#boards" class="api-nav-link">
                        <i class="bi bi-kanban"></i> Boards & Sprints
                    </a>
                    <a href="#users" class="api-nav-link">
                        <i class="bi bi-people"></i> Users
                    </a>
                    <a href="#search" class="api-nav-link">
                        <i class="bi bi-search"></i> Search
                    </a>
                    <a href="#errors" class="api-nav-link">
                        <i class="bi bi-exclamation-triangle"></i> Error Handling
                    </a>
                    <a href="#rate-limiting" class="api-nav-link">
                        <i class="bi bi-speedometer"></i> Rate Limiting
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="api-main-content">
            <!-- Overview Section -->
            <section id="overview" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">API Overview</h2>
                    <p class="api-section-description">Get started with the REST API</p>
                </div>

                <div class="api-card">
                    <p>The Jira Clone API allows you to programmatically interact with projects, issues, boards, and more. The API is built on REST principles and uses JSON for all requests and responses.</p>

                    <div class="api-info-box">
                        <strong class="info-label">Base URL</strong>
                        <code class="info-code"><?= e(url('/api/v1')) ?></code>
                    </div>

                    <div class="api-features-grid">
                        <div class="feature-column">
                            <h6 class="feature-title"><i class="bi bi-star"></i> Features</h6>
                            <ul class="feature-list">
                                <li>Full REST API for all major features</li>
                                <li>JWT-based authentication</li>
                                <li>Pagination support on list endpoints</li>
                                <li>Rate limiting per user</li>
                                <li>CORS enabled for web applications</li>
                                <li>Comprehensive error messages</li>
                            </ul>
                        </div>
                        <div class="feature-column">
                            <h6 class="feature-title"><i class="bi bi-lightning"></i> HTTP Methods</h6>
                            <ul class="feature-list">
                                <li><span class="api-badge api-badge-get">GET</span> Retrieve resources</li>
                                <li><span class="api-badge api-badge-post">POST</span> Create resources</li>
                                <li><span class="api-badge api-badge-put">PUT</span> Update resources</li>
                                <li><span class="api-badge api-badge-delete">DELETE</span> Delete resources</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Authentication Section -->
            <section id="authentication" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">Authentication</h2>
                    <p class="api-section-description">Secure your API requests</p>
                </div>

                <div class="api-card">
                    <p>All API requests (except login) require authentication using JWT (JSON Web Tokens) or Personal Access Tokens.</p>

                    <div class="api-subsection">
                        <h5 class="api-subsection-title">Login to Get JWT Token</h5>
                        <div class="api-code-block">
                            <div class="code-label">POST <?= e(url('/api/v1/auth/login')) ?></div>
                            <pre><code>{
  "email": "user@example.com",
  "password": "your-password"
}</code></pre>
                            <div class="code-response-label">Response:</div>
                            <pre><code>{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 3600
}</code></pre>
                        </div>
                    </div>

                    <div class="api-subsection">
                        <h5 class="api-subsection-title">Using the Token</h5>
                        <p>Include the JWT token in the <code>Authorization</code> header:</p>
                        <div class="api-code-block">
                            <pre><code>Authorization: Bearer &lt;your-jwt-token&gt;</code></pre>
                        </div>
                    </div>

                    <div class="api-subsection">
                        <h5 class="api-subsection-title">Personal Access Tokens</h5>
                        <p>You can also use Personal Access Tokens (PATs) for long-lived authentication. <a href="<?= url('/profile/tokens') ?>" class="api-link">Create tokens in your profile</a>.</p>
                        <div class="api-code-block">
                            <pre><code>Authorization: Bearer &lt;your-pat-token&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Projects Section -->
            <section id="projects" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">Projects</h2>
                    <p class="api-section-description">Manage projects via API</p>
                </div>

                <div class="api-card">
                    <div class="api-endpoints-table">
                        <div class="table-header">
                            <div class="th method">Method</div>
                            <div class="th endpoint">Endpoint</div>
                            <div class="th description">Description</div>
                        </div>
                        <div class="table-body">
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/projects</code></div>
                                <div class="td description">List all projects</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-post">POST</span></div>
                                <div class="td endpoint"><code>/projects</code></div>
                                <div class="td description">Create a new project</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/projects/{key}</code></div>
                                <div class="td description">Get project details</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-put">PUT</span></div>
                                <div class="td endpoint"><code>/projects/{key}</code></div>
                                <div class="td description">Update project</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-delete">DELETE</span></div>
                                <div class="td endpoint"><code>/projects/{key}</code></div>
                                <div class="td description">Delete project</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/projects/{key}/members</code></div>
                                <div class="td description">List project members</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-post">POST</span></div>
                                <div class="td endpoint"><code>/projects/{key}/members</code></div>
                                <div class="td description">Add project member</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Issues Section -->
            <section id="issues" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">Issues</h2>
                    <p class="api-section-description">Create and manage issues</p>
                </div>

                <div class="api-card">
                    <div class="api-endpoints-table">
                        <div class="table-header">
                            <div class="th method">Method</div>
                            <div class="th endpoint">Endpoint</div>
                            <div class="th description">Description</div>
                        </div>
                        <div class="table-body">
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/issues</code></div>
                                <div class="td description">Search issues</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-post">POST</span></div>
                                <div class="td endpoint"><code>/issues</code></div>
                                <div class="td description">Create issue</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/issues/{key}</code></div>
                                <div class="td description">Get issue details</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-put">PUT</span></div>
                                <div class="td endpoint"><code>/issues/{key}</code></div>
                                <div class="td description">Update issue</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-delete">DELETE</span></div>
                                <div class="td endpoint"><code>/issues/{key}</code></div>
                                <div class="td description">Delete issue</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-post">POST</span></div>
                                <div class="td endpoint"><code>/issues/{key}/transitions</code></div>
                                <div class="td description">Transition issue status</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-post">POST</span></div>
                                <div class="td endpoint"><code>/issues/{key}/comments</code></div>
                                <div class="td description">Add comment</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/issues/{key}/history</code></div>
                                <div class="td description">Get issue history</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Boards & Sprints Section -->
            <section id="boards" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">Boards & Sprints</h2>
                    <p class="api-section-description">Manage boards and sprints</p>
                </div>

                <div class="api-card">
                    <div class="api-endpoints-table">
                        <div class="table-header">
                            <div class="th method">Method</div>
                            <div class="th endpoint">Endpoint</div>
                            <div class="th description">Description</div>
                        </div>
                        <div class="table-body">
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/boards</code></div>
                                <div class="td description">List all boards</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/boards/{id}/sprints</code></div>
                                <div class="td description">List sprints on board</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-post">POST</span></div>
                                <div class="td endpoint"><code>/boards/{boardId}/sprints</code></div>
                                <div class="td description">Create sprint</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-post">POST</span></div>
                                <div class="td endpoint"><code>/sprints/{id}/start</code></div>
                                <div class="td description">Start sprint</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-post">POST</span></div>
                                <div class="td endpoint"><code>/sprints/{id}/complete</code></div>
                                <div class="td description">Complete sprint</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Users Section -->
            <section id="users" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">Users</h2>
                    <p class="api-section-description">Manage users and access</p>
                </div>

                <div class="api-card">
                    <div class="api-endpoints-table">
                        <div class="table-header">
                            <div class="th method">Method</div>
                            <div class="th endpoint">Endpoint</div>
                            <div class="th description">Description</div>
                        </div>
                        <div class="table-body">
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/me</code></div>
                                <div class="td description">Get current user</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/users</code></div>
                                <div class="td description">List users</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/users/{id}</code></div>
                                <div class="td description">Get user details</div>
                            </div>
                            <div class="tr">
                                <div class="td method"><span class="api-badge api-badge-get">GET</span></div>
                                <div class="td endpoint"><code>/users/search?q=query</code></div>
                                <div class="td description">Search users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search Section -->
            <section id="search" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">Search</h2>
                    <p class="api-section-description">Search issues and resources</p>
                </div>

                <div class="api-card">
                    <div class="api-subsection">
                        <h5 class="api-subsection-title">Full-Text Search</h5>
                        <p>Search issues using simple text query:</p>
                        <div class="api-code-block">
                            <pre><code>GET <?= e(url('/api/v1/search?q=bug')) ?></code></pre>
                        </div>
                    </div>

                    <div class="api-subsection">
                        <h5 class="api-subsection-title">JQL Query Language</h5>
                        <p>Advanced search using JQL (Jira Query Language):</p>
                        <div class="api-code-block">
                            <div class="code-label">POST <?= e(url('/api/v1/jql')) ?></div>
                            <pre><code>{
  "query": "status = 'To Do' AND assignee = currentUser() AND priority > 3"
}</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Error Handling Section -->
            <section id="errors" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">Error Handling</h2>
                    <p class="api-section-description">Understanding API errors</p>
                </div>

                <div class="api-card">
                    <p>All errors are returned as JSON with an appropriate HTTP status code:</p>

                    <div class="api-code-block">
                        <pre><code>{
  "error": "Unauthorized",
  "message": "Invalid or expired token",
  "status": 401
}</code></pre>
                    </div>

                    <div class="api-subsection">
                        <h5 class="api-subsection-title">HTTP Status Codes</h5>
                        <ul class="status-codes-list">
                            <li><strong>200 OK</strong> — Request succeeded</li>
                            <li><strong>201 Created</strong> — Resource created successfully</li>
                            <li><strong>400 Bad Request</strong> — Invalid request parameters</li>
                            <li><strong>401 Unauthorized</strong> — Missing or invalid authentication</li>
                            <li><strong>403 Forbidden</strong> — Permission denied</li>
                            <li><strong>404 Not Found</strong> — Resource not found</li>
                            <li><strong>422 Unprocessable Entity</strong> — Validation error</li>
                            <li><strong>429 Too Many Requests</strong> — Rate limit exceeded</li>
                            <li><strong>500 Internal Server Error</strong> — Server error</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Rate Limiting Section -->
            <section id="rate-limiting" class="api-section">
                <div class="api-section-header">
                    <h2 class="api-section-title">Rate Limiting</h2>
                    <p class="api-section-description">API rate limits</p>
                </div>

                <div class="api-card">
                    <p>The API implements rate limiting to ensure fair usage:</p>
                    <ul class="rate-limit-list">
                        <li><strong>Public endpoints:</strong> 60 requests per minute</li>
                        <li><strong>Authenticated endpoints:</strong> 300 requests per minute</li>
                    </ul>

                    <p class="api-note-title">Rate limit information is included in response headers:</p>
                    <div class="api-code-block">
                        <pre><code>X-RateLimit-Limit: 300
X-RateLimit-Remaining: 299
X-RateLimit-Reset: 1640000000</code></pre>
                    </div>

                    <div class="api-note">
                        <i class="bi bi-info-circle"></i>
                        If you exceed the rate limit, you'll receive a 429 status code and should retry after the reset time.
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
:root {
    --api-primary-color: #8B1956;
    --api-primary-dark: #6F123F;
    --api-primary-light: #E77817;
    --api-text-primary: #161B22;
    --api-text-secondary: #626F86;
    --api-text-muted: #97A0AF;
    --api-bg-primary: #FFFFFF;
    --api-bg-secondary: #F7F8FA;
    --api-bg-tertiary: #ECEDF0;
    --api-border-color: #DFE1E6;
    --api-shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --api-shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --api-shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --api-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Page Wrapper */
.api-page-wrapper {
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - 80px);
    background-color: var(--api-bg-secondary);
}

/* Breadcrumb Section */
.api-breadcrumb-section {
    background-color: var(--api-bg-primary);
    border-bottom: 1px solid var(--api-border-color);
    padding: 0 32px;
    height: 48px;
    display: flex;
    align-items: center;
    box-shadow: var(--api-shadow-sm);
}

.api-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
}

.api-breadcrumb .breadcrumb-link {
    color: #8B1956 !important;
    text-decoration: none !important;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color var(--api-transition);
}

.api-breadcrumb .breadcrumb-link:visited {
    color: #8B1956 !important;
}

.api-breadcrumb .breadcrumb-link:hover {
    color: #6F123F !important;
    text-decoration: none !important;
}

.api-breadcrumb a {
    color: #8B1956 !important;
}

.api-breadcrumb a:hover {
    color: #6F123F !important;
}

.breadcrumb-separator {
    color: var(--api-text-muted);
}

.breadcrumb-current {
    color: var(--api-text-primary);
    font-weight: 600;
}

/* Page Header */
.api-page-header {
    background-color: var(--api-bg-primary);
    border-bottom: 1px solid var(--api-border-color);
    padding: 24px 32px;
    box-shadow: var(--api-shadow-sm);
}

.api-page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--api-text-primary);
    margin: 0 0 8px 0;
    letter-spacing: -0.3px;
}

.api-page-subtitle {
    font-size: 14px;
    color: var(--api-text-secondary);
    font-weight: 400;
    display: inline;
    margin-left: 8px;
}

.api-header-description {
    font-size: 14px;
    color: var(--api-text-secondary);
    margin: 8px 0 0 0;
}

/* Content Container */
.api-content-container {
    display: flex;
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 48px;
    gap: 32px;
    flex: 1;
    width: 100%;
}

/* Sidebar */
.api-sidebar {
    flex-shrink: 0;
    width: 280px;
}

.api-sidebar-content {
    position: sticky;
    top: 24px;
    background-color: var(--api-bg-primary);
    border: 1px solid var(--api-border-color);
    border-radius: 8px;
    padding: 20px;
    box-shadow: var(--api-shadow-md);
}

.api-sidebar-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--api-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.api-nav-items {
    display: flex;
    flex-direction: column;
    gap: 0;
    list-style: none;
    padding: 0;
    margin: 0;
}

.api-nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 12px;
    color: #8B1956 !important;
    text-decoration: none;
    font-size: 13px;
    border-left: 3px solid transparent;
    transition: all var(--api-transition);
    border-radius: 4px;
}

.api-nav-link:hover {
    background-color: var(--api-bg-secondary);
    color: #8B1956 !important;
    border-left-color: #8B1956 !important;
}

.api-nav-link.active {
    background-color: rgba(139, 25, 86, 0.08);
    color: #8B1956 !important;
    border-left-color: #8B1956 !important;
    font-weight: 600;
}

.api-nav-link i {
    font-size: 14px;
    flex-shrink: 0;
}

/* Main Content */
.api-main-content {
    flex: 1;
    min-width: 0;
}

/* Section */
.api-section {
    margin-bottom: 48px;
    scroll-margin-top: 100px;
}

.api-section-header {
    margin-bottom: 24px;
}

.api-section-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--api-text-primary);
    margin: 0 0 8px 0;
    letter-spacing: -0.2px;
}

.api-section-description {
    font-size: 14px;
    color: var(--api-text-secondary);
    margin: 0;
}

/* Card */
.api-card {
    background-color: var(--api-bg-primary);
    border: 1px solid var(--api-border-color);
    border-radius: 8px;
    padding: 32px;
    box-shadow: var(--api-shadow-md);
}

.api-card p {
    font-size: 14px;
    color: var(--api-text-secondary);
    line-height: 1.6;
    margin: 0 0 16px 0;
}

/* Subsection */
.api-subsection {
    margin-top: 24px;
}

.api-subsection:first-child {
    margin-top: 0;
}

.api-subsection-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--api-text-primary);
    margin: 0 0 12px 0;
}

/* Info Box */
.api-info-box {
    background-color: var(--api-bg-secondary);
    border-left: 4px solid var(--api-primary-color);
    padding: 16px;
    border-radius: 4px;
    margin: 20px 0;
}

.info-label {
    display: block;
    font-size: 12px;
    color: var(--api-text-secondary);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 8px;
}

.info-code {
    background-color: var(--api-bg-primary);
    padding: 8px 12px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 13px;
    color: var(--api-primary-color);
    display: inline-block;
}

/* Features Grid */
.api-features-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin: 32px 0 0 0;
}

.feature-column {
    padding: 20px;
    background-color: var(--api-bg-secondary);
    border-radius: 6px;
    border: 1px solid var(--api-border-color);
}

.feature-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--api-text-primary);
    margin: 0 0 12px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 13px;
    color: var(--api-text-secondary);
}

.feature-list li {
    padding: 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.feature-list li:before {
    content: "✓";
    color: #8B1956 !important;
    font-weight: 700;
}

/* Code Blocks */
.api-code-block {
    background-color: var(--api-bg-secondary);
    border: 1px solid var(--api-border-color);
    border-radius: 6px;
    padding: 16px;
    margin: 16px 0;
    overflow-x: auto;
}

.code-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--api-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 8px;
}

.code-response-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--api-text-secondary);
    margin-top: 12px;
    margin-bottom: 6px;
}

.api-code-block pre {
    margin: 0;
    font-family: 'Courier New', monospace;
    font-size: 12px;
}

.api-code-block code {
    color: var(--api-text-primary);
    background: transparent;
}

/* Endpoints Table */
.api-endpoints-table {
    display: flex;
    flex-direction: column;
    gap: 0;
    border: 1px solid var(--api-border-color);
    border-radius: 6px;
    overflow: hidden;
}

.table-header {
    display: grid;
    grid-template-columns: 100px 250px 1fr;
    gap: 0;
    background-color: var(--api-bg-secondary);
    border-bottom: 1px solid var(--api-border-color);
    font-weight: 700;
    font-size: 13px;
}

.table-header .th {
    padding: 12px 16px;
    color: var(--api-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.table-header .th.method {
    text-align: center;
}

.table-body .tr {
    display: grid;
    grid-template-columns: 100px 250px 1fr;
    gap: 0;
    border-bottom: 1px solid var(--api-border-color);
}

.table-body .tr:last-child {
    border-bottom: none;
}

.table-body .tr:hover {
    background-color: var(--api-bg-secondary);
}

.td {
    padding: 16px;
    font-size: 13px;
    color: var(--api-text-secondary);
}

.td.method {
    text-align: center;
}

.td.endpoint code {
    background-color: var(--api-bg-secondary);
    padding: 4px 8px;
    border-radius: 4px;
    color: #8B1956 !important;
    font-size: 12px;
}

/* Badges */
.api-badge {
    display: inline-block;
    font-size: 10px;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 3px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    min-width: 45px;
    text-align: center;
    color: white;
}

.api-badge-get {
    background-color: #0052CC;
}

.api-badge-post {
    background-color: #36B37E;
}

.api-badge-put {
    background-color: #FFAB00;
    color: #161B22;
}

.api-badge-delete {
    background-color: #AE2A19;
}

/* Status Codes List */
.status-codes-list {
    list-style: none;
    padding: 0;
    margin: 16px 0;
}

.status-codes-list li {
    padding: 10px 0;
    font-size: 13px;
    color: var(--api-text-secondary);
    border-bottom: 1px solid var(--api-border-color);
}

.status-codes-list li:last-child {
    border-bottom: none;
}

.status-codes-list strong {
    color: var(--api-text-primary);
}

/* Rate Limit List */
.rate-limit-list {
    list-style: none;
    padding: 0;
    margin: 16px 0;
    font-size: 14px;
    color: var(--api-text-secondary);
}

.rate-limit-list li {
    padding: 8px 0;
    padding-left: 24px;
    position: relative;
}

.rate-limit-list li:before {
    content: "▸";
    position: absolute;
    left: 0;
    color: #8B1956 !important;
    font-weight: 700;
}

/* Note */
.api-note-title {
    font-size: 14px;
    color: var(--api-text-secondary);
    margin: 20px 0 16px 0;
    display: block;
}

.api-note {
    background-color: rgba(139, 25, 86, 0.05);
    border-left: 4px solid #8B1956 !important;
    padding: 12px 16px;
    border-radius: 4px;
    font-size: 13px;
    color: var(--api-text-secondary);
    margin: 16px 0 !important;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.api-note i {
    flex-shrink: 0;
    color: #8B1956 !important;
    margin-top: 2px;
}

/* Links */
.api-link {
    color: #8B1956 !important;
    text-decoration: none;
    transition: color var(--api-transition);
    font-weight: 500;
}

.api-link:hover {
    color: #6F123F !important;
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 1199px) {
    .api-content-container {
        gap: 28px;
        padding: 28px 40px;
    }

    .api-sidebar {
        width: 260px;
    }

    .api-features-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 991px) {
    .api-content-container {
        flex-direction: column;
        gap: 28px;
        padding: 24px 32px;
    }

    .api-sidebar {
        width: 100%;
    }

    .api-sidebar-content {
        position: relative;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
        padding: 0;
    }

    .api-sidebar-title {
        grid-column: 1 / -1;
        padding: 16px 24px 8px 24px;
        margin: 0;
        border-bottom: 1px solid var(--api-border-color);
    }

    .api-nav-items {
        grid-column: 1 / -1;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0;
    }

    .api-nav-link {
        flex: 1;
        min-width: 150px;
        text-align: center;
        border-left: none;
        border-top: 1px solid var(--api-border-color);
        border-right: 1px solid var(--api-border-color);
        border-radius: 0;
        padding: 12px 8px;
    }

    .table-header,
    .table-body .tr {
        grid-template-columns: 80px 1fr;
    }

    .table-header .th.endpoint,
    .td.endpoint {
        display: none;
    }
}

@media (max-width: 768px) {
    .api-page-header {
        padding: 20px 24px;
    }

    .api-page-title {
        font-size: 24px;
    }

    .api-content-container {
        padding: 24px 16px;
    }

    .api-card {
        padding: 24px;
    }

    .api-features-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .feature-column {
        padding: 16px;
    }

    .api-section {
        margin-bottom: 32px;
    }

    .table-header,
    .table-body .tr {
        grid-template-columns: 1fr;
    }

    .table-header .th,
    .td {
        padding: 12px;
    }

    .table-header .th.method {
        text-align: left;
    }

    .td.method {
        text-align: left;
    }
}

@media (max-width: 480px) {
    .api-breadcrumb-section {
        padding: 0 16px;
    }

    .api-breadcrumb {
        font-size: 12px;
    }

    .api-page-header {
        padding: 16px;
    }

    .api-page-title {
        font-size: 20px;
    }

    .api-page-subtitle {
        font-size: 12px;
    }

    .api-content-container {
        padding: 16px;
    }

    .api-sidebar {
        width: 100%;
    }

    .api-sidebar-content {
        grid-template-columns: 1fr;
    }

    .api-sidebar-title {
        padding: 12px;
        margin-bottom: 8px;
    }

    .api-nav-link {
        border-right: none;
        padding: 12px;
        font-size: 13px;
        min-width: auto;
        border-top: 1px solid var(--api-border-color);
    }

    .api-nav-link:first-of-type {
        border-top: none;
    }

    .api-card {
        padding: 16px;
    }

    .api-section-title {
        font-size: 18px;
    }

    .api-subsection-title {
        font-size: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.api-nav-link');
    const sections = document.querySelectorAll('.api-section');
    const mainContent = document.querySelector('.api-main-content');

    if (!mainContent) return;

    function updateActiveLink() {
        let currentSection = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const scrollTop = mainContent.parentElement.parentElement.scrollTop || window.scrollY;
            
            if (scrollTop >= sectionTop - 120) {
                currentSection = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + currentSection) {
                link.classList.add('active');
            }
        });
    }

    // Update on scroll
    window.addEventListener('scroll', updateActiveLink);
    updateActiveLink();

    // Smooth scroll on link click
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offset = target.offsetTop - 100;
                window.scrollTo({
                    top: offset,
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script>

<?php \App\Core\View::endSection(); ?>
