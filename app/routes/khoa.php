<?php
require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../mongo_helper.php';

function handleKhoa($method, $query) {
    try {
        $pdo = getDBConnection();

        switch ($method) {
            case 'GET':
                if (isset($query['id'])) {
                    validateId($query['id']);

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

                    RequestLogger::end($result ? 1 : 0, $result ? 200 : 404);
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
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    RequestLogger::end(count($result), 200);
                    sendResponse($result);
                }
                break;

            case 'POST':
                $data = getJsonInput();
                validateRequiredFields($data, ['MaKhoa', 'TenKhoa']);
                validateId($data['MaKhoa']);

                $data['TenKhoa'] = sanitizeString($data['TenKhoa']);

                $stmt = $pdo->prepare("INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES (?, ?)");
                $stmt->execute([$data['MaKhoa'], $data['TenKhoa']]);

                // Log to MongoDB
                $site = determineSite($data['MaKhoa']);
                MongoHelper::logAudit('Khoa', 'INSERT', $data, null, $site);

                RequestLogger::end(1, 201);
                sendResponse(['message' => 'Khoa created successfully'], 201);
                break;

            case 'PUT':
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }

                validateId($query['id']);

                // Get old data first
                $stmt = $pdo->prepare("SELECT * FROM Khoa_Global WHERE MaKhoa = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$oldData) {
                    sendResponse(['error' => 'Khoa not found'], 404);
                }

                $data = getJsonInput();
                validateRequiredFields($data, ['TenKhoa']);

                $data['TenKhoa'] = sanitizeString($data['TenKhoa']);

                $stmt = $pdo->prepare("UPDATE Khoa_Global SET TenKhoa = ? WHERE MaKhoa = ?");
                $stmt->execute([$data['TenKhoa'], $query['id']]);

                // Log to MongoDB
                $newData = ['MaKhoa' => $query['id'], 'TenKhoa' => $data['TenKhoa']];
                $site = determineSite($query['id']);
                MongoHelper::logAudit('Khoa', 'UPDATE', $newData, $oldData, $site);

                RequestLogger::end(1, 200);
                sendResponse(['message' => 'Khoa updated successfully']);
                break;

            case 'DELETE':
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }

                validateId($query['id']);

                // Get data before delete
                $stmt = $pdo->prepare("SELECT * FROM Khoa_Global WHERE MaKhoa = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$oldData) {
                    sendResponse(['error' => 'Khoa not found'], 404);
                    return;
                }

                $stmt = $pdo->prepare("DELETE FROM Khoa_Global WHERE MaKhoa = ?");
                $stmt->execute([$query['id']]);

                // Log to MongoDB
                $site = isset($oldData['MaKhoa']) ? determineSite($oldData['MaKhoa']) : 'Unknown';
                MongoHelper::logAudit('Khoa', 'DELETE', null, $oldData, $site);

                RequestLogger::end(1, 200);
                sendResponse(['message' => 'Khoa deleted successfully']);
                break;

            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        RequestLogger::end(0, 500);
        sendResponse(['error' => $e->getMessage()], 500);
    }
}