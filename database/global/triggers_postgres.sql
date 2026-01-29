-- =============================================
-- FUNCTIONS & TRIGGERS FOR Khoa_Global
-- =============================================

CREATE OR REPLACE FUNCTION tr_khoa_global_insert()
RETURNS TRIGGER AS $$
DECLARE
    target_table VARCHAR;
BEGIN
    -- Validate: Check if MaKhoa already exists globally
    IF EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa = NEW.MaKhoa) THEN
        RAISE EXCEPTION 'Mã khoa "%" đã tồn tại!', NEW.MaKhoa;
    END IF;

    -- Route to appropriate site
    IF NEW.MaKhoa < 'M' THEN
        INSERT INTO site_a.Khoa (MaKhoa, TenKhoa) VALUES (NEW.MaKhoa, NEW.TenKhoa);
    ELSIF NEW.MaKhoa >= 'M' AND NEW.MaKhoa < 'S' THEN
        INSERT INTO site_b.Khoa (MaKhoa, TenKhoa) VALUES (NEW.MaKhoa, NEW.TenKhoa);
    ELSE
        INSERT INTO site_c.Khoa (MaKhoa, TenKhoa) VALUES (NEW.MaKhoa, NEW.TenKhoa);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_Khoa_Global_Insert
INSTEAD OF INSERT ON Khoa_Global
FOR EACH ROW EXECUTE FUNCTION tr_khoa_global_insert();

CREATE OR REPLACE FUNCTION tr_khoa_global_update()
RETURNS TRIGGER AS $$
BEGIN
    -- Prevent MaKhoa change
    IF OLD.MaKhoa <> NEW.MaKhoa THEN
        RAISE EXCEPTION 'Không được phép thay đổi Mã Khoa!';
    END IF;

    -- Update on appropriate site
    IF OLD.MaKhoa < 'M' THEN
        UPDATE site_a.Khoa SET TenKhoa = NEW.TenKhoa WHERE MaKhoa = OLD.MaKhoa;
    ELSIF OLD.MaKhoa >= 'M' AND OLD.MaKhoa < 'S' THEN
        UPDATE site_b.Khoa SET TenKhoa = NEW.TenKhoa WHERE MaKhoa = OLD.MaKhoa;
    ELSE
        UPDATE site_c.Khoa SET TenKhoa = NEW.TenKhoa WHERE MaKhoa = OLD.MaKhoa;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_Khoa_Global_Update
INSTEAD OF UPDATE ON Khoa_Global
FOR EACH ROW EXECUTE FUNCTION tr_khoa_global_update();

CREATE OR REPLACE FUNCTION tr_khoa_global_delete()
RETURNS TRIGGER AS $$
BEGIN
    -- Check related SinhVien
    IF EXISTS (SELECT 1 FROM SinhVien_Global WHERE MaKhoa = OLD.MaKhoa) THEN
        RAISE EXCEPTION 'Không thể xóa khoa "%" vì còn sinh viên liên quan!', OLD.MaKhoa;
    END IF;

    -- Check related CTDaoTao
    IF EXISTS (SELECT 1 FROM CTDaoTao_Global WHERE MaKhoa = OLD.MaKhoa) THEN
        RAISE EXCEPTION 'Không thể xóa khoa "%" vì còn chương trình đào tạo liên quan!', OLD.MaKhoa;
    END IF;

    -- Delete from appropriate site
    IF OLD.MaKhoa < 'M' THEN
        DELETE FROM site_a.Khoa WHERE MaKhoa = OLD.MaKhoa;
    ELSIF OLD.MaKhoa >= 'M' AND OLD.MaKhoa < 'S' THEN
        DELETE FROM site_b.Khoa WHERE MaKhoa = OLD.MaKhoa;
    ELSE
        DELETE FROM site_c.Khoa WHERE MaKhoa = OLD.MaKhoa;
    END IF;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_Khoa_Global_Delete
INSTEAD OF DELETE ON Khoa_Global
FOR EACH ROW EXECUTE FUNCTION tr_khoa_global_delete();

-- =============================================
-- FUNCTIONS & TRIGGERS FOR MonHoc_Global
-- =============================================

CREATE OR REPLACE FUNCTION tr_monhoc_global_insert()
RETURNS TRIGGER AS $$
BEGIN
    -- Validate: Check if MaMH already exists (check Site A as proxy for all)
    IF EXISTS (SELECT 1 FROM site_a.MonHoc WHERE MaMH = NEW.MaMH) THEN
        RAISE EXCEPTION 'Mã môn học "%" đã tồn tại!', NEW.MaMH;
    END IF;

    -- Insert to all 3 sites
    INSERT INTO site_a.MonHoc (MaMH, TenMH) VALUES (NEW.MaMH, NEW.TenMH);
    INSERT INTO site_b.MonHoc (MaMH, TenMH) VALUES (NEW.MaMH, NEW.TenMH);
    INSERT INTO site_c.MonHoc (MaMH, TenMH) VALUES (NEW.MaMH, NEW.TenMH);

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_MonHoc_Global_Insert
INSTEAD OF INSERT ON MonHoc_Global
FOR EACH ROW EXECUTE FUNCTION tr_monhoc_global_insert();

CREATE OR REPLACE FUNCTION tr_monhoc_global_update()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.MaMH <> NEW.MaMH THEN
        RAISE EXCEPTION 'Không được phép thay đổi Mã Môn Học!';
    END IF;

    -- Update on all 3 sites
    UPDATE site_a.MonHoc SET TenMH = NEW.TenMH WHERE MaMH = OLD.MaMH;
    UPDATE site_b.MonHoc SET TenMH = NEW.TenMH WHERE MaMH = OLD.MaMH;
    UPDATE site_c.MonHoc SET TenMH = NEW.TenMH WHERE MaMH = OLD.MaMH;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_MonHoc_Global_Update
INSTEAD OF UPDATE ON MonHoc_Global
FOR EACH ROW EXECUTE FUNCTION tr_monhoc_global_update();

CREATE OR REPLACE FUNCTION tr_monhoc_global_delete()
RETURNS TRIGGER AS $$
BEGIN
    -- Check related CTDaoTao
    IF EXISTS (SELECT 1 FROM CTDaoTao_Global WHERE MaMH = OLD.MaMH) THEN
        RAISE EXCEPTION 'Không thể xóa môn học "%" vì còn trong chương trình đào tạo!', OLD.MaMH;
    END IF;

    -- Check related DangKy
    IF EXISTS (SELECT 1 FROM DangKy_Global WHERE MaMon = OLD.MaMH) THEN
        RAISE EXCEPTION 'Không thể xóa môn học "%" vì có sinh viên đã đăng ký!', OLD.MaMH;
    END IF;

    -- Delete from all 3 sites
    DELETE FROM site_a.MonHoc WHERE MaMH = OLD.MaMH;
    DELETE FROM site_b.MonHoc WHERE MaMH = OLD.MaMH;
    DELETE FROM site_c.MonHoc WHERE MaMH = OLD.MaMH;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_MonHoc_Global_Delete
INSTEAD OF DELETE ON MonHoc_Global
FOR EACH ROW EXECUTE FUNCTION tr_monhoc_global_delete();

-- =============================================
-- FUNCTIONS & TRIGGERS FOR SinhVien_Global
-- =============================================

CREATE OR REPLACE FUNCTION tr_sinhvien_global_insert()
RETURNS TRIGGER AS $$
BEGIN
    -- Validate: Check if MaSV already exists
    IF EXISTS (SELECT 1 FROM SinhVien_Global WHERE MaSV = NEW.MaSV) THEN
        RAISE EXCEPTION 'Mã sinh viên "%" đã tồn tại!', NEW.MaSV;
    END IF;

    -- Validate: Check if MaKhoa exists
    IF NOT EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa = NEW.MaKhoa) THEN
        RAISE EXCEPTION 'Mã khoa "%" không tồn tại!', NEW.MaKhoa;
    END IF;

    -- Route to appropriate site
    IF NEW.MaKhoa < 'M' THEN
        INSERT INTO site_a.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (NEW.MaSV, NEW.HoTen, NEW.MaKhoa, NEW.KhoaHoc);
    ELSIF NEW.MaKhoa >= 'M' AND NEW.MaKhoa < 'S' THEN
        INSERT INTO site_b.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (NEW.MaSV, NEW.HoTen, NEW.MaKhoa, NEW.KhoaHoc);
    ELSE
        INSERT INTO site_c.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (NEW.MaSV, NEW.HoTen, NEW.MaKhoa, NEW.KhoaHoc);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_SinhVien_Global_Insert
INSTEAD OF INSERT ON SinhVien_Global
FOR EACH ROW EXECUTE FUNCTION tr_sinhvien_global_insert();

CREATE OR REPLACE FUNCTION tr_sinhvien_global_update()
RETURNS TRIGGER AS $$
BEGIN
    IF OLD.MaSV <> NEW.MaSV THEN
        RAISE EXCEPTION 'Không được phép thay đổi Mã Sinh Viên!';
    END IF;

    -- Validate: Check if new MaKhoa exists
    IF NOT EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa = NEW.MaKhoa) THEN
        RAISE EXCEPTION 'Mã khoa mới "%" không tồn tại!', NEW.MaKhoa;
    END IF;

    IF OLD.MaKhoa <> NEW.MaKhoa THEN
        -- Check if student has DangKy records
        IF EXISTS (SELECT 1 FROM DangKy_Global WHERE MaSV = OLD.MaSV) THEN
            RAISE EXCEPTION 'Không thể chuyển khoa cho sinh viên "%" vì có dữ liệu đăng ký môn học. Xóa đăng ký trước!', OLD.MaSV;
        END IF;

        -- Delete from old site
        IF OLD.MaKhoa < 'M' THEN
            DELETE FROM site_a.SinhVien WHERE MaSV = OLD.MaSV;
        ELSIF OLD.MaKhoa >= 'M' AND OLD.MaKhoa < 'S' THEN
            DELETE FROM site_b.SinhVien WHERE MaSV = OLD.MaSV;
        ELSE
            DELETE FROM site_c.SinhVien WHERE MaSV = OLD.MaSV;
        END IF;

        -- Insert to new site
        IF NEW.MaKhoa < 'M' THEN
            INSERT INTO site_a.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (NEW.MaSV, NEW.HoTen, NEW.MaKhoa, NEW.KhoaHoc);
        ELSIF NEW.MaKhoa >= 'M' AND NEW.MaKhoa < 'S' THEN
            INSERT INTO site_b.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (NEW.MaSV, NEW.HoTen, NEW.MaKhoa, NEW.KhoaHoc);
        ELSE
            INSERT INTO site_c.SinhVien (MaSV, HoTen, MaKhoa, KhoaHoc) VALUES (NEW.MaSV, NEW.HoTen, NEW.MaKhoa, NEW.KhoaHoc);
        END IF;
    ELSE
        -- MaKhoa not changed, just update other fields
        IF NEW.MaKhoa < 'M' THEN
            UPDATE site_a.SinhVien SET HoTen = NEW.HoTen, KhoaHoc = NEW.KhoaHoc WHERE MaSV = OLD.MaSV;
        ELSIF NEW.MaKhoa >= 'M' AND NEW.MaKhoa < 'S' THEN
            UPDATE site_b.SinhVien SET HoTen = NEW.HoTen, KhoaHoc = NEW.KhoaHoc WHERE MaSV = OLD.MaSV;
        ELSE
            UPDATE site_c.SinhVien SET HoTen = NEW.HoTen, KhoaHoc = NEW.KhoaHoc WHERE MaSV = OLD.MaSV;
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_SinhVien_Global_Update
INSTEAD OF UPDATE ON SinhVien_Global
FOR EACH ROW EXECUTE FUNCTION tr_sinhvien_global_update();

CREATE OR REPLACE FUNCTION tr_sinhvien_global_delete()
RETURNS TRIGGER AS $$
BEGIN
    -- Check if there are related DangKy
    IF EXISTS (SELECT 1 FROM DangKy_Global WHERE MaSV = OLD.MaSV) THEN
        RAISE EXCEPTION 'Không thể xóa sinh viên "%" vì có dữ liệu đăng ký môn học!', OLD.MaSV;
    END IF;

    -- Delete from appropriate site
    IF OLD.MaKhoa < 'M' THEN
        DELETE FROM site_a.SinhVien WHERE MaSV = OLD.MaSV;
    ELSIF OLD.MaKhoa >= 'M' AND OLD.MaKhoa < 'S' THEN
        DELETE FROM site_b.SinhVien WHERE MaSV = OLD.MaSV;
    ELSE
        DELETE FROM site_c.SinhVien WHERE MaSV = OLD.MaSV;
    END IF;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_SinhVien_Global_Delete
INSTEAD OF DELETE ON SinhVien_Global
FOR EACH ROW EXECUTE FUNCTION tr_sinhvien_global_delete();

-- =============================================
-- FUNCTIONS & TRIGGERS FOR CTDaoTao_Global
-- =============================================

CREATE OR REPLACE FUNCTION tr_ctdaotao_global_insert()
RETURNS TRIGGER AS $$
BEGIN
    -- Validate: Check if combination already exists
    IF EXISTS (SELECT 1 FROM CTDaoTao_Global WHERE MaKhoa = NEW.MaKhoa AND KhoaHoc = NEW.KhoaHoc AND MaMH = NEW.MaMH) THEN
        RAISE EXCEPTION 'Chương trình đào tạo cho khoa "%", khóa học % và môn "%" đã tồn tại!', NEW.MaKhoa, NEW.KhoaHoc, NEW.MaMH;
    END IF;

    -- Validate: Check if MaKhoa exists
    IF NOT EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa = NEW.MaKhoa) THEN
        RAISE EXCEPTION 'Mã khoa "%" không tồn tại!', NEW.MaKhoa;
    END IF;

    -- Validate: Check if MaMH exists
    IF NOT EXISTS (SELECT 1 FROM MonHoc_Global WHERE MaMH = NEW.MaMH) THEN
        RAISE EXCEPTION 'Mã môn học "%" không tồn tại!', NEW.MaMH;
    END IF;

    -- Route to appropriate site
    IF NEW.MaKhoa < 'M' THEN
        INSERT INTO site_a.CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES (NEW.MaKhoa, NEW.KhoaHoc, NEW.MaMH);
    ELSIF NEW.MaKhoa >= 'M' AND NEW.MaKhoa < 'S' THEN
        INSERT INTO site_b.CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES (NEW.MaKhoa, NEW.KhoaHoc, NEW.MaMH);
    ELSE
        INSERT INTO site_c.CTDaoTao (MaKhoa, KhoaHoc, MaMH) VALUES (NEW.MaKhoa, NEW.KhoaHoc, NEW.MaMH);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_CTDaoTao_Global_Insert
INSTEAD OF INSERT ON CTDaoTao_Global
FOR EACH ROW EXECUTE FUNCTION tr_ctdaotao_global_insert();

CREATE OR REPLACE FUNCTION tr_ctdaotao_global_update()
RETURNS TRIGGER AS $$
BEGIN
    RAISE EXCEPTION 'Không được phép cập nhật chương trình đào tạo! Xóa và thêm mới nếu cần thay đổi.';
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_CTDaoTao_Global_Update
INSTEAD OF UPDATE ON CTDaoTao_Global
FOR EACH ROW EXECUTE FUNCTION tr_ctdaotao_global_update();

CREATE OR REPLACE FUNCTION tr_ctdaotao_global_delete()
RETURNS TRIGGER AS $$
BEGIN
    -- Route to appropriate site
    IF OLD.MaKhoa < 'M' THEN
        DELETE FROM site_a.CTDaoTao WHERE MaKhoa = OLD.MaKhoa AND KhoaHoc = OLD.KhoaHoc AND MaMH = OLD.MaMH;
    ELSIF OLD.MaKhoa >= 'M' AND OLD.MaKhoa < 'S' THEN
        DELETE FROM site_b.CTDaoTao WHERE MaKhoa = OLD.MaKhoa AND KhoaHoc = OLD.KhoaHoc AND MaMH = OLD.MaMH;
    ELSE
        DELETE FROM site_c.CTDaoTao WHERE MaKhoa = OLD.MaKhoa AND KhoaHoc = OLD.KhoaHoc AND MaMH = OLD.MaMH;
    END IF;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_CTDaoTao_Global_Delete
INSTEAD OF DELETE ON CTDaoTao_Global
FOR EACH ROW EXECUTE FUNCTION tr_ctdaotao_global_delete();

-- =============================================
-- FUNCTIONS & TRIGGERS FOR DangKy_Global
-- =============================================
-- Not strictly in the T-SQL original but needed for completeness

CREATE OR REPLACE FUNCTION tr_dangky_global_insert()
RETURNS TRIGGER AS $$
DECLARE
    v_MaKhoa VARCHAR(10);
BEGIN
    -- Get SV Info to determine site
    SELECT MaKhoa INTO v_MaKhoa FROM SinhVien_Global WHERE MaSV = NEW.MaSV;
    
    IF v_MaKhoa IS NULL THEN
         RAISE EXCEPTION 'Mã sinh viên "%" không tồn tại!', NEW.MaSV;
    END IF;

    IF NEW.MaMon IS NULL THEN
         RAISE EXCEPTION 'Mã môn không được để trống!';
    END IF;

    -- Route to appropriate site
    IF v_MaKhoa < 'M' THEN
        INSERT INTO site_a.DangKy (MaSV, MaMon, DiemThi) VALUES (NEW.MaSV, NEW.MaMon, NEW.DiemThi);
    ELSIF v_MaKhoa >= 'M' AND v_MaKhoa < 'S' THEN
        INSERT INTO site_b.DangKy (MaSV, MaMon, DiemThi) VALUES (NEW.MaSV, NEW.MaMon, NEW.DiemThi);
    ELSE
        INSERT INTO site_c.DangKy (MaSV, MaMon, DiemThi) VALUES (NEW.MaSV, NEW.MaMon, NEW.DiemThi);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_DangKy_Global_Insert
INSTEAD OF INSERT ON DangKy_Global
FOR EACH ROW EXECUTE FUNCTION tr_dangky_global_insert();

CREATE OR REPLACE FUNCTION tr_dangky_global_update()
RETURNS TRIGGER AS $$
DECLARE
    v_MaKhoa VARCHAR(10);
BEGIN
    -- Get SV Info to determine site
    SELECT MaKhoa INTO v_MaKhoa FROM SinhVien_Global WHERE MaSV = OLD.MaSV;

    IF v_MaKhoa < 'M' THEN
        UPDATE site_a.DangKy SET DiemThi = NEW.DiemThi WHERE MaSV = OLD.MaSV AND MaMon = OLD.MaMon;
    ELSIF v_MaKhoa >= 'M' AND v_MaKhoa < 'S' THEN
        UPDATE site_b.DangKy SET DiemThi = NEW.DiemThi WHERE MaSV = OLD.MaSV AND MaMon = OLD.MaMon;
    ELSE
        UPDATE site_c.DangKy SET DiemThi = NEW.DiemThi WHERE MaSV = OLD.MaSV AND MaMon = OLD.MaMon;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_DangKy_Global_Update
INSTEAD OF UPDATE ON DangKy_Global
FOR EACH ROW EXECUTE FUNCTION tr_dangky_global_update();

CREATE OR REPLACE FUNCTION tr_dangky_global_delete()
RETURNS TRIGGER AS $$
DECLARE
    v_MaKhoa VARCHAR(10);
BEGIN
    -- Get SV Info to determine site
    SELECT MaKhoa INTO v_MaKhoa FROM SinhVien_Global WHERE MaSV = OLD.MaSV;

    IF v_MaKhoa < 'M' THEN
        DELETE FROM site_a.DangKy WHERE MaSV = OLD.MaSV AND MaMon = OLD.MaMon;
    ELSIF v_MaKhoa >= 'M' AND v_MaKhoa < 'S' THEN
        DELETE FROM site_b.DangKy WHERE MaSV = OLD.MaSV AND MaMon = OLD.MaMon;
    ELSE
        DELETE FROM site_c.DangKy WHERE MaSV = OLD.MaSV AND MaMon = OLD.MaMon;
    END IF;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER TR_DangKy_Global_Delete
INSTEAD OF DELETE ON DangKy_Global
FOR EACH ROW EXECUTE FUNCTION tr_dangky_global_delete();
