<?php
/**
 * Enhanced Session Management System
 * Handles session timeout, security, and proper session lifecycle
 */

class SessionManager
{
    // Session timeout in seconds (30 minutes)
    private static $timeout = 1800; // 30 minutes

    // Session refresh interval in seconds (5 minutes)
    private static $refreshInterval = 300; // 5 minutes

    /**
     * Initialize secure session with proper configuration
     */
    public static function init()
    {
        // Configure session settings for security
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');

        // Set session lifetime
        ini_set('session.gc_maxlifetime', self::$timeout);
        ini_set('session.cookie_lifetime', self::$timeout);

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        // Initialize session tracking if new session
        if (!isset($_SESSION['session_started'])) {
            self::regenerateSession();
            $_SESSION['session_started'] = time();
            $_SESSION['last_activity'] = time();
            $_SESSION['session_fingerprint'] = self::generateFingerprint();
        }

        // Check session validity
        self::validateSession();
    }

    /**
     * Validate current session for security and timeout
     */
    public static function validateSession()
    {
        // Check if session is expired
        if (isset($_SESSION['last_activity'])) {
            $timeSinceLastActivity = time() - $_SESSION['last_activity'];

            if ($timeSinceLastActivity > self::$timeout) {
                self::destroy('Session expired due to inactivity');
                return false;
            }
        }

        // Check session fingerprint for security
        if (isset($_SESSION['session_fingerprint'])) {
            if ($_SESSION['session_fingerprint'] !== self::generateFingerprint()) {
                self::destroy('Session fingerprint mismatch - possible hijacking attempt');
                return false;
            }
        }

        // Update last activity timestamp
        $_SESSION['last_activity'] = time();

        // Regenerate session ID periodically for security
        if (isset($_SESSION['last_regeneration'])) {
            if ((time() - $_SESSION['last_regeneration']) > self::$refreshInterval) {
                self::regenerateSession();
            }
        }

        return true;
    }

    /**
     * Check if user is logged in with valid session
     */
    public static function isLoggedIn()
    {
        // First validate session
        if (!self::validateSession()) {
            return false;
        }

        // Check if user_id exists and is valid
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return false;
        }

        // Optional: Verify user still exists in database
        // This can be enabled for extra security but may impact performance
        if (defined('VERIFY_USER_EXISTS') && VERIFY_USER_EXISTS) {
            if (!self::verifyUserExists($_SESSION['user_id'])) {
                // If user verification is enabled and the user doesn't exist, treat as not logged in.
                return false;
            }
        }

        return true;
    }

    /**
     * Login user with proper session setup
     */
    public static function login($userId, $userName = null, $userRole = null)
    {
        // Regenerate session ID for security
        self::regenerateSession();

        // Set user session data
        $_SESSION['user_id'] = $userId;
        if ($userName)
            $_SESSION['user_name'] = $userName;
        if ($userRole)
            $_SESSION['user_role'] = $userRole;

        // Set login timestamp
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        // Log successful login
        self::logSessionEvent('login', "User $userId logged in successfully");

        return true;
    }

    /**
     * Logout user and cleanup session
     */
    public static function logout($reason = 'User logout')
    {
        $userId = $_SESSION['user_id'] ?? 'unknown';

        // Log logout
        self::logSessionEvent('logout', "User $userId logged out: $reason");

        // Destroy session
        self::destroy($reason);
    }

    /**
     * Destroy session completely
     */
    public static function destroy($reason = 'Session destroyed')
    {
        // Log session destruction
        self::logSessionEvent('destroy', $reason);

        // Clear session data
        $_SESSION = array();

        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * Regenerate session ID for security
     */
    private static function regenerateSession()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }

    /**
     * Generate session fingerprint for security
     */
    private static function generateFingerprint()
    {
        $fingerprint = '';
        $fingerprint .= $_SERVER['HTTP_USER_AGENT'] ?? '';
        $fingerprint .= $_SERVER['REMOTE_ADDR'] ?? '';
        $fingerprint .= $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';

        return hash('sha256', $fingerprint);
    }

    /**
     * Verify user still exists in database
     */
    private static function verifyUserExists($userId)
    {
        try {
            $db = new Database();
            $db->query('SELECT user_id FROM users WHERE user_id = ? AND status = "active"');
            $db->bind(1, $userId);
            $db->execute();

            return $db->rowCount() > 0;
        } catch (Exception $e) {
            self::logSessionEvent('error', "Failed to verify user exists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get session information for debugging
     */
    public static function getSessionInfo()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return ['status' => 'No active session'];
        }

        return [
            'session_id' => session_id(),
            'status' => session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive',
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_name' => $_SESSION['user_name'] ?? null,
            'login_time' => isset($_SESSION['login_time']) ? date('Y-m-d H:i:s', $_SESSION['login_time']) : null,
            'last_activity' => isset($_SESSION['last_activity']) ? date('Y-m-d H:i:s', $_SESSION['last_activity']) : null,
            'time_remaining' => isset($_SESSION['last_activity']) ? (self::$timeout - (time() - $_SESSION['last_activity'])) : null,
            'is_logged_in' => self::isLoggedIn()
        ];
    }

    /**
     * Extend session timeout (for active users)
     */
    public static function extendSession()
    {
        if (self::isLoggedIn()) {
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }

    /**
     * Check if session is about to expire (within 5 minutes)
     */
    public static function isAboutToExpire()
    {
        if (!isset($_SESSION['last_activity'])) {
            return false;
        }

        $timeRemaining = self::$timeout - (time() - $_SESSION['last_activity']);
        return $timeRemaining <= 300; // 5 minutes
    }

    /**
     * Log session events for debugging and security
     */
    private static function logSessionEvent($event, $message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? 'guest';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $logMessage = "[$timestamp] SESSION_$event: $message (Session: $sessionId, User: $userId, IP: $ip)\n";

        // Log to file if defined
        if (defined('SESSION_LOG_FILE')) {
            file_put_contents(SESSION_LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
        }

        // Also log to main error log
        if (function_exists('logError')) {
            logError("Session $event: $message", [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'ip' => $ip
            ]);
        }
    }

    /**
     * Get timeout settings
     */
    public static function getTimeoutSettings()
    {
        return [
            'timeout_seconds' => self::$timeout,
            'timeout_minutes' => self::$timeout / 60,
            'refresh_interval' => self::$refreshInterval,
            'refresh_minutes' => self::$refreshInterval / 60
        ];
    }

    /**
     * Set custom timeout (in seconds)
     */
    public static function setTimeout($seconds)
    {
        self::$timeout = $seconds;
        ini_set('session.gc_maxlifetime', $seconds);
        ini_set('session.cookie_lifetime', $seconds);
    }
}
?>