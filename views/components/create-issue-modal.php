<!-- Create Issue Modal Component -->
<style>
    /* Create Issue Modal - Standard Design */
    #createIssueModal .modal-dialog {
        max-width: 600px;
        height: auto;
        max-height: 90vh;
        margin-top: 10px;
        /* Added small top margin as requested */
    }

    #createIssueModal .modal-content {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    #createIssueModal .modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        background-color: #ffffff;
        border-radius: 8px 8px 0 0;
    }

    #createIssueModal .modal-title {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    #createIssueModal .modal-body {
        padding: 20px;
        max-height: calc(90vh - 120px);
        overflow-y: auto;
    }

    #createIssueModal .form-label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }

    #createIssueModal .form-control,
    #createIssueModal .form-select {
        font-size: 14px;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    #createIssueModal .form-control:focus,
    #createIssueModal .form-select:focus {
        border-color: #8b1956;
        box-shadow: 0 0 0 3px rgba(139, 25, 86, 0.1);
        outline: none;
    }

    #createIssueModal .form-control::placeholder {
        color: #9ca3af;
        font-size: 14px;
    }

    #createIssueModal .form-group {
        margin-bottom: 16px;
    }

    #createIssueModal .required-star {
        color: #ef4444;
        margin-left: 2px;
    }

    /* Drag & Drop Zone Styles */
    .upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 6px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f9fafb;
        position: relative;
    }

    .upload-zone:hover,
    .upload-zone.dragover {
        border-color: #8b1956;
        background: #fdf2f8;
    }

    .upload-zone-icon {
        font-size: 24px;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .upload-zone-text {
        font-size: 13px;
        color: #4b5563;
    }

    .upload-zone-hint {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .file-preview-list {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .file-preview-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        font-size: 13px;
    }

    .file-preview-name {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .remove-file-btn {
        color: #ef4444;
        cursor: pointer;
        background: none;
        border: none;
        padding: 2px;
    }

    .remove-file-btn:hover {
        color: #dc2626;
    }

    #createIssueModal .modal-footer {
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
        background-color: #f9fafb;
        border-radius: 0 0 8px 8px;
    }

    #createIssueModal .btn {
        font-size: 14px;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.15s ease-in-out;
    }

    #createIssueModal .btn-primary {
        background-color: #8b1956;
        border-color: #8b1956;
        color: #ffffff;
    }

    #createIssueModal .btn-primary:hover {
        background-color: #6f123f;
        border-color: #6f123f;
    }

    #createIssueModal .btn-secondary {
        background-color: #ffffff;
        border-color: #d1d5db;
        color: #6b7280;
    }

    #createIssueModal .btn-secondary:hover {
        background-color: #f9fafb;
        border-color: #9ca3af;
        color: #4b5563;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        #createIssueModal .modal-dialog {
            max-width: 95vw;
            margin: 10px;
        }

        #createIssueModal .modal-body {
            padding: 16px;
        }

        #createIssueModal .modal-header,
        #createIssueModal .modal-footer {
            padding: 12px 16px;
        }
    }
</style>

<div class="modal fade" id="createIssueModal" tabindex="-1" aria-labelledby="createIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createIssueModalLabel">Create Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createIssueForm">
                    <!-- Project Field -->
                    <div class="form-group">
                        <label for="issueProject" class="form-label">
                            Project<span class="required-star">*</span>
                        </label>
                        <select class="form-select" id="issueProject" name="project" required>
                            <option value="">Select a project</option>
                        </select>
                    </div>

                    <!-- Issue Type Field -->
                    <div class="form-group">
                        <label for="issueType" class="form-label">
                            Issue Type<span class="required-star">*</span>
                        </label>
                        <select class="form-select" id="issueType" name="issueType" required>
                            <option value="">Select issue type</option>
                        </select>
                    </div>

                    <!-- Summary Field -->
                    <div class="form-group">
                        <label for="issueSummary" class="form-label">
                            Summary<span class="required-star">*</span>
                        </label>
                        <input type="text" class="form-control" id="issueSummary" name="summary"
                            placeholder="Enter issue summary" required maxlength="500">
                    </div>

                    <!-- Description Field -->
                    <div class="form-group">
                        <label for="issueDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="issueDescription" name="description" rows="3"
                            placeholder="Enter issue description" maxlength="5000"></textarea>
                    </div>

                    <!-- Attachments Drag & Drop -->
                    <div class="form-group">
                        <label class="form-label">Attachments</label>
                        <div class="upload-zone" id="uploadZone">
                            <input type="file" id="fileInput" multiple style="display: none;">
                            <div class="upload-zone-icon">
                                <i class="bi bi-cloud-arrow-up"></i>
                            </div>
                            <div class="upload-zone-text">Drop files here or click to upload</div>
                            <div class="upload-zone-hint">Max file size: 10MB</div>
                        </div>
                        <div class="file-preview-list" id="filePreviewList"></div>
                    </div>

                    <!-- Assignee Field -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="issueAssignee" class="form-label">Assignee</label>
                                <select class="form-select" id="issueAssignee" name="assignee">
                                    <option value="">Automatic</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Reporter</label>
                                <?php
                                $currentUser = \App\Core\Session::user();
                                $userAvatar = $currentUser['avatar'] ?? null;
                                $userName = $currentUser['display_name'] ?? $currentUser['name'] ?? 'Unknown';
                                $userEmail = $currentUser['email'] ?? '';
                                ?>
                                <div class="form-control d-flex align-items-center bg-light" readonly>
                                    <?php if ($userAvatar): ?>
                                        <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Avatar"
                                            class="rounded-circle me-2" width="24" height="24">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2"
                                            style="width: 24px; height: 24px; font-size: 12px;">
                                            <?= strtoupper(substr($userName, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($userName) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Priority Field -->
                    <div class="form-group">
                        <label for="issuePriority" class="form-label">Priority</label>
                        <select class="form-select" id="issuePriority" name="priority">
                            <option value="">Select priority</option>
                        </select>
                    </div>

                    <!-- Date Fields Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="issueStartDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="issueStartDate" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="issueEndDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="issueEndDate" name="end_date">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createIssueBtn">Create</button>
            </div>
        </div>
    </div>
</div>