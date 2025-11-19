-- Dữ liệu mẫu cho database Global - Site C
USE Global;
GO

-- Chèn dữ liệu vào bảng Khoa
-- (Không có khoa nào bắt đầu bằng S-Z trong data mẫu)
-- INSERT INTO Khoa (MaKhoa, TenKhoa) VALUES
-- ('SP', N'Thể thao');
GO

-- Chèn dữ liệu vào bảng MonHoc
INSERT INTO MonHoc (MaMH, TenMH) VALUES
('MH001', N'Toán cao cấp'),
('MH004', N'Vật lý đại cương'),
('MH009', N'Quản lý du lịch'),
('MH010', N'Ngôn ngữ Anh chuyên ngành'),
('MH017', N'Luật dân sự'),
('MH018', N'Luật hình sự'),
('MH019', N'Tư tưởng Hồ Chí Minh');
GO

-- Chèn dữ liệu vào bảng CTDaoTao
-- (Không có CTDaoTao cho khoa >= S)
-- INSERT INTO CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES
-- ('DLKS', 2020, 'MH001'),
-- ('DLKS', 2020, 'MH004'),
-- ('DLKS', 2020, 'MH009'),
-- ('DLKS', 2020, 'MH010'),
-- ('LUAT', 2020, 'MH017'),
-- ('LUAT', 2020, 'MH018'),
-- ('LLCT', 2020, 'MH019');
GO

-- Chèn dữ liệu vào bảng SinhVien
-- (Không có SinhVien cho khoa >= S)
-- INSERT INTO SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES
-- ('SV004', N'Phạm Thị D', 'DLKS', 2020),
-- ('SV006', N'Đỗ Văn F', 'DLKS', 2020),
-- ('SV011', N'Vũ Minh H', 'DLKS', 2021),
-- ('SV012', N'Trần Thu I', 'DLKS', 2021),
-- ('SV017', N'Lê Quốc J', 'LUAT', 2020),
-- ('SV018', N'Nguyễn Anh K', 'LLCT', 2020);
GO

-- Chèn dữ liệu vào bảng DangKy
-- (Không có DangKy cho SinhVien >= S)
-- INSERT INTO DangKy (MaSV, MaMon, DiemThi) VALUES
-- ('SV004', 'MH001', 9.0),
-- ('SV004', 'MH004', 8.5),
-- ('SV004', 'MH009', 9.5),
-- ('SV006', 'MH001', 7.0),
-- ('SV006', 'MH004', 8.0),
-- ('SV006', 'MH010', 7.5),
-- ('SV011', 'MH001', 8.5),
-- ('SV011', 'MH009', 9.0),
-- ('SV012', 'MH004', 8.0),
-- ('SV012', 'MH010', 9.0),
-- ('SV017', 'MH017', 8.5),
-- ('SV018', 'MH019', 9.0);
GO