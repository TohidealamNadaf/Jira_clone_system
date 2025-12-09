<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <?php if ($user['avatar'] ?? null): ?>
                        <img src="<?= e($user['avatar']) ?>" class="rounded-circle" width="120" height="120" alt="Avatar">
                        <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px; font-size: 3rem;">
                            <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h4 class="mb-1"><?= e($user['display_name'] ?? $user['first_name'] . ' ' . $user['last_name']) ?></h4>
                    <p class="text-muted mb-0"><?= e($user['email']) ?></p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('/profile') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                    <a href="<?= url('/profile/tokens') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-key me-2"></i> API Tokens
                    </a>
                    <a href="<?= url('/profile/notifications') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-bell me-2"></i> Notifications
                    </a>
                    <a href="<?= url('/profile/security') ?>" class="list-group-item list-group-item-action active">
                        <i class="bi bi-shield-lock me-2"></i> Security
                    </a>
                </div>
            </div>
        </div>

        <!-- Security Content -->
        <div class="col-lg-9">
            <!-- Change Password -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/profile/password') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required minlength="8">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Active Sessions</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        You are currently logged in from this device.
                        <br>
                        <small class="text-muted">Last login: <?= $user['last_login_at'] ? format_datetime($user['last_login_at']) : 'Unknown' ?></small>
                    </div>
                </div>
            </div>

            <!-- Recent Login Activity -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Recent Login Activity</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($loginActivity)): ?>
                    <p class="text-muted mb-0">No recent login activity recorded.</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($loginActivity as $activity): ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-box-arrow-in-right text-success me-2"></i>
                                        <?= e(ucwords(str_replace('_', ' ', $activity['action']))) ?>
                                    </td>
                                    <td><?= e($activity['ip_address'] ?? 'Unknown') ?></td>
                                    <td><?= format_datetime($activity['created_at']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
