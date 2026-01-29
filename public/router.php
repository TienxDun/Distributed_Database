<?php
/**
 * Router for PHP built-in server
 * Handles both API routes and UI files for monolithic deployment (Render)
 */

require_once __DIR__ . '/../app/Core/request_logger.php';

// Start request logging
RequestLogger::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 1. Serve static files directly if they exist in the public folder
if (file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// 2. Map clean routes to views
$viewRoutes = [
    '/' => 'dashboard.php',
    '/ui.php' => 'dashboard.php',
    '/logs-ui' => 'logs.php',
    '/logs.php' => 'logs.php',
    '/stats-ui' => 'stats.php',
    '/stats.php' => 'stats.php',
    '/maintenance-ui' => 'maintenance.php',
    '/maintenance.php' => 'maintenance.php',
];

// Only serve UI if not on API port (8080) or if explicitly requested via a UI path
$isApiPort = ($_SERVER['SERVER_PORT'] === '8080');
$isUiPath = ($uri !== '/' && $uri !== '/ui.php' && array_key_exists($uri, $viewRoutes));

if (array_key_exists($uri, $viewRoutes)) {
    // If it's the root path on API port, don't serve UI, let it fall through to index.php
    if ($uri === '/' && $isApiPort) {
        // Fall through to index.php
    } else {
        require_once __DIR__ . '/../app/Views/' . $viewRoutes[$uri];
        exit;
    }
}

// 3. Fallback for API routes (handled by index.php)
require_once __DIR__ . '/index.php';
