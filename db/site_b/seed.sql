-- Dữ liệu mẫu cho database Global - Site B
USE Global;
GO

-- Chèn dữ liệu vào bảng Khoa
INSERT INTO Khoa (MaKhoa, TenKhoa) VALUES
('QHQT', N'Quan hệ Quốc tế và Truyền thông'),
('QTKD', N'Quản trị kinh doanh'),
('KTTC', N'Kinh tế - Tài chính');
GO

-- Chèn dữ liệu vào bảng MonHoc
INSERT INTO MonHoc (MaMH, TenMH) VALUES
('MH001', N'Toán cao cấp'),
('MH003', N'Kinh tế vi mô'),
('MH007', N'Kinh tế vĩ mô'),
('MH008', N'Tài chính doanh nghiệp'),
('MH014', N'Quan hệ Quốc tế'),
('MH015', N'Marketing'),
('MH016', N'Quản trị nhân sự');
GO

-- Chèn dữ liệu vào bảng CTDaoTao
INSERT INTO CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES
('QHQT', 2020, 'MH014'),
('QTKD', 2020, 'MH001'),
('QTKD', 2020, 'MH003'),
('QTKD', 2020, 'MH015'),
('QTKD', 2020, 'MH016'),
('KTTC', 2020, 'MH001'),
('KTTC', 2020, 'MH007'),
('KTTC', 2020, 'MH008');
GO

-- Chèn dữ liệu vào bảng SinhVien
INSERT INTO SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES
('SV003', N'Lê Văn C', 'KTTC', 2020),
('SV005', N'Hoàng Thị E', 'KTTC', 2020),
('SV009', N'Trần Quốc F', 'KTTC', 2021),
('SV010', N'Nguyễn Lan G', 'KTTC', 2021),
('SV015', N'Phạm Minh H', 'QHQT', 2020),
('SV016', N'Đỗ Anh I', 'QTKD', 2020);
GO

-- Chèn dữ liệu vào bảng DangKy
INSERT INTO DangKy (MaSV, MaMon, DiemThi) VALUES
('SV003', 'MH001', 6.5),
('SV003', 'MH007', 7.0),
('SV003', 'MH008', 6.0),
('SV005', 'MH001', 8.0),
('SV005', 'MH007', 8.5),
('SV005', 'MH008', 7.5),
('SV009', 'MH001', 9.0),
('SV009', 'MH007', 8.5),
('SV010', 'MH007', 8.0),
('SV010', 'MH008', 9.0),
('SV015', 'MH014', 8.5),
('SV016', 'MH015', 9.0);
GO