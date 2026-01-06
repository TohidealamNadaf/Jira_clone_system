<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>


<div class="doc-wrapper">
    <!-- Standard Breadcrumb Navigation -->
    <div class="breadcrumb-section">
        <div class="breadcrumb">
            <a href="<?= url('/') ?>" class="breadcrumb-link">
                <i class="bi bi-house-door"></i> Home
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
            <span class="breadcrumb-separator">/</span>
            <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
                <?= e($project['name']) ?>
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Documentation</span>
        </div>
    </div>

    <!-- Header Section with Actions -->
    <div class="doc-header-wrapper">

        <div class="doc-header-main">
            <div class="doc-title-block">
                <div class="doc-icon-badge">
                    <i class="bi bi-folder2-open"></i>
                </div>
                <div>
                    <h1 class="doc-page-title">Documentation Hub</h1>
                    <p class="doc-page-subtitle">Manage, track, and collaborate on project resources.</p>
                </div>
            </div>
            <div class="doc-header-actions">
                <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#uploadModal">
                    <i class="bi bi-cloud-upload me-2"></i> Upload Document
                </button>
            </div>
        </div>
    </div>

    <div class="doc-body-content">
        <!-- Stats Row -->
        <div class="doc-stats-row">
            <div class="doc-stat-card">
                <div class="stat-icon-wrapper bg-soft-primary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['total_documents'] ?></div>
                    <div class="stat-label">Total Files</div>
                </div>
            </div>

            <div class="doc-stat-card">
                <div class="stat-icon-wrapper bg-soft-info">
                    <i class="bi bi-palette"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['designs'] ?></div>
                    <div class="stat-label">Designs</div>
                </div>
            </div>

            <div class="doc-stat-card">
                <div class="stat-icon-wrapper bg-soft-success">
                    <i class="bi bi-gear"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $stats['technical'] ?></div>
                    <div class="stat-label">Technical</div>
                </div>
            </div>

            <div class="doc-stat-card">
                <div class="stat-icon-wrapper bg-soft-warning">
                    <i class="bi bi-hdd"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value">
                        <?= $stats['total_size'] > 0 ? App\Services\ProjectDocumentationService::formatFileSize($stats['total_size']) : '0 B' ?>
                    </div>
                    <div class="stat-label">Storage Used</div>
                </div>
            </div>
        </div>

        <!-- Filters & Toolbar -->
        <div class="doc-toolbar">
            <div class="search-wrapper">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="docSearch" placeholder="Filter documents by name..."
                    value="<?= e($filters['search']) ?>" class="form-control search-input">
            </div>

            <div class="filter-wrapper">
                <select id="categoryFilter" class="form-select filter-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?= $value ?>" <?= $filters['category'] === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button id="clearFilters" class="btn btn-outline-secondary btn-icon-text">
                    <i class="bi bi-x-lg"></i> Clear
                </button>
            </div>
        </div>

        <!-- Documents Grid/List -->
        <div class="doc-list-container">
            <?php if (!empty($documents)): ?>
                <div class="doc-grid" id="documentList">
                    <?php foreach ($documents as $document): ?>
                        <div class="doc-item card-hover" data-id="<?= $document['id'] ?>"
                            data-category="<?= $document['category'] ?>" data-title="<?= strtolower($document['title']) ?>"
                            data-filename="<?= strtolower($document['original_filename']) ?>">

                            <div class="doc-item-left">
                                <div class="doc-file-icon">
                                    <i
                                        class="<?= App\Services\ProjectDocumentationService::getFileIcon($document['mime_type'], $document['filename']) ?>"></i>
                                </div>
                                <div class="doc-content-info">
                                    <div class="doc-header-row">
                                        <h4 class="doc-title"><?= e($document['title']) ?></h4>
                                        <span class="doc-badge category-<?= $document['category'] ?>">
                                            <?= $categories[$document['category']] ?>
                                        </span>
                                    </div>
                                    <p class="doc-orig-filename"><?= e($document['original_filename']) ?></p>
                                    <div class="doc-meta-row">
                                        <span title="Version">v<?= e($document['version']) ?></span>
                                        <span class="meta-dot">•</span>
                                        <span><?= App\Services\ProjectDocumentationService::formatFileSize($document['size']) ?></span>
                                        <span class="meta-dot">•</span>
                                        <span><?= date('M j, Y', strtotime($document['created_at'])) ?></span>
                                        <span class="meta-dot">•</span>
                                        <span class="doc-uploader">
                                            <i class="bi bi-person-circle"></i>
                                            <?= e($document['first_name'] . ' ' . $document['last_name']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="doc-item-actions">
                                <?php if ($document['download_count'] > 0): ?>
                                    <div class="download-stat" title="Total Downloads">
                                        <i class="bi bi-arrow-down-circle"></i> <?= $document['download_count'] ?>
                                    </div>
                                <?php endif; ?>

                                <div class="btn-group">
                                    <a href="<?= url("/projects/{$project['key']}/documentation/{$document['id']}/download") ?>"
                                        class="btn btn-light btn-sm" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button class="btn btn-light btn-sm view-doc" data-bs-toggle="modal"
                                        data-bs-target="#previewModal"
                                        data-url="<?= url("/projects/{$project['key']}/documentation/{$document['id']}/download") ?>"
                                        data-mime="<?= $document['mime_type'] ?>"
                                        data-filename="<?= e($document['original_filename']) ?>" title="Preview">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm edit-doc" data-id="<?= $document['id'] ?>"
                                        title="Edit Metadata">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-light btn-sm delete-doc text-danger" data-id="<?= $document['id'] ?>"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state-card">
                    <div class="empty-icon-circle">
                        <i class="bi bi-folder-plus"></i>
                    </div>
                    <h3>No documents yet</h3>
                    <p>There are no documents in this project. Upload your first file to get started.</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-cloud-upload me-2"></i> Upload File
                    </button>
                </div>
            <?php endif; ?>
        </div>
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
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <p>Drag & drop your file here or <span class="text-plum cursor-pointer">browse</span></p>
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
    /* 
     * PROJECT DOCUMENTATION - PREMIUM COMPACT UI
     * Theme: "Lumina Plum" | Style: Glassmorphic & Standard Scale
     */

    :root {
        --plum-900: #500724;
        --plum-700: #8B1956;
        --plum-600: #AC2066;
        --plum-500: #D42F7C;
        --plum-100: #FCE7F3;
        --plum-50: #FDF2F8;

        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.4);

        --text-msg: #374151;
        --text-head: #111827;

        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;

        --transition-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    body {
        background: #F3F4F6 !important;
    }

    /* Main Container */
    .doc-wrapper {
        min-height: calc(100vh - 60px);
        background:
            radial-gradient(circle at 10% 20%, rgba(139, 25, 86, 0.03) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(37, 99, 235, 0.03) 0%, transparent 40%),
            #F8FAFC;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        padding-bottom: 40px;
        position: relative;
    }

    /* Decorative Top Mesh (Reduced height) */
    .doc-wrapper::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 200px;
        top: 0;
        left: 0;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, #F8FAFC 100%),
            linear-gradient(to right, rgba(139, 25, 86, 0.05), rgba(59, 130, 246, 0.05));
        z-index: 0;
        pointer-events: none;
    }

    /* ----------------------------------------------------
       BREADCRUMB SECTION (Standard Consistency)
       ---------------------------------------------------- */
    .breadcrumb-section {
        background: var(--bg-primary);
        border-bottom: 1px solid var(--border-color, #DFE1E6);
        padding: 8px 24px;
        flex-shrink: 0;
        position: relative;
        z-index: 15;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        padding: 0;
        font-size: 13px;
    }

    .breadcrumb-link {
        color: var(--plum-700);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .breadcrumb-link:hover {
        color: var(--plum-900);
        text-decoration: underline;
    }

    .breadcrumb-separator {
        color: #64748B;
        margin: 0 2px;
        font-weight: 300;
    }

    .breadcrumb-current {
        color: var(--text-head);
        font-weight: 600;
    }

    /* ----------------------------------------------------
       HEADER SECTION (Compacted)
       ---------------------------------------------------- */
    .doc-header-wrapper {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(229, 231, 235, 0.8);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02);
        position: relative;
        z-index: 10;
        margin-bottom: 1.5rem;
    }

    .doc-header-main {
        padding: 1.25rem 1.5rem;
        /* Standardized spacing */
        display: flex;
        justify-content: space-between;
        align-items: center;
        /* Center vertically for cleaner row */
    }

    .doc-title-block {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .doc-icon-badge {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--plum-700), var(--plum-500));
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 4px 12px -2px rgba(139, 25, 86, 0.25);
    }

    .doc-page-title {
        font-size: 1.5rem;
        /* Reduced from 2rem */
        font-weight: 700;
        color: var(--text-head);
        margin: 0;
        letter-spacing: -0.01em;
    }

    .doc-page-subtitle {
        font-size: 0.875rem;
        color: #6B7280;
        margin-top: 0;
        font-weight: 400;
    }

    .doc-header-actions .btn-primary {
        background: linear-gradient(135deg, var(--plum-700) 0%, var(--plum-600) 100%);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        box-shadow: 0 2px 4px rgba(139, 25, 86, 0.15);
        transition: all 0.2s ease;
    }

    .doc-header-actions .btn-primary:hover {
        box-shadow: 0 4px 8px rgba(139, 25, 86, 0.25);
        transform: translateY(-1px);
    }

    /* ----------------------------------------------------
       STATS & CONTENT (Compacted)
       ---------------------------------------------------- */
    .doc-body-content {
        max-width: 1200px;
        /* Tighter container */
        margin: 0 auto;
        padding: 0 1.5rem;
        position: relative;
        z-index: 5;
    }

    .doc-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .doc-stat-card {
        background: white;
        border-radius: var(--radius-md);
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid rgba(229, 231, 235, 0.6);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .doc-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .stat-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .bg-soft-primary {
        background: #EEF2FF;
        color: #4338CA;
    }

    .bg-soft-info {
        background: #F0F9FF;
        color: #0369A1;
    }

    .bg-soft-success {
        background: #F0FDF4;
        color: #15803D;
    }

    .bg-soft-warning {
        background: #FFFBEB;
        color: #B45309;
    }

    .stat-info .stat-value {
        font-size: 1.25rem;
        /* Standard H4 size */
        font-weight: 700;
        color: var(--text-head);
        line-height: 1.1;
    }

    .stat-info .stat-label {
        font-size: 0.813rem;
        color: #6B7280;
        font-weight: 500;
    }

    /* ----------------------------------------------------
       TOOLBAR (Standard Input Sizes)
       ---------------------------------------------------- */
    .doc-toolbar {
        background: white;
        padding: 0.75rem;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #E5E7EB;
    }

    .search-wrapper {
        flex: 1;
        max-width: 360px;
        position: relative;
    }

    .search-input {
        background: #F9FAFB !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 8px !important;
        padding-left: 2.5rem !important;
        height: 38px !important;
        /* Standard height */
        font-size: 0.875rem;
    }

    .search-input:focus {
        background: white !important;
        border-color: var(--plum-500) !important;
        box-shadow: 0 0 0 3px var(--plum-100) !important;
    }

    .search-icon {
        position: absolute;
        left: 0.875rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
        font-size: 0.875rem;
        pointer-events: none;
    }

    .filter-wrapper {
        display: flex;
        gap: 0.5rem;
    }

    .filter-select {
        background-color: #F9FAFB;
        border: 1px solid #E5E7EB !important;
        border-radius: 8px !important;
        height: 38px !important;
        /* Standard height */
        padding: 0 2rem 0 0.75rem !important;
        font-size: 0.875rem;
        color: #374151;
        cursor: pointer;
        min-width: 160px;
    }

    .btn-icon-text {
        height: 38px;
        padding: 0 12px;
        font-size: 0.875rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ----------------------------------------------------
       DOCUMENT LIST (Compact Rows)
       ---------------------------------------------------- */
    .doc-grid {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .doc-item {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: var(--radius-md);
        padding: 0.75rem 1rem;
        /* Compact padding */
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.15s ease;
    }

    .doc-item:hover {
        border-color: var(--plum-300, #F472B6);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }

    .doc-item-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .doc-file-icon {
        width: 40px;
        height: 40px;
        background: #F1F5F9;
        color: #64748B;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .doc-item:hover .doc-file-icon {
        background: var(--plum-50);
        color: var(--plum-700);
    }

    .doc-content-info {
        flex: 1;
        min-width: 0;
    }

    .doc-header-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 2px;
    }

    .doc-title {
        font-size: 0.938rem;
        /* 15px */
        font-weight: 600;
        color: var(--text-head);
        margin: 0;
    }

    .doc-badge {
        font-size: 0.688rem;
        padding: 1px 6px;
        border-radius: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    /* Badge Colors */
    .category-design {
        background: rgba(59, 130, 246, 0.1);
        color: #2563EB;
    }

    .category-technical {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .category-report {
        background: rgba(245, 158, 11, 0.1);
        color: #D97706;
    }

    .category-requirement {
        background: rgba(139, 92, 246, 0.1);
        color: #7C3AED;
    }

    .category-other {
        background: #F3F4F6;
        color: #4B5563;
    }

    .doc-orig-filename {
        font-family: 'SF Mono', 'Menlo', monospace;
        font-size: 0.75rem;
        color: #9CA3AF;
        margin: 0;
    }

    .doc-meta-row {
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.75rem;
        color: #6B7280;
    }

    .doc-uploader {
        background: none;
        padding: 0;
        color: #6B7280;
        font-weight: 400;
    }

    /* Actions */
    .doc-item-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .download-stat {
        font-size: 0.813rem;
        color: #9CA3AF;
    }

    .btn-group {
        background: transparent;
        padding: 0;
        gap: 2px;
    }

    .btn-group .btn {
        width: 32px;
        height: 32px;
        border-radius: 6px !important;
        border: 1px solid #E5E7EB;
        background: white;
        color: #64748B;
        font-size: 14px;
    }

    .btn-group .btn:hover {
        border-color: var(--plum-700);
        color: var(--plum-700);
        background: var(--plum-50);
    }

    .btn-group .btn.delete-doc:hover {
        border-color: #EF4444;
        color: #EF4444;
        background: #FEF2F2;
    }

    /* ----------------------------------------------------
       UPLOAD MODAL & DRAG-DROP (Premium Lumina Style)
       ---------------------------------------------------- */
    .drag-drop-zone {
        border: 2px dashed #D1D5DB;
        border-radius: var(--radius-md);
        padding: 2.5rem 1.5rem;
        text-align: center;
        background: #F9FAFB;
        cursor: pointer;
        transition: all 0.2s var(--transition-spring);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        position: relative;
    }

    .drag-drop-zone:hover,
    .drag-drop-zone.dragover {
        border-color: var(--plum-600);
        background: var(--plum-50);
        transform: translateY(-2px);
    }

    .drag-drop-zone i {
        font-size: 2.5rem;
        color: var(--plum-500);
        margin-bottom: 0.25rem;
    }

    .drag-drop-zone p {
        font-size: 0.938rem;
        font-weight: 500;
        color: #374151;
        margin: 0;
    }

    .drag-drop-zone .file-info {
        font-size: 0.813rem;
        color: #6B7280;
    }

    .text-plum {
        color: var(--plum-700) !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .modal-content {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .modal-header {
        border-bottom: 1px solid #F3F4F6;
        padding: 1.25rem 1.5rem;
    }

    .modal-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-head);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-footer {
        background: #F9FAFB;
        border-top: 1px solid #F3F4F6;
        padding: 1rem 1.5rem;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-head);
        margin-bottom: 0.5rem;
    }

    .modal-body .form-control,
    .modal-body .form-select {
        border-radius: 8px;
        border: 1px solid #E5E7EB;
        padding: 0.625rem 0.875rem;
        font-size: 0.938rem;
        transition: all 0.2s;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        border-color: var(--plum-500);
        box-shadow: 0 0 0 4px var(--plum-100);
        outline: none;
    }

    /* Primary Button Global Consistency */
    .btn-primary {
        background: linear-gradient(135deg, var(--plum-700) 0%, var(--plum-600) 100%) !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(139, 25, 86, 0.15) !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--plum-600) 0%, var(--plum-500) 100%) !important;
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.25) !important;
        transform: translateY(-1px);
        color: white !important;
    }

    /* Modal spacing tweaks */
    #uploadModal .modal-body,
    #editModal .modal-body {
        padding: 1.5rem;
    }

    /* Empty State */
    .empty-state-card {
        background: white;
        border-radius: 12px;
        padding: 4rem 2rem;
        text-align: center;
        border: 2px dashed #E5E7EB;
    }

    .empty-icon-circle {
        width: 80px;
        height: 80px;
        background: #F9FAFB;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 32px;
        color: #9CA3AF;
    }

    /* Animation */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .doc-item {
        animation: slideIn 0.3s ease-out forwards;
    }

    /* Responsive */
    @media (max-width: 768px) {

        .doc-header-main,
        .doc-toolbar {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .search-wrapper {
            max-width: none;
        }

        .doc-item {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
        }

        .doc-item-actions {
            border-top: 1px solid #F3F4F6;
            padding-top: 0.75rem;
            justify-content: space-between;
        }
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