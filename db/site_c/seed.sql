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

-- Chèn dữ liệu vào bảng SinhVien (2022-2025)
INSERT INTO SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES
-- SLCT students
('22DH000017', N'Nguyễn Thị MMM', 'SLCT', 2022),
('22DH000018', N'Trần Văn NNN', 'SLCT', 2022),
('23DH000017', N'Lê Thị OOO', 'SLCT', 2023),
('23DH000018', N'Phạm Văn PPP', 'SLCT', 2023),
('24DH000017', N'Hoàng Thị QQQ', 'SLCT', 2024),
('24DH000018', N'Đỗ Văn RRR', 'SLCT', 2024),
('25DH000017', N'Bùi Thị SSS', 'SLCT', 2025),
('25DH000018', N'Vũ Văn TTT', 'SLCT', 2025),
-- SUAT students
('22DH000019', N'Nguyễn Thị UUU', 'SUAT', 2022),
('22DH000020', N'Trần Văn VVV', 'SUAT', 2022),
('23DH000019', N'Lê Thị WWW', 'SUAT', 2023),
('23DH000020', N'Phạm Văn XXX', 'SUAT', 2023),
('24DH000019', N'Hoàng Thị YYY', 'SUAT', 2024),
('24DH000020', N'Đỗ Văn ZZZ', 'SUAT', 2024),
('25DH000019', N'Bùi Thị AAAA', 'SUAT', 2025),
('25DH000020', N'Vũ Văn BBBB', 'SUAT', 2025),
-- TLKS students
('22DH000021', N'Nguyễn Thị CCCC', 'TLKS', 2022),
('22DH000022', N'Trần Văn DDDD', 'TLKS', 2022),
('23DH000021', N'Lê Thị EEEE', 'TLKS', 2023),
('23DH000022', N'Phạm Văn FFFF', 'TLKS', 2023),
('24DH000021', N'Hoàng Thị GGGG', 'TLKS', 2024),
('24DH000022', N'Đỗ Văn HHHH', 'TLKS', 2024),
('25DH000021', N'Bùi Thị IIII', 'TLKS', 2025),
('25DH000022', N'Vũ Văn JJJJ', 'TLKS', 2025);
GO

-- Chèn dữ liệu vào bảng DangKy (2022-2025)
INSERT INTO DangKy (MaSV, MaMon, DiemThi) VALUES
-- SLCT students
('22DH000017', 'MH039', 8.0), ('22DH000017', 'MH040', 9.0), ('22DH000017', 'MH041', 8.5),
('22DH000018', 'MH039', 7.5), ('22DH000018', 'MH040', 8.0), ('22DH000018', 'MH041', 9.0),
('23DH000017', 'MH039', 8.5), ('23DH000017', 'MH040', 7.0), ('23DH000017', 'MH041', 8.0),
('23DH000018', 'MH039', 9.0), ('23DH000018', 'MH040', 8.5), ('23DH000018', 'MH041', 7.5),
('24DH000017', 'MH039', 8.0), ('24DH000017', 'MH042', 9.0), ('24DH000017', 'MH043', 8.5),
('24DH000018', 'MH039', 7.5), ('24DH000018', 'MH042', 8.0), ('24DH000018', 'MH043', 9.0),
('25DH000017', 'MH040', 8.5), ('25DH000017', 'MH041', 7.0), ('25DH000017', 'MH042', 8.0),
('25DH000018', 'MH040', 9.0), ('25DH000018', 'MH041', 8.5), ('25DH000018', 'MH042', 7.5),
-- SUAT students
('22DH000019', 'MH044', 8.0), ('22DH000019', 'MH045', 9.0), ('22DH000019', 'MH046', 8.5),
('22DH000020', 'MH044', 7.5), ('22DH000020', 'MH045', 8.0), ('22DH000020', 'MH046', 9.0),
('23DH000019', 'MH044', 8.5), ('23DH000019', 'MH045', 7.0), ('23DH000019', 'MH046', 8.0),
('23DH000020', 'MH044', 9.0), ('23DH000020', 'MH045', 8.5), ('23DH000020', 'MH046', 7.5),
('24DH000019', 'MH044', 8.0), ('24DH000019', 'MH045', 9.0), ('24DH000019', 'MH053', 8.5),
('24DH000020', 'MH044', 7.5), ('24DH000020', 'MH045', 8.0), ('24DH000020', 'MH053', 9.0),
('25DH000019', 'MH045', 8.5), ('25DH000019', 'MH046', 7.0), ('25DH000019', 'MH053', 8.0),
('25DH000020', 'MH045', 9.0), ('25DH000020', 'MH046', 8.5), ('25DH000020', 'MH053', 7.5),
-- TLKS students
('22DH000021', 'MH047', 8.0), ('22DH000021', 'MH048', 9.0), ('22DH000021', 'MH049', 8.5),
('22DH000022', 'MH047', 7.5), ('22DH000022', 'MH048', 8.0), ('22DH000022', 'MH049', 9.0),
('23DH000021', 'MH047', 8.5), ('23DH000021', 'MH048', 7.0), ('23DH000021', 'MH049', 8.0),
('23DH000022', 'MH047', 9.0), ('23DH000022', 'MH048', 8.5), ('23DH000022', 'MH049', 7.5),
('24DH000021', 'MH047', 8.0), ('24DH000021', 'MH050', 9.0), ('24DH000021', 'MH051', 8.5),
('24DH000022', 'MH047', 7.5), ('24DH000022', 'MH050', 8.0), ('24DH000022', 'MH051', 9.0),
('25DH000021', 'MH048', 8.5), ('25DH000021', 'MH049', 7.0), ('25DH000021', 'MH050', 8.0),
('25DH000022', 'MH048', 9.0), ('25DH000022', 'MH049', 8.5), ('25DH000022', 'MH050', 7.5);
GO
