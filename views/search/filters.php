<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-xl py-4">
    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-6">
        <div>
            <h1 class="h3 fw-600 mb-1" style="color: #161B22;">Filters</h1>
            <p class="text-muted mb-0" style="font-size: 13px;">Create and manage filters to quickly find issues</p>
        </div>
        <a href="<?= url('/search') ?>" class="btn btn-primary" style="font-weight: 500; padding: 8px 16px;">
            <i class="bi bi-plus-lg me-2"></i>Create filter
        </a>
    </div>

    <div class="row">
        <!-- My Filters Section -->
        <div class="col-lg-8">
            <div class="mb-6">
                <div class="d-flex align-items-center mb-3">
                    <h3 class="h5 mb-0 fw-600" style="color: #161B22;">My filters</h3>
                    <span class="badge bg-light text-dark ms-2" style="font-weight: 500; font-size: 12px;">
                        <?= count($myFilters ?? []) ?>
                    </span>
                </div>

                <?php if (!empty($myFilters)): ?>
                <div class="space-y-2">
                    <?php foreach ($myFilters as $filter): ?>
                    <div class="card border mb-2 filter-card transition-card" style="border-color: #DFE1E6; background: #FFFFFF; cursor: pointer;">
                        <div class="card-body p-4 d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="d-flex align-items-center mb-2">
                                    <a href="<?= url('/search?' . $filter['jql']) ?>" class="text-decoration-none fw-600" style="color: #0052CC; font-size: 15px;">
                                        <?= e($filter['name']) ?>
                                    </a>
                                    <?php if (!empty($filter['share_type']) && $filter['share_type'] !== 'private'): ?>
                                    <span class="badge ms-2" style="background-color: #F1F2F4; color: #626F86; font-size: 11px; font-weight: 500;">
                                        <i class="bi bi-share me-1"></i><?= ucfirst(e($filter['share_type'])) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted small mb-2" style="color: #626F86; font-size: 12px;">
                                    JQL: <code class="text-dark" style="background-color: #F1F2F4; padding: 2px 6px; border-radius: 3px; font-size: 11px;">
                                        <?= e(substr($filter['jql'], 0, 60)) ?><?= strlen($filter['jql']) > 60 ? '...' : '' ?>
                                    </code>
                                </p>
                                <p class="text-muted small mb-0" style="color: #738496; font-size: 12px;">
                                    Updated <?= time_ago($filter['updated_at']) ?>
                                </p>
                            </div>
                            <div class="ms-3 d-flex gap-1" style="flex-shrink: 0;">
                                <a href="<?= url('/search?' . $filter['jql']) ?>" class="btn btn-sm btn-light" style="color: #626F86; border-color: #DFE1E6;" title="Use filter">
                                    <i class="bi bi-play"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-light" style="color: #626F86; border-color: #DFE1E6;" data-bs-toggle="modal" data-bs-target="#editFilterModal-<?= $filter['id'] ?>" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="<?= url('/filters/' . $filter['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this filter?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-light" style="color: #DE350B; border-color: #DFE1E6;" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Filter Modal -->
                    <div class="modal fade" id="editFilterModal-<?= $filter['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0" style="border-radius: 8px;">
                                <div class="modal-header border-bottom" style="border-color: #DFE1E6;">
                                    <h6 class="modal-title fw-600" style="color: #161B22;">Edit filter</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="<?= url('/filters/' . $filter['id']) ?>">
                                    <?= csrf_field() ?>
                                    <div class="modal-body">
                                        <div class="mb-4">
                                            <label class="form-label fw-500 mb-2" style="color: #161B22; font-size: 13px;">Filter name</label>
                                            <input type="text" name="name" class="form-control" style="border-color: #DFE1E6;" value="<?= e($filter['name']) ?>" required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-500 mb-2" style="color: #161B22; font-size: 13px;">JQL query</label>
                                            <textarea name="jql" class="form-control font-monospace" style="border-color: #DFE1E6; font-size: 12px;" rows="5" required><?= e($filter['jql']) ?></textarea>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-500 mb-2" style="color: #161B22; font-size: 13px;">Share</label>
                                            <select name="share_type" class="form-select" style="border-color: #DFE1E6;">
                                                <option value="private" <?= ($filter['share_type'] ?? 'private') === 'private' ? 'selected' : '' ?>>Private</option>
                                                <option value="project" <?= ($filter['share_type'] ?? 'private') === 'project' ? 'selected' : '' ?>>Project</option>
                                                <option value="global" <?= ($filter['share_type'] ?? 'private') === 'global' ? 'selected' : '' ?>>Global</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top" style="border-color: #DFE1E6;">
                                        <button type="button" class="btn btn-light" style="color: #626F86; border-color: #DFE1E6;" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="card border" style="border-color: #EBECF0; background: #FAFBFC;">
                    <div class="card-body py-6 text-center">
                        <div class="mb-3">
                            <i class="bi bi-funnel fs-3 text-muted d-block mb-3" style="color: #738496; opacity: 0.7;"></i>
                        </div>
                        <h6 class="fw-600 mb-2" style="color: #161B22;">No filters yet</h6>
                        <p class="text-muted small mb-3" style="color: #626F86; font-size: 13px;">Save a filter to quickly search for issues with specific criteria</p>
                        <a href="<?= url('/search') ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Create your first filter
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Shared Filters Section -->
            <div class="mb-6">
                <div class="d-flex align-items-center mb-3">
                    <h3 class="h5 mb-0 fw-600" style="color: #161B22;">Shared with you</h3>
                    <span class="badge bg-light text-dark ms-2" style="font-weight: 500; font-size: 12px;">
                        <?= count($sharedFilters ?? []) ?>
                    </span>
                </div>

                <?php if (!empty($sharedFilters)): ?>
                <div class="space-y-2">
                    <?php foreach ($sharedFilters as $filter): ?>
                    <div class="card border mb-2 filter-card transition-card" style="border-color: #DFE1E6; background: #FFFFFF;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="d-flex align-items-center mb-2">
                                        <a href="<?= url('/search?' . $filter['jql']) ?>" class="text-decoration-none fw-600" style="color: #0052CC; font-size: 15px;">
                                            <?= e($filter['name']) ?>
                                        </a>
                                        <span class="badge ms-2" style="background-color: #F1F2F4; color: #626F86; font-size: 11px; font-weight: 500;">
                                            <i class="bi bi-share me-1"></i><?= ucfirst(e($filter['share_type'] ?? 'unknown')) ?>
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2" style="color: #626F86; font-size: 12px;">
                                        <i class="bi bi-person me-1"></i>
                                        Shared by <strong><?= e($filter['owner_name'] ?? 'Unknown') ?></strong>
                                    </p>
                                    <p class="text-muted small mb-0" style="color: #738496; font-size: 12px;">
                                        Updated <?= time_ago($filter['updated_at']) ?>
                                    </p>
                                </div>
                                <a href="<?= url('/search?' . $filter['jql']) ?>" class="btn btn-sm btn-light ms-3" style="color: #626F86; border-color: #DFE1E6; flex-shrink: 0;" title="Use filter">
                                    <i class="bi bi-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="card border" style="border-color: #EBECF0; background: #FAFBFC;">
                    <div class="card-body py-6 text-center">
                        <div class="mb-3">
                            <i class="bi bi-share fs-3 text-muted d-block mb-3" style="color: #738496; opacity: 0.7;"></i>
                        </div>
                        <h6 class="fw-600 mb-2" style="color: #161B22;">No shared filters</h6>
                        <p class="text-muted small mb-0" style="color: #626F86; font-size: 13px;">Team members haven't shared any filters with you yet</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Filter Statistics -->
            <div class="card border mb-4" style="border-color: #DFE1E6; background: #FAFBFC;">
                <div class="card-body p-4">
                    <h6 class="fw-600 mb-3" style="color: #161B22; font-size: 13px;">Filter statistics</h6>
                    <div class="space-y-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="color: #626F86; font-size: 13px;">Total filters</span>
                            <span class="fw-600" style="color: #161B22; font-size: 14px;"><?= count($myFilters ?? []) + count($sharedFilters ?? []) ?></span>
                        </div>
                        <div style="border-color: #DFE1E6; border-bottom: 1px solid #DFE1E6;"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="color: #626F86; font-size: 13px;">Personal filters</span>
                            <span class="fw-600" style="color: #161B22; font-size: 14px;"><?= count($myFilters ?? []) ?></span>
                        </div>
                        <div style="border-color: #DFE1E6; border-bottom: 1px solid #DFE1E6;"></div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="color: #626F86; font-size: 13px;">Shared filters</span>
                            <span class="fw-600" style="color: #161B22; font-size: 14px;"><?= count($sharedFilters ?? []) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card border" style="border-color: #DFE1E6; background: #FFFAEB;">
                <div class="card-body p-4">
                    <h6 class="fw-600 mb-2" style="color: #161B22; font-size: 13px;">
                        <i class="bi bi-lightbulb me-2" style="color: #974F0C;"></i>Pro tip
                    </h6>
                    <p class="text-muted small mb-0" style="color: #626F86; font-size: 12px;">
                        Use JQL (Jira Query Language) to create powerful filters. Learn more about advanced search syntax.
                    </p>
                    <a href="<?= url('/search/advanced') ?>" class="btn btn-sm btn-link mt-2 p-0" style="font-size: 12px;">
                        Advanced search →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .space-y-2 > * + * {
        margin-top: 0.5rem;
    }

    .space-y-3 > * + * {
        margin-top: 0.75rem;
    }

    .transition-card {
        transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .transition-card:hover {
        border-color: #B6C2CF !important;
        box-shadow: 0 1px 3px rgba(9, 30, 66, 0.13);
        transform: translateY(-2px);
    }

    .mb-6 {
        margin-bottom: 2rem;
    }

    code {
        font-family: 'Monaco', 'Courier New', monospace;
    }
</style>

<?php \App\Core\View::endSection(); ?>
