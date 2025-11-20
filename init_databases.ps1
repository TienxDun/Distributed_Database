# Script tự động khởi tạo databases HUFLIT
# Chạy tuần tự các lệnh sqlcmd với encoding UTF-8

Write-Host "=== Khởi tạo Database HUFLIT ===" -ForegroundColor Green
Write-Host ""

# Hiển thị menu lựa chọn
Write-Host "Chọn chế độ khởi tạo:" -ForegroundColor Cyan
Write-Host "1. Khởi tạo với dữ liệu mẫu (khuyến nghị)" -ForegroundColor White
Write-Host "2. Khởi tạo không có dữ liệu mẫu (chỉ cấu trúc)" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Nhập lựa chọn của bạn (1 hoặc 2)"

if ($choice -ne "1" -and $choice -ne "2") {
    Write-Host "Lựa chọn không hợp lệ! Vui lòng chạy lại script và chọn 1 hoặc 2." -ForegroundColor Red
    exit 1
}

$withSampleData = ($choice -eq "1")

if ($withSampleData) {
    Write-Host "`nKhởi tạo với dữ liệu mẫu..." -ForegroundColor Green
} else {
    Write-Host "`nKhởi tạo không có dữ liệu mẫu..." -ForegroundColor Green
}

Write-Host ""

# Set console encoding to UTF-8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

Write-Host "1. Khởi tạo Site A..." -ForegroundColor Yellow
sqlcmd -S localhost,14334 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_a\init.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi khởi tạo Site A!" -ForegroundColor Red; exit 1 }

if ($withSampleData) {
    Write-Host "2. Seed dữ liệu Site A..." -ForegroundColor Yellow
    sqlcmd -S localhost,14334 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_a\seed.sql
    if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi seed Site A!" -ForegroundColor Red; exit 1 }
}

Write-Host "3. Khởi tạo Site B..." -ForegroundColor Yellow
sqlcmd -S localhost,14335 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_b\init.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi khởi tạo Site B!" -ForegroundColor Red; exit 1 }

if ($withSampleData) {
    Write-Host "4. Seed dữ liệu Site B..." -ForegroundColor Yellow
    sqlcmd -S localhost,14335 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_b\seed.sql
    if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi seed Site B!" -ForegroundColor Red; exit 1 }
}

Write-Host "5. Khởi tạo Site C..." -ForegroundColor Yellow
sqlcmd -S localhost,14336 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_c\init.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi khởi tạo Site C!" -ForegroundColor Red; exit 1 }

if ($withSampleData) {
    Write-Host "6. Seed dữ liệu Site C..." -ForegroundColor Yellow
    sqlcmd -S localhost,14336 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_c\seed.sql
    if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi seed Site C!" -ForegroundColor Red; exit 1 }
}

Write-Host "7. Khởi tạo HUFLIT Database với Linked Servers..." -ForegroundColor Yellow
sqlcmd -S localhost,14333 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\global\init.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi khởi tạo HUFLIT!" -ForegroundColor Red; exit 1 }

Write-Host ""
Write-Host "=== Hoàn thành khởi tạo Database HUFLIT ===" -ForegroundColor Green

if ($withSampleData) {
    Write-Host "✓ Database đã được khởi tạo với dữ liệu mẫu" -ForegroundColor Green
} else {
    Write-Host "✓ Database đã được khởi tạo (không có dữ liệu mẫu)" -ForegroundColor Green
}

Write-Host "Bạn có thể test API tại: http://localhost:8080" -ForegroundColor Cyan
Write-Host "Hoặc truy cập giao diện UI tại: http://localhost:8081/ui.php" -ForegroundColor Cyan