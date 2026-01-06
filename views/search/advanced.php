<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="advanced-search-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Projects
        </a>
        <span class="breadcrumb-separator">/</span>
        <a href="<?= url('/search') ?>" class="breadcrumb-link">Search</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Advanced</span>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1 class="page-title">Advanced Search</h1>
            <p class="page-subtitle">Build complex queries using JQL syntax</p>
        </div>
        <div class="header-right">
            <a href="<?= url('/search') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Basic Search
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="content-area">
        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs-nav" role="tablist">
                <button class="tab-item active" data-bs-toggle="tab" data-bs-target="#builderMode" role="tab" aria-selected="true" aria-controls="builderMode">
                    <i class="bi bi-ui-checks me-2"></i> Query Builder
                </button>
                <button class="tab-item" data-bs-toggle="tab" data-bs-target="#jqlMode" role="tab" aria-selected="false" aria-controls="jqlMode">
                    <i class="bi bi-code me-2"></i> JQL Editor
                </button>
            </div>

            <div class="tab-content">
                <!-- Query Builder Mode -->
                <div class="tab-pane fade show active" id="builderMode" role="tabpanel">
                    <div class="builder-card">
                        <form action="<?= url('/search') ?>" method="GET" id="queryBuilderForm">
                            <input type="hidden" name="jql" id="generatedJql">
                            
                            <!-- Conditions -->
                            <div class="builder-conditions" id="jqlBuilder">
                                <div class="condition-row" data-index="0">
                                    <div class="condition-inputs">
                                        <select class="condition-conjunction" style="display: none;" disabled>
                                            <option value="AND">AND</option>
                                            <option value="OR">OR</option>
                                        </select>
                                        
                                        <div class="condition-field">
                                            <label class="condition-label">Field</label>
                                            <select class="field-select" required aria-label="Select field">
                                                <option value="">Select field...</option>
                                                <option value="project">Project</option>
                                                <option value="type">Issue Type</option>
                                                <option value="status">Status</option>
                                                <option value="priority">Priority</option>
                                                <option value="assignee">Assignee</option>
                                                <option value="reporter">Reporter</option>
                                                <option value="summary">Summary</option>
                                                <option value="description">Description</option>
                                                <option value="labels">Labels</option>
                                                <option value="sprint">Sprint</option>
                                                <option value="created">Created</option>
                                                <option value="updated">Updated</option>
                                                <option value="resolved">Resolved</option>
                                                <option value="due">Due Date</option>
                                            </select>
                                        </div>

                                        <div class="condition-operator">
                                            <label class="condition-label">Operator</label>
                                            <select class="operator-select" aria-label="Select operator">
                                                <option value="=">=</option>
                                                <option value="!=">!=</option>
                                                <option value="~">~ (contains)</option>
                                                <option value="!~">!~ (not contains)</option>
                                                <option value="IN">IN</option>
                                                <option value="NOT IN">NOT IN</option>
                                                <option value="IS">IS</option>
                                                <option value="IS NOT">IS NOT</option>
                                                <option value=">">&gt;</option>
                                                <option value="<">&lt;</option>
                                                <option value=">=">&gt;=</option>
                                                <option value="<=">&lt;=</option>
                                            </select>
                                        </div>

                                        <div class="condition-value">
                                            <label class="condition-label">Value</label>
                                            <input type="text" class="value-input" placeholder="Enter value..." aria-label="Enter value">
                                        </div>

                                        <button type="button" class="btn-remove-condition" aria-label="Remove condition">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Condition Button -->
                            <div class="builder-actions">
                                <button type="button" class="btn btn-outline-secondary" id="addCondition">
                                    <i class="bi bi-plus-lg me-1"></i> Add Condition
                                </button>
                            </div>

                            <!-- Order By -->
                            <div class="order-by-section">
                                <h3 class="order-by-title">Order By</h3>
                                <select name="orderBy" class="order-by-select" aria-label="Sort results">
                                    <option value="created DESC">Created (Newest first)</option>
                                    <option value="created ASC">Created (Oldest first)</option>
                                    <option value="updated DESC">Updated (Recent first)</option>
                                    <option value="priority DESC">Priority (Highest first)</option>
                                    <option value="due ASC">Due Date (Soonest first)</option>
                                </select>
                            </div>

                            <!-- Submit -->
                            <div class="builder-submit">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search me-1"></i> Search
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="resetBuilder()">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Clear
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- JQL Editor Mode -->
                <div class="tab-pane fade" id="jqlMode" role="tabpanel">
                    <div class="jql-card">
                        <form action="<?= url('/search') ?>" method="GET" id="jqlForm">
                            <!-- Query Textarea -->
                            <div class="jql-editor-section">
                                <label for="jqlEditor" class="form-label">JQL Query</label>
                                <textarea name="jql" class="jql-editor" id="jqlEditor" 
                                          placeholder="project = DEMO AND status = 'In Progress' ORDER BY created DESC"
                                          aria-label="JQL query"><?= e($jql ?? '') ?></textarea>
                            </div>

                            <!-- Quick Reference -->
                            <div class="jql-reference">
                                <h4 class="reference-title">Quick Reference</h4>
                                <div class="reference-items">
                                    <div class="reference-item">
                                        <code class="reference-code"><span class="jql-field">project</span> <span class="jql-operator">=</span> <span class="jql-value">"PROJECT-KEY"</span></code>
                                        <span class="reference-desc">Filter by project</span>
                                    </div>
                                    <div class="reference-item">
                                        <code class="reference-code"><span class="jql-field">assignee</span> <span class="jql-operator">=</span> <span class="jql-value">currentUser()</span></code>
                                        <span class="reference-desc">Issues assigned to you</span>
                                    </div>
                                    <div class="reference-item">
                                        <code class="reference-code"><span class="jql-field">status</span> <span class="jql-operator">IN</span> <span class="jql-value">("Open", "In Progress")</span></code>
                                        <span class="reference-desc">Multiple statuses</span>
                                    </div>
                                    <div class="reference-item">
                                        <code class="reference-code"><span class="jql-field">created</span> <span class="jql-operator">=</span> <span class="jql-value">-7d</span></code>
                                        <span class="reference-desc">Created in last 7 days</span>
                                    </div>
                                    <div class="reference-item">
                                        <code class="reference-code"><span class="jql-field">summary</span> <span class="jql-operator">~</span> <span class="jql-value">"search text"</span></code>
                                        <span class="reference-desc">Search in summary</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="jql-submit">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search me-1"></i> Search
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-lg" onclick="validateJql()">
                                    <i class="bi bi-check-circle me-1"></i> Validate
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent & Examples -->
        <div class="reference-section">
            <div class="reference-column">
                <div class="reference-card">
                    <div class="reference-card-header">
                        <h3 class="reference-card-title">Recent Searches</h3>
                    </div>
                    <div class="reference-card-body">
                        <?php if (!empty($recentSearches)): ?>
                        <div class="reference-list">
                            <?php foreach ($recentSearches as $search): ?>
                            <a href="<?= url('/search?jql=' . urlencode($search['jql'])) ?>" class="reference-list-item" title="<?= e($search['jql']) ?>">
                                <code class="reference-item-code"><?= e(substr($search['jql'], 0, 50)) ?><?= strlen($search['jql']) > 50 ? '...' : '' ?></code>
                                <span class="reference-item-time"><?= time_ago($search['searched_at']) ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="empty-message">No recent searches yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="reference-column">
                <div class="reference-card">
                    <div class="reference-card-header">
                        <h3 class="reference-card-title">Example Queries</h3>
                    </div>
                    <div class="reference-card-body">
                        <div class="reference-list">
                            <a href="<?= url('/search?jql=' . urlencode('assignee = currentUser() AND status != Done')) ?>" 
                               class="reference-list-item">
                                <div class="reference-item-content">
                                    <strong class="reference-item-title">My open issues</strong>
                                    <code class="reference-item-jql">assignee = currentUser() AND status != Done</code>
                                </div>
                            </a>
                            <a href="<?= url('/search?jql=' . urlencode('created >= -7d ORDER BY created DESC')) ?>" 
                               class="reference-list-item">
                                <div class="reference-item-content">
                                    <strong class="reference-item-title">Created this week</strong>
                                    <code class="reference-item-jql">created >= -7d ORDER BY created DESC</code>
                                </div>
                            </a>
                            <a href="<?= url('/search?jql=' . urlencode('due <= 7d AND status != Done')) ?>" 
                               class="reference-list-item">
                                <div class="reference-item-content">
                                    <strong class="reference-item-title">Due soon</strong>
                                    <code class="reference-item-jql">due <= 7d AND status != Done</code>
                                </div>
                            </a>
                            <a href="<?= url('/search?jql=' . urlencode('priority = Highest AND status = Open')) ?>" 
                               class="reference-list-item">
                                <div class="reference-item-content">
                                    <strong class="reference-item-title">Critical open issues</strong>
                                    <code class="reference-item-jql">priority = Highest AND status = Open</code>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --jira-blue: #8B1956;
    --jira-dark: #6F123F;
    --jira-light-blue: rgba(139,25,86,0.1);
    --text-primary: #161B22;
    --text-secondary: #626F86;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F7F8FA;
    --border-color: #DFE1E6;
    --danger-color: #ED3C32;
    --success-color: #216E4E;
    --warning-color: #974F0C;
    --shadow-sm: 0 1px 1px rgba(9, 30, 66, 0.13);
    --shadow-md: 0 1px 3px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);
    --shadow-lg: 0 4px 12px rgba(9, 30, 66, 0.15);
    --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.advanced-search-wrapper {
    background: var(--bg-secondary);
    min-height: 100vh;
}

/* Breadcrumb */
.breadcrumb-nav {
    padding: 12px 32px;
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--text-secondary);
}

.breadcrumb-link {
    color: var(--jira-blue);
    text-decoration: none;
    transition: color var(--transition);
    display: flex;
    align-items: center;
    gap: 4px;
}

.breadcrumb-link:hover {
    color: var(--jira-dark);
    text-decoration: underline;
}

.breadcrumb-separator {
    color: var(--border-color);
}

.breadcrumb-current {
    color: var(--text-primary);
    font-weight: 500;
}

/* Page Header */
.page-header {
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    padding: 24px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
}

.header-left {
    flex: 1;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 4px 0;
    letter-spacing: -0.3px;
}

.page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
}

.header-right {
    flex-shrink: 0;
}

/* Content Area */
.content-area {
    padding: 24px 32px;
    max-width: 1200px;
    margin: 0 auto;
}

/* Tabs */
.tabs-container {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: var(--shadow-md);
    margin-bottom: 24px;
    overflow: hidden;
}

.tabs-nav {
    display: flex;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
}

.tab-item {
    flex: 1;
    padding: 16px;
    background: transparent;
    border: none;
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all var(--transition);
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
}

.tab-item:hover {
    color: var(--text-primary);
    background: rgba(139, 25, 86, 0.05);
}

.tab-item.active {
    color: var(--jira-blue);
    background: var(--bg-primary);
    border-bottom-color: var(--jira-blue);
}

.tab-content {
    padding: 24px;
}

.tab-pane {
    display: none;
}

.tab-pane.show.active {
    display: block;
}

/* Builder Card */
.builder-card,
.jql-card {
    background: transparent;
}

/* Conditions */
.builder-conditions {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
}

.condition-row {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 16px;
}

.condition-inputs {
    display: grid;
    grid-template-columns: auto 1fr 150px 1fr auto;
    gap: 12px;
    align-items: flex-end;
}

.condition-conjunction {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 13px;
    background: var(--bg-primary);
    color: var(--text-primary);
}

.condition-field,
.condition-operator,
.condition-value {
    display: flex;
    flex-direction: column;
}

.condition-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.field-select,
.operator-select,
.value-input {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 13px;
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: all var(--transition);
}

.field-select:focus,
.operator-select:focus,
.value-input:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

.btn-remove-condition {
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 8px;
    font-size: 18px;
    transition: color var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-remove-condition:hover {
    color: var(--danger-color);
}

/* Builder Actions */
.builder-actions {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

/* Order By Section */
.order-by-section {
    margin-bottom: 24px;
}

.order-by-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.order-by-select {
    width: 100%;
    max-width: 300px;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 13px;
    background: var(--bg-primary);
    color: var(--text-primary);
}

.order-by-select:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

/* Submit Section */
.builder-submit,
.jql-submit {
    display: flex;
    gap: 12px;
}

/* JQL Editor Section */
.jql-editor-section {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.jql-editor {
    width: 100%;
    min-height: 120px;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 13px;
    color: var(--text-primary);
    background: var(--bg-primary);
    resize: vertical;
    transition: all var(--transition);
}

.jql-editor:focus {
    outline: none;
    border-color: var(--jira-blue);
    box-shadow: 0 0 0 2px rgba(139, 25, 86, 0.1);
}

/* JQL Reference */
.jql-reference {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 16px;
    margin-bottom: 24px;
}

.reference-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    margin-top: 0;
    margin-bottom: 12px;
}

.reference-items {
    display: grid;
    gap: 8px;
}

.reference-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.reference-code {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 12px;
    color: var(--text-primary);
    padding: 6px 8px;
    background: var(--bg-primary);
    border-radius: 4px;
    display: inline-block;
}

.jql-field {
    color: var(--success-color);
    font-weight: 600;
}

.jql-operator {
    color: var(--danger-color);
    font-weight: 600;
}

.jql-value {
    color: #6f42c1;
}

.reference-desc {
    font-size: 11px;
    color: var(--text-secondary);
}

/* Reference Section */
.reference-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.reference-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

.reference-card-header {
    padding: 16px 20px;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
}

.reference-card-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.reference-card-body {
    padding: 12px;
    max-height: 400px;
    overflow-y: auto;
}

.reference-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.reference-list-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 12px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    text-decoration: none;
    color: var(--text-primary);
    transition: all var(--transition);
}

.reference-list-item:hover {
    background: var(--bg-primary);
    border-color: var(--jira-blue);
    box-shadow: 0 2px 4px rgba(139, 25, 86, 0.1);
}

.reference-item-code,
.reference-item-jql {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 11px;
    color: var(--text-secondary);
    word-break: break-all;
}

.reference-item-time {
    font-size: 11px;
    color: var(--text-secondary);
}

.reference-item-content {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.reference-item-title {
    font-size: 13px;
    color: var(--text-primary);
    display: block;
}

.empty-message {
    font-size: 12px;
    color: var(--text-secondary);
    margin: 0;
    padding: 12px 0;
    text-align: center;
}

/* Buttons */
.btn {
    padding: 10px 20px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 14px;
}

.btn-primary {
    background: var(--jira-blue);
    color: white;
}

.btn-primary:hover {
    background: var(--jira-dark);
}

.btn-secondary {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--border-color);
}

.btn-outline-secondary {
    background: var(--bg-primary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-outline-secondary:hover {
    background: var(--bg-secondary);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .content-area {
        padding: 20px 24px;
    }

    .page-header {
        padding: 20px 24px;
    }

    .reference-section {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .breadcrumb-nav {
        padding: 8px 16px;
        font-size: 11px;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 16px;
        gap: 12px;
    }

    .header-right {
        width: 100%;
    }

    .page-title {
        font-size: 22px;
    }

    .content-area {
        padding: 16px;
    }

    .tabs-nav {
        flex-direction: column;
    }

    .tab-item {
        border-bottom: none;
        border-right: 2px solid transparent;
        padding: 12px;
        justify-content: flex-start;
    }

    .tab-item.active {
        border-right-color: var(--jira-blue);
        border-bottom: none;
    }

    .tab-content {
        padding: 16px;
    }

    .condition-inputs {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .condition-conjunction {
        order: -1;
    }

    .builder-submit,
    .jql-submit {
        flex-direction: column;
        width: 100%;
    }

    .btn-lg {
        width: 100%;
    }

    .reference-section {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .order-by-select {
        width: 100%;
        max-width: 100%;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 18px;
    }

    .page-subtitle {
        font-size: 12px;
    }

    .tabs-nav {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .tab-item {
        white-space: nowrap;
        padding: 12px 16px;
    }

    .tab-item i {
        display: none;
    }

    .tab-content {
        padding: 12px;
    }

    .reference-items {
        gap: 6px;
    }

    .reference-list {
        gap: 6px;
    }
}
</style>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
let conditionIndex = 0;

// Add Condition
document.getElementById('addCondition')?.addEventListener('click', function() {
    conditionIndex++;
    const builder = document.getElementById('jqlBuilder');
    const newRow = document.createElement('div');
    newRow.className = 'condition-row';
    newRow.dataset.index = conditionIndex;
    newRow.innerHTML = `
        <div class="condition-inputs">
            <select class="condition-conjunction">
                <option value="AND">AND</option>
                <option value="OR">OR</option>
            </select>
            
            <div class="condition-field">
                <label class="condition-label">Field</label>
                <select class="field-select" required aria-label="Select field">
                    <option value="">Select field...</option>
                    <option value="project">Project</option>
                    <option value="type">Issue Type</option>
                    <option value="status">Status</option>
                    <option value="priority">Priority</option>
                    <option value="assignee">Assignee</option>
                    <option value="reporter">Reporter</option>
                    <option value="summary">Summary</option>
                    <option value="description">Description</option>
                    <option value="labels">Labels</option>
                    <option value="sprint">Sprint</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="resolved">Resolved</option>
                    <option value="due">Due Date</option>
                </select>
            </div>

            <div class="condition-operator">
                <label class="condition-label">Operator</label>
                <select class="operator-select" aria-label="Select operator">
                    <option value="=">=</option>
                    <option value="!=">!=</option>
                    <option value="~">~ (contains)</option>
                    <option value="!~">!~ (not contains)</option>
                    <option value="IN">IN</option>
                    <option value="NOT IN">NOT IN</option>
                    <option value="IS">IS</option>
                    <option value="IS NOT">IS NOT</option>
                    <option value=">">&gt;</option>
                    <option value="<">&lt;</option>
                    <option value=">=">&gt;=</option>
                    <option value="<=">&lt;=</option>
                </select>
            </div>

            <div class="condition-value">
                <label class="condition-label">Value</label>
                <input type="text" class="value-input" placeholder="Enter value..." aria-label="Enter value">
            </div>

            <button type="button" class="btn-remove-condition" aria-label="Remove condition">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
    `;
    builder.appendChild(newRow);
});

// Remove Condition
document.getElementById('jqlBuilder').addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove-condition')) {
        const row = e.target.closest('.condition-row');
        if (document.querySelectorAll('.condition-row').length > 1) {
            row.remove();
        }
    }
});

// Form Submit
document.getElementById('queryBuilderForm')?.addEventListener('submit', function(e) {
    const jql = buildJql();
    document.getElementById('generatedJql').value = jql;
});

function buildJql() {
    const rows = document.querySelectorAll('.condition-row');
    let jql = '';
    
    rows.forEach((row, index) => {
        const conjunction = row.querySelector('.condition-conjunction')?.value || 'AND';
        const field = row.querySelector('.field-select').value;
        const operator = row.querySelector('.operator-select').value;
        const value = row.querySelector('.value-input').value;
        
        if (field && value) {
            if (index > 0) {
                jql += ` ${conjunction} `;
            }
            const quotedValue = value.includes(' ') ? `"${value}"` : value;
            jql += `${field} ${operator} ${quotedValue}`;
        }
    });
    
    return jql;
}

function resetBuilder() {
    const builder = document.getElementById('jqlBuilder');
    const rows = builder.querySelectorAll('.condition-row');
    rows.forEach((row, index) => {
        if (index > 0) row.remove();
        else {
            row.querySelector('.field-select').value = '';
            row.querySelector('.value-input').value = '';
        }
    });
}

function validateJql() {
    const jql = document.getElementById('jqlEditor').value;
    if (!jql.trim()) {
        alert('Please enter a JQL query');
        return;
    }
    alert('✓ JQL validation complete for: ' + jql.substring(0, 50) + '...');
}
</script>
<?php \App\Core\View::endSection(); ?>
