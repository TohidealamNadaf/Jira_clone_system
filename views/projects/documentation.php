<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>


<div class="doc-wrapper">
    <!-- Breadcrumb Navigation -->
    <nav class="doc-breadcrumb">
        <a href="<?= url('/dashboard') ?>" class="breadcrumb-item">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-divider">/</span>
        <a href="<?= url('/projects') ?>" class="breadcrumb-item">Projects</a>
        <span class="breadcrumb-divider">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-item">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-divider">/</span>
        <span class="breadcrumb-current">Documentation</span>
    </nav>

    <!-- Page Header Section -->
    <div class="doc-header-section">
        <div class="doc-header-content">
            <div class="doc-header-info">
                <h1 class="doc-page-title">Documentation Hub</h1>
                <p class="doc-page-subtitle">Central repository for project documents, guides, and resources</p>
            </div>
            <div class="doc-header-action">
                <button type="button" class="btn btn-primary doc-upload-btn" data-bs-toggle="modal"
                    data-bs-target="#uploadModal">
                    <i class="bi bi-cloud-upload"></i> Upload Document
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="doc-stats-grid">
        <div class="doc-stat-card">
            <div class="doc-stat-icon">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="doc-stat-content">
                <div class="doc-stat-value"><?= $stats['total_documents'] ?></div>
                <div class="doc-stat-label">Total Documents</div>
            </div>
        </div>

        <div class="doc-stat-card">
            <div class="doc-stat-icon category-design">
                <i class="bi bi-palette"></i>
            </div>
            <div class="doc-stat-content">
                <div class="doc-stat-value"><?= $stats['designs'] ?></div>
                <div class="doc-stat-label">Design Files</div>
            </div>
        </div>

        <div class="doc-stat-card">
            <div class="doc-stat-icon category-technical">
                <i class="bi bi-gear"></i>
            </div>
            <div class="doc-stat-content">
                <div class="doc-stat-value"><?= $stats['technical'] ?></div>
                <div class="doc-stat-label">Technical Docs</div>
            </div>
        </div>

        <div class="doc-stat-card">
            <div class="doc-stat-icon category-report">
                <i class="bi bi-bar-chart"></i>
            </div>
            <div class="doc-stat-content">
                <div class="doc-stat-value">
                    <?= $stats['total_size'] > 0 ? App\Services\ProjectDocumentationService::formatFileSize($stats['total_size']) : '0 B' ?>
                </div>
                <div class="doc-stat-label">Total Size</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="doc-filters">
        <div class="doc-filters-left">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="docSearch" placeholder="Search documents..." value="<?= e($filters['search']) ?>"
                    class="form-control">
            </div>

            <select id="categoryFilter" class="form-select">
                <option value="">All Categories</option>
                <?php foreach ($categories as $value => $label): ?>
                    <option value="<?= $value ?>" <?= $filters['category'] === $value ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="doc-filters-right">
            <button id="clearFilters" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear Filters
            </button>
        </div>
    </div>

    <!-- Documents List -->
    <div class="doc-content">
        <?php if (!empty($documents)): ?>
            <div class="doc-list" id="documentList">
                <?php foreach ($documents as $document): ?>
                    <div class="doc-item" data-id="<?= $document['id'] ?>" data-category="<?= $document['category'] ?>"
                        data-title="<?= strtolower($document['title']) ?>"
                        data-filename="<?= strtolower($document['original_filename']) ?>">

                        <!-- Document Icon -->
                        <div class="doc-icon">
                            <i
                                class="<?= App\Services\ProjectDocumentationService::getFileIcon($document['mime_type'], $document['filename']) ?>"></i>
                        </div>

                        <!-- Document Info -->
                        <div class="doc-info">
                            <h4 class="doc-title"><?= e($document['title']) ?></h4>
                            <p class="doc-description"><?= e($document['description'] ?? 'No description') ?></p>
                            <div class="doc-meta">
                                <span class="doc-category category-<?= $document['category'] ?>">
                                    <?= $categories[$document['category']] ?>
                                </span>
                                <span
                                    class="doc-size"><?= App\Services\ProjectDocumentationService::formatFileSize($document['size']) ?></span>
                                <span class="doc-version">v<?= e($document['version']) ?></span>
                                <?php if ($document['download_count'] > 0): ?>
                                    <span class="doc-downloads">
                                        <i class="bi bi-download"></i> <?= $document['download_count'] ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="doc-footer">
                                <span class="doc-author">
                                    <i class="bi bi-person"></i>
                                    <?= e($document['first_name'] . ' ' . $document['last_name']) ?>
                                </span>
                                <span class="doc-date">
                                    <i class="bi bi-calendar"></i>
                                    <?= date('M j, Y', strtotime($document['created_at'])) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Document Actions -->
                        <div class="doc-actions">
                            <a href="<?= url("/projects/{$project['key']}/documentation/{$document['id']}/download") ?>"
                                class="btn btn-primary btn-sm" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <button class="btn btn-outline-primary btn-sm view-doc" data-bs-toggle="modal"
                                data-bs-target="#previewModal"
                                data-url="<?= url("/projects/{$project['key']}/documentation/{$document['id']}/download") ?>"
                                data-mime="<?= $document['mime_type'] ?>"
                                data-filename="<?= e($document['original_filename']) ?>" title="Preview">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm edit-doc" data-id="<?= $document['id'] ?>"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm delete-doc" data-id="<?= $document['id'] ?>"
                                title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="doc-empty-state">
                <div class="doc-empty-icon">
                    <i class="bi bi-file-earmark-x"></i>
                </div>
                <h3>No Documents Found</h3>
                <p>Start by uploading your first document to the project repository.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="bi bi-cloud-upload"></i> Upload First Document
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="margin-top: 10px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="bi bi-cloud-upload"></i> Upload Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Select File *</label>
                        <div class="drag-drop-zone" id="dropZone">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <p>Drag & drop your file here or <span class="text-primary">browse</span></p>
                            <span class="file-info text-muted">Supported formats: PDF, Word, Excel, Images,
                                Videos</span>
                            <input type="file" id="documentFile" name="document" required hidden
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,.odt,.ods,.odp,.rpt,.jpg,.jpeg,.png,.gif,.bmp,.svg,.mp4,.avi,.mov,.wmv,.flv,.webm,.mkv,.mp3,.wav,.flac,.aac,.ogg,.wma,.zip,.rar,.7z,.tar,.gz">
                        </div>
                        <div id="selectedFileDisplay" class="mt-2 d-none">
                            <div
                                class="alert alert-secondary d-flex align-items-center justify-content-between py-2 px-3">
                                <span><i class="bi bi-file-earmark me-2"></i><span id="fileName"></span></span>
                                <button type="button" class="btn-close small" id="removeFileBtn"></button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="docTitle" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="docTitle" name="title" required maxlength="255"
                            placeholder="Enter document title">
                    </div>

                    <div class="mb-3">
                        <label for="docDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="docDescription" name="description" rows="3"
                            placeholder="Enter document description (optional)"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="docCategory" class="form-label">Category</label>
                            <select class="form-select" id="docCategory" name="category">
                                <?php foreach ($categories as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="docVersion" class="form-label">Version</label>
                            <input type="text" class="form-control" id="docVersion" name="version" value="1.0"
                                placeholder="e.g., 1.0, 2.1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="docIsPublic" name="is_public" value="1"
                                checked>
                            <label class="form-check-label" for="docIsPublic">
                                Make this document publicly visible to all project members
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="uploadBtn" form="uploadForm">
                    <i class="bi bi-cloud-upload"></i> Upload Document
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="margin-top: 10px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="bi bi-pencil"></i> Edit Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <?= csrf_field() ?>
                    <input type="hidden" id="editDocId" name="document_id">
                    <div class="mb-3">
                        <label for="editDocTitle" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="editDocTitle" name="title" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="editDocDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDocDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editDocCategory" class="form-label">Category</label>
                            <select class="form-select" id="editDocCategory" name="category">
                                <?php foreach ($categories as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="editDocVersion" class="form-label">Version</label>
                            <input type="text" class="form-control" id="editDocVersion" name="version">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editDocIsPublic" name="is_public"
                                value="1">
                            <label class="form-check-label" for="editDocIsPublic">
                                Make this document publicly visible to all project members
                            </label>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="updateBtn" form="editForm">
                    <i class="bi bi-check-lg"></i> Update Document
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this document?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
                <p><strong>Document:</strong> <span id="deleteDocName"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash"></i> Delete Document
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xxl-custom modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content h-100">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye"></i> <span id="previewTitle">Document Preview</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="previewBody">
                <div class="d-flex justify-content-center align-items-center h-100 text-muted">
                    <div class="spinner-border text-primary me-2" role="status"></div>
                    <span>Loading preview...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Documentation Hub Redesign - Enterprise Jira-like UI */

    .doc-wrapper {
        background: var(--bg-secondary);
        min-height: calc(100vh - 100px);
        padding: 0;
    }

    /* Breadcrumb Navigation */
    .doc-breadcrumb {
        background: var(--bg-primary);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 4px;
        border-bottom: 1px solid var(--border-color);
        font-size: 13px;
    }

    .doc-breadcrumb .breadcrumb-item {
        color: var(--jira-blue);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition-base);
    }

    .doc-breadcrumb .breadcrumb-item:hover {
        color: var(--jira-blue-dark);
    }

    .doc-breadcrumb .breadcrumb-divider {
        color: var(--text-secondary);
        margin: 0 4px;
    }

    .doc-breadcrumb .breadcrumb-current {
        color: var(--text-primary);
        font-weight: 500;
    }

    /* Page Header Section */
    .doc-header-section {
        background: var(--bg-primary);
        padding: 20px 20px 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .doc-header-content {
        max-width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
    }

    .doc-header-info {
        flex: 1;
    }

    .doc-page-title {
        font-size: 26px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0 0 4px 0;
        letter-spacing: -0.2px;
    }

    .doc-page-subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.4;
    }

    .doc-header-action {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .doc-upload-btn {
        white-space: nowrap;
    }

    /* Content Area */
    .doc-content {
        padding: 16px 20px;
    }

    /* Statistics Grid */
    .doc-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .doc-stat-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-base);
    }

    .doc-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--jira-blue);
    }

    .doc-stat-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        background: var(--jira-blue-light);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--jira-blue);
        font-size: 20px;
    }

    .doc-stat-icon.category-design {
        background: rgba(24, 144, 255, 0.1);
        color: #1890ff;
    }

    .doc-stat-icon.category-technical {
        background: rgba(82, 196, 26, 0.1);
        color: #52c41a;
    }

    .doc-stat-icon.category-report {
        background: rgba(250, 140, 22, 0.1);
        color: #fa8c16;
    }

    .doc-stat-content {
        flex: 1;
    }

    .doc-stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
        margin-bottom: 4px;
    }

    .doc-stat-label {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Filters Section */
    .doc-filters {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        box-shadow: var(--shadow-sm);
        gap: 12px;
    }

    .doc-filters-left {
        display: flex;
        gap: 10px;
        align-items: center;
        flex: 1;
    }

    .search-box {
        position: relative;
        flex: 1;
        max-width: 320px;
    }

    .search-box i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        z-index: 1;
        font-size: 13px;
    }

    .search-box input {
        padding-left: 32px;
        padding-right: 12px;
        height: 32px;
        font-size: 13px;
        width: 100%;
    }

    .doc-filters-left .form-select {
        height: 32px;
        font-size: 13px;
        min-width: 220px;
        padding-top: 2px;
        padding-bottom: 2px;
    }

    .doc-filters-right {
        flex-shrink: 0;
    }

    /* Document List */
    .doc-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .doc-item {
        display: flex;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 12px;
        gap: 12px;
        align-items: flex-start;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-base);
    }

    .doc-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--jira-blue);
    }

    .doc-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        background: var(--jira-blue-light);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--jira-blue);
        font-size: 16px;
    }

    .doc-info {
        flex: 1;
        min-width: 0;
    }

    .doc-info .doc-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 4px 0;
        word-break: break-word;
    }

    .doc-description {
        font-size: 12px;
        color: var(--text-secondary);
        margin: 0 0 8px 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .doc-meta {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 8px;
        font-size: 11px;
        flex-wrap: wrap;
    }

    .doc-category {
        padding: 3px 8px;
        border-radius: 3px;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.3px;
    }

    .doc-category.category-requirement {
        background: rgba(82, 196, 26, 0.1);
        color: #52c41a;
    }

    .doc-category.category-design {
        background: rgba(24, 144, 255, 0.1);
        color: #1890ff;
    }

    .doc-category.category-technical {
        background: rgba(250, 173, 20, 0.1);
        color: #faad14;
    }

    .doc-category.category-user_guide {
        background: rgba(114, 46, 209, 0.1);
        color: #722ed1;
    }

    .doc-category.category-training {
        background: rgba(24, 144, 255, 0.1);
        color: #1890ff;
    }

    .doc-category.category-report {
        background: rgba(250, 140, 22, 0.1);
        color: #fa8c16;
    }

    .doc-category.category-other {
        background: rgba(140, 140, 140, 0.1);
        color: #8c8c8c;
    }

    .doc-size,
    .doc-version,
    .doc-downloads {
        color: var(--text-secondary);
    }

    .doc-footer {
        display: flex;
        gap: 10px;
        font-size: 11px;
        color: var(--text-secondary);
        flex-wrap: wrap;
    }

    .doc-author,
    .doc-date {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .doc-actions {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
    }

    .doc-actions .btn {
        padding: 4px 10px;
        font-size: 12px;
        height: 28px;
    }

    /* Empty State */
    .doc-empty-state {
        text-align: center;
        padding: 40px 20px;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        border-style: dashed;
    }

    .doc-empty-icon {
        font-size: 48px;
        color: var(--text-secondary);
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .doc-empty-state h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 6px 0;
    }

    .doc-empty-state p {
        color: var(--text-secondary);
        margin: 0 0 16px 0;
        font-size: 13px;
    }

    /* Modal Styles */
    .modal-content {
        border: none;
        box-shadow: var(--shadow-lg);
        border-radius: 8px;
    }

    .modal-header {
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color);
        padding: 16px 20px;
    }

    .modal-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .modal-body {
        padding: 20px;
        background: var(--bg-primary);
    }

    .modal-body .mb-3 {
        margin-bottom: 14px !important;
    }

    .modal-body .form-label {
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
        color: var(--text-primary);
    }

    .modal-body .form-control,
    .modal-body .form-select {
        font-size: 13px;
        height: 32px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        border-color: var(--jira-blue);
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
    }

    .modal-body .form-text {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .modal-body textarea.form-control {
        height: auto;
        min-height: 80px;
        resize: vertical;
        font-size: 13px;
    }

    .modal-body .form-check {
        margin-top: 8px;
    }

    .modal-body .form-check-label {
        font-size: 13px;
        color: var(--text-primary);
        margin-left: 6px;
    }

    .modal-footer {
        background: var(--bg-secondary);
        border-top: 1px solid var(--border-color);
        padding: 12px 20px;
        gap: 8px;
    }

    .modal-footer .btn {
        font-size: 13px;
        padding: 6px 16px;
        height: 32px;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .doc-wrapper {
            padding: 0;
        }

        .doc-breadcrumb {
            padding: 12px 20px;
        }

        .doc-header-section {
            padding: 24px 20px 20px;
        }

        .doc-content {
            padding: 20px;
        }

        .doc-stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .doc-header-content {
            flex-direction: column;
            gap: 16px;
        }
    }

    @media (max-width: 768px) {
        .doc-breadcrumb {
            padding: 12px 16px;
            font-size: 13px;
        }

        .doc-header-section {
            padding: 20px 16px 16px;
        }

        .doc-header-content {
            flex-direction: column;
            gap: 12px;
        }

        .doc-page-title {
            font-size: 24px;
        }

        .doc-page-subtitle {
            font-size: 13px;
        }

        .doc-content {
            padding: 16px;
        }

        .doc-stats-grid {
            grid-template-columns: 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .doc-filters {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
            padding: 12px;
        }

        .doc-filters-left {
            flex-direction: column;
            gap: 10px;
        }

        .search-box {
            max-width: none;
        }

        .doc-filters-left .form-select {
            min-width: auto;
            width: 100%;
        }

        .doc-item {
            flex-direction: column;
            padding: 12px;
            gap: 12px;
        }

        .doc-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            font-size: 16px;
        }

        .doc-meta {
            justify-content: flex-start;
            gap: 8px;
        }

        .doc-footer {
            gap: 8px;
        }

        .doc-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .doc-empty-state {
            padding: 40px 16px;
        }
    }

    @media (max-width: 480px) {
        .doc-header-section {
            padding: 16px 12px 12px;
        }

        .doc-page-title {
            font-size: 20px;
        }

        .doc-content {
            padding: 12px;
        }

        .doc-breadcrumb {
            padding: 10px 12px;
            font-size: 12px;
            gap: 2px;
        }

        .search-box input {
            height: 32px;
            font-size: 13px;
        }

        .doc-filters {
            padding: 10px;
            gap: 10px;
        }

        .doc-stat-card {
            padding: 12px;
            gap: 12px;
        }

        .doc-stat-value {
            font-size: 20px;
        }

        .doc-stat-label {
            font-size: 12px;
        }

        .doc-item {
            padding: 10px;
        }

        .doc-info .doc-title {
            font-size: 14px;
        }

        .doc-description {
            font-size: 12px;
            -webkit-line-clamp: 1;
        }
    }

    /* Drag & Drop Styles */
    .drag-drop-zone {
        border: 2px dashed var(--border-color);
        border-radius: 8px;
        padding: 30px 20px;
        text-align: center;
        background: var(--bg-secondary);
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
    }

    .drag-drop-zone:hover,
    .drag-drop-zone.dragover {
        border-color: var(--jira-blue);
        background: rgba(24, 144, 255, 0.05);
    }

    .drag-drop-zone i {
        font-size: 32px;
        color: var(--text-secondary);
        margin-bottom: 10px;
        display: block;
    }

    .drag-drop-zone p {
        margin: 0 0 5px;
        font-weight: 500;
        color: var(--text-primary);
    }

    .drag-drop-zone .file-info {
        font-size: 11px;
        display: block;
    }

    /* Fix Dropdown Cutoff */
    .modal-body .form-select {
        height: 38px !important;
        /* Increased height */
        padding-top: 6px !important;
        /* Adjusted padding */
        padding-bottom: 6px !important;
        line-height: 1.5;
    }

    #previewModal {
        z-index: 10000 !important;
        /* Ensure on top of everything including multiple backdrops */
    }

    .modal-xxl-custom {
        width: 95vw !important;
        max-width: 95vw !important;
        margin-left: auto !important;
        margin-right: auto !important;
        margin-top: 10px !important;
    }

    #previewModal .modal-content {
        height: 90vh;
        /* Consistent height */
    }

    /* Fix "Stuck" dark color on buttons after modal close (Focus State) */
    .doc-actions .btn:focus {
        box-shadow: none !important;
        background-color: transparent !important;
    }
    
    .doc-actions .btn-outline-primary:focus {
        color: var(--jira-blue) !important;
        border-color: var(--jira-blue) !important;
    }
    
    .doc-actions .btn-outline-secondary:focus {
        color: #6c757d !important;
        border-color: #6c757d !important;
    }

    .doc-actions .btn-outline-danger:focus {
        color: #dc3545 !important;
        border-color: #dc3545 !important;
    }
    
    /* Ensure Hover still works */
    .doc-actions .btn-outline-primary:hover {
        background-color: var(--jira-blue) !important;
        color: white !important;
    }
</style>

<script>
    // Documentation Hub JavaScript
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = '<?= csrf_token() ?>';
        let currentDeleteId = null;
        let currentEditId = null;

        // Search functionality
        const searchInput = document.getElementById('docSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const documentItems = document.querySelectorAll('.doc-item');

        function filterDocuments() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;

            documentItems.forEach(item => {
                const title = item.dataset.title;
                const filename = item.dataset.filename;
                const category = item.dataset.category;

                const matchesSearch = !searchTerm || title.includes(searchTerm) || filename.includes(searchTerm);
                const matchesCategory = !selectedCategory || category === selectedCategory;

                if (matchesSearch && matchesCategory) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', filterDocuments);
        categoryFilter.addEventListener('change', filterDocuments);

        clearFiltersBtn.addEventListener('click', function () {
            searchInput.value = '';
            categoryFilter.value = '';
            filterDocuments();
        });

        // Drag and Drop Logic
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('documentFile');
        const fileDisplay = document.getElementById('selectedFileDisplay');
        const fileNameSpan = document.getElementById('fileName');
        const removeFileBtn = document.getElementById('removeFileBtn');

        // Trigger file input on click
        dropZone.addEventListener('click', () => fileInput.click());

        // Drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
        });

        // Handle file drop
        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        });

        // Handle file select
        fileInput.addEventListener('change', function () {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            if (files.length > 0) {
                const file = files[0];
                fileInput.files = files; // Sync with input
                fileNameSpan.textContent = file.name;
                fileDisplay.classList.remove('d-none');
                dropZone.classList.add('d-none');
            }
        }

        // Remove file
        removeFileBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent dropZone click
            fileInput.value = '';
            fileDisplay.classList.add('d-none');
            dropZone.classList.remove('d-none');
        });

        // Upload form handling
        const uploadForm = document.getElementById('uploadForm');
        uploadForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(uploadForm);
            const uploadBtn = document.getElementById('uploadBtn');
            const originalText = uploadBtn.innerHTML;

            uploadBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';
            uploadBtn.disabled = true;

            fetch('<?= url("/projects/{$project['key']}/documentation/upload") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                        uploadForm.reset();
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    alert('Upload failed. Please try again.');
                })
                .finally(() => {
                    uploadBtn.innerHTML = originalText;
                    uploadBtn.disabled = false;
                });
        });

        // Edit functionality
        document.querySelectorAll('.edit-doc').forEach(btn => {
            btn.addEventListener('click', function () {
                const docId = this.dataset.id;
                currentEditId = docId;

                fetch(`<?= url("/projects/{$project['key']}/documentation") ?>/${docId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const doc = data.document;
                            document.getElementById('editDocId').value = doc.id;
                            document.getElementById('editDocTitle').value = doc.title;
                            document.getElementById('editDocDescription').value = doc.description || '';
                            document.getElementById('editDocCategory').value = doc.category;
                            document.getElementById('editDocVersion').value = doc.version;
                            document.getElementById('editDocIsPublic').checked = doc.is_public;

                            new bootstrap.Modal(document.getElementById('editModal')).show();
                        } else {
                            alert('Error: ' + data.error);
                        }
                    });
            });
        });

        // Edit form handling
        const editForm = document.getElementById('editForm');
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(editForm);
            const updateBtn = document.getElementById('updateBtn');
            const originalText = updateBtn.innerHTML;

            updateBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
            updateBtn.disabled = true;

            formData.append('_method', 'PUT');

            fetch(`<?= url("/projects/{$project['key']}/documentation") ?>/${currentEditId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Update error:', error);
                    alert('Update failed. Please try again.');
                })
                .finally(() => {
                    updateBtn.innerHTML = originalText;
                    updateBtn.disabled = false;
                });
        });

        // Delete functionality
        document.querySelectorAll('.delete-doc').forEach(btn => {
            btn.addEventListener('click', function () {
                currentDeleteId = this.dataset.id;

                const docItem = this.closest('.doc-item');
                const docTitle = docItem.querySelector('.doc-title').textContent;
                document.getElementById('deleteDocName').textContent = docTitle;

                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });

        // Confirm delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (!currentDeleteId) return;

            fetch(`<?= url("/projects/{$project['key']}/documentation") ?>/${currentDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                        location.reload();
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    alert('Delete failed. Please try again.');
                });
        });

        // Preview Functionality
        // Preview Functionality - Event Driven Approach
        const previewModal = document.getElementById('previewModal');
        if (previewModal) {
            previewModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;

                // Extract info from data-* attributes
                const url = button.getAttribute('data-url');
                const mime = button.getAttribute('data-mime');
                const filename = button.getAttribute('data-filename');

                // Update Modal Content
                const previewBody = document.getElementById('previewBody');
                const previewTitle = document.getElementById('previewTitle');

                previewTitle.textContent = filename;
                previewBody.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100 p-5"><div class="spinner-border text-primary me-2"></div> Loading...</div>';

                // Render content
                setTimeout(() => {
                    try {
                        renderPreview(url, mime, previewBody, filename);
                    } catch (e) {
                        console.error("Preview Error:", e);
                        previewBody.innerHTML = '<div class="alert alert-danger m-4">Failed to load preview.</div>';
                    }
                }, 200); // Slight delay to ensure modal is fully ready
            });
        }

        function renderPreview(url, mime, container, filename) {
            // Append preview param for local files
            const previewUrl = url + (url.includes('?') ? '&' : '?') + 'preview=1';

            // Native Browser Support (PDF, Image, Audio, Video, Text)
            const nativeTypes = [
                'application/pdf',
                'image/',
                'video/',
                'audio/',
                'text/',
                'application/json',
                'application/javascript'
            ];

            const isNative = nativeTypes.some(type => mime.startsWith(type) || mime === 'application/pdf');

            if (isNative) {
                if (mime === 'application/pdf') {
                    container.innerHTML = `<iframe src="${previewUrl}" width="100%" height="100%" style="border:none; min-height: 80vh;"></iframe>`;
                } else if (mime.startsWith('image/')) {
                    container.innerHTML = `<div class="d-flex justify-content-center align-items-center h-100 bg-light p-4"><img src="${previewUrl}" class="img-fluid" style="max-height: 80vh;"></div>`;
                } else if (mime.startsWith('video/')) {
                    container.innerHTML = `<div class="d-flex justify-content-center align-items-center h-100 bg-black"><video controls class="w-100" style="max-height: 80vh;"><source src="${previewUrl}" type="${mime}"></video></div>`;
                } else if (mime.startsWith('audio/')) {
                    container.innerHTML = `<div class="d-flex justify-content-center align-items-center h-100 bg-light p-5"><audio controls class="w-75"><source src="${previewUrl}" type="${mime}"></audio></div>`;
                } else {
                    // Text/Code
                    fetch(previewUrl).then(res => res.text()).then(txt => {
                        container.innerHTML = `<pre class="p-4 bg-light m-0 overflow-auto" style="height: 80vh;"><code>${txt.replace(/</g, '&lt;')}</code></pre>`;
                    });
                }
            }
            // Google Docs Viewer for Office Files (DOC, PPT, XLS)
            else {
                const isLocalhost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname) || window.location.hostname.endsWith('.test');

                if (isLocalhost) {
                    container.innerHTML = `
                            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center p-5">
                                <i class="bi bi-laptop display-1 text-muted mb-3"></i>
                                <h4>Localhost / Private Network Detected</h4>
                                <p class="text-muted mb-4" style="max-width: 400px;">
                                    Google Docs Viewer requires a public URL to preview Office documents (${mime}). 
                                    Since you are running locally, this service cannot reach your file.
                                </p>
                                <a href="${url}" class="btn btn-primary"><i class="bi bi-download"></i> Download File Instead</a>
                            </div>
                        `;
                } else {
                    // Public Server - Use Google Viewer
                    const encodedUrl = encodeURIComponent(window.location.origin + url + '&preview=1');
                    const googleViewerUrl = `https://docs.google.com/viewer?url=${encodedUrl}&embedded=true`;

                    container.innerHTML = `
                            <div class="d-flex flex-column h-100">
                                <div class="alert alert-info m-0 rounded-0 p-2 small">
                                    <i class="bi bi-info-circle"></i> Preview via Google Docs
                                </div>
                                <iframe src="${googleViewerUrl}" width="100%" height="100%" style="border:none; flex:1; min-height: 70vh;"></iframe>
                            </div>
                        `;
                }
            }
        }
    });
</script>



<?php \App\Core\View::endSection(); ?>