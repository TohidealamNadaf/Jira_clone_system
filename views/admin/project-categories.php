<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="margin-top: 5px !important;">
        <div class="modal-content enterprise-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Project Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" method="POST" action="<?= url('/admin/project-categories') ?>">
                <?= csrf_field() ?>
                <input type="hidden" id="categoryId" name="id">
                <input type="hidden" id="categoryMethod" name="_method" value="POST">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label-compact">Category Name <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control-compact" id="categoryName" name="name" required
                            maxlength="100" placeholder="e.g. Marketing, Development">
                    </div>
                    <div class="mb-0">
                        <label for="categoryDescription" class="form-label-compact">Description</label>
                        <textarea class="form-control-compact" id="categoryDescription" name="description" rows="4"
                            maxlength="500" placeholder="High-level description of this category..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-enterprise-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-enterprise-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="page-wrapper">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
        <a href="<?= url('/admin') ?>" class="breadcrumb-link">
            <i class="bi bi-shield-lock"></i> Administration
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Project Categories</span>
    </div>

    <!-- Page Header Section -->
    <div class="page-header">
        <div class="header-left">
            <div class="header-icon-badge">
                <i class="bi bi-tags-fill"></i>
            </div>
            <div class="header-info">
                <h1 class="page-title">Project Categories</h1>
                <p class="page-subtitle">Organize and group your projects into logical categories.</p>
            </div>
        </div>
        <div class="header-actions">
            <button type="button" class="action-button primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="bi bi-plus-lg"></i>
                <span>Add Category</span>
            </button>
            <button class="action-button" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="page-content">
        <div class="content-left">
            <!-- Categories Card -->
            <div class="enterprise-card">
                <div class="card-header-bar">
                    <h2 class="card-title">All Categories</h2>
                    <span class="badge-count"><?= count($categories) ?> Total</span>
                </div>
                <div class="table-container">
                    <table class="enterprise-table">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th width="100">Projects</th>
                                <th>Created</th>
                                <th width="100" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="5" class="empty-state-row">
                                        <div class="empty-state-content">
                                            <i class="bi bi-inbox"></i>
                                            <p>No project categories found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr class="table-row-hover">
                                        <td class="name-cell">
                                            <div class="category-avatar-small">
                                                <?= strtoupper(substr($category['name'], 0, 1)) ?>
                                            </div>
                                            <span class="category-name-text"><?= e($category['name']) ?></span>
                                        </td>
                                        <td>
                                            <span
                                                class="text-description"><?= e($category['description'] ?? 'No description provided') ?></span>
                                        </td>
                                        <td>
                                            <span class="stat-badge blue">
                                                <i class="bi bi-grid-3x3-gap"></i> <?= $category['project_count'] ?? 0 ?>
                                            </span>
                                        </td>
                                        <td class="date-cell">
                                            <?= format_date($category['created_at']) ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="action-group-icons">
                                                <button type="button" class="icon-action-btn edit" data-bs-toggle="modal"
                                                    data-bs-target="#categoryModal"
                                                    onclick="editCategory(<?= htmlspecialchars(json_encode($category)) ?>)"
                                                    title="Edit Category">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <form action="<?= url('/admin/project-categories/' . $category['id']) ?>"
                                                    method="POST" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="icon-action-btn delete"
                                                        onclick="return confirm('Are you sure you want to delete this category?');"
                                                        title="Delete Category">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="content-right">
            <!-- Summary Sidebar Card -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Management Overview</h3>
                <div class="sidebar-content">
                    <div class="sidebar-stat-item">
                        <span class="sidebar-stat-label">Total Categories</span>
                        <span class="sidebar-stat-value"><?= count($categories) ?></span>
                    </div>
                </div>
            </div>

            <!-- Quick Links Card -->
            <div class="sidebar-card">
                <h3 class="sidebar-card-title">Related Actions</h3>
                <div class="sidebar-links">
                    <a href="<?= url('/admin/projects') ?>" class="sidebar-link-item">
                        <i class="bi bi-grid-3x3-gap"></i>
                        <span>Manage Projects</span>
                    </a>
                    <a href="<?= url('/admin/settings') ?>" class="sidebar-link-item">
                        <i class="bi bi-gear"></i>
                        <span>System Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    /* ============================================
   PROJECT CATEGORIES - ENTERPRISE DESIGN
   ============================================ */

    :root {
        --jira-blue: #8B1956 !important;
        --jira-blue-dark: #6F123F !important;
        --jira-dark: #161B22 !important;
        --jira-gray: #626F86 !important;
        --jira-light: #F7F8FA !important;
        --jira-border: #DFE1E6 !important;
        --white: #FFFFFF;
    }

    .page-wrapper {
        background: var(--jira-light);
        min-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }

    /* Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 32px;
        background: var(--white);
        border-bottom: 1px solid var(--jira-border);
        font-size: 13px;
        margin: 0;
    }

    .breadcrumb-link {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--jira-blue);
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-link:hover {
        color: var(--jira-blue-dark);
    }

    .breadcrumb-separator {
        color: var(--jira-gray);
    }

    .breadcrumb-current {
        color: var(--jira-dark);
        font-weight: 600;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 32px;
        background: var(--white);
        border-bottom: 1px solid var(--jira-border);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-icon-badge {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--jira-blue), var(--jira-blue-dark));
        color: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.2);
    }

    .page-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
    }

    .page-subtitle {
        font-size: 13px;
        color: var(--jira-gray);
        margin: 2px 0 0 0;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 4px;
        color: var(--jira-dark) !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        cursor: pointer;
    }

    .action-button:hover {
        background: #F4F5F7;
        border-color: #B6C2CF;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .action-button.primary {
        background: var(--jira-blue) !important;
        color: var(--white) !important;
        border: none;
    }

    .action-button.primary span {
        color: var(--white) !important;
        opacity: 1 !important;
    }

    .action-button.primary:hover {
        background: var(--jira-blue-dark) !important;
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
    }

    /* Page Content */
    .page-content {
        display: flex;
        gap: 20px;
        padding: 20px 32px;
        flex: 1;
    }

    .content-left {
        flex: 1;
        min-width: 0;
    }

    .content-right {
        width: 280px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Cards */
    .enterprise-card {
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
    }

    .card-header-bar {
        padding: 14px 20px;
        border-bottom: 1px solid var(--jira-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--jira-dark);
        margin: 0;
    }

    .badge-count {
        font-size: 11px;
        font-weight: 600;
        background: #F4F5F7;
        color: var(--jira-gray);
        padding: 2px 8px;
        border-radius: 12px;
    }

    /* Table */
    .table-container {
        overflow-x: auto;
    }

    .enterprise-table {
        width: 100%;
        border-collapse: collapse;
    }

    .enterprise-table th {
        background: #F9FAFB;
        padding: 10px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--jira-border);
    }

    .enterprise-table td {
        padding: 12px 20px;
        border-bottom: 1px solid var(--jira-border);
        font-size: 13px;
        color: var(--jira-dark);
    }

    .table-row-hover:hover {
        background: #F9FAFB;
    }

    .name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .category-avatar-small {
        width: 32px;
        height: 32px;
        background: #E9F2FF;
        color: var(--jira-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .category-name-text {
        font-weight: 600;
        color: var(--jira-dark);
    }

    .text-description {
        color: var(--jira-gray);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .stat-badge.blue {
        background: #E9F2FF;
        color: #0052CC;
    }

    .action-group-icons {
        display: flex;
        justify-content: flex-end;
        gap: 4px;
    }

    .icon-action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid transparent;
        border-radius: 4px;
        background: transparent;
        color: var(--jira-gray);
        font-size: 16px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .icon-action-btn:hover {
        background: #F4F5F7;
        color: var(--jira-dark);
        border-color: var(--jira-border);
    }

    .icon-action-btn.edit:hover {
        color: #0052CC;
    }

    .icon-action-btn.delete:hover {
        color: #DE350B;
    }

    /* Sidebar Cards */
    .sidebar-card {
        background: var(--white);
        border: 1px solid var(--jira-border);
        border-radius: 8px;
        padding: 16px;
    }

    .sidebar-card-title {
        font-size: 11px;
        font-weight: 700;
        color: var(--jira-gray);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 12px 0;
    }

    .sidebar-stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
    }

    .sidebar-stat-label {
        font-size: 13px;
        color: var(--jira-gray);
    }

    .sidebar-stat-value {
        font-size: 16px;
        font-weight: 700;
        color: var(--jira-dark);
    }

    .sidebar-links {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sidebar-link-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--jira-dark);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        padding: 6px 10px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .sidebar-link-item:hover {
        background: var(--jira-light);
        color: var(--jira-blue);
    }

    .sidebar-link-item i {
        color: var(--jira-gray);
    }

    /* Empty State */
    .empty-state-row {
        padding: 40px 0 !important;
    }

    .empty-state-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        color: #A5ADBA;
    }

    .empty-state-content i {
        font-size: 32px;
    }

    .empty-state-content p {
        font-size: 14px;
        margin: 0;
    }

    /* Modal Styling */
    #categoryModal.modal,
    #categoryModal .modal-dialog {
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        margin-top: 5px !important;
    }

    .enterprise-modal {
        background-color: #ffffff !important;
        background: #ffffff !important;
        border-radius: 12px;
        border: none !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
        overflow: hidden;
    }

    .enterprise-modal .modal-header {
        padding: 24px 32px 16px;
        background: var(--white);
        border-radius: 12px 12px 0 0;
        border-bottom: 1px solid var(--jira-border);
    }

    .enterprise-modal .modal-title {
        font-weight: 700;
        font-size: 18px;
        color: var(--jira-dark);
    }

    .enterprise-modal .modal-footer {
        padding: 16px 32px 24px;
        border-top: 1px solid var(--jira-border);
    }

    .form-label-compact {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--jira-gray);
        margin-bottom: 6px;
    }

    .form-control-compact {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #F4F5F7;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.2s;
        background: #F9FAFB;
    }

    .form-control-compact:focus {
        outline: none;
        border-color: var(--jira-blue);
        background: var(--white);
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
    }

    .btn-enterprise-primary {
        background: var(--jira-blue);
        color: var(--white);
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-enterprise-primary:hover {
        background: var(--jira-blue-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(139, 25, 86, 0.3);
    }

    .btn-enterprise-secondary {
        background: white;
        color: var(--jira-dark);
        border: 1px solid var(--jira-border);
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-enterprise-secondary:hover {
        background: #F4F5F7;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .page-content {
            flex-direction: column;
        }

        .content-right {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 576px) {
        .content-right {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }

        .header-actions {
            width: 100%;
        }

        .action-button {
            flex: 1;
            justify-content: center;
        }
    }
</style>

<script>
    function editCategory(category) {
        document.getElementById('categoryModalLabel').textContent = 'Edit Project Category';
        document.getElementById('categoryForm').action = '<?= url('/admin/project-categories') ?>/' + category.id;
        document.getElementById('categoryId').value = category.id;
        document.getElementById('categoryMethod').value = 'PUT';
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categoryDescription').value = category.description || '';
    }

    // Reset form when modal is closed
    document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('categoryModalLabel').textContent = 'Add Project Category';
        document.getElementById('categoryForm').action = '<?= url('/admin/project-categories') ?>';
        document.getElementById('categoryId').value = '';
        document.getElementById('categoryMethod').value = 'POST';
        document.getElementById('categoryForm').reset();
    });
</script>

<?php \App\Core\View::endsection(); ?>