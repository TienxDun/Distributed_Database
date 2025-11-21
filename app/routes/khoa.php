<?php
require_once '../common.php';

function handleKhoa($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['id'])) {
                    $stmt = $pdo->prepare("
                        SELECT MaKhoa, TenKhoa,
                            CASE 
                                WHEN MaKhoa < 'M' THEN 'Site A'
                                WHEN MaKhoa >= 'M' AND MaKhoa < 'S' THEN 'Site B'
                                ELSE 'Site C'
                            END AS Site
                        FROM Khoa_Global WHERE MaKhoa = ?");
                    $stmt->execute([$query['id']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    sendResponse($result ?: ['error' => 'Not found'], $result ? 200 : 404);
                } else {
                    $stmt = $pdo->query("
                        SELECT MaKhoa, TenKhoa,
                            CASE 
                                WHEN MaKhoa < 'M' THEN 'Site A'
                                WHEN MaKhoa >= 'M' AND MaKhoa < 'S' THEN 'Site B'
                                ELSE 'Site C'
                            END AS Site
                        FROM Khoa_Global");
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;
            case 'POST':
                // Create new Khoa via trigger
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['MaKhoa']) || !isset($data['TenKhoa'])) {
                    sendResponse(['error' => 'Missing required fields: MaKhoa, TenKhoa'], 400);
                    break;
                }
                $stmt = $pdo->prepare("INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES (?, ?)");
                $stmt->execute([$data['MaKhoa'], $data['TenKhoa']]);
                sendResponse(['message' => 'Khoa created successfully', 'MaKhoa' => $data['MaKhoa']], 201);
                break;
            case 'PUT':
                // Update Khoa via trigger
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['TenKhoa'])) {
                    sendResponse(['error' => 'Missing required field: TenKhoa'], 400);
                    break;
                }
                $stmt = $pdo->prepare("UPDATE Khoa_Global SET TenKhoa = ? WHERE MaKhoa = ?");
                $stmt->execute([$data['TenKhoa'], $query['id']]);
                sendResponse(['message' => 'Khoa updated successfully']);
                break;
            case 'DELETE':
                // Delete Khoa via trigger
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }
                $stmt = $pdo->prepare("DELETE FROM Khoa_Global WHERE MaKhoa = ?");
                $stmt->execute([$query['id']]);
                sendResponse(['message' => 'Khoa deleted successfully']);
                break;
            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>