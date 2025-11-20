<?php
require_once '../common.php';

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