<?php
function getDBConnection()
{
    $dbHost = getenv('DB_HOST');
    $dbPort = getenv('DB_PORT');
    $dbName = getenv('DB_NAME');
    $dbUser = getenv('DB_USER');
    $dbPass = getenv('DB_PASS');

    $dsn = "pgsql:host={$dbHost};port={$dbPort};dbname={$dbName}";

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
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

function sendResponse($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function getJsonInput()
{
    return json_decode(file_get_contents('php://input'), true);
}
?>