/**
 * Real-Time Notifications System
 * Server-Sent Events (SSE) for live updates
 * No page refresh required
 * 
 * Features:
 * - Instant notification delivery
 * - Live notification count badge
 * - Toast notifications
 * - Sound alerts (optional)
 * - Browser notification (with permission)
 */

class RealtimeNotifications {
    constructor() {
        this.eventSource = null;
        this.lastEventId = 0;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 3000; // 3 seconds
        this.notificationSound = true;
        this.browserNotificationsEnabled = false;
        this.audioContext = null;
        this.hasUserGesture = false;

        this.init();
    }

    /**
     * Initialize real-time notifications
     */
    init() {
        console.log('🔔 [REALTIME] Initializing real-time notification system');

        // Request browser notification permission
        this.requestBrowserNotificationPermission();

        // Load recent notifications on init
        this.loadRecentNotifications();

        // Start SSE stream
        this.connectToStream();

        // Setup UI elements
        this.setupUI();

        // Handle page visibility (pause when hidden)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('🔔 [REALTIME] Page hidden - stream may pause');
            } else {
                console.log('🔔 [REALTIME] Page visible - reconnecting if needed');
                if (!this.isConnected) {
                    this.connectToStream();
                }
            }
        });

        // Reconnect on window focus
        window.addEventListener('focus', () => {
            if (!this.isConnected) {
                console.log('🔔 [REALTIME] Window focused - attempting reconnect');
                this.connectToStream();
            }
        });

        // Setup audio context on first user gesture
        // Setup audio context on first user gesture
        const initAudio = () => {
            if (!this.audioContext) {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            }

            // Only consider it a success if we can actually resume
            if (this.audioContext.state === 'suspended') {
                this.audioContext.resume().then(() => {
                    console.log('🔊 [REALTIME] AudioContext resumed successfully');
                    this.hasUserGesture = true;
                    // Only remove listeners if successful
                    document.removeEventListener('click', initAudio);
                    document.removeEventListener('keydown', initAudio);
                }).catch(e => {
                    console.log('ℹ️ [REALTIME] Audio resume failed, keeping listeners:', e);
                });
            } else if (this.audioContext.state === 'running') {
                console.log('🔊 [REALTIME] AudioContext already running');
                this.hasUserGesture = true;
                document.removeEventListener('click', initAudio);
                document.removeEventListener('keydown', initAudio);
            }
        };
        document.addEventListener('click', initAudio);
        document.addEventListener('keydown', initAudio);

        console.log('✅ [REALTIME] Notification system initialized');
    }

    /**
     * Connect to SSE stream
     */
    connectToStream() {
        if (this.eventSource) {
            this.eventSource.close();
        }

        console.log(`🔌 [REALTIME] Connecting to notification stream... (attempt ${this.reconnectAttempts + 1})`);

        // Get base path from meta tag (handles deployment in subdirectories)
        const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
        const url = basePath.replace(/\/$/, '') + `/notifications/stream?lastId=${this.lastEventId}`;

        console.log(`🔌 [REALTIME] Base path: ${basePath}`);
        console.log(`🔌 [REALTIME] Stream URL: ${url}`);

        this.eventSource = new EventSource(url);

        // Handle incoming notifications
        this.eventSource.addEventListener('notification', (event) => {
            try {
                const data = JSON.parse(event.data);
                console.log('📨 [REALTIME] New notification received:', data);

                this.lastEventId = data.id;
                this.handleNotification(data);
                this.reconnectAttempts = 0; // Reset on success
            } catch (error) {
                console.error('❌ [REALTIME] Error parsing notification:', error);
            }
        });

        // Handle stream open
        this.eventSource.addEventListener('open', () => {
            console.log('✅ [REALTIME] Connected to notification stream');
            this.isConnected = true;
            this.reconnectAttempts = 0;
            this.updateConnectionStatus(true);
        });

        // Handle errors
        this.eventSource.addEventListener('error', (event) => {
            console.error('❌ [REALTIME] Stream error:', event);
            this.isConnected = false;
            this.updateConnectionStatus(false);

            // Attempt reconnect
            if (this.reconnectAttempts < this.maxReconnectAttempts) {
                this.reconnectAttempts++;
                console.log(`🔄 [REALTIME] Reconnecting in ${this.reconnectDelay / 1000}s... (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
                setTimeout(() => this.connectToStream(), this.reconnectDelay);
            } else {
                console.error('❌ [REALTIME] Max reconnection attempts exceeded');
            }
        });

        // Handle stream close
        this.eventSource.addEventListener('close', () => {
            console.log('🔚 [REALTIME] Stream closed');
            this.isConnected = false;
            this.updateConnectionStatus(false);
        });
    }

    /**
     * Handle incoming notification
     */
    handleNotification(data) {
        // Update notification count
        this.updateNotificationCount();

        // Show toast notification
        this.showToast(data);

        // Play sound
        if (this.notificationSound) {
            this.playNotificationSound();
        }

        // Show browser notification
        if (this.browserNotificationsEnabled) {
            this.showBrowserNotification(data);
        }

        // Update any open notification panels
        this.updateNotificationPanel(data);

        // Trigger custom event for other listeners
        window.dispatchEvent(new CustomEvent('notification-received', { detail: data }));
    }

    /**
     * Show toast notification
     */
    showToast(data) {
        const toastContainer = document.getElementById('notificationToastContainer') || this.createToastContainer();

        const toastElement = document.createElement('div');
        toastElement.className = 'realtime-toast-notification';
        toastElement.innerHTML = `
            <div class="toast-content">
                <div class="toast-icon">${this.getIconForType(data.type)}</div>
                <div class="toast-message-container">
                    <div class="toast-message">${this.escapeHtml(data.message)}</div>
                    <div class="toast-timestamp">${this.getTimeAgo(data.timestamp)}</div>
                </div>
                <button class="toast-close" aria-label="Close">✕</button>
            </div>
        `;

        toastContainer.appendChild(toastElement);

        // Auto-dismiss after 8 seconds
        const timeout = setTimeout(() => {
            toastElement.classList.add('dismissing');
            setTimeout(() => toastElement.remove(), 300);
        }, 8000);

        // Close button
        toastElement.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(timeout);
            toastElement.classList.add('dismissing');
            setTimeout(() => toastElement.remove(), 300);
        });

        // Click to navigate to issue
        if (data.issueId) {
            toastElement.addEventListener('click', (e) => {
                if (!e.target.closest('.toast-close')) {
                    const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
                    const basePathClean = basePath.replace(/\/$/, '');
                    window.location.href = basePathClean + `/issues/${data.issueId}`;
                }
            });
        }

        console.log('🍞 [REALTIME] Toast notification shown');
    }

    /**
     * Render a notification item HTML
     */
    renderNotificationItem(n) {
        const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
        const basePathClean = basePath.replace(/\/$/, '');
        const actionUrl = n.action_url || n.actionUrl || '#';
        const title = n.title || this.getIconForType(n.type) + ' Notification';
        const message = n.message || '';
        const timestamp = n.timestamp || n.created_at;
        const isRead = n.is_read || n.read_at || false;

        return `
            <a href="${actionUrl}" class="dropdown-item d-flex align-items-start gap-2 py-2" 
               style="text-decoration: none;" data-notification-id="${n.id}">
                <div style="flex: 1; border-left: 3px solid ${isRead ? 'transparent' : 'var(--jira-blue)'}; padding-left: 8px;">
                    <div class="small fw-semibold text-dark">${this.escapeHtml(title)}</div>
                    <div class="text-muted" style="font-size: 12px;">
                        ${message ? this.escapeHtml(message).substring(0, 60) + (message.length > 60 ? '...' : '') : ''}
                    </div>
                    <div class="text-muted" style="font-size: 11px; margin-top: 4px;">
                        ${this.getTimeAgo(timestamp)}
                    </div>
                </div>
                ${!isRead ? '<span class="badge bg-primary ms-2" style="font-size: 10px;">New</span>' : ''}
            </a>
        `;
    }

    /**
     * Show browser notification
     */
    showBrowserNotification(data) {
        if (!('Notification' in window) || Notification.permission !== 'granted') {
            return;
        }

        const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
        const basePathClean = basePath.replace(/\/$/, '');

        const notification = new Notification('Jira Clone', {
            body: data.message,
            icon: basePathClean + '/assets/images/logo.png',
            badge: basePathClean + '/assets/images/badge.png',
            tag: `notification-${data.id}`,
            requireInteraction: false,
        });

        // Click handler
        notification.addEventListener('click', () => {
            window.focus();
            if (data.issueId) {
                window.location.href = basePathClean + `/issues/${data.issueId}`;
            }
            notification.close();
        });

        console.log('🔔 [REALTIME] Browser notification shown');
    }

    /**
     * Update notification count badge
     */
    updateNotificationCount() {
        const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
        const url = basePath.replace(/\/$/, '') + '/api/v1/notifications/stats';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const unreadCount = data.unreadCount || (data.data ? data.data.unread : 0) || 0;
                const badge = document.getElementById('unreadBadge');
                if (badge) {
                    badge.textContent = unreadCount;
                    badge.style.display = unreadCount > 0 ? 'inline-block' : 'none';
                }

                const dropdown = document.getElementById('notificationDropdown');
                if (dropdown) {
                    dropdown.setAttribute('data-unread', unreadCount);
                }

                console.log(`📊 [REALTIME] Updated notification count: ${unreadCount}`);
            })
            .catch(err => console.error('❌ [REALTIME] Error updating count:', err));
    }

    /**
     * Update notification panel if open
        const panel = document.getElementById('notificationDropdown');
        if (!panel) return;

        // Add to top of list
        const panelContent = panel.querySelector('#notificationList');
        if (panelContent) {
            // Check if this ID is already in the list to prevent duplicates
            if (panelContent.querySelector(`[data-notification-id="${data.id}"]`)) {
                return;
            }

            // Remove "No notifications" message if it exists
            const emptyMsg = panelContent.querySelector('.text-muted');
            if (emptyMsg && (emptyMsg.textContent.includes('No notifications') || emptyMsg.textContent.includes('Loading...'))) {
                panelContent.innerHTML = '';
            }
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = this.renderNotificationItem(data);
            const notificationElement = tempDiv.firstElementChild;
            
            panelContent.insertBefore(notificationElement, panelContent.firstChild);
            
            // Limit to 10 items in dropdown
            while (panelContent.children.length > 10) {
                panelContent.lastElementChild.remove();
            }
        }
    }

    /**
     * Load recent notifications on init
     */
    loadRecentNotifications() {
        const basePath = document.querySelector('meta[name="app-base-path"]')?.content || '/';
        const url = basePath.replace(/\/$/, '') + '/api/v1/notifications?limit=10';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                const notifications = data.data || data.notifications || [];
                if (notifications.length > 0) {
                    this.lastEventId = Math.max(...notifications.map(n => n.id)) || 0;
                    console.log(`📥 [REALTIME] Loaded ${notifications.length} recent notifications (lastId: ${this.lastEventId})`);

                    const panelContent = document.getElementById('notificationList');
                    if (panelContent) {
                        panelContent.innerHTML = notifications.map(n => this.renderNotificationItem(n)).join('');
                    }
                } else {
                    const panelContent = document.getElementById('notificationList');
                    if (panelContent) {
                        panelContent.innerHTML = '<div class="px-3 py-3 text-center text-muted"><small>No notifications</small></div>';
                    }
                }
            })
            .catch(err => {
                console.error('❌ [REALTIME] Error loading notifications:', err);
                const panelContent = document.getElementById('notificationList');
                if (panelContent) {
                    panelContent.innerHTML = '<div class="px-3 py-3 text-center text-danger"><small>Error loading</small></div>';
                }
            });
    }

    /**
     * Setup UI elements (badge, dropdown, etc.)
     */
    setupUI() {
        // Create toast container if needed
        if (!document.getElementById('notificationToastContainer')) {
            this.createToastContainer();
        }

        // Setup notification bell click refresh
        const bell = document.getElementById('notificationBell');
        if (bell) {
            bell.addEventListener('click', () => {
                // Wait for Bootstrap to toggle dropdown
                setTimeout(() => {
                    const isExpanded = bell.classList.contains('show') || bell.getAttribute('aria-expanded') === 'true';
                    if (isExpanded) {
                        this.loadRecentNotifications();
                        this.updateNotificationCount();
                    }
                }, 100);
            });
        }
    }

    /**
     * Create toast container
     */
    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'notificationToastContainer';
        container.className = 'realtime-toast-container';
        document.body.appendChild(container);
        return container;
    }

    /**
     * Request browser notification permission
     */
    requestBrowserNotificationPermission() {
        if (!('Notification' in window)) {
            console.log('ℹ️ [REALTIME] Browser notifications not supported');
            return;
        }

        if (Notification.permission === 'granted') {
            this.browserNotificationsEnabled = true;
            console.log('✅ [REALTIME] Browser notifications already enabled');
        } else if (Notification.permission !== 'denied') {
            // Ask user
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.browserNotificationsEnabled = true;
                    console.log('✅ [REALTIME] Browser notifications enabled by user');
                }
            });
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        if (!this.notificationSound) return;

        // Check if context exists and is ready
        if (!this.audioContext || !this.hasUserGesture) {
            // Try to init if missing (rare case if initAudio failed silently)
            // but don't force it without gesture
            return;
        }

        // Double check state
        if (this.audioContext.state !== 'running') {
            this.audioContext.resume().catch(() => { });
            // If still not running, abort
            if (this.audioContext.state !== 'running') return;
        }

        // Simple beep using Web Audio API
        try {
            const oscillator = this.audioContext.createOscillator();
            const gainNode = this.audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(this.audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, this.audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.1);

            oscillator.start(this.audioContext.currentTime);
            oscillator.stop(this.audioContext.currentTime + 0.1);

            console.log('🔊 [REALTIME] Notification sound played');
        } catch (error) {
            // Silently fail for audio context issues
            console.warn('⚠️ [REALTIME] Audio playback failed:', error);
        }
    }

    /**
     * Update connection status indicator
     */
    updateConnectionStatus(isConnected) {
        const indicator = document.getElementById('realtimeConnectionStatus');
        if (indicator) {
            indicator.classList.toggle('connected', isConnected);
            indicator.classList.toggle('disconnected', !isConnected);
            indicator.title = isConnected ? 'Real-time notifications connected' : 'Real-time notifications disconnected';
        }
    }

    /**
     * Get icon for notification type
     */
    getIconForType(type) {
        const icons = {
            'issue_assigned': '🎯',
            'issue_commented': '💬',
            'issue_status_changed': '✅',
            'issue_created': '📝',
            'issue_mentioned': '👤',
            'issue_watched': '👁️',
            'comment_reply': '↩️',
            'worklog_added': '⏱️',
        };
        return icons[type] || '🔔';
    }

    /**
     * Get time ago string
     */
    getTimeAgo(timestamp) {
        const now = new Date();
        const then = new Date(timestamp);
        const seconds = Math.floor((now - then) / 1000);

        if (seconds < 60) return 'just now';
        if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
        if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
        return `${Math.floor(seconds / 86400)}d ago`;
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Disconnect (cleanup)
     */
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.isConnected = false;
            console.log('🔌 [REALTIME] Disconnected from notification stream');
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    window.realtimeNotifications = new RealtimeNotifications();
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.realtimeNotifications) {
        window.realtimeNotifications.disconnect();
    }
});
