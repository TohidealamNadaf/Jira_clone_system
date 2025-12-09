<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <a href="<?= url('/reports') ?>" style="color: #0052CC; text-decoration: none; font-size: 14px; font-weight: 600;">Reports</a>
        <span style="color: #626F86; font-size: 14px;">/</span>
        <span style="color: #161B22; font-size: 14px; font-weight: 600;">Version Progress</span>
    </div>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin-bottom: 8px;">Version Progress</h1>
        <p style="font-size: 15px; color: #626F86; margin: 0;">Track progress toward software releases</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $version): ?>
            <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 20px; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
                <h6 style="font-size: 14px; font-weight: 600; color: #161B22; margin-bottom: 12px;">
                    <?= e($version['name'] ?? 'Unknown') ?>
                </h6>
                
                <div style="margin-bottom: 12px;">
                    <p style="font-size: 12px; color: #626F86; margin: 0;">
                        📁 <?= e($version['project_name'] ?? '') ?>
                    </p>
                </div>

                <?php if (!empty($version['release_date'])): ?>
                <div style="margin-bottom: 12px;">
                    <p style="font-size: 12px; color: #626F86; margin: 0;">
                        📅 <?= date('M d, Y', strtotime($version['release_date'])) ?>
                    </p>
                </div>
                <?php endif; ?>

                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                        <span style="color: #626F86;">Total Issues</span>
                        <strong style="color: #161B22;"><?= e($version['total_issues'] ?? 0) ?></strong>
                    </div>
                    <div style="height: 8px; background-color: #EBECF0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: 100%; background-color: #0052CC;"></div>
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px;">
                        <span style="color: #626F86;">Completed</span>
                        <strong style="color: #161B22;"><?= e($version['completed_issues'] ?? 0) ?></strong>
                    </div>
                    <?php 
                    $percentage = ($version['total_issues'] ?? 0) > 0 
                        ? round((($version['completed_issues'] ?? 0) / ($version['total_issues'] ?? 1)) * 100) 
                        : 0;
                    ?>
                    <div style="height: 8px; background-color: #EBECF0; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: <?= $percentage ?>%; background-color: #22c55e;"></div>
                    </div>
                </div>

                <div style="border-top: 1px solid #EBECF0; padding-top: 12px;">
                    <p style="font-size: 12px; color: #626F86; margin: 0;">
                        Progress: <strong style="color: #161B22;"><?= $percentage ?>%</strong>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; color: #626F86; padding: 40px 20px;">
                <p style="font-size: 48px; margin: 0 0 16px 0;">📭</p>
                <p style="margin: 0;">No versions available</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
