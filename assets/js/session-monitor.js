/**
 * Client-Side Session Management
 * Monitors session status and handles timeouts gracefully
 */

class SessionMonitor {
    constructor(options = {}) {
        this.checkInterval = options.checkInterval || 60000; // Check every minute
        this.warningTime = options.warningTime || 300; // Warn when 5 minutes left
        this.sessionUrl = options.sessionUrl || '/session-status.php';
        this.loginUrl = options.loginUrl || '/users/login';

        this.intervalId = null;
        this.warningShown = false;
        this.modalElement = null;

        this.init();
    }

    init() {
        this.startMonitoring();
        this.setupActivityListeners();
        this.createWarningModal();
    }

    startMonitoring() {
        this.intervalId = setInterval(() => {
            this.checkSessionStatus();
        }, this.checkInterval);
    }

    stopMonitoring() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }

    async checkSessionStatus() {
        try {
            const response = await fetch(this.sessionUrl, {
                method: 'GET',
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();

                if (!data.logged_in) {
                    this.handleSessionExpired();
                } else if (data.time_remaining <= this.warningTime && !this.warningShown) {
                    this.showSessionWarning(data.time_remaining);
                } else if (data.time_remaining > this.warningTime && this.warningShown) {
                    this.hideSessionWarning();
                }
            }
        } catch (error) {
            console.warn('Failed to check session status:', error);
        }
    }

    setupActivityListeners() {
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];

        events.forEach(event => {
            document.addEventListener(event, () => {
                this.extendSession();
            }, { passive: true, once: false });
        });
    }

    async extendSession() {
        try {
            await fetch('/extend-session.php', {
                method: 'POST',
                credentials: 'same-origin'
            });
        } catch (error) {
            console.warn('Failed to extend session:', error);
        }
    }

    createWarningModal() {
        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="sessionWarningModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Session Expiring Soon
                            </h5>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-clock fa-3x text-warning"></i>
                                </div>
                                <h5>Your session will expire in <span id="countdown"></span></h5>
                                <p class="text-muted">
                                    Click "Stay Logged In" to continue your session, or you will be automatically logged out.
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="stayLoggedInBtn">
                                <i class="fas fa-refresh mr-2"></i>Stay Logged In
                            </button>
                            <button type="button" class="btn btn-secondary" id="logoutNowBtn">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to body
        const modalDiv = document.createElement('div');
        modalDiv.innerHTML = modalHtml;
        document.body.appendChild(modalDiv);

        this.modalElement = document.getElementById('sessionWarningModal');

        // Setup button handlers
        document.getElementById('stayLoggedInBtn').addEventListener('click', () => {
            this.extendSession();
            this.hideSessionWarning();
            this.showSuccessMessage('Session extended successfully!');
        });

        document.getElementById('logoutNowBtn').addEventListener('click', () => {
            this.logout();
        });
    }

    showSessionWarning(timeRemaining) {
        this.warningShown = true;

        if (this.modalElement && typeof $ !== 'undefined') {
            $('#sessionWarningModal').modal('show');

            // Start countdown
            this.startCountdown(timeRemaining);
        }
    }

    hideSessionWarning() {
        this.warningShown = false;

        if (this.modalElement && typeof $ !== 'undefined') {
            $('#sessionWarningModal').modal('hide');
        }
    }

    startCountdown(seconds) {
        const countdownElement = document.getElementById('countdown');

        const updateCountdown = () => {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;

            countdownElement.textContent =
                `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;

            if (seconds <= 0) {
                this.handleSessionExpired();
                return;
            }

            seconds--;
            setTimeout(updateCountdown, 1000);
        };

        updateCountdown();
    }

    handleSessionExpired() {
        this.stopMonitoring();

        // Show expiration message
        this.showExpiredMessage();

        // Redirect to login after delay
        setTimeout(() => {
            window.location.href = this.loginUrl;
        }, 3000);
    }

    showExpiredMessage() {
        const message = `
            <div class="alert alert-danger alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Session Expired!</strong><br>
                You will be redirected to login page in 3 seconds.
                <button type="button" class="close" onclick="window.location.href='${this.loginUrl}'">
                    <span>&times;</span>
                </button>
            </div>
        `;

        const alertDiv = document.createElement('div');
        alertDiv.innerHTML = message;
        document.body.appendChild(alertDiv);
    }

    showSuccessMessage(text) {
        const message = `
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas fa-check-circle mr-2"></i>
                ${text}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;

        const alertDiv = document.createElement('div');
        alertDiv.innerHTML = message;
        document.body.appendChild(alertDiv);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 3000);
    }

    logout() {
        window.location.href = '/users/logout';
    }
}

// Initialize session monitor when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Only initialize if user is logged in (check for user_id in session)
    if (typeof URLROOT !== 'undefined') {
        window.sessionMonitor = new SessionMonitor({
            sessionUrl: URLROOT + '/session-status.php',
            loginUrl: URLROOT + '/users/login',
            checkInterval: 60000, // 1 minute
            warningTime: 300 // 5 minutes
        });
    }
});
