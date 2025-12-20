<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="documentation-page-wrapper">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb-nav">
        <a href="<?= url('/dashboard') ?>" class="breadcrumb-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <span class="breadcrumb-sep">/</span>
        <a href="<?= url('/projects') ?>" class="breadcrumb-link">Projects</a>
        <span class="breadcrumb-sep">/</span>
        <a href="<?= url("/projects/{$project['key']}") ?>" class="breadcrumb-link">
            <?= e($project['name']) ?>
        </a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-current">Documentation</span>
    </nav>

    <!-- Page Header -->
    <div class="doc-header">
        <div class="doc-header-left">
            <h1 class="doc-title">Documentation Hub</h1>
            <p class="doc-subtitle">Central repository for project documents, guides, and resources</p>
        </div>
        <div class="doc-header-right">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cloud-upload"></i> Upload Document
            </button>
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
                <div class="doc-stat-value"><?= $stats['total_size'] > 0 ? App\Services\ProjectDocumentationService::formatFileSize($stats['total_size']) : '0 B' ?></div>
                <div class="doc-stat-label">Total Size</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="doc-filters">
        <div class="doc-filters-left">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" 
                       id="docSearch" 
                       placeholder="Search documents..." 
                       value="<?= e($filters['search']) ?>"
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
                    <div class="doc-item" data-id="<?= $document['id'] ?>" 
                         data-category="<?= $document['category'] ?>"
                         data-title="<?= strtolower($document['title']) ?>"
                         data-filename="<?= strtolower($document['original_filename']) ?>">
                        
                        <!-- Document Icon -->
                        <div class="doc-icon">
                            <i class="<?= App\Services\ProjectDocumentationService::getFileIcon($document['mime_type'], $document['filename']) ?>"></i>
                        </div>
                        
                        <!-- Document Info -->
                        <div class="doc-info">
                            <h4 class="doc-title"><?= e($document['title']) ?></h4>
                            <p class="doc-description"><?= e($document['description'] ?? 'No description') ?></p>
                            <div class="doc-meta">
                                <span class="doc-category category-<?= $document['category'] ?>">
                                    <?= $categories[$document['category']] ?>
                                </span>
                                <span class="doc-size"><?= App\Services\ProjectDocumentationService::formatFileSize($document['size']) ?></span>
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
                               class="btn btn-primary btn-sm"
                               title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <button class="btn btn-outline-secondary btn-sm edit-doc" 
                                    data-id="<?= $document['id'] ?>"
                                    title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm delete-doc" 
                                    data-id="<?= $document['id'] ?>"
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="bi bi-cloud-upload"></i> Upload Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="documentFile" class="form-label">Select File *</label>
                        <input type="file" 
                               class="form-control" 
                               id="documentFile" 
                               name="document" 
                               required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf,.odt,.ods,.odp,.rpt,.jpg,.jpeg,.png,.gif,.bmp,.svg,.mp4,.avi,.mov,.wmv,.flv,.webm,.mkv,.mp3,.wav,.flac,.aac,.ogg,.wma,.zip,.rar,.7z,.tar,.gz">
                        <div class="form-text">
                            Supported formats: PDF, Word, Excel, PowerPoint, Reports, Images, Videos, Audio, Archives (Max 50MB)
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="docTitle" class="form-label">Title *</label>
                        <input type="text" 
                               class="form-control" 
                               id="docTitle" 
                               name="title" 
                               required
                               maxlength="255"
                               placeholder="Enter document title">
                    </div>
                    
                    <div class="mb-3">
                        <label for="docDescription" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="docDescription" 
                                  name="description" 
                                  rows="3"
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
                            <input type="text" 
                                   class="form-control" 
                                   id="docVersion" 
                                   name="version" 
                                   value="1.0"
                                   placeholder="e.g., 1.0, 2.1">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="docIsPublic" 
                                   name="is_public" 
                                   value="1" 
                                   checked>
                            <label class="form-check-label" for="docIsPublic">
                                Make this document publicly visible to all project members
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">
                        <i class="bi bi-cloud-upload"></i> Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="bi bi-pencil"></i> Edit Document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm">
                <input type="hidden" id="editDocId" name="document_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDocTitle" class="form-label">Title *</label>
                        <input type="text" 
                               class="form-control" 
                               id="editDocTitle" 
                               name="title" 
                               required
                               maxlength="255">
                    </div>
                    
                    <div class="mb-3">
                        <label for="editDocDescription" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="editDocDescription" 
                                  name="description" 
                                  rows="3"></textarea>
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
                            <input type="text" 
                                   class="form-control" 
                                   id="editDocVersion" 
                                   name="version">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="editDocIsPublic" 
                                   name="is_public" 
                                   value="1">
                            <label class="form-check-label" for="editDocIsPublic">
                                Make this document publicly visible to all project members
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateBtn">
                        <i class="bi bi-check-lg"></i> Update Document
                    </button>
                </div>
            </form>
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

<style>
/* Documentation Hub Styles */
.documentation-page-wrapper {
    background-color: var(--bg-primary);
    min-height: calc(100vh - 120px);
    padding: 2rem;
}

/* Header */
.doc-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.doc-title {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.doc-subtitle {
    font-size: 15px;
    color: var(--text-secondary);
    margin: 0;
}

/* Statistics Grid */
.doc-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 2rem;
}

.doc-stat-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.doc-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.doc-stat-icon {
    width: 48px;
    height: 48px;
    background: var(--jira-blue-light);
    border-radius: var(--radius-lg);
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

.doc-stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1;
}

.doc-stat-label {
    font-size: 13px;
    color: var(--text-secondary);
    margin-top: 0.25rem;
}

/* Filters */
.doc-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-sm);
}

.doc-filters-left {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex: 1;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 400px;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
    z-index: 1;
}

.search-box input {
    padding-left: 40px;
}

/* Document List */
.doc-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.doc-item {
    display: flex;
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    gap: 1rem;
    align-items: flex-start;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.doc-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.doc-icon {
    width: 48px;
    height: 48px;
    background: var(--jira-blue-light);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--jira-blue);
    font-size: 20px;
    flex-shrink: 0;
}

.doc-info {
    flex: 1;
}

.doc-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.doc-description {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0 0 1rem 0;
    line-height: 1.4;
}

.doc-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 12px;
}

.doc-category {
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius);
    font-weight: 500;
    text-transform: uppercase;
    font-size: 10px;
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

.doc-size, .doc-version, .doc-downloads {
    color: var(--text-secondary);
}

.doc-footer {
    display: flex;
    gap: 1rem;
    font-size: 12px;
    color: var(--text-secondary);
}

.doc-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

/* Empty State */
.doc-empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.doc-empty-icon {
    font-size: 64px;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.doc-empty-state h3 {
    font-size: 20px;
    color: var(--text-primary);
    margin: 1rem 0;
}

.doc-empty-state p {
    color: var(--text-secondary);
    margin-bottom: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .documentation-page-wrapper {
        padding: 1rem;
    }
    
    .doc-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .doc-stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .doc-filters {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .doc-filters-left {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .search-box {
        max-width: none;
    }
    
    .doc-item {
        flex-direction: column;
        text-align: center;
    }
    
    .doc-meta {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .doc-footer {
        justify-content: center;
    }
    
    .doc-actions {
        justify-content: center;
        margin-top: 1rem;
    }
}
</style>

<script>
// Documentation Hub JavaScript
document.addEventListener('DOMContentLoaded', function() {
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
    
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        categoryFilter.value = '';
        filterDocuments();
    });
    
    // Upload form handling
    const uploadForm = document.getElementById('uploadForm');
    uploadForm.addEventListener('submit', function(e) {
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
                'X-Requested-With': 'XMLHttpRequest'
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
        btn.addEventListener('click', function() {
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
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(editForm);
        const updateBtn = document.getElementById('updateBtn');
        const originalText = updateBtn.innerHTML;
        
        updateBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
        updateBtn.disabled = true;
        
        fetch(`<?= url("/projects/{$project['key']}/documentation") ?>/${currentEditId}`, {
            method: 'PUT',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
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
        btn.addEventListener('click', function() {
            currentDeleteId = this.dataset.id;
            
            const docItem = this.closest('.doc-item');
            const docTitle = docItem.querySelector('.doc-title').textContent;
            document.getElementById('deleteDocName').textContent = docTitle;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });
    
    // Confirm delete
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!currentDeleteId) return;
        
        fetch(`<?= url("/projects/{$project['key']}/documentation") ?>/${currentDeleteId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
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
});
</script>

<?php \App\Core\View::endSection(); ?>