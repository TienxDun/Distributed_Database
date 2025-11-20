<?php
require_once '../common.php';

function handleMonHoc($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['id'])) {
                    $stmt = $pdo->prepare("SELECT * FROM MonHoc_Global WHERE MaMH = ?");
                    $stmt->execute([$query['id']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    sendResponse($result ?: ['error' => 'Not found'], $result ? 200 : 404);
                } else {
                    $stmt = $pdo->query("SELECT * FROM MonHoc_Global");
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;
            case 'POST':
                // Create new MonHoc via trigger (syncs to all 3 sites)
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['MaMH']) || !isset($data['TenMH'])) {
                    sendResponse(['error' => 'Missing required fields: MaMH, TenMH'], 400);
                    break;
                }
                $stmt = $pdo->prepare("INSERT INTO MonHoc_Global (MaMH, TenMH) VALUES (?, ?)");
                $stmt->execute([$data['MaMH'], $data['TenMH']]);
                sendResponse(['message' => 'MonHoc created successfully on all sites', 'MaMH' => $data['MaMH']], 201);
                break;
            case 'PUT':
                // Update MonHoc via trigger (syncs to all 3 sites)
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['TenMH'])) {
                    sendResponse(['error' => 'Missing required field: TenMH'], 400);
                    break;
                }
                $stmt = $pdo->prepare("UPDATE MonHoc_Global SET TenMH = ? WHERE MaMH = ?");
                $stmt->execute([$data['TenMH'], $query['id']]);
                sendResponse(['message' => 'MonHoc updated successfully on all sites']);
                break;
            case 'DELETE':
                // Delete MonHoc via trigger (from all 3 sites)
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }
                $stmt = $pdo->prepare("DELETE FROM MonHoc_Global WHERE MaMH = ?");
                $stmt->execute([$query['id']]);
                sendResponse(['message' => 'MonHoc deleted successfully from all sites']);
                break;
            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>