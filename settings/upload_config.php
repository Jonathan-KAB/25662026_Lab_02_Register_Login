<?php
/**
 * Upload Configuration
 * Handles different upload paths for local (XAMPP) and remote (school server) environments
 */

// Detect environment
function getUploadBasePath() {
    // Check if we're on the school server (outside public_html)
    // Look for uploads directory one level up from the web root
    $uploadsOutside = __DIR__ . '/../../uploads';
    
    // Check if we're in a public_html structure
    if (strpos(__DIR__, 'public_html') !== false && is_dir($uploadsOutside)) {
        // School server: uploads is outside public_html
        return realpath($uploadsOutside);
    }
    
    // Local XAMPP: uploads is inside the project
    return __DIR__ . '/../uploads';
}

// Get the web-accessible path for uploaded images
function getUploadWebPath() {
    // Check if we're on the school server
    $uploadsOutside = __DIR__ . '/../../uploads';
    
    if (strpos(__DIR__, 'public_html') !== false && is_dir($uploadsOutside)) {
        // School server: uploads is accessed from root
        return '/uploads';
    }
    
    // Local XAMPP: uploads is relative to project
    return 'uploads';
}

// Define constants
if (!defined('UPLOAD_BASE_PATH')) {
    define('UPLOAD_BASE_PATH', getUploadBasePath());
}

if (!defined('UPLOAD_WEB_PATH')) {
    define('UPLOAD_WEB_PATH', getUploadWebPath());
}
?>