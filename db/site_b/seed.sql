-- Chèn dữ liệu mẫu cho Site B (HUFLIT - Khoa 'M' đến < 'S')
-- Dữ liệu dựa trên thông tin thực tế của Đại học Ngoại thương TP.HCM (HUFLIT)

USE SiteB;
GO

-- Chèn dữ liệu vào bảng Khoa
INSERT INTO Khoa (MaKhoa, TenKhoa) VALUES
('NN', N'Ngôn ngữ'),
('NVPD', N'Ngôn ngữ và Văn hóa Phương Đông'),
('QHQT', N'Quan hệ quốc tế'),
('QTKD', N'Quản trị kinh doanh');
GO

-- Chèn dữ liệu vào bảng MonHoc
INSERT INTO MonHoc (MaMH, TenMH) VALUES
('MH019', N'Tiếng Anh cơ bản'),
('MH020', N'Lập trình Java'),
('MH021', N'Phân tích hệ thống'),
('MH022', N'An ninh mạng'),
('MH023', N'Tiếng Trung'),
('MH024', N'Tiếng Nhật'),
('MH025', N'Tiếng Hàn Quốc'),
('MH026', N'Văn hóa Trung Quốc'),
('MH027', N'Văn hóa Nhật Bản'),
('MH028', N'Văn hóa Hàn Quốc'),
('MH029', N'Lịch sử quan hệ quốc tế'),
('MH030', N'Luật quốc tế'),
('MH031', N'Tiếng Trung thương mại'),
('MH032', N'Tiếng Hàn thương mại'),
('MH033', N'Quản trị nhân sự'),
('MH034', N'Marketing quốc tế'),
('MH035', N'Kinh doanh quốc tế'),
('MH036', N'Tài chính quốc tế'),
('MH037', N'Đàm phán quốc tế'),
('MH038', N'Logistics và chuỗi cung ứng');
GO

-- Chèn dữ liệu vào bảng CTDaoTao (2018-2025)
INSERT INTO CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES
-- NN 2018-2022
('NN', 2018, 'MH019'), ('NN', 2018, 'MH023'), ('NN', 2018, 'MH024'),
('NN', 2019, 'MH019'), ('NN', 2019, 'MH023'), ('NN', 2019, 'MH025'),
('NN', 2020, 'MH019'), ('NN', 2020, 'MH023'), ('NN', 2020, 'MH024'),
('NN', 2021, 'MH019'), ('NN', 2021, 'MH023'), ('NN', 2021, 'MH025'),
('NN', 2022, 'MH019'), ('NN', 2022, 'MH023'), ('NN', 2022, 'MH024'),
-- NVPD 2018-2022
('NVPD', 2018, 'MH023'), ('NVPD', 2018, 'MH026'), ('NVPD', 2018, 'MH031'),
('NVPD', 2019, 'MH024'), ('NVPD', 2019, 'MH027'), ('NVPD', 2019, 'MH032'),
('NVPD', 2020, 'MH025'), ('NVPD', 2020, 'MH028'), ('NVPD', 2020, 'MH031'),
('NVPD', 2021, 'MH023'), ('NVPD', 2021, 'MH026'), ('NVPD', 2021, 'MH032'),
('NVPD', 2022, 'MH024'), ('NVPD', 2022, 'MH027'), ('NVPD', 2022, 'MH031'),
-- QHQT 2018-2022
('QHQT', 2018, 'MH029'), ('QHQT', 2018, 'MH030'), ('QHQT', 2018, 'MH037'),
('QHQT', 2019, 'MH029'), ('QHQT', 2019, 'MH030'), ('QHQT', 2019, 'MH037'),
('QHQT', 2020, 'MH029'), ('QHQT', 2020, 'MH030'), ('QHQT', 2020, 'MH037'),
('QHQT', 2021, 'MH029'), ('QHQT', 2021, 'MH030'), ('QHQT', 2021, 'MH037'),
('QHQT', 2022, 'MH029'), ('QHQT', 2022, 'MH030'), ('QHQT', 2022, 'MH037'),
-- QTKD 2018-2022
('QTKD', 2018, 'MH033'), ('QTKD', 2018, 'MH034'), ('QTKD', 2018, 'MH035'),
('QTKD', 2019, 'MH033'), ('QTKD', 2019, 'MH034'), ('QTKD', 2019, 'MH036'),
('QTKD', 2020, 'MH033'), ('QTKD', 2020, 'MH034'), ('QTKD', 2020, 'MH035'),
('QTKD', 2021, 'MH033'), ('QTKD', 2021, 'MH034'), ('QTKD', 2021, 'MH036'),
('QTKD', 2022, 'MH033'), ('QTKD', 2022, 'MH034'), ('QTKD', 2022, 'MH035'),
-- 2023-2025 (mở rộng)
('NN', 2023, 'MH019'), ('NN', 2023, 'MH023'), ('NN', 2023, 'MH024'),
('NVPD', 2023, 'MH023'), ('NVPD', 2023, 'MH026'), ('NVPD', 2023, 'MH031'),
('QHQT', 2023, 'MH029'), ('QHQT', 2023, 'MH030'), ('QHQT', 2023, 'MH037'),
('QTKD', 2023, 'MH033'), ('QTKD', 2023, 'MH034'), ('QTKD', 2023, 'MH035'),
('NN', 2024, 'MH019'), ('NN', 2024, 'MH025'), ('NN', 2024, 'MH031'),
('NVPD', 2024, 'MH024'), ('NVPD', 2024, 'MH027'), ('NVPD', 2024, 'MH032'),
('QHQT', 2024, 'MH029'), ('QHQT', 2024, 'MH030'), ('QHQT', 2024, 'MH038'),
('QTKD', 2024, 'MH033'), ('QTKD', 2024, 'MH036'), ('QTKD', 2024, 'MH037'),
('NN', 2025, 'MH023'), ('NN', 2025, 'MH024'), ('NN', 2025, 'MH025'),
('NVPD', 2025, 'MH023'), ('NVPD', 2025, 'MH026'), ('NVPD', 2025, 'MH031'),
('QHQT', 2025, 'MH029'), ('QHQT', 2025, 'MH037'), ('QHQT', 2025, 'MH038'),
('QTKD', 2025, 'MH035'), ('QTKD', 2025, 'MH036'), ('QTKD', 2025, 'MH037');
GO

-- Chèn dữ liệu vào bảng SinhVien (2018-2025)
INSERT INTO SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES
-- NN students
('SV033', N'Nguyễn Thị GG', 'NN', 2018),
('SV034', N'Trần Văn HH', 'NN', 2019),
('SV035', N'Lê Thị II', 'NN', 2020),
('SV036', N'Phạm Văn JJ', 'NN', 2021),
('SV037', N'Hoàng Thị KK', 'NN', 2022),
('SV038', N'Đỗ Văn LL', 'NN', 2023),
('SV039', N'Bùi Thị MM', 'NN', 2024),
('SV040', N'Vũ Văn NN', 'NN', 2025),
-- NVPD students
('SV041', N'Nguyễn Thị OO', 'NVPD', 2018),
('SV042', N'Trần Văn PP', 'NVPD', 2019),
('SV043', N'Lê Thị QQ', 'NVPD', 2020),
('SV044', N'Phạm Văn RR', 'NVPD', 2021),
('SV045', N'Hoàng Thị SS', 'NVPD', 2022),
('SV046', N'Đỗ Văn TT', 'NVPD', 2023),
('SV047', N'Bùi Thị UU', 'NVPD', 2024),
('SV048', N'Vũ Văn VV', 'NVPD', 2025),
-- QHQT students
('SV049', N'Nguyễn Thị WW', 'QHQT', 2018),
('SV050', N'Trần Văn XX', 'QHQT', 2019),
('SV051', N'Lê Thị YY', 'QHQT', 2020),
('SV052', N'Phạm Văn ZZ', 'QHQT', 2021),
('SV053', N'Hoàng Thị AAA', 'QHQT', 2022),
('SV054', N'Đỗ Văn BBB', 'QHQT', 2023),
('SV055', N'Bùi Thị CCC', 'QHQT', 2024),
('SV056', N'Vũ Văn DDD', 'QHQT', 2025),
-- QTKD students
('SV057', N'Nguyễn Thị EEE', 'QTKD', 2018),
('SV058', N'Trần Văn FFF', 'QTKD', 2019),
('SV059', N'Lê Thị GGG', 'QTKD', 2020),
('SV060', N'Phạm Văn HHH', 'QTKD', 2021),
('SV061', N'Hoàng Thị III', 'QTKD', 2022),
('SV062', N'Đỗ Văn JJJ', 'QTKD', 2023),
('SV063', N'Bùi Thị KKK', 'QTKD', 2024),
('SV064', N'Vũ Văn LLL', 'QTKD', 2025);
GO

-- Chèn dữ liệu vào bảng DangKy (2018-2025)
INSERT INTO DangKy (MaSV, MaMon, DiemThi) VALUES
-- NN students
('SV033', 'MH019', 8.5), ('SV033', 'MH023', 9.0), ('SV033', 'MH024', 8.0),
('SV034', 'MH019', 7.5), ('SV034', 'MH023', 8.5), ('SV034', 'MH025', 9.0),
('SV035', 'MH019', 8.0), ('SV035', 'MH023', 7.0), ('SV035', 'MH024', 8.5),
('SV036', 'MH019', 9.0), ('SV036', 'MH023', 8.5), ('SV036', 'MH025', 7.5),
('SV037', 'MH019', 8.0), ('SV037', 'MH023', 9.0), ('SV037', 'MH024', 8.5),
('SV038', 'MH019', 7.5), ('SV038', 'MH023', 8.0), ('SV038', 'MH025', 9.0),
('SV039', 'MH019', 8.5), ('SV039', 'MH023', 7.0), ('SV039', 'MH024', 8.0),
('SV040', 'MH019', 9.0), ('SV040', 'MH023', 8.5), ('SV040', 'MH025', 7.5),
-- NVPD students
('SV041', 'MH023', 8.0), ('SV041', 'MH026', 9.0), ('SV041', 'MH031', 8.5),
('SV042', 'MH024', 7.5), ('SV042', 'MH027', 8.0), ('SV042', 'MH032', 9.0),
('SV043', 'MH025', 8.5), ('SV043', 'MH028', 7.0), ('SV043', 'MH031', 8.0),
('SV044', 'MH023', 9.0), ('SV044', 'MH026', 8.5), ('SV044', 'MH032', 7.5),
('SV045', 'MH024', 8.0), ('SV045', 'MH027', 9.0), ('SV045', 'MH031', 8.5),
('SV046', 'MH025', 7.5), ('SV046', 'MH028', 8.0), ('SV046', 'MH032', 9.0),
('SV047', 'MH023', 8.5), ('SV047', 'MH026', 7.0), ('SV047', 'MH031', 8.0),
('SV048', 'MH024', 9.0), ('SV048', 'MH027', 8.5), ('SV048', 'MH032', 7.5),
-- QHQT students
('SV049', 'MH029', 8.0), ('SV049', 'MH030', 9.0), ('SV049', 'MH037', 8.5),
('SV050', 'MH029', 7.5), ('SV050', 'MH030', 8.0), ('SV050', 'MH037', 9.0),
('SV051', 'MH029', 8.5), ('SV051', 'MH030', 7.0), ('SV051', 'MH037', 8.0),
('SV052', 'MH029', 9.0), ('SV052', 'MH030', 8.5), ('SV052', 'MH037', 7.5),
('SV053', 'MH029', 8.0), ('SV053', 'MH030', 9.0), ('SV053', 'MH037', 8.5),
('SV054', 'MH029', 7.5), ('SV054', 'MH030', 8.0), ('SV054', 'MH037', 9.0),
('SV055', 'MH029', 8.5), ('SV055', 'MH030', 7.0), ('SV055', 'MH037', 8.0),
('SV056', 'MH029', 9.0), ('SV056', 'MH030', 8.5), ('SV056', 'MH038', 7.5),
-- QTKD students
('SV057', 'MH033', 8.0), ('SV057', 'MH034', 9.0), ('SV057', 'MH035', 8.5),
('SV058', 'MH033', 7.5), ('SV058', 'MH034', 8.0), ('SV058', 'MH036', 9.0),
('SV059', 'MH033', 8.5), ('SV059', 'MH034', 7.0), ('SV059', 'MH035', 8.0),
('SV060', 'MH033', 9.0), ('SV060', 'MH034', 8.5), ('SV060', 'MH036', 7.5),
('SV061', 'MH033', 8.0), ('SV061', 'MH034', 9.0), ('SV061', 'MH035', 8.5),
('SV062', 'MH033', 7.5), ('SV062', 'MH034', 8.0), ('SV062', 'MH036', 9.0),
('SV063', 'MH033', 8.5), ('SV063', 'MH034', 7.0), ('SV063', 'MH035', 8.0),
('SV064', 'MH033', 9.0), ('SV064', 'MH034', 8.5), ('SV064', 'MH037', 7.5);
GO
