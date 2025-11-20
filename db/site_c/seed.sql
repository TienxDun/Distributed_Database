-- Chèn dữ liệu mẫu cho Site C (HUFLIT - Khoa >= 'S')
-- Dữ liệu dựa trên thông tin thực tế của Đại học Ngoại thương TP.HCM (HUFLIT)

USE SiteC;
GO

-- Chèn dữ liệu vào bảng Khoa
INSERT INTO Khoa (MaKhoa, TenKhoa) VALUES
('SLCT', N'Sư phạm Lịch sử'),
('SUAT', N'Sư phạm Anh'),
('TLKS', N'Thể dục thể thao');
GO

-- Chèn dữ liệu vào bảng MonHoc
INSERT INTO MonHoc (MaMH, TenMH) VALUES
('MH039', N'Lịch sử thế giới'),
('MH040', N'Lịch sử Việt Nam'),
('MH041', N'Giáo dục học'),
('MH042', N'Tâm lý học giáo dục'),
('MH043', N'Phương pháp dạy học'),
('MH044', N'Tiếng Anh nâng cao'),
('MH045', N'Văn học Anh'),
('MH046', N'Ngôn ngữ học'),
('MH047', N'Giáo dục thể chất'),
('MH048', N'Thể dục học'),
('MH049', N'Giáo dục thể dục'),
('MH050', N'Y học thể dục'),
('MH051', N'Quản lý thể dục thể thao'),
('MH052', N'Đạo đức giáo dục'),
('MH053', N'Công nghệ thông tin trong giáo dục');
GO

-- Chèn dữ liệu vào bảng CTDaoTao (2018-2025)
INSERT INTO CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES
-- SLCT 2018-2022
('SLCT', 2018, 'MH039'), ('SLCT', 2018, 'MH040'), ('SLCT', 2018, 'MH041'),
('SLCT', 2019, 'MH039'), ('SLCT', 2019, 'MH040'), ('SLCT', 2019, 'MH042'),
('SLCT', 2020, 'MH039'), ('SLCT', 2020, 'MH040'), ('SLCT', 2020, 'MH041'),
('SLCT', 2021, 'MH039'), ('SLCT', 2021, 'MH040'), ('SLCT', 2021, 'MH042'),
('SLCT', 2022, 'MH039'), ('SLCT', 2022, 'MH040'), ('SLCT', 2022, 'MH041'),
-- SUAT 2018-2022
('SUAT', 2018, 'MH044'), ('SUAT', 2018, 'MH045'), ('SUAT', 2018, 'MH046'),
('SUAT', 2019, 'MH044'), ('SUAT', 2019, 'MH045'), ('SUAT', 2019, 'MH043'),
('SUAT', 2020, 'MH044'), ('SUAT', 2020, 'MH045'), ('SUAT', 2020, 'MH046'),
('SUAT', 2021, 'MH044'), ('SUAT', 2021, 'MH045'), ('SUAT', 2021, 'MH043'),
('SUAT', 2022, 'MH044'), ('SUAT', 2022, 'MH045'), ('SUAT', 2022, 'MH046'),
-- TLKS 2018-2022
('TLKS', 2018, 'MH047'), ('TLKS', 2018, 'MH048'), ('TLKS', 2018, 'MH049'),
('TLKS', 2019, 'MH047'), ('TLKS', 2019, 'MH048'), ('TLKS', 2019, 'MH050'),
('TLKS', 2020, 'MH047'), ('TLKS', 2020, 'MH048'), ('TLKS', 2020, 'MH049'),
('TLKS', 2021, 'MH047'), ('TLKS', 2021, 'MH048'), ('TLKS', 2021, 'MH050'),
('TLKS', 2022, 'MH047'), ('TLKS', 2022, 'MH048'), ('TLKS', 2022, 'MH049'),
-- 2023-2025 (mở rộng)
('SLCT', 2023, 'MH039'), ('SLCT', 2023, 'MH040'), ('SLCT', 2023, 'MH041'),
('SUAT', 2023, 'MH044'), ('SUAT', 2023, 'MH045'), ('SUAT', 2023, 'MH046'),
('TLKS', 2023, 'MH047'), ('TLKS', 2023, 'MH048'), ('TLKS', 2023, 'MH049'),
('SLCT', 2024, 'MH039'), ('SLCT', 2024, 'MH042'), ('SLCT', 2024, 'MH043'),
('SUAT', 2024, 'MH044'), ('SUAT', 2024, 'MH045'), ('SUAT', 2024, 'MH053'),
('TLKS', 2024, 'MH047'), ('TLKS', 2024, 'MH050'), ('TLKS', 2024, 'MH051'),
('SLCT', 2025, 'MH040'), ('SLCT', 2025, 'MH041'), ('SLCT', 2025, 'MH042'),
('SUAT', 2025, 'MH045'), ('SUAT', 2025, 'MH046'), ('SUAT', 2025, 'MH053'),
('TLKS', 2025, 'MH048'), ('TLKS', 2025, 'MH049'), ('TLKS', 2025, 'MH050');
GO

-- Chèn dữ liệu vào bảng SinhVien (2018-2025)
INSERT INTO SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES
-- SLCT students
('SV065', N'Nguyễn Thị MMM', 'SLCT', 2018),
('SV066', N'Trần Văn NNN', 'SLCT', 2019),
('SV067', N'Lê Thị OOO', 'SLCT', 2020),
('SV068', N'Phạm Văn PPP', 'SLCT', 2021),
('SV069', N'Hoàng Thị QQQ', 'SLCT', 2022),
('SV070', N'Đỗ Văn RRR', 'SLCT', 2023),
('SV071', N'Bùi Thị SSS', 'SLCT', 2024),
('SV072', N'Vũ Văn TTT', 'SLCT', 2025),
-- SUAT students
('SV073', N'Nguyễn Thị UUU', 'SUAT', 2018),
('SV074', N'Trần Văn VVV', 'SUAT', 2019),
('SV075', N'Lê Thị WWW', 'SUAT', 2020),
('SV076', N'Phạm Văn XXX', 'SUAT', 2021),
('SV077', N'Hoàng Thị YYY', 'SUAT', 2022),
('SV078', N'Đỗ Văn ZZZ', 'SUAT', 2023),
('SV079', N'Bùi Thị AAAA', 'SUAT', 2024),
('SV080', N'Vũ Văn BBBB', 'SUAT', 2025),
-- TLKS students
('SV081', N'Nguyễn Thị CCCC', 'TLKS', 2018),
('SV082', N'Trần Văn DDDD', 'TLKS', 2019),
('SV083', N'Lê Thị EEEE', 'TLKS', 2020),
('SV084', N'Phạm Văn FFFF', 'TLKS', 2021),
('SV085', N'Hoàng Thị GGGG', 'TLKS', 2022),
('SV086', N'Đỗ Văn HHHH', 'TLKS', 2023),
('SV087', N'Bùi Thị IIII', 'TLKS', 2024),
('SV088', N'Vũ Văn JJJJ', 'TLKS', 2025);
GO

-- Chèn dữ liệu vào bảng DangKy (2018-2025)
INSERT INTO DangKy (MaSV, MaMon, DiemThi) VALUES
-- SLCT students
('SV065', 'MH039', 8.5), ('SV065', 'MH040', 9.0), ('SV065', 'MH041', 8.0),
('SV066', 'MH039', 7.5), ('SV066', 'MH040', 8.5), ('SV066', 'MH042', 9.0),
('SV067', 'MH039', 8.0), ('SV067', 'MH040', 7.0), ('SV067', 'MH041', 8.5),
('SV068', 'MH039', 9.0), ('SV068', 'MH040', 8.5), ('SV068', 'MH042', 7.5),
('SV069', 'MH039', 8.0), ('SV069', 'MH040', 9.0), ('SV069', 'MH041', 8.5),
('SV070', 'MH039', 7.5), ('SV070', 'MH040', 8.0), ('SV070', 'MH042', 9.0),
('SV071', 'MH039', 8.5), ('SV071', 'MH040', 7.0), ('SV071', 'MH041', 8.0),
('SV072', 'MH039', 9.0), ('SV072', 'MH040', 8.5), ('SV072', 'MH042', 7.5),
-- SUAT students
('SV073', 'MH044', 8.0), ('SV073', 'MH045', 9.0), ('SV073', 'MH046', 8.5),
('SV074', 'MH044', 7.5), ('SV074', 'MH045', 8.0), ('SV074', 'MH043', 9.0),
('SV075', 'MH044', 8.5), ('SV075', 'MH045', 7.0), ('SV075', 'MH046', 8.0),
('SV076', 'MH044', 9.0), ('SV076', 'MH045', 8.5), ('SV076', 'MH043', 7.5),
('SV077', 'MH044', 8.0), ('SV077', 'MH045', 9.0), ('SV077', 'MH046', 8.5),
('SV078', 'MH044', 7.5), ('SV078', 'MH045', 8.0), ('SV078', 'MH043', 9.0),
('SV079', 'MH044', 8.5), ('SV079', 'MH045', 7.0), ('SV079', 'MH046', 8.0),
('SV080', 'MH044', 9.0), ('SV080', 'MH045', 8.5), ('SV080', 'MH053', 7.5),
-- TLKS students
('SV081', 'MH047', 8.0), ('SV081', 'MH048', 9.0), ('SV081', 'MH049', 8.5),
('SV082', 'MH047', 7.5), ('SV082', 'MH048', 8.0), ('SV082', 'MH050', 9.0),
('SV083', 'MH047', 8.5), ('SV083', 'MH048', 7.0), ('SV083', 'MH049', 8.0),
('SV084', 'MH047', 9.0), ('SV084', 'MH048', 8.5), ('SV084', 'MH050', 7.5),
('SV085', 'MH047', 8.0), ('SV085', 'MH048', 9.0), ('SV085', 'MH049', 8.5),
('SV086', 'MH047', 7.5), ('SV086', 'MH048', 8.0), ('SV086', 'MH050', 9.0),
('SV087', 'MH047', 8.5), ('SV087', 'MH048', 7.0), ('SV087', 'MH049', 8.0),
('SV088', 'MH047', 9.0), ('SV088', 'MH048', 8.5), ('SV088', 'MH051', 7.5);
GO
