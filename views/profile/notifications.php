<?php
declare(strict_types=1);

use App\Core\View;

View::extends('layouts.app');
View::section('content');

$preferences = $preferences ?? [];
?>

<div class="profile-settings-container">
    <div class="settings-header">
        <h1>Notification Settings</h1>
        <p class="subtitle">Choose how you want to receive notifications</p>
    </div>

    <div class="settings-content">
        <!-- Preferences Form -->
        <form id="notificationPreferencesForm" class="preferences-form">
            <div class="form-section">
                <h3>Event Notifications</h3>
                <p class="section-description">Customize how you're notified for different events</p>

                <div class="preferences-grid">
                    <!-- Issue Created -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Issue Created</h4>
                            <p class="preference-description">New issues in your projects</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_created_in_app" class="channel-input" 
                                    <?= (isset($preferences['issue_created']) && $preferences['issue_created']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_created_email" class="channel-input" 
                                    <?= (isset($preferences['issue_created']) && $preferences['issue_created']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_created_push" class="channel-input" 
                                    <?= (isset($preferences['issue_created']) && $preferences['issue_created']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Assigned -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Issue Assigned</h4>
                            <p class="preference-description">When you're assigned an issue</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_assigned_in_app" class="channel-input" 
                                    <?= (isset($preferences['issue_assigned']) && $preferences['issue_assigned']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_assigned_email" class="channel-input" 
                                    <?= (isset($preferences['issue_assigned']) && $preferences['issue_assigned']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_assigned_push" class="channel-input" 
                                    <?= (isset($preferences['issue_assigned']) && $preferences['issue_assigned']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Commented -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Issue Commented</h4>
                            <p class="preference-description">New comments on your issues</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_commented_in_app" class="channel-input" 
                                    <?= (isset($preferences['issue_commented']) && $preferences['issue_commented']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_commented_email" class="channel-input" 
                                    <?= (isset($preferences['issue_commented']) && $preferences['issue_commented']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_commented_push" class="channel-input" 
                                    <?= (isset($preferences['issue_commented']) && $preferences['issue_commented']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Status Changed -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Status Changed</h4>
                            <p class="preference-description">When issue status changes</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_status_changed_in_app" class="channel-input" 
                                    <?= (isset($preferences['issue_status_changed']) && $preferences['issue_status_changed']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_status_changed_email" class="channel-input" 
                                    <?= (isset($preferences['issue_status_changed']) && $preferences['issue_status_changed']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_status_changed_push" class="channel-input" 
                                    <?= (isset($preferences['issue_status_changed']) && $preferences['issue_status_changed']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Mentioned -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Mentioned</h4>
                            <p class="preference-description">When you're mentioned in an issue</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_mentioned_in_app" class="channel-input" 
                                    <?= (isset($preferences['issue_mentioned']) && $preferences['issue_mentioned']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_mentioned_email" class="channel-input" 
                                    <?= (isset($preferences['issue_mentioned']) && $preferences['issue_mentioned']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_mentioned_push" class="channel-input" 
                                    <?= (isset($preferences['issue_mentioned']) && $preferences['issue_mentioned']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Issue Watched -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Issue Watched</h4>
                            <p class="preference-description">Changes to issues you're watching</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_watched_in_app" class="channel-input" 
                                    <?= (isset($preferences['issue_watched']) && $preferences['issue_watched']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_watched_email" class="channel-input" 
                                    <?= (isset($preferences['issue_watched']) && $preferences['issue_watched']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="issue_watched_push" class="channel-input" 
                                    <?= (isset($preferences['issue_watched']) && $preferences['issue_watched']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Comment Reply -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Comment Replies</h4>
                            <p class="preference-description">When someone replies to your comment</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="comment_reply_in_app" class="channel-input" 
                                    <?= (isset($preferences['comment_reply']) && $preferences['comment_reply']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="comment_reply_email" class="channel-input" 
                                    <?= (isset($preferences['comment_reply']) && $preferences['comment_reply']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="comment_reply_push" class="channel-input" 
                                    <?= (isset($preferences['comment_reply']) && $preferences['comment_reply']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Project Created -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Project Created</h4>
                            <p class="preference-description">New projects you're added to</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="project_created_in_app" class="channel-input" 
                                    <?= (isset($preferences['project_created']) && $preferences['project_created']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="project_created_email" class="channel-input" 
                                    <?= (isset($preferences['project_created']) && $preferences['project_created']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="project_created_push" class="channel-input" 
                                    <?= (isset($preferences['project_created']) && $preferences['project_created']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>

                    <!-- Project Member Added -->
                    <div class="preference-card">
                        <div class="preference-header">
                            <h4>Project Member Added</h4>
                            <p class="preference-description">When you're added to a project</p>
                        </div>
                        <div class="preference-channels">
                            <label class="channel-checkbox">
                                <input type="checkbox" name="project_member_added_in_app" class="channel-input" 
                                    <?= (isset($preferences['project_member_added']) && $preferences['project_member_added']['in_app']) ? 'checked' : '' ?>>
                                <span class="channel-icon">📱</span>
                                <span class="channel-name">In-App</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="project_member_added_email" class="channel-input" 
                                    <?= (isset($preferences['project_member_added']) && $preferences['project_member_added']['email']) ? 'checked' : '' ?>>
                                <span class="channel-icon">✉️</span>
                                <span class="channel-name">Email</span>
                            </label>
                            <label class="channel-checkbox">
                                <input type="checkbox" name="project_member_added_push" class="channel-input" 
                                    <?= (isset($preferences['project_member_added']) && $preferences['project_member_added']['push']) ? 'checked' : '' ?>>
                                <span class="channel-icon">🔔</span>
                                <span class="channel-name">Push</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle"></i> Save Preferences
                </button>
                <button type="button" id="resetBtn" class="btn btn-secondary btn-lg">
                    Reset to Defaults
                </button>
            </div>

            <!-- Success/Error Messages -->
            <div id="successMessage" class="alert alert-success d-none">
                <i class="bi bi-check-circle"></i> Notification preferences updated successfully!
            </div>
            <div id="errorMessage" class="alert alert-danger d-none">
                <i class="bi bi-exclamation-circle"></i> Error updating preferences
            </div>
        </form>
    </div>
</div>

<style>
.profile-settings-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 20px;
}

.settings-header {
    margin-bottom: 40px;
}

.settings-header h1 {
    font-size: 28px;
    font-weight: 600;
    margin: 0 0 8px;
    color: #161b22;
}

.subtitle {
    margin: 0;
    color: #656d76;
    font-size: 14px;
}

.settings-content {
    background: white;
    border: 1px solid #d0d7de;
    border-radius: 12px;
    padding: 32px;
}

.form-section {
    margin-bottom: 32px;
}

.form-section h3 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 8px;
    color: #161b22;
}

.section-description {
    margin: 0 0 16px;
    font-size: 13px;
    color: #656d76;
}

.preferences-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.preference-card {
    border: 1px solid #d0d7de;
    border-radius: 8px;
    padding: 16px;
    background: #f6f8fa;
    transition: all 0.2s ease;
}

.preference-card:hover {
    border-color: #b6e3ff;
    background: #f0f7ff;
}

.preference-header {
    margin-bottom: 12px;
}

.preference-header h4 {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: 600;
    color: #161b22;
}

.preference-description {
    margin: 0;
    font-size: 12px;
    color: #656d76;
}

.preference-channels {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.channel-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 6px;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.channel-checkbox:hover {
    background: rgba(0, 82, 204, 0.1);
}

.channel-input {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: #0052cc;
}

.channel-icon {
    font-size: 14px;
}

.channel-name {
    font-size: 13px;
    color: #161b22;
    flex: 1;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #d0d7de;
}

.btn {
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #0052cc;
    color: white;
}

.btn-primary:hover {
    background: #003d82;
}

.btn-secondary {
    background: #e1e4e8;
    color: #161b22;
}

.btn-secondary:hover {
    background: #d0d7de;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 15px;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-top: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.alert-success {
    background: #dffcf0;
    color: #216e4e;
    border: 1px solid #34d399;
}

.alert-danger {
    background: #ffeceb;
    color: #da3633;
    border: 1px solid #f85149;
}

.d-none {
    display: none;
}

@media (max-width: 768px) {
    .settings-header h1 {
        font-size: 22px;
    }

    .settings-content {
        padding: 20px;
    }

    .preferences-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-lg {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('notificationPreferencesForm');
    const resetBtn = document.getElementById('resetBtn');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    
    // Get base URL for API calls
    const appUrl = '<?= url("/") ?>';

    // CRITICAL #2 FIX: Define valid event types and channels (client-side validation)
    const VALID_EVENT_TYPES = [
        'issue_created', 'issue_assigned', 'issue_commented',
        'issue_status_changed', 'issue_mentioned', 'issue_watched',
        'project_created', 'project_member_added', 'comment_reply'
    ];
    
    const VALID_CHANNELS = ['in_app', 'email', 'push'];

    // Handle form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const data = {};
        let clientValidationWarnings = [];

        // CRITICAL #2 FIX: Parse form data into object with event_type structure
         // Include client-side validation before sending
         formData.forEach((value, key) => {
             // CRITICAL FIX: Handle "in_app" channel correctly
             // Form sends: issue_created_in_app, issue_created_email, issue_created_push
             // We need to extract: eventType='issue_created', channel='in_app'|'email'|'push'
             let eventType = '';
             let channel = '';
             
             if (key.endsWith('_in_app')) {
                 // Remove _in_app suffix
                 eventType = key.substring(0, key.length - 7); // 7 = '_in_app'.length
                 channel = 'in_app';
             } else if (key.endsWith('_email')) {
                 // Remove _email suffix
                 eventType = key.substring(0, key.length - 6); // 6 = '_email'.length
                 channel = 'email';
             } else if (key.endsWith('_push')) {
                 // Remove _push suffix
                 eventType = key.substring(0, key.length - 5); // 5 = '_push'.length
                 channel = 'push';
             } else {
                 console.warn(`[CRITICAL #2] Could not parse field name: ${key}`);
                 clientValidationWarnings.push(`Invalid field name format: ${key}`);
                 return;
             }

             // CRITICAL #2 FIX: Validate event type against whitelist
             if (!VALID_EVENT_TYPES.includes(eventType)) {
                 console.warn(`[CRITICAL #2] Invalid event_type detected: ${eventType}`);
                 clientValidationWarnings.push(`Invalid event type: ${eventType}`);
                 return; // Skip this one
             }

             // CRITICAL #2 FIX: Validate channel against whitelist
             if (!VALID_CHANNELS.includes(channel)) {
                 console.warn(`[CRITICAL #2] Invalid channel detected: ${channel}`);
                 clientValidationWarnings.push(`Invalid channel for ${eventType}: ${channel}`);
                 return; // Skip this one
             }

             if (!data[eventType]) {
                 data[eventType] = {};
             }
             data[eventType][channel] = value === 'on'; // Checkbox values
         });

        // If we have client-side validation warnings, log them
        if (clientValidationWarnings.length > 0) {
            console.warn('[CRITICAL #2] Client-side validation warnings:', clientValidationWarnings);
        }

        try {
            const response = await fetch(appUrl + 'api/v1/notifications/preferences', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ preferences: data })
            });

            const responseData = await response.json();

            if (response.ok) {
                // CRITICAL #2 FIX: Check for partial success or invalid entries
                if (responseData.invalid_count > 0) {
                    // Show partial success with warnings
                    successMessage.classList.remove('d-none');
                    errorMessage.classList.add('d-none');
                    
                    // Log the detailed errors
                    console.warn('[CRITICAL #2] Partial success - invalid entries:', responseData.errors);
                    
                    // Show warning to user about invalid preferences
                    const warningDiv = document.createElement('div');
                    warningDiv.className = 'alert alert-warning alert-dismissible fade show mt-3';
                    warningDiv.role = 'alert';
                    warningDiv.innerHTML = `
                        <strong>Warning:</strong> ${responseData.invalid_count} preference(s) were invalid and were not saved.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    successMessage.insertAdjacentElement('afterend', warningDiv);
                    
                    // Auto-remove warning after 8 seconds
                    setTimeout(() => {
                        warningDiv.remove();
                    }, 8000);
                } else {
                    // Full success
                    successMessage.classList.remove('d-none');
                    errorMessage.classList.add('d-none');
                }
                
                console.log('[CRITICAL #2] Preferences saved:', responseData);
                setTimeout(() => {
                    successMessage.classList.add('d-none');
                }, 5000);
            } else {
                console.error('[CRITICAL #2] API error:', responseData);
                errorMessage.classList.remove('d-none');
                successMessage.classList.add('d-none');
                
                // CRITICAL #2 FIX: Show detailed error information
                if (responseData.errors && Array.isArray(responseData.errors)) {
                    const errorDetails = responseData.errors.map(err => {
                        if (err.valid_types) {
                            return `${err.event_type}: ${err.error} (valid: ${err.valid_types.join(', ')})`;
                        }
                        return `${err.event_type || 'Unknown'}: ${err.error}`;
                    }).join('\n');
                    console.error('[CRITICAL #2] Error details:\n' + errorDetails);
                } else if (responseData.details) {
                    console.error('[CRITICAL #2] Error details:', responseData.details);
                }
            }
        } catch (error) {
            console.error('[CRITICAL #2] Error saving preferences:', error);
            errorMessage.classList.remove('d-none');
            successMessage.classList.add('d-none');
        }
    });

    // Handle reset button
    resetBtn.addEventListener('click', function() {
        if (confirm('Reset all preferences to defaults?')) {
            const inputs = form.querySelectorAll('input[type="checkbox"]');
            inputs.forEach(input => {
                // Default: in_app and email checked, push unchecked
                if (input.name.endsWith('_in_app') || input.name.endsWith('_email')) {
                    input.checked = true;
                } else if (input.name.endsWith('_push')) {
                    input.checked = false;
                }
            });
        }
    });
});
</script>

<?php View::endSection();
