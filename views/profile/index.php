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
                        <button class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle" 
                                data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>
                    <h4 class="mb-1"><?= e($user['display_name'] ?? $user['first_name'] . ' ' . $user['last_name']) ?></h4>
                    <p class="text-muted mb-0"><?= e($user['email']) ?></p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= url('/profile') ?>" class="list-group-item list-group-item-action active">
                        <i class="bi bi-person me-2"></i> Profile
                    </a>
                    <a href="<?= url('/profile/tokens') ?>" class="list-group-item list-group-item-action">
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

        <!-- Profile Content -->
        <div class="col-lg-9">
            <!-- Profile Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form action="<?= url('/profile') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="PUT">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" 
                                       value="<?= e($user['first_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" 
                                       value="<?= e($user['last_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= e($user['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-select">
                                    <?php foreach ($timezones ?? [] as $tz): ?>
                                    <option value="<?= $tz ?>" <?= ($user['timezone'] ?? 'UTC') === $tz ? 'selected' : '' ?>>
                                        <?= e($tz) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

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

            <!-- Activity Stats -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Activity Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3 text-center">
                            <h3 class="mb-0"><?= e($stats['issues_assigned'] ?? 0) ?></h3>
                            <small class="text-muted">Issues Assigned</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="mb-0"><?= e($stats['issues_completed'] ?? 0) ?></h3>
                            <small class="text-muted">Issues Completed</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="mb-0"><?= e($stats['comments_made'] ?? 0) ?></h3>
                            <small class="text-muted">Comments Made</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h3 class="mb-0"><?= e($stats['projects_count'] ?? 0) ?></h3>
                            <small class="text-muted">Projects</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Avatar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- Step 1: Upload File -->
            <div id="avatarStep1">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Upload Image</label>
                        <input type="file" id="avatarInput" class="form-control" accept="image/*">
                        <small class="text-muted">Max file size: 2MB. Supported formats: JPEG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="nextStep" disabled>Next</button>
                </div>
            </div>

            <!-- Step 2: Crop Image -->
            <div id="avatarStep2" style="display: none;">
                <div class="modal-body">
                    <p class="text-muted mb-3">Adjust your avatar in the circle below</p>
                    <div style="position: relative; max-width: 100%;">
                        <!-- Circular Overlay -->
                        <div style="position: relative; width: 300px; height: 300px; margin: 0 auto; border-radius: 50%; overflow: hidden; border: 3px solid #dee2e6; background: #f8f9fa;">
                            <img id="cropperImage" style="max-width: 100%; display: block;">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Zoom</label>
                        <input type="range" id="zoomSlider" class="form-range" min="1" max="3" step="0.1" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="backStep">Back</button>
                    <button type="button" class="btn btn-primary" id="uploadAvatar">Upload Avatar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Cropper CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedFile = null;
    let cropper = null;
    let canvas = null;

    // File input change
    document.getElementById('avatarInput').addEventListener('change', function(e) {
        selectedFile = e.target.files[0];
        
        if (!selectedFile) {
            document.getElementById('nextStep').disabled = true;
            return;
        }

        // Validate file size
        if (selectedFile.size > 2 * 1024 * 1024) {
            alert('File size exceeds 2MB limit');
            document.getElementById('nextStep').disabled = true;
            return;
        }

        document.getElementById('nextStep').disabled = false;
    });

    // Next Step button
    document.getElementById('nextStep').addEventListener('click', function() {
        if (!selectedFile) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('cropperImage');
            img.src = e.target.result;
            
            // Destroy previous cropper if exists
            if (cropper) {
                cropper.destroy();
            }

            // Initialize cropper with circular aspect ratio
            cropper = new Cropper(img, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                restore: true,
                guides: false,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: true,
            });
        };
        reader.readAsDataURL(selectedFile);

        // Hide step 1, show step 2
        document.getElementById('avatarStep1').style.display = 'none';
        document.getElementById('avatarStep2').style.display = 'block';
    });

    // Back button
    document.getElementById('backStep').addEventListener('click', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        document.getElementById('avatarStep1').style.display = 'block';
        document.getElementById('avatarStep2').style.display = 'none';
        document.getElementById('avatarInput').value = '';
    });

    // Zoom slider
    document.getElementById('zoomSlider').addEventListener('input', function(e) {
        const zoomValue = parseFloat(e.target.value);
        if (cropper) {
            cropper.zoomTo(zoomValue);
        }
    });

    // Upload Avatar button
    document.getElementById('uploadAvatar').addEventListener('click', function() {
        if (!cropper) return;

        // Show loading state
        const uploadBtn = document.getElementById('uploadAvatar');
        const originalText = uploadBtn.innerText;
        uploadBtn.disabled = true;
        uploadBtn.innerText = 'Uploading...';

        // Get canvas from cropper
        const canvas = cropper.getCroppedCanvas({
            maxWidth: 400,
            maxHeight: 400,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Convert canvas to blob
        canvas.toBlob(function(blob) {
            // Create FormData
            const formData = new FormData();
            formData.append('avatar', blob, 'avatar.png');

            // Get CSRF token from meta tag (in layout)
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (csrfToken) {
                formData.append('_csrf_token', csrfToken);
            }

            // Upload via AJAX
            fetch('<?= url("/profile/avatar") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            })
            .then(response => {
                // Log response for debugging
                if (!response.ok && response.status !== 200) {
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}`);
                    });
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
                        modal.hide();
                        
                        // Show success message
                        alert('Avatar uploaded successfully!');
                        
                        // Reload page to show new avatar
                        setTimeout(() => location.reload(), 800);
                    } else {
                        throw new Error(data.error || 'Upload failed');
                    }
                } catch (e) {
                    throw new Error('Invalid response: ' + e.message);
                }
            })
            .catch(error => {
                uploadBtn.disabled = false;
                uploadBtn.innerText = originalText;
                console.error('Upload error:', error);
                alert('Error uploading avatar:\n' + error.message);
            });
        }, 'image/png');
    });

    // Reset modal on close
    document.getElementById('avatarModal').addEventListener('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        document.getElementById('avatarStep1').style.display = 'block';
        document.getElementById('avatarStep2').style.display = 'none';
        document.getElementById('avatarInput').value = '';
        document.getElementById('zoomSlider').value = 1;
    });
});
</script>

<?php \App\Core\View::endSection(); ?>
