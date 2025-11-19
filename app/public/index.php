<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once '../common.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = $_GET;

$routes = [
    '/khoa' => 'khoa',
    '/monhoc' => 'monhoc',
    '/sinhvien' => 'sinhvien',
    '/dangky' => 'dangky',
    '/ctdaotao' => 'ctdaotao',
];

$method = $_SERVER['REQUEST_METHOD'];

if (array_key_exists($path, $routes)) {
    $routeFile = '../routes/' . $routes[$path] . '.php';
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
    require_once '../routes/default.php';
    handleDefault($method, $query);
}
?> 