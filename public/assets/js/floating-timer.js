/**
 * Floating Timer Widget
 * 
 * Displays a floating timer widget that:
 * - Shows elapsed time in HH:MM:SS format
 * - Displays live cost calculation
 * - Allows pause/resume/stop controls
 * - Persists across page navigation
 * - Syncs with server every 5 seconds
 * 
 * Usage:
 * FloatingTimer.init();
 * FloatingTimer.startTimer(issueId, projectId);
 * FloatingTimer.pauseTimer();
 * FloatingTimer.resumeTimer();
 * FloatingTimer.stopTimer(description);
 */

const FloatingTimer = (() => {
    let config = {
        apiBaseUrl: '/api/v1/time-tracking',
        syncInterval: 5000, // Sync with server every 5 seconds
        containerSelector: 'body'
    };

    let state = {
        isRunning: false,
        isPaused: false,
        elapsedSeconds: 0,
        startTime: null,
        timeLogId: null,
        issueId: null,
        issueSummary: '',
        issueKey: '',
        rateAmount: 0,
        rateType: 'hourly',
        timerInterval: null,
        syncInterval: null
    };

    let elements = {
        container: null,
        timerDisplay: null,
        costDisplay: null,
        issueDisplay: null,
        startBtn: null,
        pauseBtn: null,
        resumeBtn: null,
        stopBtn: null,
        minimizeBtn: null
    };

    /**
     * Initialize the floating timer
     */
    function init(customConfig = {}) {
        config = { ...config, ...customConfig };
        
        createTimerHTML();
        cacheElements();
        attachEventListeners();
        checkExistingTimer();
        
        console.log('[FloatingTimer] Initialized');
    }

    /**
     * Create the floating timer HTML
     */
    function createTimerHTML() {
        const html = `
            <div id="floating-timer" class="floating-timer floating-timer--hidden">
                <div class="floating-timer__header">
                    <div class="floating-timer__issue">
                        <span class="floating-timer__issue-key">-</span>
                        <span class="floating-timer__issue-summary">-</span>
                    </div>
                    <button class="floating-timer__minimize-btn" title="Minimize">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <div class="floating-timer__body">
                    <div class="floating-timer__time-display">
                        <div class="floating-timer__time">00:00:00</div>
                        <div class="floating-timer__status">Stopped</div>
                    </div>

                    <div class="floating-timer__cost-display">
                        <span class="floating-timer__cost-label">Cost:</span>
                        <span class="floating-timer__cost">$0.00</span>
                    </div>

                    <div class="floating-timer__controls">
                        <button class="floating-timer__btn floating-timer__btn--start" title="Start Timer">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <button class="floating-timer__btn floating-timer__btn--pause" title="Pause Timer" style="display:none;">
                            <i class="bi bi-pause-fill"></i>
                        </button>
                        <button class="floating-timer__btn floating-timer__btn--resume" title="Resume Timer" style="display:none;">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <button class="floating-timer__btn floating-timer__btn--stop" title="Stop Timer">
                            <i class="bi bi-stop-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.querySelector(config.containerSelector).insertAdjacentHTML('beforeend', html);
    }

    /**
     * Cache DOM elements
     */
    function cacheElements() {
        elements = {
            container: document.getElementById('floating-timer'),
            timerDisplay: document.querySelector('.floating-timer__time'),
            costDisplay: document.querySelector('.floating-timer__cost'),
            issueDisplay: document.querySelector('.floating-timer__issue-key'),
            issueSummaryDisplay: document.querySelector('.floating-timer__issue-summary'),
            statusDisplay: document.querySelector('.floating-timer__status'),
            startBtn: document.querySelector('.floating-timer__btn--start'),
            pauseBtn: document.querySelector('.floating-timer__btn--pause'),
            resumeBtn: document.querySelector('.floating-timer__btn--resume'),
            stopBtn: document.querySelector('.floating-timer__btn--stop'),
            minimizeBtn: document.querySelector('.floating-timer__minimize-btn')
        };
    }

    /**
     * Attach event listeners
     */
    function attachEventListeners() {
        elements.startBtn.addEventListener('click', handleStartClick);
        elements.pauseBtn.addEventListener('click', () => pauseTimer());
        elements.resumeBtn.addEventListener('click', () => resumeTimer());
        elements.stopBtn.addEventListener('click', handleStopClick);
        elements.minimizeBtn.addEventListener('click', toggleMinimize);
    }

    /**
     * Check if there's an existing running timer on page load
     */
    async function checkExistingTimer() {
        try {
            const response = await fetch(`${config.apiBaseUrl}/status`);
            const data = await response.json();

            if (data.status === 'running') {
                state.isRunning = true;
                state.timeLogId = data.time_log_id;
                state.issueId = data.issue_id;
                state.issueKey = data.issue_key;
                state.issueSummary = data.issue_summary;
                state.startTime = data.started_at * 1000;
                state.elapsedSeconds = data.elapsed_seconds;
                state.rateType = data.rate_type;
                state.rateAmount = data.rate_amount;

                updateDisplay();
                showTimer();
                startTimerTick();
                startSync();
            }
        } catch (error) {
            console.warn('[FloatingTimer] Could not check existing timer:', error);
        }
    }

    /**
     * Handle start button click (modal or prompt for issue)
     */
    function handleStartClick() {
        // In a real implementation, this would open a modal to select an issue
        // For now, we'll assume the issue is being worked on
        console.log('[FloatingTimer] Start clicked - should show issue selector');
    }

    /**
     * Start timer for specific issue
     */
    async function startTimer(issueId, projectId, issueSummary, issueKey) {
        try {
            const response = await fetch(`${config.apiBaseUrl}/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({
                    issue_id: issueId,
                    project_id: projectId
                })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.error || 'Failed to start timer');
            }

            const data = await response.json();

            state.isRunning = true;
            state.isPaused = false;
            state.timeLogId = data.time_log_id;
            state.issueId = issueId;
            state.issueSummary = issueSummary || 'Issue';
            state.issueKey = issueKey || 'ISSUE';
            state.startTime = data.start_time * 1000;
            state.elapsedSeconds = 0;

            updateDisplay();
            showTimer();
            startTimerTick();
            startSync();

            console.log('[FloatingTimer] Timer started for issue:', issueKey);
            showNotification('Timer started', 'success');
        } catch (error) {
            console.error('[FloatingTimer] Error starting timer:', error);
            showNotification(error.message, 'error');
        }
    }

    /**
     * Pause timer
     */
    async function pauseTimer() {
        try {
            const response = await fetch(`${config.apiBaseUrl}/pause`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                }
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.error || 'Failed to pause timer');
            }

            state.isPaused = true;
            stopTimerTick();

            updateDisplay();
            showNotification('Timer paused', 'info');

            console.log('[FloatingTimer] Timer paused');
        } catch (error) {
            console.error('[FloatingTimer] Error pausing timer:', error);
            showNotification(error.message, 'error');
        }
    }

    /**
     * Resume timer
     */
    async function resumeTimer() {
        try {
            const response = await fetch(`${config.apiBaseUrl}/resume`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                }
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.error || 'Failed to resume timer');
            }

            const data = await response.json();

            state.isPaused = false;
            state.startTime = data.start_time ? data.start_time * 1000 : Date.now();
            state.elapsedSeconds = data.elapsed_seconds || 0;

            updateDisplay();
            startTimerTick();
            showNotification('Timer resumed', 'success');

            console.log('[FloatingTimer] Timer resumed');
        } catch (error) {
            console.error('[FloatingTimer] Error resuming timer:', error);
            showNotification(error.message, 'error');
        }
    }

    /**
     * Handle stop button click (show confirmation or description modal)
     */
    function handleStopClick() {
        // Show prompt for description
        const description = prompt('What were you working on? (optional)', '');
        if (description !== null) {
            stopTimer(description || null);
        }
    }

    /**
     * Stop timer
     */
    async function stopTimer(description = null) {
        try {
            const response = await fetch(`${config.apiBaseUrl}/stop`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': getCsrfToken()
                },
                body: JSON.stringify({
                    description: description
                })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.error || 'Failed to stop timer');
            }

            const data = await response.json();

            // Reset state
            state.isRunning = false;
            state.isPaused = false;
            state.elapsedSeconds = 0;
            state.timeLogId = null;
            state.issueId = null;

            stopTimerTick();
            stopSync();
            hideTimer();

            console.log('[FloatingTimer] Timer stopped');
            showNotification(
                `Logged ${formatSeconds(data.elapsed_seconds)} for $${data.cost.toFixed(2)}`,
                'success'
            );
        } catch (error) {
            console.error('[FloatingTimer] Error stopping timer:', error);
            showNotification(error.message, 'error');
        }
    }

    /**
     * Start the timer tick (updates display every second)
     */
    function startTimerTick() {
        if (state.timerInterval) {
            clearInterval(state.timerInterval);
        }

        state.timerInterval = setInterval(() => {
            if (!state.isPaused && state.isRunning) {
                const now = Date.now();
                const elapsed = Math.floor((now - state.startTime) / 1000);
                state.elapsedSeconds = elapsed;
                updateDisplay();
            }
        }, 1000);
    }

    /**
     * Stop the timer tick
     */
    function stopTimerTick() {
        if (state.timerInterval) {
            clearInterval(state.timerInterval);
            state.timerInterval = null;
        }
    }

    /**
     * Start syncing with server
     */
    function startSync() {
        if (state.syncInterval) {
            clearInterval(state.syncInterval);
        }

        state.syncInterval = setInterval(syncWithServer, config.syncInterval);
    }

    /**
     * Stop syncing with server
     */
    function stopSync() {
        if (state.syncInterval) {
            clearInterval(state.syncInterval);
            state.syncInterval = null;
        }
    }

    /**
     * Sync timer state with server
     */
    async function syncWithServer() {
        try {
            const response = await fetch(`${config.apiBaseUrl}/status`);
            const data = await response.json();

            if (data.status === 'stopped') {
                // Timer was stopped on another tab/window
                state.isRunning = false;
                stopTimerTick();
                hideTimer();
            } else if (data.status === 'running' && data.elapsed_seconds) {
                // Update elapsed seconds from server
                state.elapsedSeconds = data.elapsed_seconds;
            }
        } catch (error) {
            console.warn('[FloatingTimer] Sync error:', error);
        }
    }

    /**
     * Update the display
     */
    function updateDisplay() {
        // Update time display
        elements.timerDisplay.textContent = formatSeconds(state.elapsedSeconds);

        // Update cost display
        let cost = 0;
        if (state.rateType === 'hourly') {
            cost = (state.elapsedSeconds / 3600) * state.rateAmount;
        } else if (state.rateType === 'minutely') {
            cost = (state.elapsedSeconds / 60) * state.rateAmount;
        } else if (state.rateType === 'secondly') {
            cost = state.elapsedSeconds * state.rateAmount;
        }
        elements.costDisplay.textContent = `$${cost.toFixed(2)}`;

        // Update issue display
        elements.issueDisplay.textContent = state.issueKey;
        elements.issueSummaryDisplay.textContent = state.issueSummary;

        // Update status and buttons
        if (state.isRunning) {
            if (state.isPaused) {
                elements.statusDisplay.textContent = 'Paused';
                elements.pauseBtn.style.display = 'none';
                elements.resumeBtn.style.display = 'inline-block';
                elements.startBtn.style.display = 'none';
            } else {
                elements.statusDisplay.textContent = 'Running';
                elements.pauseBtn.style.display = 'inline-block';
                elements.resumeBtn.style.display = 'none';
                elements.startBtn.style.display = 'none';
            }
        } else {
            elements.statusDisplay.textContent = 'Stopped';
            elements.pauseBtn.style.display = 'none';
            elements.resumeBtn.style.display = 'none';
            elements.startBtn.style.display = 'inline-block';
        }
    }

    /**
     * Show the timer widget
     */
    function showTimer() {
        elements.container.classList.remove('floating-timer--hidden');
        elements.container.classList.add('floating-timer--visible');
    }

    /**
     * Hide the timer widget
     */
    function hideTimer() {
        elements.container.classList.remove('floating-timer--visible');
        elements.container.classList.add('floating-timer--hidden');
    }

    /**
     * Toggle minimize state
     */
    function toggleMinimize() {
        elements.container.classList.toggle('floating-timer--minimized');
    }

    /**
     * Format seconds to HH:MM:SS
     */
    function formatSeconds(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        return [hours, minutes, secs]
            .map(v => String(v).padStart(2, '0'))
            .join(':');
    }

    /**
     * Get CSRF token from meta tag
     */
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    /**
     * Show notification toast
     */
    function showNotification(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `floating-timer__toast floating-timer__toast--${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('floating-timer__toast--show');
        }, 10);

        setTimeout(() => {
            toast.classList.remove('floating-timer__toast--show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Public API
    return {
        init,
        startTimer,
        pauseTimer,
        resumeTimer,
        stopTimer,
        getState: () => ({ ...state }),
        getConfig: () => ({ ...config })
    };
})();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    FloatingTimer.init();
});
