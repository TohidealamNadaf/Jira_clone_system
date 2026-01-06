<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Modal Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= url('assets/css/app.css') ?>">
    <style>
        .test-container {
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .test-btn {
            background: var(--jira-blue, #8B1956);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .test-btn:hover {
            background: var(--jira-blue-dark, #6B0F44);
        }
        .status {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>Calendar Modal Test</h1>
        <p>Testing the calendar modal fixes for:</p>
        <ul>
            <li>Modal not dimming/disabled</li>
            <li>Modal scrolling working</li>
            <li>Backdrop click handling</li>
            <li>Event propagation</li>
        </ul>

        <div class="test-buttons">
            <button class="test-btn" onclick="testEventModal()">
                <i class="bi bi-calendar-event"></i> Test Event Modal
            </button>
            <button class="test-btn" onclick="testCreateModal()">
                <i class="bi bi-plus-circle"></i> Test Create Modal
            </button>
            <button class="test-btn" onclick="testExportModal()">
                <i class="bi bi-download"></i> Test Export Modal
            </button>
        </div>

        <div id="status"></div>

        <div class="test-info">
            <h3>How to Test:</h3>
            <ol>
                <li>Click each test button to open different modals</li>
                <li>Check that modal is fully visible (not dimmed)</li>
                <li>Try scrolling within the modal content</li>
                <li>Click the backdrop (dark area) to close modal</li>
                <li>Click the X button to close modal</li>
                <li>Press ESC key to close modal</li>
                <li>Check that background scroll is restored</li>
            </ol>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="jira-modal" id="eventModal" onclick="handleBackdropClick(event)" aria-hidden="true">
        <div class="modal-dialog modal-standard">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Test Event Details</h2>
                    <button class="modal-close" onclick="closeEventModal()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body-scroll">
                    <div class="event-info">
                        <div class="event-type-badge">
                            <i class="bi bi-bug"></i>
                            Bug
                        </div>
                        <div class="event-basic">
                            <h3 class="event-key">TEST-123</h3>
                            <h4 class="event-summary">Test modal functionality</h4>
                        </div>
                    </div> 

                    <div class="event-details-grid">
                        <div class="detail-row">
                            <label>Project</label>
                            <span>Test Project</span>
                        </div>
                        <div class="detail-row">
                            <label>Status</label>
                            <span class="status-badge">In Progress</span>
                        </div>
                        <div class="detail-row">
                            <label>Priority</label>
                            <span class="priority-badge urgent">Urgent</span>
                        </div>
                    </div> 

                    <div class="event-description">
                        <h4>Description</h4>
                        <div class="description-content">
                            This is a test modal to verify that scrolling works correctly and the modal is not dimmed or disabled.
                            Scroll through this content to test the scrolling functionality.
                            <br><br>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                            <br><br>
                            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            <br><br>
                            Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                            <br><br>
                            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        </div>
                    </div> 
                </div>
                <div class="modal-footer">
                    <div class="footer-left">
                        <button class="jira-btn jira-btn-ghost">
                            <i class="bi bi-eye"></i>
                            Watch
                        </button>
                    </div>
                    <div class="footer-right">
                        <button class="jira-btn jira-btn-primary">
                            <i class="bi bi-box-arrow-up-right"></i>
                            View Issue
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div class="jira-modal" id="createEventModal" onclick="handleBackdropClick(event, 'create')" aria-hidden="true">
        <div class="modal-dialog modal-standard">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Test Create Modal</h2>
                    <button class="modal-close" onclick="closeCreateModal()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body-scroll">
                    <form id="createEventForm">
                        <div class="form-section">
                            <h3>Event Details</h3>
                            <div class="form-row">
                                <div class="form-group half">
                                    <label>Event Type</label>
                                    <select class="jira-select">
                                        <option value="issue">Issue Due Date</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="milestone">Milestone</option>
                                    </select>
                                </div>
                                <div class="form-group half">
                                    <label>Project</label>
                                    <select class="jira-select">
                                        <option value="">Select project...</option>
                                        <option value="TEST">Test Project</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="jira-input" placeholder="Event title..." required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="jira-textarea" rows="3" placeholder="Event description..."></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group half">
                                    <label>Start Date</label>
                                    <input type="datetime-local" class="jira-input" required>
                                </div>
                                <div class="form-group half">
                                    <label>End Date</label>
                                    <input type="datetime-local" class="jira-input" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group half">
                                    <label>Priority</label>
                                    <select class="jira-select">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                                <div class="form-group half">
                                    <label>Attendees</label>
                                    <input type="text" class="jira-input" placeholder="Add attendees...">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>Reminders</h3>
                            <div class="reminder-settings">
                                <div class="reminder-item">
                                    <input type="checkbox" id="reminder1" checked>
                                    <label for="reminder1">15 minutes before</label>
                                </div>
                                <div class="reminder-item">
                                    <input type="checkbox" id="reminder2">
                                    <label for="reminder2">1 hour before</label>
                                </div>
                                <div class="reminder-item">
                                    <input type="checkbox" id="reminder3">
                                    <label for="reminder3">1 day before</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3>Test Content for Scrolling</h3>
                            <p>This modal is now standard size (600px width) like the event details modal. Scroll through this content to test.</p>
                            <br><br>
                            <div style="height: 300px; background: #f0f0f0; padding: 20px; border-radius: 8px;">
                                <p>Scrollable content area - this should be scrollable within the standard-sized modal.</p>
                                <br><br>
                                <p>Keep scrolling to test modal scrolling...</p>
                                <br><br>
                                <p>Scrolling should work smoothly.</p>
                                <br><br>
                                <p>End of test content.</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="footer-right">
                        <button class="jira-btn jira-btn-secondary" onclick="closeCreateModal()">Cancel</button>
                        <button class="jira-btn jira-btn-primary">
                            <i class="bi bi-check-lg"></i>
                            Create Event
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="jira-modal" id="exportModal" onclick="handleBackdropClick(event, 'export')" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Test Export Modal</h2>
                    <button class="modal-close" onclick="closeExportModal()">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div style="padding: 20px;">
                        <h3>Export Options</h3>
                        <p>This is a smaller modal to test various modal sizes.</p>
                        <br><br>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <p>Modal content area</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="footer-right">
                        <button class="jira-btn jira-btn-secondary" onclick="closeExportModal()">Cancel</button>
                        <button class="jira-btn jira-btn-primary">
                            <i class="bi bi-download"></i>
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functions (copied from calendar.js fixes)
        window.handleBackdropClick = function(event, modalType) {
            const isBackdrop = event.target.classList.contains('jira-modal');
            
            if (isBackdrop) {
                switch(modalType) {
                    case 'create':
                        closeCreateModal();
                        break;
                    case 'export':
                        closeExportModal();
                        break;
                    default:
                        closeEventModal();
                }
            }
        };

        window.closeEventModal = function() {
            const eventModal = document.getElementById('eventModal');
            if (eventModal) {
                eventModal.style.display = 'none';
                eventModal.classList.remove('open');
                eventModal.setAttribute('aria-hidden', 'true');
                
                const scrollY = document.body.style.top;
                document.body.style.overflow = 'auto';
                document.body.style.position = 'static';
                document.body.style.width = 'auto';
                document.body.style.top = '';
                
                if (scrollY) {
                    window.scrollTo(0, parseInt(scrollY || '0') * -1);
                }
                
                showStatus('Event modal closed successfully', 'success');
            }
        };

        window.closeCreateModal = function() {
            const createEventModal = document.getElementById('createEventModal');
            if (createEventModal) {
                createEventModal.style.display = 'none';
                createEventModal.classList.remove('open');
                createEventModal.setAttribute('aria-hidden', 'true');
                
                const scrollY = document.body.style.top;
                document.body.style.overflow = 'auto';
                document.body.style.position = 'static';
                document.body.style.width = 'auto';
                document.body.style.top = '';
                
                if (scrollY) {
                    window.scrollTo(0, parseInt(scrollY || '0') * -1);
                }
                
                showStatus('Create modal closed successfully', 'success');
            }
        };

        window.closeExportModal = function() {
            const exportModal = document.getElementById('exportModal');
            if (exportModal) {
                exportModal.style.display = 'none';
                exportModal.classList.remove('open');
                exportModal.setAttribute('aria-hidden', 'true');
                
                const scrollY = document.body.style.top;
                document.body.style.overflow = 'auto';
                document.body.style.position = 'static';
                document.body.style.width = 'auto';
                document.body.style.top = '';
                
                if (scrollY) {
                    window.scrollTo(0, parseInt(scrollY || '0') * -1);
                }
                
                showStatus('Export modal closed successfully', 'success');
            }
        };

        // Test functions
        function testEventModal() {
            const eventModal = document.getElementById('eventModal');
            if (eventModal) {
                eventModal.style.display = 'flex';
                eventModal.classList.add('open');
                eventModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                document.body.style.top = `-${window.scrollY}px`;
                
                showStatus('Event modal opened - test scrolling and backdrop clicks', 'success');
            }
        }

        function testCreateModal() {
            const createEventModal = document.getElementById('createEventModal');
            if (createEventModal) {
                createEventModal.style.display = 'flex';
                createEventModal.classList.add('open');
                createEventModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                document.body.style.top = `-${window.scrollY}px`;
                
                showStatus('Create modal opened - now standard size with scrolling', 'success');
            }
        }

        function testExportModal() {
            const exportModal = document.getElementById('exportModal');
            if (exportModal) {
                exportModal.style.display = 'flex';
                exportModal.classList.add('open');
                exportModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                document.body.style.top = `-${window.scrollY}px`;
                
                showStatus('Export modal opened - smaller modal test', 'success');
            }
        }

        function showStatus(message, type) {
            const statusDiv = document.getElementById('status');
            statusDiv.innerHTML = `<div class="status ${type}">${message}</div>`;
            
            // Clear status after 3 seconds
            setTimeout(() => {
                statusDiv.innerHTML = '';
            }, 3000);
        }

        // ESC key handler
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const visibleModals = document.querySelectorAll('.jira-modal.open');
                visibleModals.forEach(modal => {
                    modal.style.display = 'none';
                    modal.classList.remove('open');
                    modal.setAttribute('aria-hidden', 'true');
                });
                
                const scrollY = document.body.style.top;
                document.body.style.overflow = 'auto';
                document.body.style.position = 'static';
                document.body.style.width = 'auto';
                document.body.style.top = '';
                
                if (scrollY) {
                    window.scrollTo(0, parseInt(scrollY || '0') * -1);
                }
                
                showStatus('ESC key pressed - all modals closed', 'success');
            }
        });

        // Prevent modal content clicks from bubbling to backdrop
        document.addEventListener('click', function(event) {
            const modalDialog = event.target.closest('.modal-dialog');
            if (modalDialog) {
                event.stopPropagation();
            }
        });
    </script>
</body>
</html>