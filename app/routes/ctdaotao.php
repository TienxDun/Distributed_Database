<?php
require_once '../common.php';
require_once '../mongo_helper.php';

function determineSite($maKhoa) {
    if ($maKhoa < 'M') return 'Site_A';
    if ($maKhoa >= 'M' && $maKhoa < 'S') return 'Site_B';
    return 'Site_C';
}

function handleCTDaoTao($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['khoa']) && isset($query['khoahoc'])) {
                    // Lấy danh sách môn học thuộc chương trình đào tạo cụ thể
                    $stmt = $pdo->prepare("SELECT m.* FROM CTDaoTao_Global c JOIN MonHoc_Global m ON c.MaMH = m.MaMH JOIN Khoa_Global k ON c.MaKhoa = k.MaKhoa WHERE (k.TenKhoa = ? OR k.MaKhoa = ?) AND c.KhoaHoc = ?");
                    $stmt->execute([$query['khoa'], $query['khoa'], $query['khoahoc']]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    sendResponse($result);
                } elseif (isset($query['khoa'])) {
                    // Lấy tất cả môn học của khoa
                    $stmt = $pdo->prepare("SELECT m.* FROM CTDaoTao_Global c JOIN MonHoc_Global m ON c.MaMH = m.MaMH JOIN Khoa_Global k ON c.MaKhoa = k.MaKhoa WHERE k.TenKhoa = ? OR k.MaKhoa = ?");
                    $stmt->execute([$query['khoa'], $query['khoa']]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    sendResponse($result);
                } elseif (isset($query['khoahoc'])) {
                    // Lấy tất cả môn học của khóa học
                    $stmt = $pdo->prepare("SELECT m.* FROM CTDaoTao_Global c JOIN MonHoc_Global m ON c.MaMH = m.MaMH WHERE c.KhoaHoc = ?");
                    $stmt->execute([$query['khoahoc']]);
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    sendResponse($result);
                } elseif (isset($query['khoa']) && isset($query['khoahoc']) && isset($query['mamh'])) {
                    $stmt = $pdo->prepare("SELECT c.* FROM CTDaoTao_Global c JOIN Khoa_Global k ON c.MaKhoa = k.MaKhoa WHERE (k.TenKhoa = ? OR k.MaKhoa = ?) AND c.KhoaHoc = ? AND c.MaMH = ?");
                    $stmt->execute([$query['khoa'], $query['khoa'], $query['khoahoc'], $query['mamh']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    sendResponse($result ?: ['error' => 'Not found'], $result ? 200 : 404);
                } else {
                    $stmt = $pdo->query("
                        SELECT MaKhoa, KhoaHoc, MaMH,
                            CASE 
                                WHEN MaKhoa < 'M' THEN 'Site A'
                                WHEN MaKhoa >= 'M' AND MaKhoa < 'S' THEN 'Site B'
                                ELSE 'Site C'
                            END AS Site
                        FROM CTDaoTao_Global");
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;
            case 'POST':
                // Create new CTDaoTao via trigger
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['MaKhoa']) || !isset($data['KhoaHoc']) || !isset($data['MaMH'])) {
                    sendResponse(['error' => 'Missing required fields: MaKhoa, KhoaHoc, MaMH'], 400);
                    break;
                }
                $stmt = $pdo->prepare("INSERT INTO CTDaoTao_Global (MaKhoa, KhoaHoc, MaMH) VALUES (?, ?, ?)");
                $stmt->execute([$data['MaKhoa'], $data['KhoaHoc'], $data['MaMH']]);
                
                // Log to MongoDB
                $site = determineSite($data['MaKhoa']);
                MongoHelper::logAudit('CTDaoTao', 'INSERT', $data, null, $site);
                
                sendResponse(['message' => 'CTDaoTao created successfully'], 201);
                break;
            case 'PUT':
                // UPDATE not allowed for composite primary key
                sendResponse(['error' => 'Update not allowed. Delete and create new record instead.'], 405);
                break;
            case 'DELETE':
                // Delete CTDaoTao via trigger
                if (!isset($query['khoa']) || !isset($query['khoahoc']) || !isset($query['monhoc'])) {
                    sendResponse(['error' => 'Missing required parameters: khoa, khoahoc, monhoc'], 400);
                    break;
                }
                
                // Get data before delete
                $stmt = $pdo->prepare("SELECT * FROM CTDaoTao_Global WHERE MaKhoa = ? AND KhoaHoc = ? AND MaMH = ?");
                $stmt->execute([$query['khoa'], $query['khoahoc'], $query['monhoc']]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $stmt = $pdo->prepare("DELETE FROM CTDaoTao_Global WHERE MaKhoa = ? AND KhoaHoc = ? AND MaMH = ?");
                $stmt->execute([$query['khoa'], $query['khoahoc'], $query['monhoc']]);
                
                // Log to MongoDB
                if ($oldData) {
                    $site = determineSite($query['khoa']);
                    MongoHelper::logAudit('CTDaoTao', 'DELETE', null, $oldData, $site);
                }
                
                sendResponse(['message' => 'CTDaoTao deleted successfully']);
                break;
            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>