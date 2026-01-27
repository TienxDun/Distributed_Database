#!/bin/bash

# Script tự động khởi tạo dữ liệu mẫu cho PostgreSQL HUFLIT Database
# Chạy lệnh psql để import dữ liệu từ file seed_postgres.sql

echo "=== Khởi tạo Dữ liệu Mẫu PostgreSQL HUFLIT ==="
echo ""

# Kiểm tra Docker Compose có đang chạy không
if ! docker-compose ps --services --filter "status=running" | grep -q "postgres"; then
    echo "Lỗi: Container postgres không đang chạy!"
    echo "Vui lòng chạy 'docker-compose up -d' trước."
    exit 1
fi

echo "1. Kiểm tra kết nối đến PostgreSQL..."
if ! docker-compose exec -T postgres psql -U admin -d huflit -c "SELECT 1;" >/dev/null 2>&1; then
    echo "✗ Không thể kết nối đến PostgreSQL"
    exit 1
fi
echo "✓ Kết nối thành công"

echo ""
echo "2. Reset dữ liệu hiện có..."

# Reset các bảng trong tất cả schemas
schemas=("site_a" "site_b" "site_c")
tables=("DangKy" "SinhVien" "CTDaoTao" "Khoa" "MonHoc")

for schema in "${schemas[@]}"; do
    for table in "${tables[@]}"; do
        echo "  - Xóa dữ liệu bảng ${schema}.${table}..."
        docker-compose exec -T postgres psql -U admin -d huflit -c "TRUNCATE TABLE ${schema}.${table} CASCADE;" >/dev/null 2>&1
    done
done

echo "✓ Đã reset toàn bộ dữ liệu"

echo ""
echo "3. Import dữ liệu mẫu từ seed_postgres.sql..."

# Chạy file seed_postgres.sql
if ! docker-compose exec -T postgres psql -U admin -d huflit -f /docker-entrypoint-initdb.d/03_seed.sql >/dev/null 2>&1; then
    echo "✗ Lỗi khi import dữ liệu mẫu"
    exit 1
fi
echo "✓ Đã import dữ liệu mẫu thành công"

echo ""
echo "4. Kiểm tra kết quả import..."

# Kiểm tra số lượng records trong mỗi bảng
echo "Số lượng records trong các bảng:"

queries=(
    "SELECT 'site_a.khoa' as table_name, COUNT(*) as count FROM site_a.khoa"
    "SELECT 'site_a.sinhvien' as table_name, COUNT(*) as count FROM site_a.sinhvien"
    "SELECT 'site_a.monhoc' as table_name, COUNT(*) as count FROM site_a.monhoc"
    "SELECT 'site_a.ctdaotao' as table_name, COUNT(*) as count FROM site_a.ctdaotao"
    "SELECT 'site_a.dangky' as table_name, COUNT(*) as count FROM site_a.dangky"
    "SELECT 'site_b.khoa' as table_name, COUNT(*) as count FROM site_b.khoa"
    "SELECT 'site_b.sinhvien' as table_name, COUNT(*) as count FROM site_b.sinhvien"
    "SELECT 'site_b.monhoc' as table_name, COUNT(*) as count FROM site_b.monhoc"
    "SELECT 'site_b.ctdaotao' as table_name, COUNT(*) as count FROM site_b.ctdaotao"
    "SELECT 'site_b.dangky' as table_name, COUNT(*) as count FROM site_b.dangky"
    "SELECT 'site_c.khoa' as table_name, COUNT(*) as count FROM site_c.khoa"
    "SELECT 'site_c.sinhvien' as table_name, COUNT(*) as count FROM site_c.sinhvien"
    "SELECT 'site_c.monhoc' as table_name, COUNT(*) as count FROM site_c.monhoc"
    "SELECT 'site_c.ctdaotao' as table_name, COUNT(*) as count FROM site_c.ctdaotao"
    "SELECT 'site_c.dangky' as table_name, COUNT(*) as count FROM site_c.dangky"
)

for query in "${queries[@]}"; do
    result=$(docker-compose exec -T postgres psql -U admin -d huflit -c "$query" --csv 2>/dev/null | tail -n 1)
    if [ ! -z "$result" ]; then
        table_name=$(echo $result | cut -d',' -f1)
        count=$(echo $result | cut -d',' -f2)
        printf "  %-20s : %s\n" "$table_name" "$count"
    fi
done

echo ""
echo "=== Hoàn thành khởi tạo dữ liệu mẫu ==="
echo "✓ Database đã được khởi tạo với dữ liệu mẫu đầy đủ"
echo ""
echo "Bạn có thể truy cập:"
echo "  - Giao diện quản lý: http://localhost:8081/ui.php"
echo "  - Công cụ bảo trì: http://localhost:8081/maintenance.php"
echo "  - API: http://localhost:8080"