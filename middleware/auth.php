<?php

/**
 * Authentication Middleware Functions
 * 
 * Usage:
 * require_once '../middleware/auth.php';
 * 
 * requireLogin();      // Check if user is logged in
 * requireAdmin();      // Check if user is admin
 * requireRole('user'); // Check if user has specific role
 */

require_once __DIR__ . '/../includes/flash.php';

/**
 * Check if user is logged in
 * Redirects to index.php if not logged in
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        setFlash('error', 'Please log in to access this page.');
        header('Location: ' . getRootPath() . '/index.php');
        exit();
    }
}

/**
 * Check if user is admin
 * Shows 403 error if user is not admin
 */
function requireAdmin() {
    // First check if logged in
    if (!isset($_SESSION['user_id'])) {
        setFlash('error', 'Please log in to access this page.');
        header('Location: ' . getRootPath() . '/index.php');
        exit();
    }

    // Then check if admin
    if ($_SESSION['role'] !== 'admin') {
        setFlash('error', 'Access denied. Only administrators can access this page.');
        header('Location: ' . getRootPath() . '/index.php');
        exit();
    }
}

/**
 * Check if user has specific role
 * @param string|array $roles - Single role or array of roles
 */
function requireRole($roles) {
    // First check if logged in
    if (!isset($_SESSION['user_id'])) {
        setFlash('error', 'Please log in to access this page.');
        header('Location: ' . getRootPath() . '/index.php');
        exit();
    }

    // Convert single role to array
    $allowedRoles = is_array($roles) ? $roles : [$roles];

    // Check if user role is in allowed roles
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        setFlash('error', 'Access denied. You do not have permission to access this page.');
        header('Location: ' . getRootPath() . '/index.php');
        exit();
    }
}

/**
 * Check if user is logged in (without redirect)
 * Returns true/false
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin (without redirect)
 * Returns true/false
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user name
 */
function getCurrentUserName() {
    return $_SESSION['name'] ?? 'Guest';
}

/**
 * Helper function to get root path
 */
function getRootPath() {
    // Adjust based on current file location
    $currentDir = dirname(__FILE__);
    // Go up one level from middleware
    return dirname($currentDir);
}

?>
