<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item active">Project Categories</li>
                </ol>
            </nav>
            <h2 class="mb-0">Project Categories</h2>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="bi bi-plus-lg me-1"></i> Add Category
        </button>
    </div>

    <!-- Categories Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Projects</th>
                        <th>Created</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3"></i>
                                <p class="mt-2">No project categories found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <strong><?= e($category['name']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted"><?= e($category['description'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $category['project_count'] ?? 0 ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= format_date($category['created_at']) ?>
                                    </small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" 
                                            onclick="editCategory(<?= htmlspecialchars(json_encode($category)) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="<?= url('/admin/project-categories/' . $category['id']) ?>" method="POST" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to delete this category?');">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="categoryModalLabel">Add Project Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" method="POST" action="<?= url('/admin/project-categories') ?>">
                <?= csrf_field() ?>
                <input type="hidden" id="categoryId" name="id">
                <input type="hidden" id="categoryMethod" name="_method" value="POST">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name *</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3" maxlength="500"></textarea>
                        <small class="text-muted">Optional description for this category</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('categoryModalLabel').textContent = 'Add Project Category';
    document.getElementById('categoryForm').action = '<?= url('/admin/project-categories') ?>';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryMethod').value = 'POST';
    document.getElementById('categoryForm').reset();
});
</script>

<?php \App\Core\View::endsection(); ?>
