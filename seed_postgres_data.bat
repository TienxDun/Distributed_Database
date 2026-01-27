@echo off
chcp 65001 >nul
REM Script tự động khởi tạo dữ liệu mẫu cho PostgreSQL HUFLIT Database
REM Chạy lệnh psql để import dữ liệu từ file seed_postgres.sql

echo === Khởi tạo Dữ liệu Mẫu PostgreSQL HUFLIT ===
echo.

REM Kiểm tra Docker Compose có đang chạy không
docker-compose ps --services --filter "status=running" | findstr postgres >nul
if errorlevel 1 (
    echo Lỗi: Container postgres không đang chạy!
    echo Vui lòng chạy 'docker-compose up -d' trước.
    goto :error
)

echo 1. Kiểm tra kết nối đến PostgreSQL...
docker-compose exec -T postgres psql -U admin -d huflit -c "SELECT 1;" >nul 2>&1
if errorlevel 1 (
    echo ✗ Không thể kết nối đến PostgreSQL
    goto :error
)
echo ✓ Kết nối thành công

echo.
echo 2. Reset dữ liệu hiện có...

REM Reset các bảng trong tất cả schemas
set schemas=site_a site_b site_c
set tables=DangKy SinhVien CTDaoTao Khoa MonHoc

for %%s in (%schemas%) do (
    for %%t in (%tables%) do (
        echo   - Xóa dữ liệu bảng %%s.%%t...
        docker-compose exec -T postgres psql -U admin -d huflit -c "TRUNCATE TABLE %%s.%%t CASCADE;" >nul 2>&1
    )
)

echo ✓ Đã reset toàn bộ dữ liệu

echo.
echo 3. Import dữ liệu mẫu từ seed_postgres.sql...

REM Chạy file seed_postgres.sql
docker-compose exec -T postgres psql -U admin -d huflit -f /docker-entrypoint-initdb.d/03_seed.sql >nul 2>&1
if errorlevel 1 (
    echo ✗ Lỗi khi import dữ liệu mẫu
    goto :error
)
echo ✓ Đã import dữ liệu mẫu thành công

echo.
echo 4. Kiểm tra kết quả import...

REM Kiểm tra số lượng records trong mỗi bảng
echo Số lượng records trong các bảng:

set queries[0]="SELECT 'site_a.khoa' as table_name, COUNT(*) as count FROM site_a.khoa"
set queries[1]="SELECT 'site_a.sinhvien' as table_name, COUNT(*) as count FROM site_a.sinhvien"
set queries[2]="SELECT 'site_a.monhoc' as table_name, COUNT(*) as count FROM site_a.monhoc"
set queries[3]="SELECT 'site_a.ctdaotao' as table_name, COUNT(*) as count FROM site_a.ctdaotao"
set queries[4]="SELECT 'site_a.dangky' as table_name, COUNT(*) as count FROM site_a.dangky"
set queries[5]="SELECT 'site_b.khoa' as table_name, COUNT(*) as count FROM site_b.khoa"
set queries[6]="SELECT 'site_b.sinhvien' as table_name, COUNT(*) as count FROM site_b.sinhvien"
set queries[7]="SELECT 'site_b.monhoc' as table_name, COUNT(*) as count FROM site_b.monhoc"
set queries[8]="SELECT 'site_b.ctdaotao' as table_name, COUNT(*) as count FROM site_b.ctdaotao"
set queries[9]="SELECT 'site_b.dangky' as table_name, COUNT(*) as count FROM site_b.dangky"
set queries[10]="SELECT 'site_c.khoa' as table_name, COUNT(*) as count FROM site_c.khoa"
set queries[11]="SELECT 'site_c.sinhvien' as table_name, COUNT(*) as count FROM site_c.sinhvien"
set queries[12]="SELECT 'site_c.monhoc' as table_name, COUNT(*) as count FROM site_c.monhoc"
set queries[13]="SELECT 'site_c.ctdaotao' as table_name, COUNT(*) as count FROM site_c.ctdaotao"
set queries[14]="SELECT 'site_c.dangky' as table_name, COUNT(*) as count FROM site_c.dangky"

for /L %%i in (0,1,14) do (
    for /F "tokens=*" %%q in ("!queries[%%i]!") do (
        for /F "tokens=1,2 delims=," %%a in ('docker-compose exec -T postgres psql -U admin -d huflit -c "%%q" --csv 2^>nul ^| findstr /v "table_name,count"') do (
            echo   %%~a                          : %%b
        )
    )
)

echo.
echo === Hoàn thành khởi tạo dữ liệu mẫu ===
echo ✓ Database đã được khởi tạo với dữ liệu mẫu đầy đủ
echo.
echo Bạn có thể truy cập:
echo   - Giao diện quản lý: http://localhost:8081/ui.php
echo   - Công cụ bảo trì: http://localhost:8081/maintenance.php
echo   - API: http://localhost:8080
goto :end

:error
echo.
echo Script thất bại!
pause
exit /b 1

:end
echo.
pause