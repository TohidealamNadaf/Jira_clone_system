<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="<?= url('/') ?>">
            <i class="bi bi-kanban me-2"></i>
            <?= e(config('app.name')) ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-folder me-1"></i> Projects
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('/projects') ?>">View All Projects</a></li>
                        <?php if (can('create-projects')): ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="<?= url('/projects/create') ?>">Create Project</a></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-list-task me-1"></i> Issues
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('/search?assignee=currentUser()') ?>">Assigned to
                                Me</a></li>
                        <li><a class="dropdown-item" href="<?= url('/search?reporter=currentUser()') ?>">Reported by
                                Me</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= url('/search') ?>">Search Issues</a></li>
                        <li><a class="dropdown-item" href="<?= url('/filters') ?>">Saved Filters</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bar-chart me-1"></i> Reports
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= url('/reports') ?>">All Reports</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= url('/reports?type=burndown') ?>">Burndown Chart</a></li>
                        <li><a class="dropdown-item" href="<?= url('/reports?type=velocity') ?>">Velocity Chart</a></li>
                    </ul>
                </li>

                <?php if ($user['is_admin'] ?? false): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/admin') ?>">
                            <i class="bi bi-gear me-1"></i> Admin
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Search -->
            <form class="d-flex me-3" action="<?= url('/search') ?>" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="q" placeholder="Search issues..."
                        value="<?= e($_GET['q'] ?? '') ?>" style="min-width: 200px;">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- Quick Create -->
            <a href="#" class="btn btn-light me-3" data-bs-toggle="modal" data-bs-target="#quickCreateModal">
                <i class="bi bi-plus-lg"></i> Create
            </a>

            <!-- Notifications -->
            <?php include __DIR__ . '/notifications-dropdown.php'; ?>

            <!-- User Menu -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#"
                    data-bs-toggle="dropdown">
                    <?php if ($user['avatar'] ?? null): ?>
                        <img src="<?= e($user['avatar']) ?>" class="rounded-circle me-2" width="32" height="32" alt="">
                    <?php else: ?>
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2"
                            style="width: 32px; height: 32px;">
                            <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <span><?= e($user['display_name'] ?? 'User') ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= url('/profile') ?>"><i class="bi bi-person me-2"></i>
                            Profile</a></li>
                    <li><a class="dropdown-item" href="<?= url('/profile/tokens') ?>"><i class="bi bi-key me-2"></i> API
                            Tokens</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="<?= url('/logout') ?>" method="POST">
                            <?= csrf_field() ?>
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>