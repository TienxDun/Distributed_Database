# Script tự động khởi tạo databases HUFLIT
# Chạy tuần tự các lệnh sqlcmd với encoding UTF-8

Write-Host "=== Khởi tạo Database HUFLIT ===" -ForegroundColor Green

# Set console encoding to UTF-8
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

Write-Host "1. Khởi tạo Site A..." -ForegroundColor Yellow
sqlcmd -S localhost,14334 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_a\init.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi khởi tạo Site A!" -ForegroundColor Red; exit 1 }

Write-Host "2. Seed dữ liệu Site A..." -ForegroundColor Yellow
sqlcmd -S localhost,14334 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_a\seed.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi seed Site A!" -ForegroundColor Red; exit 1 }

Write-Host "3. Khởi tạo Site B..." -ForegroundColor Yellow
sqlcmd -S localhost,14335 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_b\init.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi khởi tạo Site B!" -ForegroundColor Red; exit 1 }

Write-Host "4. Seed dữ liệu Site B..." -ForegroundColor Yellow
sqlcmd -S localhost,14335 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_b\seed.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi seed Site B!" -ForegroundColor Red; exit 1 }

Write-Host "5. Khởi tạo Site C..." -ForegroundColor Yellow
sqlcmd -S localhost,14336 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_c\init.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi khởi tạo Site C!" -ForegroundColor Red; exit 1 }

Write-Host "6. Seed dữ liệu Site C..." -ForegroundColor Yellow
sqlcmd -S localhost,14336 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\site_c\seed.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi seed Site C!" -ForegroundColor Red; exit 1 }

Write-Host "7. Khởi tạo HUFLIT Database với Linked Servers..." -ForegroundColor Yellow
sqlcmd -S localhost,14333 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\global\init.sql
if ($LASTEXITCODE -ne 0) { Write-Host "Lỗi khi khởi tạo HUFLIT!" -ForegroundColor Red; exit 1 }

Write-Host "=== Hoàn thành khởi tạo Database HUFLIT ===" -ForegroundColor Green
Write-Host "Bạn có thể test API tại: http://localhost:8080" -ForegroundColor Cyan