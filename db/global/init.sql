-- Tạo lại database Global nếu đã tồn tại
DROP DATABASE IF EXISTS Global;
GO

-- Tạo database Global với collation hỗ trợ tiếng Việt
CREATE DATABASE Global COLLATE Vietnamese_CI_AS;
GO

USE Global;
GO

-- Tạo linked servers đến các sites
IF EXISTS (SELECT 1 FROM sys.servers WHERE name = 'SITE_A') BEGIN
    EXEC sp_droplinkedsrvlogin 'SITE_A', NULL;
    EXEC sp_dropserver 'SITE_A';
END
EXEC sp_addlinkedserver @server='SITE_A', @srvproduct='', @provider='MSOLEDBSQL', @datasrc='mssql_site_a,1433';
EXEC sp_addlinkedsrvlogin @rmtsrvname='SITE_A', @useself='false', @locallogin=NULL, @rmtuser='sa', @rmtpassword='Your@STROng!Pass#Word';

IF EXISTS (SELECT 1 FROM sys.servers WHERE name = 'SITE_B') BEGIN
    EXEC sp_droplinkedsrvlogin 'SITE_B', NULL;
    EXEC sp_dropserver 'SITE_B';
END
EXEC sp_addlinkedserver @server='SITE_B', @srvproduct='', @provider='MSOLEDBSQL', @datasrc='mssql_site_b,1433';
EXEC sp_addlinkedsrvlogin @rmtsrvname='SITE_B', @useself='false', @locallogin=NULL, @rmtuser='sa', @rmtpassword='Your@STROng!Pass#Word';

IF EXISTS (SELECT 1 FROM sys.servers WHERE name = 'SITE_C') BEGIN
    EXEC sp_droplinkedsrvlogin 'SITE_C', NULL;
    EXEC sp_dropserver 'SITE_C';
END
EXEC sp_addlinkedserver @server='SITE_C', @srvproduct='', @provider='MSOLEDBSQL', @datasrc='mssql_site_c,1433';
EXEC sp_addlinkedsrvlogin @rmtsrvname='SITE_C', @useself='false', @locallogin=NULL, @rmtuser='sa', @rmtpassword='Your@STROng!Pass#Word';
GO

-- Tạo views toàn cục
CREATE VIEW Khoa_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.Khoa
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.Khoa
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.Khoa;
GO

CREATE VIEW MonHoc_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.MonHoc
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.MonHoc
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.MonHoc;
GO

CREATE VIEW CTDaoTao_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.CTDaoTao
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.CTDaoTao
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.CTDaoTao;
GO

CREATE VIEW SinhVien_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.SinhVien
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.SinhVien
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.SinhVien;
GO

CREATE VIEW DangKy_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.DangKy
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.DangKy
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.DangKy;
GO