<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Notifications</h2>
            <p class="text-muted mb-0">
                <?= $unreadCount ?> unread notification<?= $unreadCount !== 1 ? 's' : '' ?>
            </p>
        </div>
        <?php if ($unreadCount > 0): ?>
        <form action="<?= url('/notifications/mark-read') ?>" method="POST" class="d-inline">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-check-all me-1"></i> Mark All as Read
            </button>
        </form>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted mb-3 d-block"></i>
                <h5 class="text-muted">No notifications</h5>
                <p class="text-muted mb-0">You're all caught up!</p>
            </div>
            <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notification): ?>
                <?php $isUnread = empty($notification['read_at']); ?>
                <div class="list-group-item d-flex align-items-start py-3 <?= $isUnread ? 'bg-light' : '' ?>">
                    <div class="flex-shrink-0 me-3">
                        <?php
                        $iconClass = 'bi-bell';
                        $iconColor = 'text-primary';
                        $type = $notification['type'] ?? '';
                        
                        if (str_contains($type, 'assigned')) {
                            $iconClass = 'bi-person-plus';
                            $iconColor = 'text-success';
                        } elseif (str_contains($type, 'comment')) {
                            $iconClass = 'bi-chat-dots';
                            $iconColor = 'text-info';
                        } elseif (str_contains($type, 'updated') || str_contains($type, 'changed')) {
                            $iconClass = 'bi-pencil-square';
                            $iconColor = 'text-warning';
                        } elseif (str_contains($type, 'mention')) {
                            $iconClass = 'bi-at';
                            $iconColor = 'text-purple';
                        }
                        ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
                             style="width: 40px; height: 40px;">
                            <i class="bi <?= $iconClass ?> <?= $iconColor ?>"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-1 <?= $isUnread ? 'fw-semibold' : '' ?>">
                                    <?= e($notification['data']['message'] ?? 'Notification') ?>
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= time_ago($notification['created_at']) ?>
                                </small>
                            </div>
                            <?php if ($isUnread): ?>
                            <span class="badge bg-primary rounded-pill">New</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if (($pagination['last_page'] ?? 1) > 1): ?>
            <div class="card-footer bg-transparent">
                <nav aria-label="Notifications pagination">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <?php if ($pagination['current_page'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('/notifications?page=' . ($pagination['current_page'] - 1)) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('/notifications?page=' . $i) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= url('/notifications?page=' . ($pagination['current_page'] + 1)) ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>
