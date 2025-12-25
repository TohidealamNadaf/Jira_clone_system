<aside class="sidebar bg-dark text-white" id="sidebar">
    <div class="sidebar-header p-3 border-bottom border-secondary">
        <a href="<?= url('/') ?>" class="text-white text-decoration-none d-flex align-items-center">
            <i class="bi bi-kanban fs-4 me-2"></i>
            <span class="fs-5 fw-semibold"><?= e(config('app.name')) ?></span>
        </a>
    </div>

    <nav class="sidebar-nav p-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?= ($currentRoute ?? '') === 'dashboard' ? 'active bg-primary rounded' : '' ?>"
                    href="<?= url('/dashboard') ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item mt-3">
                <small class="text-muted text-uppercase px-3">Projects</small>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?= ($currentRoute ?? '') === 'projects' ? 'active bg-primary rounded' : '' ?>"
                    href="<?= url('/projects') ?>">
                    <i class="bi bi-folder me-2"></i> All Projects
                </a>
            </li>
            <?php if (can('create-projects')): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= url('/projects/create') ?>">
                        <i class="bi bi-plus-circle me-2"></i> Create Project
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item mt-3">
                <small class="text-muted text-uppercase px-3">Issues</small>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= url('/search?assignee=currentUser()') ?>">
                    <i class="bi bi-person-check me-2"></i> Assigned to Me
                    <?php if (($stats['assigned_count'] ?? 0) > 0): ?>
                        <span class="badge bg-primary ms-auto"><?= e($stats['assigned_count']) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="<?= url('/search?reporter=currentUser()') ?>">
                    <i class="bi bi-file-earmark-text me-2"></i> Reported by Me
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?= ($currentRoute ?? '') === 'search' ? 'active bg-primary rounded' : '' ?>"
                    href="<?= url('/search') ?>">
                    <i class="bi bi-search me-2"></i> Search Issues
                </a>
            </li>

            <li class="nav-item mt-3">
                <small class="text-muted text-uppercase px-3">Saved Filters</small>
            </li>
            <?php if (!empty($savedFilters)): ?>
                <?php foreach (array_slice($savedFilters, 0, 5) as $filter): ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="<?= url('/filters/' . $filter['id']) ?>">
                            <i class="bi bi-funnel me-2"></i> <?= e($filter['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link text-white-50" href="<?= url('/filters') ?>">
                    <i class="bi bi-plus me-2"></i> Manage Filters
                </a>
            </li>

            <?php if ($user['is_admin'] ?? false): ?>
                <li class="nav-item mt-3">
                    <small class="text-muted text-uppercase px-3">Administration</small>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?= ($currentRoute ?? '') === 'admin' ? 'active bg-primary rounded' : '' ?>"
                        href="<?= url('/admin') ?>">
                        <i class="bi bi-gear me-2"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= url('/admin/users') ?>">
                        <i class="bi bi-people me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= url('/admin/roles') ?>">
                        <i class="bi bi-shield-check me-2"></i> Roles & Permissions
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="sidebar-footer mt-auto p-3 border-top border-secondary">
        <div class="d-flex align-items-center">
            <?php if ($user['avatar'] ?? null): ?>
                <img src="<?= e($user['avatar']) ?>" class="rounded-circle me-2" width="36" height="36" alt="">
            <?php else: ?>
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2"
                    style="width: 36px; height: 36px;">
                    <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                </div>
            <?php endif; ?>
            <div class="flex-grow-1">
                <div class="small fw-medium"><?= e($user['display_name'] ?? 'User') ?></div>
                <div class="small text-muted"><?= e($user['email'] ?? '') ?></div>
            </div>
            <form action="<?= url('/logout') ?>" method="POST" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<style>
    .sidebar {
        width: 260px;
        min-height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: column;
        z-index: 1000;
    }

    .sidebar .nav-link {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 0.375rem;
    }

    .sidebar .nav-link.active {
        font-weight: 500;
    }

    .main-content-with-sidebar {
        margin-left: 260px;
    }

    @media (max-width: 991.98px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .main-content-with-sidebar {
            margin-left: 0;
        }
    }
</style>