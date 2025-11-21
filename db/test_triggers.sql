-- =============================================
-- TEST CASES CHO INSTEAD OF TRIGGERS
-- Kiểm tra các thao tác INSERT, UPDATE, DELETE qua Global Views
-- =============================================

USE HUFLIT;
GO

PRINT N'';
PRINT N'========================================';
PRINT N'BẮT ĐẦU KIỂM TRA TRIGGERS';
PRINT N'========================================';
GO

-- =============================================
-- TEST 1: KHOA_GLOBAL TRIGGERS
-- =============================================
PRINT N'';
PRINT N'--- TEST 1: KIỂM TRA TRIGGER KHOA_GLOBAL ---';
GO

-- Test 1.1: INSERT Khoa mới vào Site A (MaKhoa < 'M')
PRINT N'Test 1.1: Thêm Khoa vào Site A...';
INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES ('TEST', N'Khoa Test Site A');
SELECT * FROM Khoa_Global WHERE MaKhoa = 'TEST';
GO

-- Test 1.2: INSERT Khoa mới vào Site B ('M' <= MaKhoa < 'S')
PRINT N'Test 1.2: Thêm Khoa vào Site B...';
INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES ('MTEST', N'Khoa Test Site B');
SELECT * FROM Khoa_Global WHERE MaKhoa = 'MTEST';
GO

-- Test 1.3: INSERT Khoa mới vào Site C (MaKhoa >= 'S')
PRINT N'Test 1.3: Thêm Khoa vào Site C...';
INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES ('ZTEST', N'Khoa Test Site C');
SELECT * FROM Khoa_Global WHERE MaKhoa = 'ZTEST';
GO

-- Test 1.4: UPDATE Khoa
PRINT N'Test 1.4: Cập nhật tên Khoa...';
UPDATE Khoa_Global SET TenKhoa = N'Khoa Test Site A - Đã sửa' WHERE MaKhoa = 'TEST';
SELECT * FROM Khoa_Global WHERE MaKhoa = 'TEST';
GO

-- Test 1.5: DELETE Khoa (không có ràng buộc)
PRINT N'Test 1.5: Xóa Khoa không có dữ liệu liên quan...';
DELETE FROM Khoa_Global WHERE MaKhoa = 'ZTEST';
SELECT * FROM Khoa_Global WHERE MaKhoa = 'ZTEST';
GO

-- Test 1.6: Thử INSERT trùng MaKhoa (phải lỗi)
PRINT N'Test 1.6: Thử thêm Khoa trùng mã (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES ('TEST', N'Khoa trùng');
    PRINT N'❌ LỖI: Không báo lỗi khi thêm mã trùng!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- =============================================
-- TEST 2: MONHOC_GLOBAL TRIGGERS
-- =============================================
PRINT N'';
PRINT N'--- TEST 2: KIỂM TRA TRIGGER MONHOC_GLOBAL ---';
GO

-- Test 2.1: INSERT MonHoc mới (sync sang cả 3 sites)
PRINT N'Test 2.1: Thêm Môn học mới...';
INSERT INTO MonHoc_Global (MaMH, TenMH) VALUES ('TEST01', N'Môn Test 01');
SELECT * FROM MonHoc_Global WHERE MaMH = 'TEST01';
GO

-- Test 2.2: UPDATE MonHoc
PRINT N'Test 2.2: Cập nhật tên Môn học...';
UPDATE MonHoc_Global SET TenMH = N'Môn Test 01 - Đã sửa' WHERE MaMH = 'TEST01';
SELECT * FROM MonHoc_Global WHERE MaMH = 'TEST01';
GO

-- Test 2.3: Thử INSERT trùng MaMH (phải lỗi)
PRINT N'Test 2.3: Thử thêm Môn học trùng mã (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO MonHoc_Global (MaMH, TenMH) VALUES ('TEST01', N'Môn trùng');
    PRINT N'❌ LỖI: Không báo lỗi khi thêm mã trùng!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- =============================================
-- TEST 3: SINHVIEN_GLOBAL TRIGGERS
-- =============================================
PRINT N'';
PRINT N'--- TEST 3: KIỂM TRA TRIGGER SINHVIEN_GLOBAL ---';
GO

-- Test 3.1: INSERT SinhVien vào Site A (MaKhoa = 'TEST' < 'M')
PRINT N'Test 3.1: Thêm Sinh viên vào Site A...';
INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) 
VALUES ('SV001', N'Nguyễn Văn A', 'TEST', 2024);
SELECT * FROM SinhVien_Global WHERE MaSV = 'SV001';
GO

-- Test 3.2: INSERT SinhVien vào Site B (MaKhoa = 'MTEST')
PRINT N'Test 3.2: Thêm Sinh viên vào Site B...';
INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) 
VALUES ('SV002', N'Trần Thị B', 'MTEST', 2024);
SELECT * FROM SinhVien_Global WHERE MaSV = 'SV002';
GO

-- Test 3.3: UPDATE SinhVien (không đổi khoa)
PRINT N'Test 3.3: Cập nhật thông tin Sinh viên (không đổi khoa)...';
UPDATE SinhVien_Global SET HoTen = N'Nguyễn Văn A - Đã sửa' WHERE MaSV = 'SV001';
SELECT * FROM SinhVien_Global WHERE MaSV = 'SV001';
GO

-- Test 3.4: Thử INSERT trùng MaSV (phải lỗi)
PRINT N'Test 3.4: Thử thêm Sinh viên trùng mã (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) 
    VALUES ('SV001', N'Sinh viên trùng', 'TEST', 2024);
    PRINT N'❌ LỖI: Không báo lỗi khi thêm mã trùng!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- Test 3.5: Thử INSERT với MaKhoa không tồn tại (phải lỗi)
PRINT N'Test 3.5: Thử thêm Sinh viên với Khoa không tồn tại (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) 
    VALUES ('SV999', N'Sinh viên lỗi', 'XXXXX', 2024);
    PRINT N'❌ LỖI: Không báo lỗi khi thêm khoa không tồn tại!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- =============================================
-- TEST 4: CTDAOTAO_GLOBAL TRIGGERS
-- =============================================
PRINT N'';
PRINT N'--- TEST 4: KIỂM TRA TRIGGER CTDAOTAO_GLOBAL ---';
GO

-- Test 4.1: INSERT CTDaoTao vào Site A
PRINT N'Test 4.1: Thêm Chương trình đào tạo vào Site A...';
INSERT INTO CTDaoTao_Global (MaKhoa, KhoaHoc, MaMH) 
VALUES ('TEST', 2024, 'TEST01');
SELECT * FROM CTDaoTao_Global WHERE MaKhoa = 'TEST' AND KhoaHoc = 2024 AND MaMH = 'TEST01';
GO

-- Test 4.2: INSERT CTDaoTao vào Site B
PRINT N'Test 4.2: Thêm Chương trình đào tạo vào Site B...';
INSERT INTO CTDaoTao_Global (MaKhoa, KhoaHoc, MaMH) 
VALUES ('MTEST', 2024, 'TEST01');
SELECT * FROM CTDaoTao_Global WHERE MaKhoa = 'MTEST' AND KhoaHoc = 2024 AND MaMH = 'TEST01';
GO

-- Test 4.3: Thử INSERT trùng (phải lỗi)
PRINT N'Test 4.3: Thử thêm Chương trình trùng (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO CTDaoTao_Global (MaKhoa, KhoaHoc, MaMH) 
    VALUES ('TEST', 2024, 'TEST01');
    PRINT N'❌ LỖI: Không báo lỗi khi thêm chương trình trùng!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- Test 4.4: Thử INSERT với MaKhoa không tồn tại (phải lỗi)
PRINT N'Test 4.4: Thử thêm CT với Khoa không tồn tại (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO CTDaoTao_Global (MaKhoa, KhoaHoc, MaMH) 
    VALUES ('XXXXX', 2024, 'TEST01');
    PRINT N'❌ LỖI: Không báo lỗi khi thêm khoa không tồn tại!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- Test 4.5: Thử INSERT với MaMH không tồn tại (phải lỗi)
PRINT N'Test 4.5: Thử thêm CT với Môn không tồn tại (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO CTDaoTao_Global (MaKhoa, KhoaHoc, MaMH) 
    VALUES ('TEST', 2024, 'XXXXX');
    PRINT N'❌ LỖI: Không báo lỗi khi thêm môn không tồn tại!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- Test 4.6: Thử UPDATE CTDaoTao (phải lỗi - không cho phép)
PRINT N'Test 4.6: Thử cập nhật CT (phải báo lỗi - không cho phép)...';
BEGIN TRY
    UPDATE CTDaoTao_Global SET KhoaHoc = 2025 WHERE MaKhoa = 'TEST' AND MaMH = 'TEST01';
    PRINT N'❌ LỖI: Không báo lỗi khi update CT!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- =============================================
-- TEST 5: DANGKY_GLOBAL TRIGGERS
-- =============================================
PRINT N'';
PRINT N'--- TEST 5: KIỂM TRA TRIGGER DANGKY_GLOBAL ---';
GO

-- Test 5.1: INSERT DangKy cho sinh viên Site A
PRINT N'Test 5.1: Thêm Đăng ký cho sinh viên Site A...';
INSERT INTO DangKy_Global (MaSV, MaMon, DiemThi) 
VALUES ('SV001', 'TEST01', NULL);
SELECT * FROM DangKy_Global WHERE MaSV = 'SV001' AND MaMon = 'TEST01';
GO

-- Test 5.2: INSERT DangKy cho sinh viên Site B
PRINT N'Test 5.2: Thêm Đăng ký cho sinh viên Site B...';
INSERT INTO DangKy_Global (MaSV, MaMon, DiemThi) 
VALUES ('SV002', 'TEST01', 8.5);
SELECT * FROM DangKy_Global WHERE MaSV = 'SV002' AND MaMon = 'TEST01';
GO

-- Test 5.3: UPDATE DiemThi
PRINT N'Test 5.3: Cập nhật điểm thi...';
UPDATE DangKy_Global SET DiemThi = 9.0 WHERE MaSV = 'SV001' AND MaMon = 'TEST01';
SELECT * FROM DangKy_Global WHERE MaSV = 'SV001' AND MaMon = 'TEST01';
GO

-- Test 5.4: Thử INSERT trùng (phải lỗi)
PRINT N'Test 5.4: Thử thêm Đăng ký trùng (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO DangKy_Global (MaSV, MaMon, DiemThi) 
    VALUES ('SV001', 'TEST01', 7.0);
    PRINT N'❌ LỖI: Không báo lỗi khi thêm đăng ký trùng!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- Test 5.5: Thử INSERT với MaSV không tồn tại (phải lỗi)
PRINT N'Test 5.5: Thử thêm Đăng ký với SV không tồn tại (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO DangKy_Global (MaSV, MaMon, DiemThi) 
    VALUES ('SV999', 'TEST01', 8.0);
    PRINT N'❌ LỖI: Không báo lỗi khi thêm SV không tồn tại!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- Test 5.6: Thử INSERT với MaMon không tồn tại (phải lỗi)
PRINT N'Test 5.6: Thử thêm Đăng ký với Môn không tồn tại (phải báo lỗi)...';
BEGIN TRY
    INSERT INTO DangKy_Global (MaSV, MaMon, DiemThi) 
    VALUES ('SV001', 'XXXXX', 8.0);
    PRINT N'❌ LỖI: Không báo lỗi khi thêm môn không tồn tại!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- =============================================
-- TEST 6: RÀNG BUỘC XÓA
-- =============================================
PRINT N'';
PRINT N'--- TEST 6: KIỂM TRA RÀNG BUỘC XÓA ---';
GO

-- Test 6.1: Thử xóa Khoa có sinh viên (phải lỗi)
PRINT N'Test 6.1: Thử xóa Khoa có sinh viên (phải báo lỗi)...';
BEGIN TRY
    DELETE FROM Khoa_Global WHERE MaKhoa = 'TEST';
    PRINT N'❌ LỖI: Không báo lỗi khi xóa khoa có sinh viên!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- Test 6.2: Thử xóa MonHoc có trong CTDaoTao (phải lỗi)
PRINT N'Test 6.2: Thử xóa Môn học có trong CT (phải báo lỗi)...';
BEGIN TRY
    DELETE FROM MonHoc_Global WHERE MaMH = 'TEST01';
    PRINT N'❌ LỖI: Không báo lỗi khi xóa môn có CT!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- Test 6.3: Thử xóa SinhVien có đăng ký (phải lỗi)
PRINT N'Test 6.3: Thử xóa Sinh viên có đăng ký (phải báo lỗi)...';
BEGIN TRY
    DELETE FROM SinhVien_Global WHERE MaSV = 'SV001';
    PRINT N'❌ LỖI: Không báo lỗi khi xóa SV có đăng ký!';
END TRY
BEGIN CATCH
    PRINT N'✓ Đúng: ' + ERROR_MESSAGE();
END CATCH
GO

-- =============================================
-- TEST 7: DỌN DỮ LIỆU TEST (THEO THỨ TỰ)
-- =============================================
PRINT N'';
PRINT N'--- TEST 7: DỌN DỮ LIỆU TEST ---';
GO

PRINT N'Xóa dữ liệu test theo thứ tự đúng...';

-- Xóa DangKy trước
DELETE FROM DangKy_Global WHERE MaSV IN ('SV001', 'SV002');
PRINT N'✓ Đã xóa DangKy';

-- Xóa CTDaoTao
DELETE FROM CTDaoTao_Global WHERE MaKhoa IN ('TEST', 'MTEST') AND MaMH = 'TEST01';
PRINT N'✓ Đã xóa CTDaoTao';

-- Xóa SinhVien
DELETE FROM SinhVien_Global WHERE MaSV IN ('SV001', 'SV002');
PRINT N'✓ Đã xóa SinhVien';

-- Xóa MonHoc
DELETE FROM MonHoc_Global WHERE MaMH = 'TEST01';
PRINT N'✓ Đã xóa MonHoc';

-- Xóa Khoa
DELETE FROM Khoa_Global WHERE MaKhoa IN ('TEST', 'MTEST');
PRINT N'✓ Đã xóa Khoa';

GO

PRINT N'';
PRINT N'========================================';
PRINT N'HOÀN THÀNH KIỂM TRA TRIGGERS';
PRINT N'========================================';
GO
