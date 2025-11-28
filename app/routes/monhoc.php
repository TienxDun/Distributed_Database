<?php
require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../mongo_helper.php';

function handleMonHoc($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['id'])) {
                    $stmt = $pdo->prepare("SELECT * FROM MonHoc_Global WHERE MaMH = ?");
                    $stmt->execute([$query['id']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    RequestLogger::end($result ? 1 : 0, $result ? 200 : 404);
                    sendResponse($result ?: ['error' => 'Not found'], $result ? 200 : 404);
                } else {
                    $stmt = $pdo->query("SELECT * FROM MonHoc_Global");
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    RequestLogger::end(count($result), 200);
                    sendResponse($result);
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
                
                // Log to MongoDB - Global since it's synced to all sites
                MongoHelper::logAudit('MonHoc', 'INSERT', $data, null, 'Global');
                
                RequestLogger::end(1, 201);
                sendResponse(['message' => 'MonHoc created successfully on all sites', 'MaMH' => $data['MaMH']], 201);
                break;
            case 'PUT':
                // Update MonHoc via trigger (syncs to all 3 sites)
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }
                
                // Get old data first
                $stmt = $pdo->prepare("SELECT * FROM MonHoc_Global WHERE MaMH = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['TenMH'])) {
                    sendResponse(['error' => 'Missing required field: TenMH'], 400);
                    break;
                }
                $stmt = $pdo->prepare("UPDATE MonHoc_Global SET TenMH = ? WHERE MaMH = ?");
                $stmt->execute([$data['TenMH'], $query['id']]);
                
                // Log to MongoDB - Global since it's synced to all sites
                $newData = ['MaMH' => $query['id'], 'TenMH' => $data['TenMH']];
                MongoHelper::logAudit('MonHoc', 'UPDATE', $newData, $oldData, 'Global');
                
                RequestLogger::end(1, 200);
                sendResponse(['message' => 'MonHoc updated successfully on all sites']);
                break;
            case 'DELETE':
                // Delete MonHoc via trigger (from all 3 sites)
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }
                
                // Get data before delete
                $stmt = $pdo->prepare("SELECT * FROM MonHoc_Global WHERE MaMH = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $stmt = $pdo->prepare("DELETE FROM MonHoc_Global WHERE MaMH = ?");
                $stmt->execute([$query['id']]);
                
                // Log to MongoDB - Global since it's synced to all sites
                if ($oldData) {
                    MongoHelper::logAudit('MonHoc', 'DELETE', null, $oldData, 'Global');
                }
                
                RequestLogger::end(1, 200);
                sendResponse(['message' => 'MonHoc deleted successfully from all sites']);
                break;
            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        RequestLogger::end(0, 500);
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>