<?php
require_once '../common.php';
require_once '../mongo_helper.php';

function determineSite($maKhoa) {
    if ($maKhoa < 'M') return 'Site_A';
    if ($maKhoa >= 'M' && $maKhoa < 'S') return 'Site_B';
    return 'Site_C';
}

function handleSinhVien($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['id'])) {
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
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;
            case 'POST':
                // Create new SinhVien via trigger
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['MaSV']) || !isset($data['HoTen']) || !isset($data['MaKhoa']) || !isset($data['KhoaHoc'])) {
                    sendResponse(['error' => 'Missing required fields: MaSV, HoTen, MaKhoa, KhoaHoc'], 400);
                    break;
                }
                $stmt = $pdo->prepare("INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (?, ?, ?, ?)");
                $stmt->execute([$data['MaSV'], $data['HoTen'], $data['MaKhoa'], $data['KhoaHoc']]);
                
                // Log to MongoDB
                $site = determineSite($data['MaKhoa']);
                MongoHelper::logAudit('SinhVien', 'INSERT', $data, null, $site);
                
                sendResponse(['message' => 'SinhVien created successfully', 'MaSV' => $data['MaSV']], 201);
                break;
            case 'PUT':
                // Update SinhVien via trigger (allows MaKhoa change = move between sites)
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }
                
                // Get old data first
                $stmt = $pdo->prepare("SELECT * FROM SinhVien_Global WHERE MaSV = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['HoTen']) || !isset($data['MaKhoa']) || !isset($data['KhoaHoc'])) {
                    sendResponse(['error' => 'Missing required fields: HoTen, MaKhoa, KhoaHoc'], 400);
                    break;
                }
                $stmt = $pdo->prepare("UPDATE SinhVien_Global SET HoTen = ?, MaKhoa = ?, KhoaHoc = ? WHERE MaSV = ?");
                $stmt->execute([$data['HoTen'], $data['MaKhoa'], $data['KhoaHoc'], $query['id']]);
                
                // Log to MongoDB
                $newData = ['MaSV' => $query['id'], 'HoTen' => $data['HoTen'], 'MaKhoa' => $data['MaKhoa'], 'KhoaHoc' => $data['KhoaHoc']];
                $site = determineSite($data['MaKhoa']);
                MongoHelper::logAudit('SinhVien', 'UPDATE', $newData, $oldData, $site);
                
                sendResponse(['message' => 'SinhVien updated successfully']);
                break;
            case 'DELETE':
                // Delete SinhVien via trigger
                if (!isset($query['id'])) {
                    sendResponse(['error' => 'Missing required parameter: id'], 400);
                    break;
                }
                
                // Get data before delete
                $stmt = $pdo->prepare("SELECT * FROM SinhVien_Global WHERE MaSV = ?");
                $stmt->execute([$query['id']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $stmt = $pdo->prepare("DELETE FROM SinhVien_Global WHERE MaSV = ?");
                $stmt->execute([$query['id']]);
                
                // Log to MongoDB
                if ($oldData) {
                    $site = determineSite($oldData['MaKhoa']);
                    MongoHelper::logAudit('SinhVien', 'DELETE', null, $oldData, $site);
                }
                
                sendResponse(['message' => 'SinhVien deleted successfully']);
                break;
            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>