<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid px-5 py-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="background-color: transparent; padding: 0; gap: 8px;">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>" style="color: var(--jira-blue); text-decoration: none;">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('/projects') ?>" style="color: var(--jira-blue); text-decoration: none;">Projects</a></li>
            <li class="breadcrumb-item"><a href="<?= url("/projects/{$project['key']}") ?>" style="color: var(--jira-blue); text-decoration: none;"><?= e($project['name']) ?></a></li>
            <li class="breadcrumb-item active" style="color: #626F86;">Sprints</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4" style="gap: 24px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; color: #161B22; margin: 0 0 4px 0; letter-spacing: -0.2px;">Sprints</h1>
            <p style="font-size: 15px; color: #626F86; margin: 0;">View and manage project sprints</p>
        </div>
        <a href="<?= url("/projects/{$project['key']}") ?>" 
           style="background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; padding: 8px 16px; border-radius: 4px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s;">
            <i class="bi bi-arrow-left"></i> Back to Project
        </a>
    </div>

    <!-- Sprints List -->
    <?php if (empty($sprints)): ?>
    <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; padding: 60px 20px; text-align: center; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13);">
        <div style="font-size: 48px; margin-bottom: 16px;">⚡</div>
        <p style="font-size: 15px; color: #626F86; margin: 0;">No sprints have been created yet.</p>
    </div>
    <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 20px;">
        <?php foreach ($sprints as $sprint): ?>
        <div style="background: white; border: 1px solid #DFE1E6; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13); transition: all 0.2s; cursor: pointer;"
             onmouseover="this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.12)'; this.style.transform='translateY(-2px)'"
             onmouseout="this.style.boxShadow='0 1px 1px rgba(9, 30, 66, 0.13), 0 0 1px rgba(9, 30, 66, 0.13)'; this.style.transform='translateY(0)'">
            
            <!-- Header -->
            <div style="padding: 16px 20px; background-color: #F7F8FA; border-bottom: 1px solid #DFE1E6;">
                <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 600; color: #161B22; margin: 0; margin-bottom: 4px;">
                            <?= e($sprint['name']) ?>
                        </h3>
                    </div>
                    <span style="background-color: 
                        <?php 
                            switch($sprint['status']) {
                                case 'planning': echo '#DEEAFE; color: var(--jira-blue);'; break;
                                case 'active': echo '#DFFCF0; color: #216E4E;'; break;
                                case 'completed': echo '#F3F0FF; color: #352C63;'; break;
                                default: echo '#F1F2F4; color: #626F86;';
                            }
                        ?>; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px;">
                        <?= ucfirst(str_replace('_', ' ', $sprint['status'])) ?>
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div style="padding: 20px;">
                <!-- Status Detail -->
                <div style="margin-bottom: 16px;">
                    <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Status</p>
                    <p style="font-size: 14px; color: #161B22; margin: 0;">
                        <?= ucfirst(str_replace('_', ' ', $sprint['status'])) ?>
                    </p>
                </div>

                <!-- Start Date -->
                <?php if ($sprint['start_date']): ?>
                <div style="margin-bottom: 16px;">
                    <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Start Date</p>
                    <p style="font-size: 14px; color: #161B22; margin: 0; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-calendar3" style="color: var(--jira-blue);"></i>
                        <?= date('M j, Y', strtotime($sprint['start_date'])) ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- End Date -->
                <?php if ($sprint['end_date']): ?>
                <div style="margin-bottom: 16px;">
                    <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">End Date</p>
                    <p style="font-size: 14px; color: #161B22; margin: 0; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-calendar3-range" style="color: var(--jira-blue);"></i>
                        <?= date('M j, Y', strtotime($sprint['end_date'])) ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Goal -->
                <?php if ($sprint['goal']): ?>
                <div style="margin-bottom: 16px;">
                    <p style="font-size: 12px; font-weight: 600; color: #626F86; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Goal</p>
                    <p style="font-size: 14px; color: #161B22; margin: 0; line-height: 1.5;">
                        <?= e($sprint['goal']) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <div style="padding: 12px 20px; background-color: #F7F8FA; border-top: 1px solid #DFE1E6; display: flex; gap: 8px;">
                <a href="<?= url("/projects/{$project['key']}/sprints/{$sprint['id']}/board") ?>" 
                   style="flex: 1; background-color: var(--jira-blue); color: white; border: none; padding: 8px 12px; border-radius: 4px; font-weight: 500; font-size: 13px; cursor: pointer; display: block; text-align: center; text-decoration: none; transition: all 0.2s;"
                   onmouseover="this.style.opacity='0.9'"
                   onmouseout="this.style.opacity='1'">
                    <i class="bi bi-kanban"></i> View Board
                </a>
                <a href="<?= url("/projects/{$project['key']}/sprints/{$sprint['id']}") ?>" 
                   style="flex: 1; background-color: transparent; color: var(--jira-blue); border: 1px solid #DFE1E6; padding: 8px 12px; border-radius: 4px; font-weight: 500; font-size: 13px; cursor: pointer; display: block; text-align: center; text-decoration: none; transition: all 0.2s;"
                   onmouseover="this.style.backgroundColor='#DEEAFE'"
                   onmouseout="this.style.backgroundColor='transparent'">
                    <i class="bi bi-gear"></i> Details
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
    .breadcrumb-item + .breadcrumb-item::before {
        content: "/";
        color: #626F86;
        margin: 0 8px;
    }
</style>

<?php \App\Core\View::endSection(); ?>
