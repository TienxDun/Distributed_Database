<?php
require_once '../common.php';

function handleDangKy($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['masv'])) {
                    // Lấy tất cả môn học mà sinh viên đăng ký
                    $stmt = $pdo->prepare("SELECT dk.*, mh.TenMH, sv.HoTen FROM DangKy_Global dk 
                                          JOIN MonHoc_Global mh ON dk.MaMon = mh.MaMH 
                                          JOIN SinhVien_Global sv ON dk.MaSV = sv.MaSV 
                                          WHERE dk.MaSV = ?");
                    $stmt->execute([$query['masv']]);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    sendResponse($results);
                } elseif (isset($query['mamon'])) {
                    // Lấy tất cả sinh viên đăng ký môn học
                    $stmt = $pdo->prepare("SELECT dk.*, sv.HoTen, sv.MaKhoa, mh.TenMH FROM DangKy_Global dk 
                                          JOIN SinhVien_Global sv ON dk.MaSV = sv.MaSV 
                                          JOIN MonHoc_Global mh ON dk.MaMon = mh.MaMH 
                                          WHERE dk.MaMon = ?");
                    $stmt->execute([$query['mamon']]);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    sendResponse($results);
                } else {
                    // Lấy tất cả đăng ký
                    $stmt = $pdo->query("SELECT dk.*, sv.HoTen, mh.TenMH FROM DangKy_Global dk 
                                        JOIN SinhVien_Global sv ON dk.MaSV = sv.MaSV 
                                        JOIN MonHoc_Global mh ON dk.MaMon = mh.MaMH");
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