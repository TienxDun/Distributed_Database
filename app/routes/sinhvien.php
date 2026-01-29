<?php
require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../mongo_helper.php';

function handleSinhVien($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['id'])) {
                    validateId($query['id']);

                    $stmt = $pdo->prepare("
                        SELECT MaSV, HoTen, MaKhoa, KhoaHoc,
                            CASE
                                WHEN MaKhoa < 'M' THEN 'Site A'
                                WHEN MaKhoa >= 'M' AND MaKhoa < 'S' THEN 'Site B'
                                ELSE 'Site C'
                            END AS Site
                        FROM SinhVien_Global WHERE MaSV = ?");
                    $stmt->execute([$query['id']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    RequestLogger::end($result ? 1 : 0, $result ? 200 : 404);
                    sendResponse($result ?: ['error' => 'Not found'], $result ? 200 : 404);
                } else {
                    $stmt = $pdo->query("
                        SELECT MaSV, HoTen, MaKhoa, KhoaHoc,
                            CASE
                                WHEN MaKhoa < 'M' THEN 'Site A'
                                WHEN MaKhoa >= 'M' AND MaKhoa < 'S' THEN 'Site B'
                                ELSE 'Site C'
                            END AS Site
                        FROM SinhVien_Global");
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    RequestLogger::end(count($result), 200);
                    sendResponse($result);
                }
                break;
            case 'POST':
                $data = getJsonInput();
                validateRequiredFields($data, ['MaSV', 'HoTen', 'MaKhoa', 'KhoaHoc']);
                validateId($data['MaSV']);

                $data['HoTen'] = sanitizeString($data['HoTen']);
                $data['MaKhoa'] = sanitizeString($data['MaKhoa']);

                $stmt = $pdo->prepare("INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (?, ?, ?, ?)");
                $stmt->execute([$data['MaSV'], $data['HoTen'], $data['MaKhoa'], (int)$data['KhoaHoc']]);

                // Log to MongoDB
                $site = determineSite($data['MaKhoa']);
                MongoHelper::logAudit('SinhVien', 'INSERT', $data, null, $site);

                RequestLogger::end(1, 201);
                sendResponse(['message' => 'SinhVien created successfully', 'MaSV' => $data['MaSV']], 201);
                break;
            case 'PUT':
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }

                validateId($query['id']);

                // Get old data first
                $stmt = $pdo->prepare("SELECT * FROM SinhVien_Global WHERE MaSV = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$oldData) {
                    sendResponse(['error' => 'SinhVien not found'], 404);
                }

                $data = getJsonInput();
                validateRequiredFields($data, ['HoTen', 'MaKhoa', 'KhoaHoc']);

                $data['HoTen'] = sanitizeString($data['HoTen']);
                $data['MaKhoa'] = sanitizeString($data['MaKhoa']);

                $stmt = $pdo->prepare("UPDATE SinhVien_Global SET HoTen = ?, MaKhoa = ?, KhoaHoc = ? WHERE MaSV = ?");
                $stmt->execute([$data['HoTen'], $data['MaKhoa'], (int)$data['KhoaHoc'], $query['id']]);

                // Log to MongoDB
                $newData = ['MaSV' => $query['id'], 'HoTen' => $data['HoTen'], 'MaKhoa' => $data['MaKhoa'], 'KhoaHoc' => $data['KhoaHoc']];
                $site = determineSite($data['MaKhoa']);
                MongoHelper::logAudit('SinhVien', 'UPDATE', $newData, $oldData, $site);

                RequestLogger::end(1, 200);
                sendResponse(['message' => 'SinhVien updated successfully']);
                break;
            case 'DELETE':
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }

                validateId($query['id']);

                // Get data before delete
                $stmt = $pdo->prepare("SELECT * FROM SinhVien_Global WHERE MaSV = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$oldData) {
                    sendResponse(['error' => 'SinhVien not found'], 404);
                    return;
                }

                $stmt = $pdo->prepare("DELETE FROM SinhVien_Global WHERE MaSV = ?");
                $stmt->execute([$query['id']]);

                // Log to MongoDB
                $site = isset($oldData['MaKhoa']) ? determineSite($oldData['MaKhoa']) : 'Unknown';
                MongoHelper::logAudit('SinhVien', 'DELETE', null, $oldData, $site);

                RequestLogger::end(1, 200);
                sendResponse(['message' => 'SinhVien deleted successfully']);
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