<?php
require_once '../common.php';

function handleDangKy($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['masv'])) {
                    // Lấy tất cả môn học mà sinh viên đăng ký
                    $stmt = $pdo->prepare("
                        SELECT dk.MaSV, dk.MaMon, dk.DiemThi, mh.TenMH, sv.HoTen, sv.MaKhoa,
                            CASE 
                                WHEN sv.MaKhoa < 'M' THEN 'Site A'
                                WHEN sv.MaKhoa >= 'M' AND sv.MaKhoa < 'S' THEN 'Site B'
                                ELSE 'Site C'
                            END AS Site
                        FROM DangKy_Global dk 
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
                    $stmt = $pdo->query("
                        SELECT dk.MaSV, dk.MaMon, dk.DiemThi, sv.HoTen, mh.TenMH, sv.MaKhoa,
                            CASE 
                                WHEN sv.MaKhoa < 'M' THEN 'Site A'
                                WHEN sv.MaKhoa >= 'M' AND sv.MaKhoa < 'S' THEN 'Site B'
                                ELSE 'Site C'
                            END AS Site
                        FROM DangKy_Global dk 
                        JOIN SinhVien_Global sv ON dk.MaSV = sv.MaSV 
                        JOIN MonHoc_Global mh ON dk.MaMon = mh.MaMH");
                    sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                }
                break;
            case 'POST':
                // Create new DangKy via trigger
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['MaSV']) || !isset($data['MaMon'])) {
                    sendResponse(['error' => 'Missing required fields: MaSV, MaMon'], 400);
                    break;
                }
                // DiemThi is optional - use null if not provided or empty
                $diemThi = (isset($data['DiemThi']) && $data['DiemThi'] !== '') ? $data['DiemThi'] : null;
                $stmt = $pdo->prepare("INSERT INTO DangKy_Global (MaSV, MaMon, DiemThi) VALUES (?, ?, ?)");
                $stmt->execute([$data['MaSV'], $data['MaMon'], $diemThi]);
                sendResponse(['message' => 'DangKy created successfully'], 201);
                break;
            case 'PUT':
                // Update DangKy via trigger (only DiemThi can be updated)
                if (!isset($query['masv']) || !isset($query['mamon'])) {
                    sendResponse(['error' => 'Missing required parameters: masv, mamon'], 400);
                    break;
                }
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['DiemThi'])) {
                    sendResponse(['error' => 'Missing required field: DiemThi'], 400);
                    break;
                }
                $stmt = $pdo->prepare("UPDATE DangKy_Global SET DiemThi = ? WHERE MaSV = ? AND MaMon = ?");
                $stmt->execute([$data['DiemThi'], $query['masv'], $query['mamon']]);
                sendResponse(['message' => 'DiemThi updated successfully']);
                break;
            case 'DELETE':
                // Delete DangKy via trigger
                if (!isset($query['masv']) || !isset($query['mamon'])) {
                    sendResponse(['error' => 'Missing required parameters: masv, mamon'], 400);
                    break;
                }
                $stmt = $pdo->prepare("DELETE FROM DangKy_Global WHERE MaSV = ? AND MaMon = ?");
                $stmt->execute([$query['masv'], $query['mamon']]);
                sendResponse(['message' => 'DangKy deleted successfully']);
                break;
            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>