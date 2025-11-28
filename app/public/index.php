<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Cache-Control, Pragma, Expires');
header('Access-Control-Max-Age: 3600'); // Cache preflight for 1 hour

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit(0);
}

require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../request_logger.php';

// Start logging
RequestLogger::start();

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = $_GET;

$routes = [
    '/khoa' => 'khoa',
    '/monhoc' => 'monhoc',
    '/sinhvien' => 'sinhvien',
    '/dangky' => 'dangky',
    '/ctdaotao' => 'ctdaotao',
    '/global' => 'global',
    '/logs' => 'logs',
    '/stats' => 'stats',
];

$method = $_SERVER['REQUEST_METHOD'];

if (array_key_exists($path, $routes)) {
    $routeFile = './routes/' . $routes[$path] . '.php';
    if (file_exists($routeFile)) {
        require_once $routeFile;
        $functionName = 'handle' . ucfirst($routes[$path]);
        if (function_exists($functionName)) {
            $functionName($method, $query);
        } else {
            sendResponse(['error' => 'Handler not found'], 500);
        }
    } else {
        sendResponse(['error' => 'Route file not found'], 500);
    }
} else {
    require_once './routes/default.php';
    handleDefault($method, $query);
}
?>