-- Create schemas for each site
CREATE SCHEMA IF NOT EXISTS site_a;
CREATE SCHEMA IF NOT EXISTS site_b;
CREATE SCHEMA IF NOT EXISTS site_c;

-- ==========================================
-- SITE A TABLES (MaKhoa < 'M')
-- ==========================================

CREATE TABLE site_a.Khoa (
    MaKhoa VARCHAR(10) PRIMARY KEY,
    TenKhoa VARCHAR(100) NOT NULL,
    CONSTRAINT CK_Khoa_SiteA CHECK (MaKhoa < 'M')
);

CREATE TABLE site_a.MonHoc (
    MaMH VARCHAR(10) PRIMARY KEY,
    TenMH VARCHAR(100) NOT NULL
);

CREATE TABLE site_a.CTDaoTao (
    MaKhoa VARCHAR(10) NOT NULL,
    KhoaHoc INT NOT NULL,
    MaMH VARCHAR(10) NOT NULL,
    PRIMARY KEY (MaKhoa, KhoaHoc, MaMH),
    FOREIGN KEY (MaKhoa) REFERENCES site_a.Khoa(MaKhoa),
    FOREIGN KEY (MaMH) REFERENCES site_a.MonHoc(MaMH),
    CONSTRAINT CK_CTDaoTao_SiteA CHECK (MaKhoa < 'M')
);

CREATE TABLE site_a.SinhVien (
    MaSV VARCHAR(10) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    MaKhoa VARCHAR(10) NOT NULL,
    KhoaHoc INT NOT NULL,
    FOREIGN KEY (MaKhoa) REFERENCES site_a.Khoa(MaKhoa),
    CONSTRAINT CK_SinhVien_SiteA CHECK (MaKhoa < 'M')
);

CREATE TABLE site_a.DangKy (
    MaSV VARCHAR(10) NOT NULL,
    MaMon VARCHAR(10) NOT NULL,
    DiemThi DECIMAL(4,2) NULL,
    PRIMARY KEY (MaSV, MaMon),
    FOREIGN KEY (MaSV) REFERENCES site_a.SinhVien(MaSV),
    FOREIGN KEY (MaMon) REFERENCES site_a.MonHoc(MaMH)
);

-- ==========================================
-- SITE B TABLES ('M' <= MaKhoa < 'S')
-- ==========================================

CREATE TABLE site_b.Khoa (
    MaKhoa VARCHAR(10) PRIMARY KEY,
    TenKhoa VARCHAR(100) NOT NULL,
    CONSTRAINT CK_Khoa_SiteB CHECK (MaKhoa >= 'M' AND MaKhoa < 'S')
);

CREATE TABLE site_b.MonHoc (
    MaMH VARCHAR(10) PRIMARY KEY,
    TenMH VARCHAR(100) NOT NULL
);

CREATE TABLE site_b.CTDaoTao (
    MaKhoa VARCHAR(10) NOT NULL,
    KhoaHoc INT NOT NULL,
    MaMH VARCHAR(10) NOT NULL,
    PRIMARY KEY (MaKhoa, KhoaHoc, MaMH),
    FOREIGN KEY (MaKhoa) REFERENCES site_b.Khoa(MaKhoa),
    FOREIGN KEY (MaMH) REFERENCES site_b.MonHoc(MaMH),
    CONSTRAINT CK_CTDaoTao_SiteB CHECK (MaKhoa >= 'M' AND MaKhoa < 'S')
);

CREATE TABLE site_b.SinhVien (
    MaSV VARCHAR(10) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    MaKhoa VARCHAR(10) NOT NULL,
    KhoaHoc INT NOT NULL,
    FOREIGN KEY (MaKhoa) REFERENCES site_b.Khoa(MaKhoa),
    CONSTRAINT CK_SinhVien_SiteB CHECK (MaKhoa >= 'M' AND MaKhoa < 'S')
);

CREATE TABLE site_b.DangKy (
    MaSV VARCHAR(10) NOT NULL,
    MaMon VARCHAR(10) NOT NULL,
    DiemThi DECIMAL(4,2) NULL,
    PRIMARY KEY (MaSV, MaMon),
    FOREIGN KEY (MaSV) REFERENCES site_b.SinhVien(MaSV),
    FOREIGN KEY (MaMon) REFERENCES site_b.MonHoc(MaMH)
);

-- ==========================================
-- SITE C TABLES (MaKhoa >= 'S')
-- ==========================================

CREATE TABLE site_c.Khoa (
    MaKhoa VARCHAR(10) PRIMARY KEY,
    TenKhoa VARCHAR(100) NOT NULL,
    CONSTRAINT CK_Khoa_SiteC CHECK (MaKhoa >= 'S')
);

CREATE TABLE site_c.MonHoc (
    MaMH VARCHAR(10) PRIMARY KEY,
    TenMH VARCHAR(100) NOT NULL
);

CREATE TABLE site_c.CTDaoTao (
    MaKhoa VARCHAR(10) NOT NULL,
    KhoaHoc INT NOT NULL,
    MaMH VARCHAR(10) NOT NULL,
    PRIMARY KEY (MaKhoa, KhoaHoc, MaMH),
    FOREIGN KEY (MaKhoa) REFERENCES site_c.Khoa(MaKhoa),
    FOREIGN KEY (MaMH) REFERENCES site_c.MonHoc(MaMH),
    CONSTRAINT CK_CTDaoTao_SiteC CHECK (MaKhoa >= 'S')
);

CREATE TABLE site_c.SinhVien (
    MaSV VARCHAR(10) PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    MaKhoa VARCHAR(10) NOT NULL,
    KhoaHoc INT NOT NULL,
    FOREIGN KEY (MaKhoa) REFERENCES site_c.Khoa(MaKhoa),
    CONSTRAINT CK_SinhVien_SiteC CHECK (MaKhoa >= 'S')
);

CREATE TABLE site_c.DangKy (
    MaSV VARCHAR(10) NOT NULL,
    MaMon VARCHAR(10) NOT NULL,
    DiemThi DECIMAL(4,2) NULL,
    PRIMARY KEY (MaSV, MaMon),
    FOREIGN KEY (MaSV) REFERENCES site_c.SinhVien(MaSV),
    FOREIGN KEY (MaMon) REFERENCES site_c.MonHoc(MaMH)
);

-- ==========================================
-- GLOBAL VIEWS
-- ==========================================

CREATE OR REPLACE VIEW Khoa_Global AS
SELECT *, 'Site A' as Site FROM site_a.Khoa
UNION ALL
SELECT *, 'Site B' as Site FROM site_b.Khoa
UNION ALL
SELECT *, 'Site C' as Site FROM site_c.Khoa;

CREATE OR REPLACE VIEW MonHoc_Global AS
SELECT DISTINCT MaMH, TenMH FROM (
    SELECT MaMH, TenMH FROM site_a.MonHoc
    UNION ALL
    SELECT MaMH, TenMH FROM site_b.MonHoc
    UNION ALL
    SELECT MaMH, TenMH FROM site_c.MonHoc
) AS AllMonHoc;

CREATE OR REPLACE VIEW CTDaoTao_Global AS
SELECT * FROM site_a.CTDaoTao
UNION ALL
SELECT * FROM site_b.CTDaoTao
UNION ALL
SELECT * FROM site_c.CTDaoTao;

CREATE OR REPLACE VIEW SinhVien_Global AS
SELECT * FROM site_a.SinhVien
UNION ALL
SELECT * FROM site_b.SinhVien
UNION ALL
SELECT * FROM site_c.SinhVien;

CREATE OR REPLACE VIEW DangKy_Global AS
SELECT * FROM site_a.DangKy
UNION ALL
SELECT * FROM site_b.DangKy
UNION ALL
SELECT * FROM site_c.DangKy;
