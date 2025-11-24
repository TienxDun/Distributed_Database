<?php
/**
 * Router for API PHP server
 * Only allows API endpoints, blocks UI files
 */

require_once __DIR__ . '/../request_logger.php';

// Start request logging
RequestLogger::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// List of blocked UI files
$blockedFiles = [
    'ui.php',
    'logs.php', 
    'stats.php',
    'index.php'
];

// Check if request is for a blocked file
foreach ($blockedFiles as $file) {
    if (strpos($uri, $file) !== false) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Not Found - This is an API server. UI files are served at http://localhost:8081'
        ]);
        exit; // Must exit to prevent PHP from processing the file
    }
}

// Allow all other requests (API endpoints, CSS, JS, etc.)
return false;
