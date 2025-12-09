<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<style>
    /* Two-column layout with sticky sidebar */
    body {
        display: flex;
        flex-direction: column;
    }

    .doc-container {
        display: flex;
        flex: 1;
        min-height: calc(100vh - 100px);
    }

    .api-sidebar-wrapper {
        width: 300px;
        background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
        border-right: 1px solid #e9ecef;
        overflow-y: auto;
        position: sticky;
        top: 80px;
        height: calc(100vh - 80px);
        padding: 0;
        margin: 0;
    }

    .api-sidebar {
        padding: 1.5rem;
    }

    .api-sidebar .nav-link {
        color: #495057;
        padding: 0.6rem 0.75rem;
        margin-bottom: 0.25rem;
        border-left: 3px solid transparent;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
        display: block;
        font-size: 0.95rem;
    }

    .api-sidebar .nav-link:hover {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
        border-left-color: #0d6efd;
        padding-left: 1rem;
    }

    .api-sidebar .nav-link.active {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
        border-left-color: #0d6efd;
        font-weight: 500;
    }

    .api-content {
        flex: 1;
        overflow-y: auto;
        padding: 2rem;
    }

    .api-section {
        scroll-margin-top: 100px;
    }

    .endpoint-table {
        font-size: 0.9rem;
    }

    .endpoint-table code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        color: #e83e8c;
    }

    .api-example {
        background: #f8f9fa;
        border-left: 4px solid #0d6efd;
        border-radius: 4px;
    }

    .api-example code {
        color: #d63384;
        font-size: 0.85rem;
    }

    .section-header {
        padding-top: 2rem;
        margin-top: 2rem;
        border-top: 1px solid #e9ecef;
    }

    .section-header:first-child {
        padding-top: 0;
        margin-top: 0;
        border-top: none;
    }

    .method-badge {
        font-weight: 600;
        font-size: 0.75rem;
        min-width: 50px;
        text-align: center;
    }

    /* Mobile responsive */
    @media (max-width: 991px) {
        .doc-container {
            flex-direction: column;
        }

        .api-sidebar-wrapper {
            width: 100%;
            height: auto;
            position: relative;
            top: 0;
            border-right: none;
            border-bottom: 1px solid #e9ecef;
        }

        .api-content {
            padding: 1.5rem;
        }
    }

    
</style>

<div class="doc-container">
    <!-- Navigation Sidebar -->
    <div class="api-sidebar-wrapper">
        <div class="api-sidebar">
            <h6 class="text-uppercase small fw-bold text-dark mb-3">
                <i class="bi bi-book me-2"></i> Documentation
            </h6>
            <nav class="nav flex-column">
                <a href="#overview" class="nav-link">Overview</a>
                <a href="#authentication" class="nav-link">Authentication</a>
                <a href="#projects" class="nav-link">Projects</a>
                <a href="#issues" class="nav-link">Issues</a>
                <a href="#boards" class="nav-link">Boards & Sprints</a>
                <a href="#users" class="nav-link">Users</a>
                <a href="#search" class="nav-link">Search</a>
                <a href="#errors" class="nav-link">Error Handling</a>
                <a href="#rate-limiting" class="nav-link">Rate Limiting</a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="api-content">
        <!-- Overview -->
        <section id="overview" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-diagram-3 me-2"></i>API Overview</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <p>The Jira Clone API allows you to programmatically interact with projects, issues, boards, and
                        more. The API is built on REST principles and uses JSON for all requests and responses.</p>

                    <div class="bg-light p-3 rounded mb-3">
                        <strong>Base URL:</strong><br>
                        <code><?= e(url('/api/v1')) ?></code>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Features</h6>
                            <ul class="small">
                                <li>Full REST API for all major features</li>
                                <li>JWT-based authentication</li>
                                <li>Pagination support on list endpoints</li>
                                <li>Rate limiting per user</li>
                                <li>CORS enabled for web applications</li>
                                <li>Comprehensive error messages</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Supported Methods</h6>
                            <ul class="small">
                                <li><span class="badge bg-info">GET</span> Retrieve resources</li>
                                <li><span class="badge bg-success">POST</span> Create resources</li>
                                <li><span class="badge bg-warning">PUT</span> Update resources</li>
                                <li><span class="badge bg-danger">DELETE</span> Delete resources</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Authentication -->
        <section id="authentication" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-shield-lock me-2"></i>Authentication</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <p>All API requests (except login) require authentication using JWT (JSON Web Tokens) or Personal
                        Access Tokens.</p>

                    <h5 class="mt-4">Login to Get JWT Token</h5>
                    <div class="bg-light p-3 rounded">
                        <code>POST <?= e(url('/api/v1/auth/login')) ?></code>
                        <pre class="mt-2"><code>{
  "email": "user@example.com",
  "password": "your-password"
}</code></pre>
                        <strong>Response:</strong>
                        <pre><code>{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "expires_in": 3600
}</code></pre>
                    </div>

                    <h5 class="mt-4">Using the Token</h5>
                    <p>Include the JWT token in the <code>Authorization</code> header:</p>
                    <div class="bg-light p-3 rounded">
                        <code>Authorization: Bearer &lt;your-jwt-token&gt;</code>
                    </div>

                    <h5 class="mt-4">Personal Access Tokens</h5>
                    <p>You can also use Personal Access Tokens (PATs) for long-lived authentication. Create tokens in <a
                            href="<?= url('/profile/tokens') ?>" target="_blank">your profile</a>.</p>
                    <div class="bg-light p-3 rounded">
                        <code>Authorization: Bearer &lt;your-pat-token&gt;</code>
                    </div>
                </div>
            </div>
        </section>

        <!-- Projects Endpoints -->
        <section id="projects" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-folder me-2"></i>Projects</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/projects</code></td>
                                <td>List all projects</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">POST</span></td>
                                <td><code>/projects</code></td>
                                <td>Create a new project</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/projects/{key}</code></td>
                                <td>Get project details</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning">PUT</span></td>
                                <td><code>/projects/{key}</code></td>
                                <td>Update project</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">DELETE</span></td>
                                <td><code>/projects/{key}</code></td>
                                <td>Delete project</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/projects/{key}/members</code></td>
                                <td>List project members</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">POST</span></td>
                                <td><code>/projects/{key}/members</code></td>
                                <td>Add project member</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Issues Endpoints -->
        <section id="issues" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-exclamation-circle me-2"></i>Issues</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/issues</code></td>
                                <td>Search issues</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">POST</span></td>
                                <td><code>/issues</code></td>
                                <td>Create issue</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/issues/{key}</code></td>
                                <td>Get issue details</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning">PUT</span></td>
                                <td><code>/issues/{key}</code></td>
                                <td>Update issue</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">DELETE</span></td>
                                <td><code>/issues/{key}</code></td>
                                <td>Delete issue</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">POST</span></td>
                                <td><code>/issues/{key}/transitions</code></td>
                                <td>Transition issue status</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">POST</span></td>
                                <td><code>/issues/{key}/comments</code></td>
                                <td>Add comment</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/issues/{key}/history</code></td>
                                <td>Get issue history</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Boards & Sprints -->
        <section id="boards" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-kanban me-2"></i>Boards & Sprints</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/boards</code></td>
                                <td>List all boards</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/boards/{id}/sprints</code></td>
                                <td>List sprints on board</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">POST</span></td>
                                <td><code>/boards/{boardId}/sprints</code></td>
                                <td>Create sprint</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">POST</span></td>
                                <td><code>/sprints/{id}/start</code></td>
                                <td>Start sprint</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">POST</span></td>
                                <td><code>/sprints/{id}/complete</code></td>
                                <td>Complete sprint</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Users -->
        <section id="users" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-people me-2"></i>Users</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Method</th>
                                <th>Endpoint</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/me</code></td>
                                <td>Get current user</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/users</code></td>
                                <td>List users</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/users/{id}</code></td>
                                <td>Get user details</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">GET</span></td>
                                <td><code>/users/search?q=query</code></td>
                                <td>Search users</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Search -->
        <section id="search" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-search me-2"></i>Search</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5>Full-Text Search</h5>
                    <p>Search issues using simple text query:</p>
                    <div class="bg-light p-3 rounded">
                        <code>GET <?= e(url('/api/v1/search?q=bug')) ?></code>
                    </div>

                    <h5 class="mt-4">JQL Query Language</h5>
                    <p>Advanced search using JQL (Jira Query Language):</p>
                    <div class="bg-light p-3 rounded">
                        <code>POST <?= e(url('/api/v1/jql')) ?></code>
                        <pre class="mt-2"><code>{
  "query": "status = 'To Do' AND assignee = currentUser() AND priority > 3"
}</code></pre>
                    </div>
                </div>
            </div>
        </section>

        <!-- Error Handling -->
        <section id="errors" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>Error Handling</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <p>All errors are returned as JSON with an appropriate HTTP status code:</p>

                    <div class="bg-light p-3 rounded mt-3">
                        <pre><code>{
  "error": "Unauthorized",
  "message": "Invalid or expired token",
  "status": 401
}</code></pre>
                    </div>

                    <h5 class="mt-4">Common Status Codes</h5>
                    <ul>
                        <li><strong>200 OK</strong> - Request succeeded</li>
                        <li><strong>201 Created</strong> - Resource created successfully</li>
                        <li><strong>400 Bad Request</strong> - Invalid request parameters</li>
                        <li><strong>401 Unauthorized</strong> - Missing or invalid authentication</li>
                        <li><strong>403 Forbidden</strong> - Permission denied</li>
                        <li><strong>404 Not Found</strong> - Resource not found</li>
                        <li><strong>422 Unprocessable Entity</strong> - Validation error</li>
                        <li><strong>429 Too Many Requests</strong> - Rate limit exceeded</li>
                        <li><strong>500 Internal Server Error</strong> - Server error</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Rate Limiting -->
        <section id="rate-limiting" class="api-section mb-5 section-header">
            <h2 class="mb-3 fw-bold"><i class="bi bi-speedometer me-2"></i>Rate Limiting</h2>
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <p>The API implements rate limiting to ensure fair usage:</p>
                    <ul>
                        <li><strong>Public endpoints:</strong> 60 requests per minute</li>
                        <li><strong>Authenticated endpoints:</strong> 300 requests per minute</li>
                    </ul>

                    <p class="mt-3">Rate limit information is included in response headers:</p>
                    <div class="bg-light p-3 rounded">
                        <code>X-RateLimit-Limit: 300</code><br>
                        <code>X-RateLimit-Remaining: 299</code><br>
                        <code>X-RateLimit-Reset: 1640000000</code>
                    </div>

                    <p class="mt-3 text-muted small">If you exceed the rate limit, you'll receive a 429 status code and
                        should retry after the reset time.</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <div class="mt-5 pt-5 border-top">
            <p class="text-muted">
                <strong>Need Help?</strong> Check out the full API reference or contact support for more information.
            </p>
        </div>
    </div>
</div>

<script>
    // Highlight active navigation link based on scroll position
    document.addEventListener('DOMContentLoaded', function () {
        const navLinks = document.querySelectorAll('.api-sidebar .nav-link');
        const sections = document.querySelectorAll('.api-section');

        const content = document.querySelector('.api-content');

        content.addEventListener('scroll', function () {
            let currentSection = '';

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (content.scrollTop >= sectionTop - 50) {
                    currentSection = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + currentSection) {
                    link.classList.add('active');
                }
            });
        });
    });
</script>

<?php \App\Core\View::endSection(); ?>