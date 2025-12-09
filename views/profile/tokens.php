<?php \App\Core\View::extends('layouts.app'); ?>

<?php \App\Core\View::section('content'); ?>

<style>
.profile-sidebar {
    background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
}

.profile-sidebar .list-group-item {
    border: none;
    transition: all 0.2s ease;
}

.profile-sidebar .list-group-item:hover:not(.active) {
    background-color: #e9ecef;
}

.profile-sidebar .list-group-item.active {
    background-color: #0d6efd;
    color: white;
}

.profile-header {
    text-align: center;
    padding: 1.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.profile-avatar {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
}
</style>

<div class="container-fluid" style="margin-top: 1rem;">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm profile-sidebar sticky-top" style="top: 80px; z-index: 999;">
                <div class="card-body profile-header">
                    <div class="profile-avatar">
                        <?php if ($user['avatar'] ?? null): ?>
                        <img src="<?= e($user['avatar']) ?>" class="rounded-circle" width="80" height="80" alt="Avatar">
                        <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="mb-1 fw-bold"><?= e($user['display_name'] ?? '') ?></h5>
                    <p class="text-muted small mb-0"><?= e($user['email']) ?></p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('/profile') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                    <a href="<?= url('/profile/tokens') ?>" class="list-group-item list-group-item-action active">
                        <i class="bi bi-key me-2"></i> API Tokens
                    </a>
                    <a href="<?= url('/profile/notifications') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-bell me-2"></i> Notifications
                    </a>
                    <a href="<?= url('/profile/security') ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-shield-lock me-2"></i> Security
                    </a>
                </div>
            </div>
        </div>

        <!-- Tokens Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h5 class="mb-0 fw-bold"><i class="bi bi-key me-2"></i>API Tokens</h5>
                        <small class="text-muted">Manage your personal API tokens for third-party integrations</small>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTokenModal">
                        <i class="bi bi-plus-lg me-1"></i> Create Token
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        API tokens allow you to authenticate with the API without using your password. 
                        Treat them like passwords and keep them secure.
                    </div>

                    <?php 
                    $successMsg = $flash['success'] ?? '';
                    $newToken = \App\Core\Session::getFlash('new_token');
                    if (!empty($successMsg) && !empty($newToken)): 
                    ?>
                    <div class="alert alert-success">
                        <h6><i class="bi bi-check-circle me-2"></i>Token Created Successfully</h6>
                        <p class="mb-2">Make sure to copy your new token now. You won't be able to see it again!</p>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" value="<?= e($newToken) ?>" readonly id="newTokenValue">
                            <button class="btn btn-outline-secondary" onclick="copyToken()">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (empty($tokens)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-key fs-1 d-block mb-3"></i>
                        <h5>No API tokens</h5>
                        <p>Create a token to authenticate with the API.</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Scopes</th>
                                    <th>Last Used</th>
                                    <th>Created</th>
                                    <th>Expires</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tokens as $token): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($token['name']) ?></strong>
                                        <br>
                                        <code class="small text-muted"><?= e(substr($token['token_preview'] ?? '****', 0, 12)) ?>...</code>
                                    </td>
                                    <td>
                                         <span class="badge bg-secondary">Full Access</span>
                                     </td>
                                    <td>
                                        <?php if ($token['last_used_at']): ?>
                                        <span class="text-muted"><?= time_ago($token['last_used_at']) ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">Never</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted"><?= format_date($token['created_at']) ?></td>
                                    <td>
                                        <?php if (!empty($token['expires_at'])): ?>
                                        <?php if (strtotime($token['expires_at']) < time()): ?>
                                        <span class="text-danger">Expired</span>
                                        <?php else: ?>
                                        <?= format_date($token['expires_at']) ?>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <span class="text-muted">Never</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="<?= url('/profile/tokens/' . $token['id']) ?>" method="POST" 
                                              onsubmit="return confirm('Revoke this token? This action cannot be undone.')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i> Revoke
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- API Documentation Link -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-book text-primary fs-1"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1">API Documentation</h5>
                            <p class="text-muted mb-0">Learn how to use the API to integrate with your applications.</p>
                        </div>
                        <a href="<?= url('/api/docs') ?>" class="btn btn-outline-primary">View Docs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Token Modal -->
<div class="modal fade" id="createTokenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create API Token</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= url('/profile/tokens') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Token Name</label>
                        <input type="text" name="name" class="form-control" required 
                               placeholder="e.g., CI/CD Pipeline, Development">
                        <small class="text-muted">A descriptive name to identify this token</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Scopes</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="scopes[]" value="*" id="scope-all" checked>
                            <label class="form-check-label" for="scope-all">
                                <strong>Full Access</strong> - Access to all API endpoints
                            </label>
                        </div>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="scopes[]" value="read:issues" id="scope-read-issues">
                            <label class="form-check-label" for="scope-read-issues">Read Issues</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="scopes[]" value="write:issues" id="scope-write-issues">
                            <label class="form-check-label" for="scope-write-issues">Write Issues</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="scopes[]" value="read:projects" id="scope-read-projects">
                            <label class="form-check-label" for="scope-read-projects">Read Projects</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="scopes[]" value="write:projects" id="scope-write-projects">
                            <label class="form-check-label" for="scope-write-projects">Write Projects</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expiration</label>
                        <select name="expires" class="form-select">
                            <option value="">Never expires</option>
                            <option value="7">7 days</option>
                            <option value="30" selected>30 days</option>
                            <option value="90">90 days</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Token</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php \App\Core\View::endSection(); ?>

<?php \App\Core\View::section('scripts'); ?>
<script>
function copyToken() {
    const input = document.getElementById('newTokenValue');
    input.select();
    document.execCommand('copy');
    
    const btn = input.nextElementSibling;
    btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
    setTimeout(() => {
        btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy';
    }, 2000);
}

// Handle scope checkbox logic
document.getElementById('scope-all').addEventListener('change', function() {
    document.querySelectorAll('input[name="scopes[]"]:not(#scope-all)').forEach(cb => {
        cb.checked = false;
        cb.disabled = this.checked;
    });
});
</script>
<?php \App\Core\View::endSection(); ?>
