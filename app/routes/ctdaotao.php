<?php
require_once '../common.php';

function handleCTDaoTao($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['makhoa']) && isset($query['khoahoc']) && isset($query['mamh'])) {
                    $stmt = $pdo->prepare("SELECT * FROM CTDaoTao_Global WHERE MaKhoa = ? AND KhoaHoc = ? AND MaMH = ?");
                    $stmt->execute([$query['makhoa'], $query['khoahoc'], $query['mamh']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    sendResponse($result ?: ['error' => 'Not found'], $result ? 200 : 404);
                } else {
                    $stmt = $pdo->query("SELECT * FROM CTDaoTao_Global");
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