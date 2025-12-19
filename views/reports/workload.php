<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <a href="<?= url('/reports') ?>" style="color: var(--jira-blue); text-decoration: none; font-size: 14px; font-weight: 600;">Reports</a>
        <span style="color: #626F86; font-size: 14px;">/</span>
        <span style="color: #161B22; font-size: 14px; font-weight: 600;">Team Workload</span>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">Team Workload</h1>
        <p style="font-size: 15px; color: #626F86; margin: 0;">Current workload distribution across team members</p>
    </div>

    <!-- Filters Section -->
    <div style="display: flex; gap: 24px; margin-bottom: 32px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                Project
            </label>
            <select class="form-select" id="projectFilter" style="width: 240px; height: 40px; border-radius: 4px; border: 1px solid #DFE1E6; font-size: 14px; color: #161B22; padding: 8px 12px; background-color: white; cursor: pointer;">
                <option value="">All Projects</option>
                <?php foreach ($projects ?? [] as $proj): ?>
                <option value="<?= $proj['id'] ?>" <?= ($selectedProject ?? 0) == $proj['id'] ? 'selected' : '' ?>>
                    <?= e($proj['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 32px;">
        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
            <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                <span style="color: #3b82f6; margin-right: 8px;">●</span>Average Load
            </p>
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                <h2 style="font-size: 36px; font-weight: 700; color: #161B22; margin: 0;">
                    <?= e(round($averageLoad ?? 0, 1)) ?>
                </h2>
                <span style="font-size: 14px; color: #626F86;">points</span>
            </div>
            <p style="font-size: 12px; color: #626F86; margin: 0;">Story points per person</p>
        </div>

        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
            <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                <span style="color: #8b5cf6; margin-right: 8px;">●</span>Unassigned Issues
            </p>
            <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                <h2 style="font-size: 36px; font-weight: 700; color: #161B22; margin: 0;">
                    <?= e($unassigned['issue_count'] ?? 0) ?>
                </h2>
                <span style="font-size: 14px; color: #626F86;">issues</span>
            </div>
            <p style="font-size: 12px; color: #626F86; margin: 0;">Pending assignment</p>
        </div>
    </div>

    <!-- Team Workload Table -->
    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <div style="padding: 24px;">
            <h6 style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">Team Members</h6>
            
            <?php if (!empty($workload)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size: 12px; font-weight: 600;">Team Member</th>
                            <th style="font-size: 12px; font-weight: 600; text-align: center;">Issues</th>
                            <th style="font-size: 12px; font-weight: 600; text-align: center;">Story Points</th>
                            <th style="font-size: 12px; font-weight: 600; text-align: center;">High Priority</th>
                            <th style="font-size: 12px; font-weight: 600; text-align: center;">Remaining Estimate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workload as $member): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($member['avatar'])): ?>
                                    <img src="<?= e($member['avatar']) ?>" alt="<?= e($member['display_name']) ?>" 
                                         class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="rounded-circle bg-secondary me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px;">
                                        <?= e(substr($member['display_name'], 0, 1)) ?>
                                    </div>
                                    <?php endif; ?>
                                    <span><?= e($member['display_name']) ?></span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: var(--jira-blue-lighter); color: var(--jira-blue);">
                                    <?= e($member['issue_count']) ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <strong><?= e($member['total_points']) ?></strong>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-warning text-dark">
                                    <?= e($member['high_priority_count']) ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <small class="text-muted"><?= e($member['remaining_estimate']) ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div style="text-align: center; color: #626F86; padding: 40px 20px;">
                    <p style="font-size: 48px; margin: 0 0 16px 0;">📭</p>
                    <p style="margin: 0;">No workload data available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
document.getElementById('projectFilter').addEventListener('change', function() {
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
