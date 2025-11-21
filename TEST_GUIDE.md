# Hướng dẫn Test Triggers - HUFLIT Distributed Database

## Tổng quan

File `db/test_triggers.sql` chứa các test cases đơn giản để kiểm tra hoạt động của INSTEAD OF Triggers trên các Global Views.

## Cấu trúc phân mảnh

### Phân vùng dữ liệu theo MaKhoa:
- **Site A**: MaKhoa < 'M' (CNTT, DLKS, KTTC, LLCT, TEST)
- **Site B**: MaKhoa >= 'M' và < 'S' (MTEST, NVPD, QHQT, QTKD)
- **Site C**: MaKhoa >= 'S' (SLCT, SUAT, TLKS, ZTEST)

### Đồng bộ dữ liệu:
- **MonHoc**: Đồng bộ trên cả 3 sites (INSERT/UPDATE/DELETE đều sync)
- **Khoa, SinhVien, CTDaoTao, DangKy**: Phân mảnh theo site

## Chạy test

### Cách 1: Chạy toàn bộ test suite
```powershell
sqlcmd -S localhost,14333 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\test_triggers.sql
```

### Cách 2: Chạy từng test riêng lẻ
Mở file `db/test_triggers.sql` và copy từng phần test để chạy trong SQL Management Studio hoặc Azure Data Studio.

## Các test cases

### TEST 1: KHOA_GLOBAL (6 tests)
✅ **Test 1.1**: Thêm Khoa vào Site A (MaKhoa = 'TEST' < 'M')
✅ **Test 1.2**: Thêm Khoa vào Site B (MaKhoa = 'MTEST')
✅ **Test 1.3**: Thêm Khoa vào Site C (MaKhoa = 'ZTEST' >= 'S')
✅ **Test 1.4**: Cập nhật tên Khoa
✅ **Test 1.5**: Xóa Khoa không có ràng buộc
✅ **Test 1.6**: Kiểm tra lỗi khi thêm Khoa trùng mã

**Kỳ vọng**:
- Insert/Update/Delete route đúng site dựa trên MaKhoa
- Báo lỗi khi thêm mã trùng

---

### TEST 2: MONHOC_GLOBAL (3 tests)
✅ **Test 2.1**: Thêm Môn học mới (sync sang cả 3 sites)
✅ **Test 2.2**: Cập nhật tên Môn học (sync trên 3 sites)
✅ **Test 2.3**: Kiểm tra lỗi khi thêm Môn học trùng mã

**Kỳ vọng**:
- Dữ liệu MonHoc được đồng bộ trên cả 3 sites
- Báo lỗi khi thêm mã trùng

---

### TEST 3: SINHVIEN_GLOBAL (5 tests)
✅ **Test 3.1**: Thêm Sinh viên vào Site A (MaKhoa = 'TEST')
✅ **Test 3.2**: Thêm Sinh viên vào Site B (MaKhoa = 'MTEST')
✅ **Test 3.3**: Cập nhật thông tin Sinh viên (không đổi khoa)
✅ **Test 3.4**: Kiểm tra lỗi khi thêm Sinh viên trùng mã
✅ **Test 3.5**: Kiểm tra lỗi khi thêm Sinh viên với Khoa không tồn tại

**Kỳ vọng**:
- Route đúng site dựa trên MaKhoa của sinh viên
- Validate MaKhoa phải tồn tại trong Khoa_Global
- Báo lỗi khi vi phạm ràng buộc

---

### TEST 4: CTDAOTAO_GLOBAL (6 tests)
✅ **Test 4.1**: Thêm Chương trình đào tạo vào Site A
✅ **Test 4.2**: Thêm Chương trình đào tạo vào Site B
✅ **Test 4.3**: Kiểm tra lỗi khi thêm Chương trình trùng
✅ **Test 4.4**: Kiểm tra lỗi khi thêm với MaKhoa không tồn tại
✅ **Test 4.5**: Kiểm tra lỗi khi thêm với MaMH không tồn tại
✅ **Test 4.6**: Kiểm tra lỗi khi UPDATE (không cho phép)

**Kỳ vọng**:
- Route đúng site dựa trên MaKhoa
- Validate MaKhoa và MaMH phải tồn tại
- Không cho phép UPDATE (composite primary key)

---

### TEST 5: DANGKY_GLOBAL (6 tests)
✅ **Test 5.1**: Thêm Đăng ký cho sinh viên Site A
✅ **Test 5.2**: Thêm Đăng ký cho sinh viên Site B (có điểm)
✅ **Test 5.3**: Cập nhật điểm thi
✅ **Test 5.4**: Kiểm tra lỗi khi thêm Đăng ký trùng
✅ **Test 5.5**: Kiểm tra lỗi khi MaSV không tồn tại
✅ **Test 5.6**: Kiểm tra lỗi khi MaMon không tồn tại

**Kỳ vọng**:
- Route đúng site dựa trên MaKhoa của sinh viên (JOIN với SinhVien_Global)
- Validate MaSV và MaMon phải tồn tại
- Chỉ cho phép UPDATE DiemThi, không cho phép đổi MaSV/MaMon

---

### TEST 6: RÀNG BUỘC XÓA (3 tests)
✅ **Test 6.1**: Kiểm tra lỗi khi xóa Khoa có sinh viên
✅ **Test 6.2**: Kiểm tra lỗi khi xóa Môn học có trong Chương trình
✅ **Test 6.3**: Kiểm tra lỗi khi xóa Sinh viên có đăng ký

**Kỳ vọng**:
- Triggers kiểm tra foreign key constraints
- Báo lỗi rõ ràng khi vi phạm ràng buộc

---

### TEST 7: DỌN DỮ LIỆU (1 test)
✅ **Test 7**: Xóa dữ liệu test theo thứ tự đúng (DangKy → CTDaoTao → SinhVien → MonHoc → Khoa)

**Kỳ vọng**:
- Xóa thành công khi tuân thủ thứ tự ràng buộc

## Kết quả mong đợi

Tất cả test cases phải PASS với các ký hiệu:
- ✅ `✓ Đúng: <thông báo lỗi>` - Test PASS (kỳ vọng có lỗi)
- ✅ `(X rows affected)` - Test PASS (thao tác thành công)
- ❌ `❌ LỖI: <mô tả>` - Test FAIL (không báo lỗi như mong đợi)

## Tạo test cases tùy chỉnh

### Template thêm Khoa mới:
```sql
-- Khoa vào Site A (MaKhoa < 'M')
INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES ('ABC', N'Khoa ABC');

-- Khoa vào Site B ('M' <= MaKhoa < 'S')
INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES ('MNO', N'Khoa MNO');

-- Khoa vào Site C (MaKhoa >= 'S')
INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES ('XYZ', N'Khoa XYZ');
```

### Template thêm Sinh viên:
```sql
INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) 
VALUES ('SV003', N'Lê Văn C', 'CNTT', 2024);
```

### Template thêm Môn học:
```sql
-- Sẽ sync sang cả 3 sites tự động
INSERT INTO MonHoc_Global (MaMH, TenMH) VALUES ('MH001', N'Toán Cao Cấp');
```

### Template thêm Đăng ký:
```sql
-- DiemThi có thể NULL (chưa thi) hoặc có giá trị
INSERT INTO DangKy_Global (MaSV, MaMon, DiemThi) 
VALUES ('SV003', 'MH001', NULL);

-- Cập nhật điểm sau
UPDATE DangKy_Global SET DiemThi = 8.5 
WHERE MaSV = 'SV003' AND MaMon = 'MH001';
```

## Test nâng cao (không có trong test suite)

### Test chuyển sinh viên sang khoa khác (cross-site move):
```sql
-- Kiểm tra SV001 đang ở Site A (MaKhoa = 'TEST')
SELECT * FROM SinhVien_Global WHERE MaSV = 'SV001';

-- Chuyển sang Site B (MaKhoa = 'MTEST')
-- CHÚ Ý: Phải xóa DangKy trước
DELETE FROM DangKy_Global WHERE MaSV = 'SV001';
UPDATE SinhVien_Global SET MaKhoa = 'MTEST' WHERE MaSV = 'SV001';

-- Kiểm tra đã chuyển sang Site B
SELECT * FROM [SITE_B].SiteB.dbo.SinhVien WHERE MaSV = 'SV001';
```

### Test transaction rollback:
```sql
BEGIN TRANSACTION;
    INSERT INTO Khoa_Global (MaKhoa, TenKhoa) VALUES ('TMP', N'Khoa Tạm');
    INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc) 
    VALUES ('SVTMP', N'Sinh viên tạm', 'TMP', 2024);
ROLLBACK TRANSACTION;

-- Kiểm tra không có dữ liệu
SELECT * FROM Khoa_Global WHERE MaKhoa = 'TMP';
SELECT * FROM SinhVien_Global WHERE MaSV = 'SVTMP';
```

## Troubleshooting

### Lỗi "Cannot insert duplicate key"
- Đảm bảo đã chạy TEST 7 để dọn dữ liệu cũ
- Hoặc đổi mã test khác (ví dụ: 'TEST' → 'TEST2')

### Lỗi "Linked server not found"
- Kiểm tra linked servers đã được tạo: `SELECT * FROM sys.servers`
- Chạy lại `db/global/init.sql`

### Lỗi "Foreign key violation"
- Kiểm tra thứ tự INSERT: Khoa → MonHoc → CTDaoTao/SinhVien → DangKy
- Kiểm tra thứ tự DELETE ngược lại

## Best Practices

1. **Luôn test trước khi deploy**: Chạy toàn bộ test suite
2. **Kiểm tra cả 3 sites**: Dùng queries để verify dữ liệu ở đúng site
3. **Test edge cases**: NULL values, empty strings, special characters
4. **Dọn dữ liệu test**: Đảm bảo không ảnh hưởng môi trường production

## Tài liệu tham khảo

- `ARCHITECTURE.md`: Kiến trúc hệ thống
- `CRUD_GUIDE.md`: Hướng dẫn CRUD operations
- `db/global/triggers.sql`: Source code triggers
- `README.md`: Hướng dẫn cài đặt
