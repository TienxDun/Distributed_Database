-- Chèn dữ liệu mẫu cho Site A (HUFLIT - Khoa < 'M')
-- Dữ liệu dựa trên thông tin thực tế của Đại học Ngoại thương TP.HCM (HUFLIT)

USE SiteA;
GO

-- Chèn dữ liệu vào bảng Khoa
INSERT INTO Khoa (MaKhoa, TenKhoa) VALUES
('CNTT', N'Công nghệ thông tin'),
('DLKS', N'Du lịch khách sạn'),
('KTTC', N'Kế toán tài chính'),
('LLCT', N'Luật công ty');
GO

-- Chèn dữ liệu vào bảng MonHoc
INSERT INTO MonHoc (MaMH, TenMH) VALUES
('MH001', N'Toán cao cấp'),
('MH002', N'Lập trình C'),
('MH003', N'Cấu trúc dữ liệu'),
('MH004', N'Hệ điều hành'),
('MH005', N'Cơ sở dữ liệu'),
('MH006', N'Mạng máy tính'),
('MH007', N'Kinh tế vĩ mô'),
('MH008', N'Nguyên lý kế toán'),
('MH009', N'Tài chính doanh nghiệp'),
('MH010', N'Luật thương mại'),
('MH011', N'Luật dân sự'),
('MH012', N'Quản trị khách sạn'),
('MH013', N'Du lịch quốc tế'),
('MH014', N'Ngôn ngữ Anh thương mại'),
('MH015', N'Marketing du lịch'),
('MH016', N'Kỹ năng mềm'),
('MH017', N'Pháp luật kinh doanh'),
('MH018', N'Thực tập doanh nghiệp');
GO

-- Chèn dữ liệu vào bảng CTDaoTao (2018-2025)
INSERT INTO CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES
-- CNTT 2018-2022
('CNTT', 2018, 'MH001'), ('CNTT', 2018, 'MH002'), ('CNTT', 2018, 'MH003'),
('CNTT', 2019, 'MH001'), ('CNTT', 2019, 'MH002'), ('CNTT', 2019, 'MH004'),
('CNTT', 2020, 'MH005'), ('CNTT', 2020, 'MH006'), ('CNTT', 2020, 'MH003'),
('CNTT', 2021, 'MH005'), ('CNTT', 2021, 'MH006'), ('CNTT', 2021, 'MH004'),
('CNTT', 2022, 'MH001'), ('CNTT', 2022, 'MH002'), ('CNTT', 2022, 'MH005'),
-- DLKS 2018-2022
('DLKS', 2018, 'MH007'), ('DLKS', 2018, 'MH012'), ('DLKS', 2018, 'MH013'),
('DLKS', 2019, 'MH014'), ('DLKS', 2019, 'MH015'), ('DLKS', 2019, 'MH016'),
('DLKS', 2020, 'MH007'), ('DLKS', 2020, 'MH012'), ('DLKS', 2020, 'MH013'),
('DLKS', 2021, 'MH014'), ('DLKS', 2021, 'MH015'), ('DLKS', 2021, 'MH016'),
('DLKS', 2022, 'MH007'), ('DLKS', 2022, 'MH012'), ('DLKS', 2022, 'MH014'),
-- KTTC 2018-2022
('KTTC', 2018, 'MH008'), ('KTTC', 2018, 'MH009'), ('KTTC', 2018, 'MH007'),
('KTTC', 2019, 'MH008'), ('KTTC', 2019, 'MH009'), ('KTTC', 2019, 'MH016'),
('KTTC', 2020, 'MH008'), ('KTTC', 2020, 'MH009'), ('KTTC', 2020, 'MH017'),
('KTTC', 2021, 'MH008'), ('KTTC', 2021, 'MH009'), ('KTTC', 2021, 'MH016'),
('KTTC', 2022, 'MH008'), ('KTTC', 2022, 'MH009'), ('KTTC', 2022, 'MH017'),
-- LLCT 2018-2022
('LLCT', 2018, 'MH010'), ('LLCT', 2018, 'MH011'), ('LLCT', 2018, 'MH017'),
('LLCT', 2019, 'MH010'), ('LLCT', 2019, 'MH011'), ('LLCT', 2019, 'MH016'),
('LLCT', 2020, 'MH010'), ('LLCT', 2020, 'MH011'), ('LLCT', 2020, 'MH017'),
('LLCT', 2021, 'MH010'), ('LLCT', 2021, 'MH011'), ('LLCT', 2021, 'MH016'),
('LLCT', 2022, 'MH010'), ('LLCT', 2022, 'MH011'), ('LLCT', 2022, 'MH017'),
-- 2023-2025 (mở rộng)
('CNTT', 2023, 'MH001'), ('CNTT', 2023, 'MH002'), ('CNTT', 2023, 'MH005'),
('DLKS', 2023, 'MH007'), ('DLKS', 2023, 'MH012'), ('DLKS', 2023, 'MH014'),
('KTTC', 2023, 'MH008'), ('KTTC', 2023, 'MH009'), ('KTTC', 2023, 'MH016'),
('LLCT', 2023, 'MH010'), ('LLCT', 2023, 'MH011'), ('LLCT', 2023, 'MH017'),
('CNTT', 2024, 'MH003'), ('CNTT', 2024, 'MH004'), ('CNTT', 2024, 'MH006'),
('DLKS', 2024, 'MH013'), ('DLKS', 2024, 'MH015'), ('DLKS', 2024, 'MH016'),
('KTTC', 2024, 'MH007'), ('KTTC', 2024, 'MH017'), ('KTTC', 2024, 'MH018'),
('LLCT', 2024, 'MH010'), ('LLCT', 2024, 'MH011'), ('LLCT', 2024, 'MH016'),
('CNTT', 2025, 'MH001'), ('CNTT', 2025, 'MH005'), ('CNTT', 2025, 'MH006'),
('DLKS', 2025, 'MH007'), ('DLKS', 2025, 'MH014'), ('DLKS', 2025, 'MH015'),
('KTTC', 2025, 'MH008'), ('KTTC', 2025, 'MH009'), ('KTTC', 2025, 'MH017'),
('LLCT', 2025, 'MH010'), ('LLCT', 2025, 'MH011'), ('LLCT', 2025, 'MH018');
GO

-- Chèn dữ liệu vào bảng SinhVien (2018-2025)
INSERT INTO SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES
-- CNTT students
('SV001', N'Nguyễn Văn A', 'CNTT', 2018),
('SV002', N'Trần Thị B', 'CNTT', 2019),
('SV003', N'Lê Văn C', 'CNTT', 2020),
('SV004', N'Phạm Thị D', 'CNTT', 2021),
('SV005', N'Hoàng Văn E', 'CNTT', 2022),
('SV006', N'Đỗ Thị F', 'CNTT', 2023),
('SV007', N'Bùi Văn G', 'CNTT', 2024),
('SV008', N'Vũ Thị H', 'CNTT', 2025),
-- DLKS students
('SV009', N'Nguyễn Thị I', 'DLKS', 2018),
('SV010', N'Trần Văn J', 'DLKS', 2019),
('SV011', N'Lê Thị K', 'DLKS', 2020),
('SV012', N'Phạm Văn L', 'DLKS', 2021),
('SV013', N'Hoàng Thị M', 'DLKS', 2022),
('SV014', N'Đỗ Văn N', 'DLKS', 2023),
('SV015', N'Bùi Thị O', 'DLKS', 2024),
('SV016', N'Vũ Văn P', 'DLKS', 2025),
-- KTTC students
('SV017', N'Nguyễn Văn Q', 'KTTC', 2018),
('SV018', N'Trần Thị R', 'KTTC', 2019),
('SV019', N'Lê Văn S', 'KTTC', 2020),
('SV020', N'Phạm Thị T', 'KTTC', 2021),
('SV021', N'Hoàng Văn U', 'KTTC', 2022),
('SV022', N'Đỗ Thị V', 'KTTC', 2023),
('SV023', N'Bùi Văn W', 'KTTC', 2024),
('SV024', N'Vũ Thị X', 'KTTC', 2025),
-- LLCT students
('SV025', N'Nguyễn Văn Y', 'LLCT', 2018),
('SV026', N'Trần Thị Z', 'LLCT', 2019),
('SV027', N'Lê Văn AA', 'LLCT', 2020),
('SV028', N'Phạm Thị BB', 'LLCT', 2021),
('SV029', N'Hoàng Văn CC', 'LLCT', 2022),
('SV030', N'Đỗ Thị DD', 'LLCT', 2023),
('SV031', N'Bùi Văn EE', 'LLCT', 2024),
('SV032', N'Vũ Thị FF', 'LLCT', 2025);
GO

-- Chèn dữ liệu vào bảng DangKy (2018-2025)
INSERT INTO DangKy (MaSV, MaMon, DiemThi) VALUES
-- CNTT students
('SV001', 'MH001', 8.5), ('SV001', 'MH002', 9.0), ('SV001', 'MH003', 7.5),
('SV002', 'MH001', 8.0), ('SV002', 'MH002', 8.5), ('SV002', 'MH004', 9.0),
('SV003', 'MH005', 7.0), ('SV003', 'MH006', 8.5), ('SV003', 'MH003', 8.0),
('SV004', 'MH005', 9.0), ('SV004', 'MH006', 8.5), ('SV004', 'MH004', 7.5),
('SV005', 'MH001', 8.0), ('SV005', 'MH002', 9.0), ('SV005', 'MH005', 8.5),
('SV006', 'MH001', 7.5), ('SV006', 'MH002', 8.0), ('SV006', 'MH005', 9.0),
('SV007', 'MH003', 8.5), ('SV007', 'MH004', 7.0), ('SV007', 'MH006', 8.0),
('SV008', 'MH001', 9.0), ('SV008', 'MH005', 8.5), ('SV008', 'MH006', 7.5),
-- DLKS students
('SV009', 'MH007', 8.0), ('SV009', 'MH012', 9.0), ('SV009', 'MH013', 8.5),
('SV010', 'MH014', 7.5), ('SV010', 'MH015', 8.0), ('SV010', 'MH016', 9.0),
('SV011', 'MH007', 8.5), ('SV011', 'MH012', 7.0), ('SV011', 'MH013', 8.0),
('SV012', 'MH014', 9.0), ('SV012', 'MH015', 8.5), ('SV012', 'MH016', 7.5),
('SV013', 'MH007', 8.0), ('SV013', 'MH012', 9.0), ('SV013', 'MH014', 8.5),
('SV014', 'MH013', 7.5), ('SV014', 'MH015', 8.0), ('SV014', 'MH016', 9.0),
('SV015', 'MH007', 8.5), ('SV015', 'MH012', 7.0), ('SV015', 'MH014', 8.0),
('SV016', 'MH013', 9.0), ('SV016', 'MH015', 8.5), ('SV016', 'MH016', 7.5),
-- KTTC students
('SV017', 'MH008', 8.0), ('SV017', 'MH009', 9.0), ('SV017', 'MH007', 8.5),
('SV018', 'MH008', 7.5), ('SV018', 'MH009', 8.0), ('SV018', 'MH016', 9.0),
('SV019', 'MH008', 8.5), ('SV019', 'MH009', 7.0), ('SV019', 'MH017', 8.0),
('SV020', 'MH008', 9.0), ('SV020', 'MH009', 8.5), ('SV020', 'MH016', 7.5),
('SV021', 'MH008', 8.0), ('SV021', 'MH009', 9.0), ('SV021', 'MH017', 8.5),
('SV022', 'MH008', 7.5), ('SV022', 'MH009', 8.0), ('SV022', 'MH016', 9.0),
('SV023', 'MH008', 8.5), ('SV023', 'MH009', 7.0), ('SV023', 'MH017', 8.0),
('SV024', 'MH008', 9.0), ('SV024', 'MH009', 8.5), ('SV024', 'MH018', 7.5),
-- LLCT students
('SV025', 'MH010', 8.0), ('SV025', 'MH011', 9.0), ('SV025', 'MH017', 8.5),
('SV026', 'MH010', 7.5), ('SV026', 'MH011', 8.0), ('SV026', 'MH016', 9.0),
('SV027', 'MH010', 8.5), ('SV027', 'MH011', 7.0), ('SV027', 'MH017', 8.0),
('SV028', 'MH010', 9.0), ('SV028', 'MH011', 8.5), ('SV028', 'MH016', 7.5),
('SV029', 'MH010', 8.0), ('SV029', 'MH011', 9.0), ('SV029', 'MH017', 8.5),
('SV030', 'MH010', 7.5), ('SV030', 'MH011', 8.0), ('SV030', 'MH016', 9.0),
('SV031', 'MH010', 8.5), ('SV031', 'MH011', 7.0), ('SV031', 'MH017', 8.0),
('SV032', 'MH010', 9.0), ('SV032', 'MH011', 8.5), ('SV032', 'MH018', 7.5);
GO
