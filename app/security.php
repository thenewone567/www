<?php
/**
 * Security Helper Functions
 */

/**
 * Sanitize input data
 * @param mixed $data Input data
 * @param string $type Type of sanitization
 * @return mixed Sanitized data
 */
function sanitizeInput($data, $type = "string") {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $data);
    }
    
    switch ($type) {
        case "email":
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        case "url":
            return filter_var($data, FILTER_SANITIZE_URL);
        case "int":
            return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
        case "float":
            return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case "string":
        default:
            return htmlspecialchars(trim($data), ENT_QUOTES, "UTF-8");
    }
}

/**
 * Validate input data
 * @param mixed $data Input data
 * @param string $type Type of validation
 * @return bool True if valid
 */
function validateInput($data, $type = "string") {
    switch ($type) {
        case "email":
            return filter_var($data, FILTER_VALIDATE_EMAIL) !== false;
        case "url":
            return filter_var($data, FILTER_VALIDATE_URL) !== false;
        case "int":
            return filter_var($data, FILTER_VALIDATE_INT) !== false;
        case "float":
            return filter_var($data, FILTER_VALIDATE_FLOAT) !== false;
        case "required":
            return !empty(trim($data));
        default:
            return is_string($data);
    }
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if valid
 */
function verifyCSRFToken($token) {
    return isset($_SESSION["csrf_token"]) && hash_equals($_SESSION["csrf_token"], $token);
}

/**
 * Escape output for HTML
 * @param string $data Data to escape
 * @return string Escaped data
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, "UTF-8");
}
