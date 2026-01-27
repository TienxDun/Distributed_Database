<?php
/**
 * Router for PHP built-in server
 * Handles both API routes and UI files for monolithic deployment (Render)
 */

require_once __DIR__ . '/../request_logger.php';

// Start request logging
RequestLogger::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 1. Serve static files directly if they exist
if (file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false; // Let PHP built-in server handle the static file or PHP file
}

// 2. Map root to ui.php
if ($uri === '/') {
    require_once __DIR__ . '/ui.php';
    exit;
}

// 3. Fallback for API routes (handled by index.php)
require_once __DIR__ . '/index.php';
