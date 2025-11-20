<?php
require_once '../common.php';

function handleGlobal($method, $query) {
    try {
        $pdo = getDBConnection();
        switch ($method) {
            case 'GET':
                if (isset($query['type'])) {
                    switch ($query['type']) {
                        case '1': // Các môn học sinh viên đã học và đạt từ điểm 5 trở lên
                            if (!isset($query['masv'])) {
                                sendResponse(['error' => 'Missing masv parameter'], 400);
                            }
                            $stmt = $pdo->prepare("SELECT m.TenMH, d.DiemThi
                                                    FROM DangKy_Global d
                                                    JOIN MonHoc_Global m ON d.MaMon = m.MaMH
                                                    WHERE d.MaSV = ? AND d.DiemThi >= 5");
                            $stmt->execute([$query['masv']]);
                            sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                            break;
                        case '2': // Các khóa học của một khoa
                            if (!isset($query['query'])) {
                                sendResponse(['error' => 'Missing query parameter'], 400);
                            }
                            $stmt = $pdo->prepare("SELECT DISTINCT c.KhoaHoc
                                                    FROM CTDaoTao_Global c
                                                    JOIN Khoa_Global k ON c.MaKhoa = k.MaKhoa
                                                    WHERE k.TenKhoa = ? OR k.MaKhoa = ?");
                            $stmt->execute([$query['query'], $query['query']]);
                            sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                            break;
                        case '3': // Các môn học bắt buộc của sinh viên
                            if (!isset($query['masv'])) {
                                sendResponse(['error' => 'Missing masv parameter'], 400);
                            }
                            $stmt = $pdo->prepare("SELECT DISTINCT m.MaMH, m.TenMH
                                                    FROM CTDaoTao_Global c
                                                    JOIN SinhVien_Global s ON c.MaKhoa = s.MaKhoa AND c.KhoaHoc = s.KhoaHoc
                                                    JOIN MonHoc_Global m ON c.MaMH = m.MaMH
                                                    WHERE s.MaSV = ?");
                            $stmt->execute([$query['masv']]);
                            sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                            break;
                        case '4': // Danh sách sinh viên đủ điều kiện tốt nghiệp
                            $stmt = $pdo->query("SELECT s.MaSV, s.HoTen
                                                 FROM SinhVien_Global s
                                                 WHERE NOT EXISTS (
                                                   SELECT *
                                                   FROM CTDaoTao_Global c
                                                   JOIN MonHoc_Global m ON c.MaMH = m.MaMH
                                                   WHERE c.MaKhoa = s.MaKhoa AND c.KhoaHoc = s.KhoaHoc
                                                     AND NOT EXISTS (
                                                       SELECT *
                                                       FROM DangKy_Global d
                                                       WHERE d.MaSV = s.MaSV AND d.MaMon = c.MaMH AND d.DiemThi >= 5
                                                     )
                                                 )");
                            sendResponse($stmt->fetchAll(PDO::FETCH_ASSOC));
                            break;
                        default:
                            sendResponse(['error' => 'Invalid type'], 400);
                    }
                } else {
                    sendResponse(['error' => 'Missing type parameter'], 400);
                }
                break;
            default:
                sendResponse(['error' => 'Method not allowed'], 405);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()], 500);
    }
}
?>