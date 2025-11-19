-- Dữ liệu mẫu cho database Global - Site A
USE Global;
GO

-- Chèn dữ liệu vào bảng Khoa
INSERT INTO Khoa (MaKhoa, TenKhoa) VALUES
('CNTT', N'Công nghệ thông tin'),
('DLKS', N'Du lịch - Khách sạn'),
('KTTC', N'Kinh tế - Tài chính'),
('LLCT', N'Lý luận chính trị'),
('LUAT', N'Luật');
GO

-- Chèn dữ liệu vào bảng MonHoc
INSERT INTO MonHoc (MaMH, TenMH) VALUES
('MH001', N'Toán cao cấp'),
('MH002', N'Lập trình C'),
('MH005', N'Cơ sở dữ liệu'),
('MH006', N'Mạng máy tính'),
('MH011', N'Tiếng Anh cơ bản'),
('MH012', N'Tiếng Nhật'),
('MH013', N'Tiếng Hàn');
GO

-- Chèn dữ liệu vào bảng CTDaoTao
INSERT INTO CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES
('CNTT', 2020, 'MH001'),
('CNTT', 2020, 'MH002'),
('CNTT', 2020, 'MH005'),
('CNTT', 2020, 'MH006'),
('DLKS', 2020, 'MH001'),
('DLKS', 2020, 'MH011'),
('KTTC', 2020, 'MH001'),
('KTTC', 2020, 'MH005'),
('LLCT', 2020, 'MH013'),
('LUAT', 2020, 'MH012');
GO

-- Chèn dữ liệu vào bảng SinhVien
INSERT INTO SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES
('SV001', N'Nguyễn Văn A', 'CNTT', 2020),
('SV002', N'Trần Thị B', 'CNTT', 2020),
('SV007', N'Lê Minh C', 'CNTT', 2021),
('SV008', N'Phạm Anh D', 'CNTT', 2021),
('SV013', N'Hoàng Lan E', 'DLKS', 2020),
('SV014', N'Vũ Quốc F', 'KTTC', 2020),
('SV019', N'Đỗ Minh G', 'LLCT', 2020),
('SV020', N'Ngô Anh H', 'LUAT', 2020);
GO

-- Chèn dữ liệu vào bảng DangKy
INSERT INTO DangKy (MaSV, MaMon, DiemThi) VALUES
('SV001', 'MH001', 8.5),
('SV001', 'MH002', 9.0),
('SV001', 'MH005', 8.0),
('SV002', 'MH001', 7.5),
('SV002', 'MH002', 8.0),
('SV002', 'MH006', 7.0),
('SV007', 'MH001', 9.0),
('SV007', 'MH005', 8.5),
('SV008', 'MH002', 8.5),
('SV008', 'MH006', 9.0),
('SV013', 'MH001', 8.0),
('SV013', 'MH011', 8.5),
('SV014', 'MH001', 9.5),
('SV014', 'MH005', 9.0),
('SV019', 'MH013', 8.0),
('SV020', 'MH012', 8.5);
GO