<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('styles'); ?>
<style>
    .jql-builder .condition-row {
        background: #f8f9fa;
        border-radius: 0.375rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .jql-editor {
        font-family: 'Consolas', 'Monaco', monospace;
        min-height: 100px;
    }
    .jql-hint {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .jql-keyword { color: #0d6efd; font-weight: bold; }
    .jql-field { color: #198754; }
    .jql-operator { color: #dc3545; }
    .jql-value { color: #6f42c1; }
</style>
<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Advanced Search</h2>
            <p class="text-muted mb-0">Build complex queries using JQL-like syntax</p>
        </div>
        <a href="<?= url('/search') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Basic Search
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <!-- Mode Toggle -->
            <ul class="nav nav-tabs mb-3" id="searchMode">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#builderMode">
                        <i class="bi bi-ui-checks me-1"></i> Query Builder
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#jqlMode">
                        <i class="bi bi-code me-1"></i> JQL Editor
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Query Builder Mode -->
                <div class="tab-pane fade show active" id="builderMode">
                    <form action="<?= url('/search') ?>" method="GET" id="queryBuilderForm">
                        <input type="hidden" name="jql" id="generatedJql">
                        
                        <div class="jql-builder" id="jqlBuilder">
                            <div class="condition-row" data-index="0">
                                <div class="row g-2 align-items-center">
                                    <div class="col-auto">
                                        <select class="form-select form-select-sm conjunction" style="width: 80px;" disabled>
                                            <option value="AND">AND</option>
                                            <option value="OR">OR</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select form-select-sm field-select" required>
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
                                    <div class="col-md-2">
                                        <select class="form-select form-select-sm operator-select">
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
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm value-input" placeholder="Value...">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-condition">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="addCondition">
                                <i class="bi bi-plus-lg me-1"></i> Add Condition
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="addGroup">
                                <i class="bi bi-braces me-1"></i> Add Group
                            </button>
                        </div>

                        <hr>

                        <!-- Order By -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Order By</label>
                                <select name="orderBy" class="form-select form-select-sm">
                                    <option value="created DESC">Created (Newest first)</option>
                                    <option value="created ASC">Created (Oldest first)</option>
                                    <option value="updated DESC">Updated (Recent first)</option>
                                    <option value="priority DESC">Priority (Highest first)</option>
                                    <option value="due ASC">Due Date (Soonest first)</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetBuilder()">
                                Clear
                            </button>
                        </div>
                    </form>
                </div>

                <!-- JQL Editor Mode -->
                <div class="tab-pane fade" id="jqlMode">
                    <form action="<?= url('/search') ?>" method="GET">
                        <div class="mb-3">
                            <label class="form-label">JQL Query</label>
                            <textarea name="jql" class="form-control jql-editor" id="jqlEditor" 
                                      placeholder="project = DEMO AND status = 'In Progress' ORDER BY created DESC"><?= e($jql ?? '') ?></textarea>
                        </div>

                        <div class="jql-hint mb-3">
                            <strong>Quick Reference:</strong>
                            <ul class="mb-0">
                                <li><span class="jql-field">project</span> <span class="jql-operator">=</span> <span class="jql-value">"PROJECT-KEY"</span></li>
                                <li><span class="jql-field">assignee</span> <span class="jql-operator">=</span> <span class="jql-value">currentUser()</span></li>
                                <li><span class="jql-field">status</span> <span class="jql-operator">IN</span> <span class="jql-value">("Open", "In Progress")</span></li>
                                <li><span class="jql-field">created</span> <span class="jql-operator">>=</span> <span class="jql-value">-7d</span> (last 7 days)</li>
                                <li><span class="jql-field">summary</span> <span class="jql-operator">~</span> <span class="jql-value">"search text"</span></li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> Search
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="validateJql()">
                                <i class="bi bi-check-circle me-1"></i> Validate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Searches -->
    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Recent Searches</h6>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentSearches ?? [] as $search): ?>
                    <a href="<?= url('/search?jql=' . urlencode($search['jql'])) ?>" class="list-group-item list-group-item-action">
                        <code class="small"><?= e($search['jql']) ?></code>
                        <small class="text-muted d-block"><?= time_ago($search['searched_at']) ?></small>
                    </a>
                    <?php endforeach; ?>
                    <?php if (empty($recentSearches)): ?>
                    <div class="list-group-item text-muted">No recent searches</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="mb-0">Example Queries</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('/search?jql=' . urlencode('assignee = currentUser() AND status != Done')) ?>" 
                       class="list-group-item list-group-item-action">
                        <strong>My open issues</strong>
                        <code class="small d-block text-muted">assignee = currentUser() AND status != Done</code>
                    </a>
                    <a href="<?= url('/search?jql=' . urlencode('created >= -7d ORDER BY created DESC')) ?>" 
                       class="list-group-item list-group-item-action">
                        <strong>Created this week</strong>
                        <code class="small d-block text-muted">created >= -7d ORDER BY created DESC</code>
                    </a>
                    <a href="<?= url('/search?jql=' . urlencode('due <= 7d AND status != Done')) ?>" 
                       class="list-group-item list-group-item-action">
                        <strong>Due soon</strong>
                        <code class="small d-block text-muted">due <= 7d AND status != Done</code>
                    </a>
                    <a href="<?= url('/search?jql=' . urlencode('priority = Highest AND status = Open')) ?>" 
                       class="list-group-item list-group-item-action">
                        <strong>Critical open issues</strong>
                        <code class="small d-block text-muted">priority = Highest AND status = Open</code>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
let conditionIndex = 0;

document.getElementById('addCondition').addEventListener('click', function() {
    conditionIndex++;
    const builder = document.getElementById('jqlBuilder');
    const newRow = document.createElement('div');
    newRow.className = 'condition-row';
    newRow.dataset.index = conditionIndex;
    newRow.innerHTML = `
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <select class="form-select form-select-sm conjunction" style="width: 80px;">
                    <option value="AND">AND</option>
                    <option value="OR">OR</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm field-select" required>
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
            <div class="col-md-2">
                <select class="form-select form-select-sm operator-select">
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
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm value-input" placeholder="Value...">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-sm btn-outline-danger remove-condition">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    `;
    builder.appendChild(newRow);
});

document.getElementById('jqlBuilder').addEventListener('click', function(e) {
    if (e.target.closest('.remove-condition')) {
        const row = e.target.closest('.condition-row');
        if (document.querySelectorAll('.condition-row').length > 1) {
            row.remove();
        }
    }
});

document.getElementById('queryBuilderForm').addEventListener('submit', function(e) {
    const jql = buildJql();
    document.getElementById('generatedJql').value = jql;
});

function buildJql() {
    const rows = document.querySelectorAll('.condition-row');
    let jql = '';
    
    rows.forEach((row, index) => {
        const conjunction = row.querySelector('.conjunction')?.value || 'AND';
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
    alert('JQL validation would check: ' + jql);
}
</script>
<?php \App\Core\View::endSection(); ?>
