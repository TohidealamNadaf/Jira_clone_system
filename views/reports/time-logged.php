<?php \App\Core\View:: extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <a href="<?= url('/reports') ?>"
            style="color: var(--jira-blue); text-decoration: none; font-size: 14px; font-weight: 600;">Reports</a>
        <span style="color: #626F86; font-size: 14px;">/</span>
        <span style="color: #161B22; font-size: 14px; font-weight: 600;">Time Logged</span>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">Time Logged</h1>
        <p style="font-size: 15px; color: #626F86; margin: 0;">Total time logged by team members</p>
    </div>

    <!-- Filters Section -->
    <div style="display: flex; gap: 24px; margin-bottom: 32px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label
                style="display: block; font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                Project
            </label>
            <select class="form-select" id="projectFilter"
                style="width: 240px; height: 40px; border-radius: 4px; border: 1px solid #DFE1E6; font-size: 14px; color: #161B22; padding: 8px 12px; background-color: white; cursor: pointer;">
                <option value="">All Projects</option>
                <?php foreach ($projects ?? [] as $proj): ?>
                    <option value="<?= $proj['id'] ?>" <?= ($selectedProject ?? 0) == $proj['id'] ? 'selected' : '' ?>>
                        <?= e($proj['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div
        style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <div style="padding: 24px;">
            <h6
                style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">
                Time Logged by Team Member</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Worklogs</th>
                        <th>Total Time Spent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)): ?>
                        <?php foreach ($data as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($user['avatar']) && ($avatarUrl = avatar($user['avatar']))): ?>
                                            <img src="<?= e($avatarUrl) ?>" alt="<?= e($user['display_name']) ?>"
                                                class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary me-2" style="width: 32px; height: 32px;"></div>
                                        <?php endif; ?>
                                        <span><?= e($user['display_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= e($user['worklog_count'] ?? 0) ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php
                                        $hours = intval(($user['total_time_spent'] ?? 0) / 3600);
                                        $minutes = intval((($user['total_time_spent'] ?? 0) % 3600) / 60);
                                        echo $hours . 'h ' . $minutes . 'm';
                                        ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: #626F86; padding: 40px 20px;">No time logged
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
    document.getElementById('projectFilter').addEventListener('change', function () {
        const url = new URL(window.location);
        if (this.value) {
            url.searchParams.set('project_id', this.value);
        } else {
            url.searchParams.delete('project_id');
        }
        window.location = url;
    });
</script>
<?php \App\Core\View::endSection(); ?>