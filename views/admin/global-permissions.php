<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Administration</a></li>
                    <li class="breadcrumb-item active">Global Permissions</li>
                </ol>
            </nav>
            <h2 class="mb-0">Global Permissions</h2>
        </div>
    </div>

    <form method="POST" action="<?= url('/admin/global-permissions') ?>">
        <?= csrf_token() ?>
        <input type="hidden" name="_method" value="PUT">

        <?php foreach ($grouped as $category => $categoryPermissions): ?>
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light border-0 py-3">
                    <h5 class="mb-0 text-dark">
                        <i class="bi bi-shield-lock me-2"></i>
                        <?= htmlspecialchars($category ?? 'Other') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40%">Permission</th>
                                    <th width="60%">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categoryPermissions as $permission): ?>
                                    <tr>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">
                                                <?= htmlspecialchars($permission['name'] ?? '') ?>
                                            </code>
                                        </td>
                                        <td>
                                            <textarea 
                                                name="permissions[<?= $permission['id'] ?>][description]"
                                                class="form-control form-control-sm"
                                                rows="2"
                                                placeholder="Permission description"
                                            ><?= htmlspecialchars($permission['description'] ?? '') ?></textarea>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-2"></i>
                Save Global Permissions
            </button>
            <a href="<?= url('/admin') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Admin
            </a>
        </div>
        </form>
        </div>

        <style>
        .table-hover tbody tr:hover {
            background-color: rgba(0, 82, 204, 0.05);
        }

        code {
            color: #0052cc;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .card {
            border-radius: 8px;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        textarea.form-control-sm {
            resize: vertical;
            font-size: 0.875rem;
            min-height: 50px;
        }
        </style>

        <?php \App\Core\View::endsection(); ?>
