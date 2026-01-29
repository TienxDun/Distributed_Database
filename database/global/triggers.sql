-- =============================================
-- INSTEAD OF Triggers for HUFLIT Global Views
-- Purpose: Route CRUD operations to appropriate site databases
-- Partitioning Logic: 
--   MaKhoa < 'M' -> SITE_A
--   MaKhoa >= 'M' AND < 'S' -> SITE_B
--   MaKhoa >= 'S' -> SITE_C
-- 
-- Schema:
--   SinhVien: MaSV, HoTen, MaKhoa, KhoaHoc
--   DangKy: MaSV, MaMon, DiemThi
--   CTDaoTao: MaKhoa, KhoaHoc, MaMH
-- =============================================

USE HUFLIT;
GO

PRINT N'===== BẮT ĐẦU TẠO INSTEAD OF TRIGGERS =====';
GO

-- =============================================
-- TRIGGERS FOR Khoa_Global
-- =============================================
PRINT N'Đang tạo triggers cho Khoa_Global...';
GO

-- INSERT Trigger: Route to appropriate site based on MaKhoa
IF OBJECT_ID('TR_Khoa_Global_Insert', 'TR') IS NOT NULL
    DROP TRIGGER TR_Khoa_Global_Insert;
GO

CREATE TRIGGER TR_Khoa_Global_Insert
ON Khoa_Global
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @TenKhoa NVARCHAR(255);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE khoa_cursor CURSOR FOR
    SELECT MaKhoa, TenKhoa FROM inserted;
    
    OPEN khoa_cursor;
    FETCH NEXT FROM khoa_cursor INTO @MaKhoa, @TenKhoa;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Validate: Check if MaKhoa already exists
        IF EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa = @MaKhoa)
        BEGIN
            RAISERROR(N'Mã khoa "%s" đã tồn tại!', 16, 1, @MaKhoa);
            CLOSE khoa_cursor;
            DEALLOCATE khoa_cursor;
            RETURN;
        END
        
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_A].SiteA.dbo.Khoa (MaKhoa, TenKhoa) VALUES (''' + @MaKhoa + N''', N''' + @TenKhoa + N''')';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_B].SiteB.dbo.Khoa (MaKhoa, TenKhoa) VALUES (''' + @MaKhoa + N''', N''' + @TenKhoa + N''')';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_C].SiteC.dbo.Khoa (MaKhoa, TenKhoa) VALUES (''' + @MaKhoa + N''', N''' + @TenKhoa + N''')';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM khoa_cursor INTO @MaKhoa, @TenKhoa;
    END
    
    CLOSE khoa_cursor;
    DEALLOCATE khoa_cursor;
END;
GO

-- UPDATE Trigger: Update on current site, prevent MaKhoa change
IF OBJECT_ID('TR_Khoa_Global_Update', 'TR') IS NOT NULL
    DROP TRIGGER TR_Khoa_Global_Update;
GO

CREATE TRIGGER TR_Khoa_Global_Update
ON Khoa_Global
INSTEAD OF UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Check if MaKhoa is being changed
    IF EXISTS (SELECT 1 FROM inserted i INNER JOIN deleted d ON i.MaKhoa <> d.MaKhoa)
    BEGIN
        RAISERROR(N'Không được phép thay đổi Mã Khoa!', 16, 1);
        RETURN;
    END
    
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @TenKhoa NVARCHAR(255);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE khoa_cursor CURSOR FOR
    SELECT MaKhoa, TenKhoa FROM inserted;
    
    OPEN khoa_cursor;
    FETCH NEXT FROM khoa_cursor INTO @MaKhoa, @TenKhoa;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'UPDATE [SITE_A].SiteA.dbo.Khoa SET TenKhoa = N''' + @TenKhoa + N''' WHERE MaKhoa = ''' + @MaKhoa + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'UPDATE [SITE_B].SiteB.dbo.Khoa SET TenKhoa = N''' + @TenKhoa + N''' WHERE MaKhoa = ''' + @MaKhoa + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'UPDATE [SITE_C].SiteC.dbo.Khoa SET TenKhoa = N''' + @TenKhoa + N''' WHERE MaKhoa = ''' + @MaKhoa + '''';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM khoa_cursor INTO @MaKhoa, @TenKhoa;
    END
    
    CLOSE khoa_cursor;
    DEALLOCATE khoa_cursor;
END;
GO

-- DELETE Trigger: Check constraints, prevent if related data exists
IF OBJECT_ID('TR_Khoa_Global_Delete', 'TR') IS NOT NULL
    DROP TRIGGER TR_Khoa_Global_Delete;
GO

CREATE TRIGGER TR_Khoa_Global_Delete
ON Khoa_Global
INSTEAD OF DELETE
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE khoa_cursor CURSOR FOR
    SELECT MaKhoa FROM deleted;
    
    OPEN khoa_cursor;
    FETCH NEXT FROM khoa_cursor INTO @MaKhoa;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Check if there are related SinhVien
        IF EXISTS (SELECT 1 FROM SinhVien_Global WHERE MaKhoa = @MaKhoa)
        BEGIN
            RAISERROR(N'Không thể xóa khoa "%s" vì còn sinh viên liên quan!', 16, 1, @MaKhoa);
            CLOSE khoa_cursor;
            DEALLOCATE khoa_cursor;
            RETURN;
        END
        
        -- Check if there are related CTDaoTao
        IF EXISTS (SELECT 1 FROM CTDaoTao_Global WHERE MaKhoa = @MaKhoa)
        BEGIN
            RAISERROR(N'Không thể xóa khoa "%s" vì còn chương trình đào tạo liên quan!', 16, 1, @MaKhoa);
            CLOSE khoa_cursor;
            DEALLOCATE khoa_cursor;
            RETURN;
        END
        
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_A].SiteA.dbo.Khoa WHERE MaKhoa = ''' + @MaKhoa + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_B].SiteB.dbo.Khoa WHERE MaKhoa = ''' + @MaKhoa + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_C].SiteC.dbo.Khoa WHERE MaKhoa = ''' + @MaKhoa + '''';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM khoa_cursor INTO @MaKhoa;
    END
    
    CLOSE khoa_cursor;
    DEALLOCATE khoa_cursor;
END;
GO

PRINT N'✓ Triggers cho Khoa_Global đã được tạo thành công';
GO

-- =============================================
-- TRIGGERS FOR MonHoc_Global
-- Synchronize across all 3 sites
-- =============================================
PRINT N'Đang tạo triggers cho MonHoc_Global...';
GO

-- INSERT Trigger: Insert to all 3 sites
IF OBJECT_ID('TR_MonHoc_Global_Insert', 'TR') IS NOT NULL
    DROP TRIGGER TR_MonHoc_Global_Insert;
GO

CREATE TRIGGER TR_MonHoc_Global_Insert
ON MonHoc_Global
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaMH NVARCHAR(10);
    DECLARE @TenMH NVARCHAR(255);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE monhoc_cursor CURSOR FOR
    SELECT MaMH, TenMH FROM inserted;
    
    OPEN monhoc_cursor;
    FETCH NEXT FROM monhoc_cursor INTO @MaMH, @TenMH;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Validate: Check if MaMH already exists
        IF EXISTS (SELECT 1 FROM [SITE_A].SiteA.dbo.MonHoc WHERE MaMH = @MaMH)
        BEGIN
            RAISERROR(N'Mã môn học "%s" đã tồn tại!', 16, 1, @MaMH);
            CLOSE monhoc_cursor;
            DEALLOCATE monhoc_cursor;
            RETURN;
        END
        
        -- Insert to all 3 sites
        BEGIN TRY
            SET @SQL = N'INSERT INTO [SITE_A].SiteA.dbo.MonHoc (MaMH, TenMH) VALUES (''' + @MaMH + N''', N''' + @TenMH + N''')';
            EXEC sp_executesql @SQL;
            
            SET @SQL = N'INSERT INTO [SITE_B].SiteB.dbo.MonHoc (MaMH, TenMH) VALUES (''' + @MaMH + N''', N''' + @TenMH + N''')';
            EXEC sp_executesql @SQL;
            
            SET @SQL = N'INSERT INTO [SITE_C].SiteC.dbo.MonHoc (MaMH, TenMH) VALUES (''' + @MaMH + N''', N''' + @TenMH + N''')';
            EXEC sp_executesql @SQL;
        END TRY
        BEGIN CATCH
            -- Rollback on error
            SET @SQL = N'DELETE FROM [SITE_A].SiteA.dbo.MonHoc WHERE MaMH = ''' + @MaMH + '''';
            EXEC sp_executesql @SQL;
            SET @SQL = N'DELETE FROM [SITE_B].SiteB.dbo.MonHoc WHERE MaMH = ''' + @MaMH + '''';
            EXEC sp_executesql @SQL;
            SET @SQL = N'DELETE FROM [SITE_C].SiteC.dbo.MonHoc WHERE MaMH = ''' + @MaMH + '''';
            EXEC sp_executesql @SQL;
            
            RAISERROR(N'Lỗi khi thêm môn học!', 16, 1);
            CLOSE monhoc_cursor;
            DEALLOCATE monhoc_cursor;
            RETURN;
        END CATCH
        
        FETCH NEXT FROM monhoc_cursor INTO @MaMH, @TenMH;
    END
    
    CLOSE monhoc_cursor;
    DEALLOCATE monhoc_cursor;
END;
GO

-- UPDATE Trigger: Update on all 3 sites, prevent MaMH change
IF OBJECT_ID('TR_MonHoc_Global_Update', 'TR') IS NOT NULL
    DROP TRIGGER TR_MonHoc_Global_Update;
GO

CREATE TRIGGER TR_MonHoc_Global_Update
ON MonHoc_Global
INSTEAD OF UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Check if MaMH is being changed
    IF EXISTS (SELECT 1 FROM inserted i INNER JOIN deleted d ON i.MaMH <> d.MaMH)
    BEGIN
        RAISERROR(N'Không được phép thay đổi Mã Môn Học!', 16, 1);
        RETURN;
    END
    
    DECLARE @MaMH NVARCHAR(10);
    DECLARE @TenMH NVARCHAR(255);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE monhoc_cursor CURSOR FOR
    SELECT MaMH, TenMH FROM inserted;
    
    OPEN monhoc_cursor;
    FETCH NEXT FROM monhoc_cursor INTO @MaMH, @TenMH;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Update on all 3 sites
        SET @SQL = N'UPDATE [SITE_A].SiteA.dbo.MonHoc SET TenMH = N''' + @TenMH + N''' WHERE MaMH = ''' + @MaMH + '''';
        EXEC sp_executesql @SQL;
        
        SET @SQL = N'UPDATE [SITE_B].SiteB.dbo.MonHoc SET TenMH = N''' + @TenMH + N''' WHERE MaMH = ''' + @MaMH + '''';
        EXEC sp_executesql @SQL;
        
        SET @SQL = N'UPDATE [SITE_C].SiteC.dbo.MonHoc SET TenMH = N''' + @TenMH + N''' WHERE MaMH = ''' + @MaMH + '''';
        EXEC sp_executesql @SQL;
        
        FETCH NEXT FROM monhoc_cursor INTO @MaMH, @TenMH;
    END
    
    CLOSE monhoc_cursor;
    DEALLOCATE monhoc_cursor;
END;
GO

-- DELETE Trigger: Delete from all 3 sites, check constraints
IF OBJECT_ID('TR_MonHoc_Global_Delete', 'TR') IS NOT NULL
    DROP TRIGGER TR_MonHoc_Global_Delete;
GO

CREATE TRIGGER TR_MonHoc_Global_Delete
ON MonHoc_Global
INSTEAD OF DELETE
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaMH NVARCHAR(10);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE monhoc_cursor CURSOR FOR
    SELECT MaMH FROM deleted;
    
    OPEN monhoc_cursor;
    FETCH NEXT FROM monhoc_cursor INTO @MaMH;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Check if there are related CTDaoTao
        IF EXISTS (SELECT 1 FROM CTDaoTao_Global WHERE MaMH = @MaMH)
        BEGIN
            RAISERROR(N'Không thể xóa môn học "%s" vì còn trong chương trình đào tạo!', 16, 1, @MaMH);
            CLOSE monhoc_cursor;
            DEALLOCATE monhoc_cursor;
            RETURN;
        END
        
        -- Check if there are related DangKy
        IF EXISTS (SELECT 1 FROM DangKy_Global WHERE MaMon = @MaMH)
        BEGIN
            RAISERROR(N'Không thể xóa môn học "%s" vì có sinh viên đã đăng ký!', 16, 1, @MaMH);
            CLOSE monhoc_cursor;
            DEALLOCATE monhoc_cursor;
            RETURN;
        END
        
        -- Delete from all 3 sites
        SET @SQL = N'DELETE FROM [SITE_A].SiteA.dbo.MonHoc WHERE MaMH = ''' + @MaMH + '''';
        EXEC sp_executesql @SQL;
        
        SET @SQL = N'DELETE FROM [SITE_B].SiteB.dbo.MonHoc WHERE MaMH = ''' + @MaMH + '''';
        EXEC sp_executesql @SQL;
        
        SET @SQL = N'DELETE FROM [SITE_C].SiteC.dbo.MonHoc WHERE MaMH = ''' + @MaMH + '''';
        EXEC sp_executesql @SQL;
        
        FETCH NEXT FROM monhoc_cursor INTO @MaMH;
    END
    
    CLOSE monhoc_cursor;
    DEALLOCATE monhoc_cursor;
END;
GO

PRINT N'✓ Triggers cho MonHoc_Global đã được tạo thành công';
GO

-- =============================================
-- TRIGGERS FOR SinhVien_Global
-- Schema: MaSV, HoTen, MaKhoa, KhoaHoc
-- Allow MaKhoa change = move between sites
-- =============================================
PRINT N'Đang tạo triggers cho SinhVien_Global...';
GO

-- INSERT Trigger: Route to appropriate site based on MaKhoa
IF OBJECT_ID('TR_SinhVien_Global_Insert', 'TR') IS NOT NULL
    DROP TRIGGER TR_SinhVien_Global_Insert;
GO

CREATE TRIGGER TR_SinhVien_Global_Insert
ON SinhVien_Global
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaSV NVARCHAR(20);
    DECLARE @HoTen NVARCHAR(255);
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @KhoaHoc INT;
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE sv_cursor CURSOR FOR
    SELECT MaSV, HoTen, MaKhoa, KhoaHoc FROM inserted;
    
    OPEN sv_cursor;
    FETCH NEXT FROM sv_cursor INTO @MaSV, @HoTen, @MaKhoa, @KhoaHoc;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Validate: Check if MaSV already exists
        IF EXISTS (SELECT 1 FROM SinhVien_Global WHERE MaSV = @MaSV)
        BEGIN
            RAISERROR(N'Mã sinh viên "%s" đã tồn tại!', 16, 1, @MaSV);
            CLOSE sv_cursor;
            DEALLOCATE sv_cursor;
            RETURN;
        END
        
        -- Validate: Check if MaKhoa exists
        IF NOT EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa = @MaKhoa)
        BEGIN
            RAISERROR(N'Mã khoa "%s" không tồn tại!', 16, 1, @MaKhoa);
            CLOSE sv_cursor;
            DEALLOCATE sv_cursor;
            RETURN;
        END
        
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_A].SiteA.dbo.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (''' 
                + @MaSV + N''', N''' + @HoTen + N''', ''' + @MaKhoa + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + ')';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_B].SiteB.dbo.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (''' 
                + @MaSV + N''', N''' + @HoTen + N''', ''' + @MaKhoa + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + ')';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_C].SiteC.dbo.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (''' 
                + @MaSV + N''', N''' + @HoTen + N''', ''' + @MaKhoa + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + ')';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM sv_cursor INTO @MaSV, @HoTen, @MaKhoa, @KhoaHoc;
    END
    
    CLOSE sv_cursor;
    DEALLOCATE sv_cursor;
END;
GO

-- UPDATE Trigger: Allow MaKhoa change (move student between sites)
IF OBJECT_ID('TR_SinhVien_Global_Update', 'TR') IS NOT NULL
    DROP TRIGGER TR_SinhVien_Global_Update;
GO

CREATE TRIGGER TR_SinhVien_Global_Update
ON SinhVien_Global
INSTEAD OF UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Check if MaSV is being changed (not allowed)
    IF EXISTS (SELECT 1 FROM inserted i INNER JOIN deleted d ON i.MaSV <> d.MaSV)
    BEGIN
        RAISERROR(N'Không được phép thay đổi Mã Sinh Viên!', 16, 1);
        RETURN;
    END
    
    DECLARE @MaSV NVARCHAR(20);
    DECLARE @HoTen NVARCHAR(255);
    DECLARE @MaKhoa_Old NVARCHAR(10);
    DECLARE @MaKhoa_New NVARCHAR(10);
    DECLARE @KhoaHoc INT;
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE sv_cursor CURSOR FOR
    SELECT i.MaSV, i.HoTen, d.MaKhoa, i.MaKhoa, i.KhoaHoc
    FROM inserted i
    INNER JOIN deleted d ON i.MaSV = d.MaSV;
    
    OPEN sv_cursor;
    FETCH NEXT FROM sv_cursor INTO @MaSV, @HoTen, @MaKhoa_Old, @MaKhoa_New, @KhoaHoc;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Validate: Check if new MaKhoa exists
        IF NOT EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa = @MaKhoa_New)
        BEGIN
            RAISERROR(N'Mã khoa mới "%s" không tồn tại!', 16, 1, @MaKhoa_New);
            CLOSE sv_cursor;
            DEALLOCATE sv_cursor;
            RETURN;
        END
        
        -- If MaKhoa changed: Move student between sites
        IF @MaKhoa_Old <> @MaKhoa_New
        BEGIN
            -- Check if student has DangKy records (must move those too)
            IF EXISTS (SELECT 1 FROM DangKy_Global WHERE MaSV = @MaSV)
            BEGIN
                RAISERROR(N'Không thể chuyển khoa cho sinh viên "%s" vì có dữ liệu đăng ký môn học. Xóa đăng ký trước!', 16, 1, @MaSV);
                CLOSE sv_cursor;
                DEALLOCATE sv_cursor;
                RETURN;
            END
            
            -- Delete from old site
            IF @MaKhoa_Old < 'M'
            BEGIN
                SET @SQL = N'DELETE FROM [SITE_A].SiteA.dbo.SinhVien WHERE MaSV = ''' + @MaSV + '''';
                EXEC sp_executesql @SQL;
            END
            ELSE IF @MaKhoa_Old >= 'M' AND @MaKhoa_Old < 'S'
            BEGIN
                SET @SQL = N'DELETE FROM [SITE_B].SiteB.dbo.SinhVien WHERE MaSV = ''' + @MaSV + '''';
                EXEC sp_executesql @SQL;
            END
            ELSE
            BEGIN
                SET @SQL = N'DELETE FROM [SITE_C].SiteC.dbo.SinhVien WHERE MaSV = ''' + @MaSV + '''';
                EXEC sp_executesql @SQL;
            END
            
            -- Insert to new site
            IF @MaKhoa_New < 'M'
            BEGIN
                SET @SQL = N'INSERT INTO [SITE_A].SiteA.dbo.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (''' 
                    + @MaSV + N''', N''' + @HoTen + N''', ''' + @MaKhoa_New + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + ')';
                EXEC sp_executesql @SQL;
            END
            ELSE IF @MaKhoa_New >= 'M' AND @MaKhoa_New < 'S'
            BEGIN
                SET @SQL = N'INSERT INTO [SITE_B].SiteB.dbo.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (''' 
                    + @MaSV + N''', N''' + @HoTen + N''', ''' + @MaKhoa_New + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + ')';
                EXEC sp_executesql @SQL;
            END
            ELSE
            BEGIN
                SET @SQL = N'INSERT INTO [SITE_C].SiteC.dbo.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (''' 
                    + @MaSV + N''', N''' + @HoTen + N''', ''' + @MaKhoa_New + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + ')';
                EXEC sp_executesql @SQL;
            END
        END
        ELSE
        BEGIN
            -- MaKhoa not changed, just update other fields
            IF @MaKhoa_New < 'M'
            BEGIN
                SET @SQL = N'UPDATE [SITE_A].SiteA.dbo.SinhVien SET HoTen = N''' + @HoTen + N''', KhoaHoc = ' 
                    + CAST(@KhoaHoc AS NVARCHAR(10)) + N' WHERE MaSV = ''' + @MaSV + '''';
                EXEC sp_executesql @SQL;
            END
            ELSE IF @MaKhoa_New >= 'M' AND @MaKhoa_New < 'S'
            BEGIN
                SET @SQL = N'UPDATE [SITE_B].SiteB.dbo.SinhVien SET HoTen = N''' + @HoTen + N''', KhoaHoc = ' 
                    + CAST(@KhoaHoc AS NVARCHAR(10)) + N' WHERE MaSV = ''' + @MaSV + '''';
                EXEC sp_executesql @SQL;
            END
            ELSE
            BEGIN
                SET @SQL = N'UPDATE [SITE_C].SiteC.dbo.SinhVien SET HoTen = N''' + @HoTen + N''', KhoaHoc = ' 
                    + CAST(@KhoaHoc AS NVARCHAR(10)) + N' WHERE MaSV = ''' + @MaSV + '''';
                EXEC sp_executesql @SQL;
            END
        END
        
        FETCH NEXT FROM sv_cursor INTO @MaSV, @HoTen, @MaKhoa_Old, @MaKhoa_New, @KhoaHoc;
    END
    
    CLOSE sv_cursor;
    DEALLOCATE sv_cursor;
END;
GO

-- DELETE Trigger: Check constraints, prevent if related data exists
IF OBJECT_ID('TR_SinhVien_Global_Delete', 'TR') IS NOT NULL
    DROP TRIGGER TR_SinhVien_Global_Delete;
GO

CREATE TRIGGER TR_SinhVien_Global_Delete
ON SinhVien_Global
INSTEAD OF DELETE
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaSV NVARCHAR(20);
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE sv_cursor CURSOR FOR
    SELECT MaSV, MaKhoa FROM deleted;
    
    OPEN sv_cursor;
    FETCH NEXT FROM sv_cursor INTO @MaSV, @MaKhoa;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Check if there are related DangKy
        IF EXISTS (SELECT 1 FROM DangKy_Global WHERE MaSV = @MaSV)
        BEGIN
            RAISERROR(N'Không thể xóa sinh viên "%s" vì có dữ liệu đăng ký môn học!', 16, 1, @MaSV);
            CLOSE sv_cursor;
            DEALLOCATE sv_cursor;
            RETURN;
        END
        
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_A].SiteA.dbo.SinhVien WHERE MaSV = ''' + @MaSV + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_B].SiteB.dbo.SinhVien WHERE MaSV = ''' + @MaSV + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_C].SiteC.dbo.SinhVien WHERE MaSV = ''' + @MaSV + '''';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM sv_cursor INTO @MaSV, @MaKhoa;
    END
    
    CLOSE sv_cursor;
    DEALLOCATE sv_cursor;
END;
GO

PRINT N'✓ Triggers cho SinhVien_Global đã được tạo thành công';
GO

-- =============================================
-- TRIGGERS FOR CTDaoTao_Global
-- Schema: MaKhoa, KhoaHoc, MaMH
-- =============================================
PRINT N'Đang tạo triggers cho CTDaoTao_Global...';
GO

-- INSERT Trigger: Route to appropriate site based on MaKhoa
IF OBJECT_ID('TR_CTDaoTao_Global_Insert', 'TR') IS NOT NULL
    DROP TRIGGER TR_CTDaoTao_Global_Insert;
GO

CREATE TRIGGER TR_CTDaoTao_Global_Insert
ON CTDaoTao_Global
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @KhoaHoc INT;
    DECLARE @MaMH NVARCHAR(10);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE ct_cursor CURSOR FOR
    SELECT MaKhoa, KhoaHoc, MaMH FROM inserted;
    
    OPEN ct_cursor;
    FETCH NEXT FROM ct_cursor INTO @MaKhoa, @KhoaHoc, @MaMH;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Validate: Check if combination already exists
        IF EXISTS (SELECT 1 FROM CTDaoTao_Global WHERE MaKhoa = @MaKhoa AND KhoaHoc = @KhoaHoc AND MaMH = @MaMH)
        BEGIN
            RAISERROR(N'Chương trình đào tạo cho khoa "%s", khóa học %d và môn "%s" đã tồn tại!', 16, 1, @MaKhoa, @KhoaHoc, @MaMH);
            CLOSE ct_cursor;
            DEALLOCATE ct_cursor;
            RETURN;
        END
        
        -- Validate: Check if MaKhoa exists
        IF NOT EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa = @MaKhoa)
        BEGIN
            RAISERROR(N'Mã khoa "%s" không tồn tại!', 16, 1, @MaKhoa);
            CLOSE ct_cursor;
            DEALLOCATE ct_cursor;
            RETURN;
        END
        
        -- Validate: Check if MaMH exists
        IF NOT EXISTS (SELECT 1 FROM MonHoc_Global WHERE MaMH = @MaMH)
        BEGIN
            RAISERROR(N'Mã môn học "%s" không tồn tại!', 16, 1, @MaMH);
            CLOSE ct_cursor;
            DEALLOCATE ct_cursor;
            RETURN;
        END
        
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_A].SiteA.dbo.CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES (''' 
                + @MaKhoa + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + N', ''' + @MaMH + N''')';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_B].SiteB.dbo.CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES (''' 
                + @MaKhoa + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + N', ''' + @MaMH + N''')';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_C].SiteC.dbo.CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES (''' 
                + @MaKhoa + N''', ' + CAST(@KhoaHoc AS NVARCHAR(10)) + N', ''' + @MaMH + N''')';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM ct_cursor INTO @MaKhoa, @KhoaHoc, @MaMH;
    END
    
    CLOSE ct_cursor;
    DEALLOCATE ct_cursor;
END;
GO

-- UPDATE Trigger: Not allowed (composite primary key)
IF OBJECT_ID('TR_CTDaoTao_Global_Update', 'TR') IS NOT NULL
    DROP TRIGGER TR_CTDaoTao_Global_Update;
GO

CREATE TRIGGER TR_CTDaoTao_Global_Update
ON CTDaoTao_Global
INSTEAD OF UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    RAISERROR(N'Không được phép cập nhật chương trình đào tạo! Xóa và thêm mới nếu cần thay đổi.', 16, 1);
    RETURN;
END;
GO

-- DELETE Trigger: Delete from appropriate site
IF OBJECT_ID('TR_CTDaoTao_Global_Delete', 'TR') IS NOT NULL
    DROP TRIGGER TR_CTDaoTao_Global_Delete;
GO

CREATE TRIGGER TR_CTDaoTao_Global_Delete
ON CTDaoTao_Global
INSTEAD OF DELETE
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @KhoaHoc INT;
    DECLARE @MaMH NVARCHAR(10);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE ct_cursor CURSOR FOR
    SELECT MaKhoa, KhoaHoc, MaMH FROM deleted;
    
    OPEN ct_cursor;
    FETCH NEXT FROM ct_cursor INTO @MaKhoa, @KhoaHoc, @MaMH;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_A].SiteA.dbo.CTDaoTao WHERE MaKhoa = ''' + @MaKhoa 
                + N''' AND KhoaHoc = ' + CAST(@KhoaHoc AS NVARCHAR(10)) + N' AND MaMH = ''' + @MaMH + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_B].SiteB.dbo.CTDaoTao WHERE MaKhoa = ''' + @MaKhoa 
                + N''' AND KhoaHoc = ' + CAST(@KhoaHoc AS NVARCHAR(10)) + N' AND MaMH = ''' + @MaMH + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_C].SiteC.dbo.CTDaoTao WHERE MaKhoa = ''' + @MaKhoa 
                + N''' AND KhoaHoc = ' + CAST(@KhoaHoc AS NVARCHAR(10)) + N' AND MaMH = ''' + @MaMH + '''';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM ct_cursor INTO @MaKhoa, @KhoaHoc, @MaMH;
    END
    
    CLOSE ct_cursor;
    DEALLOCATE ct_cursor;
END;
GO

PRINT N'✓ Triggers cho CTDaoTao_Global đã được tạo thành công';
GO

-- =============================================
-- TRIGGERS FOR DangKy_Global
-- Schema: MaSV, MaMon, DiemThi
-- =============================================
PRINT N'Đang tạo triggers cho DangKy_Global...';
GO

-- INSERT Trigger: JOIN with SinhVien to get MaKhoa, route to site
IF OBJECT_ID('TR_DangKy_Global_Insert', 'TR') IS NOT NULL
    DROP TRIGGER TR_DangKy_Global_Insert;
GO

CREATE TRIGGER TR_DangKy_Global_Insert
ON DangKy_Global
INSTEAD OF INSERT
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaSV NVARCHAR(20);
    DECLARE @MaMon NVARCHAR(10);
    DECLARE @DiemThi DECIMAL(4,2);
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE dk_cursor CURSOR FOR
    SELECT MaSV, MaMon, DiemThi FROM inserted;
    
    OPEN dk_cursor;
    FETCH NEXT FROM dk_cursor INTO @MaSV, @MaMon, @DiemThi;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Get MaKhoa from SinhVien
        SELECT @MaKhoa = MaKhoa FROM SinhVien_Global WHERE MaSV = @MaSV;
        
        IF @MaKhoa IS NULL
        BEGIN
            RAISERROR(N'Sinh viên "%s" không tồn tại!', 16, 1, @MaSV);
            CLOSE dk_cursor;
            DEALLOCATE dk_cursor;
            RETURN;
        END
        
        -- Validate: Check if combination already exists
        IF EXISTS (SELECT 1 FROM DangKy_Global WHERE MaSV = @MaSV AND MaMon = @MaMon)
        BEGIN
            RAISERROR(N'Sinh viên "%s" đã đăng ký môn học "%s"!', 16, 1, @MaSV, @MaMon);
            CLOSE dk_cursor;
            DEALLOCATE dk_cursor;
            RETURN;
        END
        
        -- Validate: Check if MonHoc exists
        IF NOT EXISTS (SELECT 1 FROM MonHoc_Global WHERE MaMH = @MaMon)
        BEGIN
            RAISERROR(N'Môn học "%s" không tồn tại!', 16, 1, @MaMon);
            CLOSE dk_cursor;
            DEALLOCATE dk_cursor;
            RETURN;
        END
        
        -- Route to appropriate site based on student's MaKhoa
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_A].SiteA.dbo.DangKy (MaSV, MaMon, DiemThi) VALUES (''' 
                + @MaSV + N''', ''' + @MaMon + N''', ' 
                + ISNULL(CAST(@DiemThi AS NVARCHAR(10)), 'NULL') + ')';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_B].SiteB.dbo.DangKy (MaSV, MaMon, DiemThi) VALUES (''' 
                + @MaSV + N''', ''' + @MaMon + N''', ' 
                + ISNULL(CAST(@DiemThi AS NVARCHAR(10)), 'NULL') + ')';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'INSERT INTO [SITE_C].SiteC.dbo.DangKy (MaSV, MaMon, DiemThi) VALUES (''' 
                + @MaSV + N''', ''' + @MaMon + N''', ' 
                + ISNULL(CAST(@DiemThi AS NVARCHAR(10)), 'NULL') + ')';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM dk_cursor INTO @MaSV, @MaMon, @DiemThi;
    END
    
    CLOSE dk_cursor;
    DEALLOCATE dk_cursor;
END;
GO

-- UPDATE Trigger: Only allow DiemThi update
IF OBJECT_ID('TR_DangKy_Global_Update', 'TR') IS NOT NULL
    DROP TRIGGER TR_DangKy_Global_Update;
GO

CREATE TRIGGER TR_DangKy_Global_Update
ON DangKy_Global
INSTEAD OF UPDATE
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Check if primary keys are being changed (not allowed)
    IF EXISTS (SELECT 1 FROM inserted i INNER JOIN deleted d ON i.MaSV <> d.MaSV OR i.MaMon <> d.MaMon)
    BEGIN
        RAISERROR(N'Không được phép thay đổi Mã Sinh Viên hoặc Mã Môn!', 16, 1);
        RETURN;
    END
    
    DECLARE @MaSV NVARCHAR(20);
    DECLARE @MaMon NVARCHAR(10);
    DECLARE @DiemThi DECIMAL(4,2);
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE dk_cursor CURSOR FOR
    SELECT MaSV, MaMon, DiemThi FROM inserted;
    
    OPEN dk_cursor;
    FETCH NEXT FROM dk_cursor INTO @MaSV, @MaMon, @DiemThi;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Get MaKhoa from SinhVien
        SELECT @MaKhoa = MaKhoa FROM SinhVien_Global WHERE MaSV = @MaSV;
        
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'UPDATE [SITE_A].SiteA.dbo.DangKy SET DiemThi = ' 
                + ISNULL(CAST(@DiemThi AS NVARCHAR(10)), 'NULL') 
                + N' WHERE MaSV = ''' + @MaSV + N''' AND MaMon = ''' + @MaMon + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'UPDATE [SITE_B].SiteB.dbo.DangKy SET DiemThi = ' 
                + ISNULL(CAST(@DiemThi AS NVARCHAR(10)), 'NULL') 
                + N' WHERE MaSV = ''' + @MaSV + N''' AND MaMon = ''' + @MaMon + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'UPDATE [SITE_C].SiteC.dbo.DangKy SET DiemThi = ' 
                + ISNULL(CAST(@DiemThi AS NVARCHAR(10)), 'NULL') 
                + N' WHERE MaSV = ''' + @MaSV + N''' AND MaMon = ''' + @MaMon + '''';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM dk_cursor INTO @MaSV, @MaMon, @DiemThi;
    END
    
    CLOSE dk_cursor;
    DEALLOCATE dk_cursor;
END;
GO

-- DELETE Trigger: Delete from appropriate site
IF OBJECT_ID('TR_DangKy_Global_Delete', 'TR') IS NOT NULL
    DROP TRIGGER TR_DangKy_Global_Delete;
GO

CREATE TRIGGER TR_DangKy_Global_Delete
ON DangKy_Global
INSTEAD OF DELETE
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @MaSV NVARCHAR(20);
    DECLARE @MaMon NVARCHAR(10);
    DECLARE @MaKhoa NVARCHAR(10);
    DECLARE @SQL NVARCHAR(MAX);
    
    DECLARE dk_cursor CURSOR FOR
    SELECT d.MaSV, d.MaMon, sv.MaKhoa
    FROM deleted d
    INNER JOIN SinhVien_Global sv ON d.MaSV = sv.MaSV;
    
    OPEN dk_cursor;
    FETCH NEXT FROM dk_cursor INTO @MaSV, @MaMon, @MaKhoa;
    
    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Route to appropriate site
        IF @MaKhoa < 'M'
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_A].SiteA.dbo.DangKy WHERE MaSV = ''' + @MaSV + N''' AND MaMon = ''' + @MaMon + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE IF @MaKhoa >= 'M' AND @MaKhoa < 'S'
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_B].SiteB.dbo.DangKy WHERE MaSV = ''' + @MaSV + N''' AND MaMon = ''' + @MaMon + '''';
            EXEC sp_executesql @SQL;
        END
        ELSE
        BEGIN
            SET @SQL = N'DELETE FROM [SITE_C].SiteC.dbo.DangKy WHERE MaSV = ''' + @MaSV + N''' AND MaMon = ''' + @MaMon + '''';
            EXEC sp_executesql @SQL;
        END
        
        FETCH NEXT FROM dk_cursor INTO @MaSV, @MaMon, @MaKhoa;
    END
    
    CLOSE dk_cursor;
    DEALLOCATE dk_cursor;
END;
GO

PRINT N'✓ Triggers cho DangKy_Global đã được tạo thành công';
GO

PRINT N'===== HOÀN THÀNH TẠO INSTEAD OF TRIGGERS =====';
GO
