-- Tạo lại database Global nếu đã tồn tại
DROP DATABASE IF EXISTS Global;
GO

-- Tạo database Global với collation hỗ trợ tiếng Việt
CREATE DATABASE Global COLLATE Vietnamese_CI_AS;
GO

USE Global;
GO

-- Tạo bảng Khoa
CREATE TABLE Khoa (
    MaKhoa NVARCHAR(10) PRIMARY KEY,
    TenKhoa NVARCHAR(100) NOT NULL
);
ALTER TABLE Khoa ADD CONSTRAINT CK_Khoa_SiteB CHECK (MaKhoa IN ('QHQT', 'QTKD', 'KTTC'));
GO

-- Tạo bảng MonHoc
CREATE TABLE MonHoc (
    MaMH NVARCHAR(10) PRIMARY KEY,
    TenMH NVARCHAR(100) NOT NULL
);
GO

-- Tạo bảng CTDaoTao
CREATE TABLE CTDaoTao (
    MaKhoa NVARCHAR(10) NOT NULL,
    KhoaHoc INT NOT NULL,
    MaMH NVARCHAR(10) NOT NULL,
    PRIMARY KEY (MaKhoa, KhoaHoc, MaMH),
    FOREIGN KEY (MaKhoa) REFERENCES Khoa(MaKhoa),
    FOREIGN KEY (MaMH) REFERENCES MonHoc(MaMH)
);
GO

-- Tạo bảng SinhVien
CREATE TABLE SinhVien (
    MaSV NVARCHAR(10) PRIMARY KEY,
    HoTen NVARCHAR(100) NOT NULL,
    MaKhoa NVARCHAR(10) NOT NULL,
    KhoaHoc INT NOT NULL,
    FOREIGN KEY (MaKhoa) REFERENCES Khoa(MaKhoa)
);
ALTER TABLE SinhVien ADD CONSTRAINT CK_SinhVien_SiteB CHECK (MaKhoa IN ('QHQT', 'QTKD', 'KTTC'));
GO

-- Tạo bảng DangKy
CREATE TABLE DangKy (
    MaSV NVARCHAR(10) NOT NULL,
    MaMon NVARCHAR(10) NOT NULL,
    DiemThi DECIMAL(4,2) NULL, -- Điểm thi, có thể NULL nếu chưa thi
    PRIMARY KEY (MaSV, MaMon),
    FOREIGN KEY (MaSV) REFERENCES SinhVien(MaSV),
    FOREIGN KEY (MaMon) REFERENCES MonHoc(MaMH)
);
GO