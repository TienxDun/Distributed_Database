<?php
require_once '../common.php';

function handleKhoa($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['id'])) {
                    $stmt = $pdo->prepare("SELECT * FROM Khoa_Global WHERE MaKhoa = ?");
                    $stmt->execute([$query['id']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    sendResponse($result ?: ['error' => 'Not found'], $result ? 200 : 404);
                } else {
                    $stmt = $pdo->query("SELECT * FROM Khoa_Global");
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;
            case 'POST':
                sendResponse(['error' => 'CRUD not supported on global views'], 405);
                break;
            case 'PUT':
                sendResponse(['error' => 'CRUD not supported on global views'], 405);
                break;
            case 'DELETE':
                sendResponse(['error' => 'CRUD not supported on global views'], 405);
                break;
            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>