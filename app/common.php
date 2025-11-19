<?php
function getDBConnection() {
    $dsn = "sqlsrv:Server={$_ENV['DB_HOST']},{$_ENV['DB_PORT']};"
         . "Database={$_ENV['DB_NAME']};TrustServerCertificate=1";

    try {
        $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        return $pdo;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'DB connection failed',
            'detail' => $e->getMessage(),
        ]);
        exit;
    }
}

function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}
?>