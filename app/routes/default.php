<?php
require_once __DIR__ . '/../Core/common.php';

function handleDefault($method, $query) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    if ($path === '/' || $path === '') {
        sendResponse([
            'name' => 'HUFLIT Distributed Database API',
            'version' => '2.0.0',
            'status' => 'online',
            'endpoints' => [
                '/khoa', '/monhoc', '/sinhvien', '/dangky', '/ctdaotao', '/global', '/logs', '/stats'
            ]
        ]);
        return;
    }
    
    sendResponse(['error' => 'Endpoint not found'], 404);
}
?>