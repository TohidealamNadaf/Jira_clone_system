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

        const url = `/jira_clone_system/public/notifications/stream?lastId=${this.lastEventId}`;

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
                    window.location.href = `/jira_clone_system/public/issues/${data.issueId}`;
                }
            });
        }

        console.log('🍞 [REALTIME] Toast notification shown');
    }

    /**
     * Show browser notification
     */
    showBrowserNotification(data) {
        if (!('Notification' in window) || Notification.permission !== 'granted') {
            return;
        }

        const notification = new Notification('Jira Clone', {
            body: data.message,
            icon: '/jira_clone_system/public/assets/images/logo.png',
            badge: '/jira_clone_system/public/assets/images/badge.png',
            tag: `notification-${data.id}`,
            requireInteraction: false,
        });

        // Click handler
        notification.addEventListener('click', () => {
            window.focus();
            if (data.issueId) {
                window.location.href = `/jira_clone_system/public/issues/${data.issueId}`;
            }
            notification.close();
        });

        console.log('🔔 [REALTIME] Browser notification shown');
    }

    /**
     * Update notification count badge
     */
    updateNotificationCount() {
        fetch('/jira_clone_system/public/notifications/unread-count')
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    badge.textContent = data.unreadCount;
                    badge.style.display = data.unreadCount > 0 ? 'flex' : 'none';
                }

                const dropdown = document.getElementById('notificationDropdown');
                if (dropdown) {
                    dropdown.setAttribute('data-unread', data.unreadCount);
                }

                console.log(`📊 [REALTIME] Updated notification count: ${data.unreadCount}`);
            })
            .catch(err => console.error('❌ [REALTIME] Error updating count:', err));
    }

    /**
     * Update notification panel if open
     */
    updateNotificationPanel(data) {
        const panel = document.getElementById('notificationPanel');
        if (!panel) return;

        // Add to top of list
        const notificationItem = document.createElement('div');
        notificationItem.className = 'notification-item unread';
        notificationItem.innerHTML = `
            <div class="notification-icon">${this.getIconForType(data.type)}</div>
            <div class="notification-content">
                <div class="notification-message">${this.escapeHtml(data.message)}</div>
                <div class="notification-timestamp">${this.getTimeAgo(data.timestamp)}</div>
            </div>
        `;

        const panelContent = panel.querySelector('.notification-items');
        if (panelContent) {
            panelContent.insertBefore(notificationItem, panelContent.firstChild);
        }
    }

    /**
     * Load recent notifications on init
     */
    loadRecentNotifications() {
        fetch('/jira_clone_system/public/notifications/recent?limit=10')
            .then(res => res.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    this.lastEventId = Math.max(...data.notifications.map(n => n.id)) || 0;
                    console.log(`📥 [REALTIME] Loaded ${data.notifications.length} recent notifications (lastId: ${this.lastEventId})`);
                }
            })
            .catch(err => console.error('❌ [REALTIME] Error loading notifications:', err));
    }

    /**
     * Setup UI elements (badge, dropdown, etc.)
     */
    setupUI() {
        // Create toast container if needed
        if (!document.getElementById('notificationToastContainer')) {
            this.createToastContainer();
        }

        // Setup notification dropdown if it exists
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) {
            dropdown.addEventListener('click', () => this.updateNotificationCount());
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
        // Simple beep using Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);

            console.log('🔊 [REALTIME] Notification sound played');
        } catch (error) {
            console.log('ℹ️ [REALTIME] Could not play notification sound:', error);
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
