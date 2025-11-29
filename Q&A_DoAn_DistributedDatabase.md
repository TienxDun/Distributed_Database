# Q&A về Đồ án Hệ thống Cơ sở dữ liệu Phân tán HUFLIT

**Sinh viên:** Kính thưa thầy cô, em xin trình bày về đồ án của em là "Hệ thống Cơ sở dữ liệu phân tán HUFLIT". Đây là đồ án môn Cơ sở dữ liệu phân tán, sử dụng SQL Server cho 3 sites phân tán, MongoDB cho nhật ký kiểm tra, và triển khai bằng Docker. Em sẽ giải thích chi tiết như đang trong vòng vấn đáp.

## Phần 1: Giới thiệu tổng quan

**Giảng viên:** Bạn có thể giới thiệu ngắn gọn về đồ án này không?

**Sinh viên:** Vâng thầy cô. Đồ án này xây dựng một hệ thống cơ sở dữ liệu phân tán cho trường Đại học Ngoại ngữ - Tin học TP.HCM (HUFLIT). Hệ thống quản lý thông tin về khoa, môn học, sinh viên, chương trình đào tạo và đăng ký học. Dữ liệu được phân tán trên 3 sites SQL Server dựa trên mã khoa (MaKhoa), và sử dụng MongoDB để lưu trữ nhật ký kiểm tra và thống kê. Ứng dụng web được xây dựng bằng PHP với giao diện người dùng tương tác, và toàn bộ hệ thống được container hóa bằng Docker để dễ dàng triển khai.

**Giảng viên:** Tại sao lại chọn mô hình phân tán?

**Sinh viên:** Thầy cô, trong môi trường giáo dục lớn như HUFLIT, dữ liệu rất lớn và phân tán theo địa lý (các khoa khác nhau). Mô hình phân tán giúp:
- Cải thiện hiệu suất truy vấn cục bộ
- Tăng khả năng mở rộng
- Giảm tải cho server trung tâm
- Đảm bảo tính sẵn sàng cao

## Phần 2: Kiến trúc hệ thống

**Giảng viên:** Hãy mô tả kiến trúc tổng quan của hệ thống.

**Sinh viên:** Hệ thống có 3 lớp chính:

1. **Lớp Frontend:** Giao diện web (localhost:8081) và API Gateway (localhost:8080)
2. **Lớp Ứng dụng:** Hai container PHP - một cho API, một cho UI
3. **Lớp Cơ sở dữ liệu:** 
   - SQL Server cluster với 4 container: 1 Global DB và 3 Site DB
   - MongoDB cho logging

Dữ liệu được phân mảnh theo MaKhoa:
- Site A: MaKhoa < 'M' (CNTT, KHMT)
- Site B: 'M' ≤ MaKhoa < 'S' (KT, NN)  
- Site C: MaKhoa ≥ 'S' (SP, SH)

**Giảng viên:** Cơ chế phân tán như thế nào?

**Sinh viên:** Em sử dụng:
- **Linked Servers:** Kết nối từ Global DB đến các sites
- **Partitioned Views:** Khung nhìn phân mảnh để truy vấn thống nhất
- **INSTEAD OF Triggers:** Xử lý INSERT/UPDATE/DELETE tự động route đến site đúng
- **MongoDB:** Lưu nhật ký tất cả thao tác để kiểm tra và thống kê

## Phần 3: Công nghệ sử dụng

**Giảng viên:** Các công nghệ chính trong đồ án?

**Sinh viên:** 
- **Backend:** PHP 8 với OOP, xử lý API RESTful
- **Database:** SQL Server 2022 cho data chính, MongoDB cho logs
- **Frontend:** HTML5, CSS3, JavaScript ES6 Modules, Chart.js cho biểu đồ
- **Containerization:** Docker & Docker Compose
- **Development:** PowerShell scripts cho automation

**Giảng viên:** Tại sao chọn SQL Server và MongoDB?

**Sinh viên:** SQL Server phù hợp cho dữ liệu quan hệ phức tạp của trường học. MongoDB NoSQL tốt cho logs không cấu trúc, dễ scale và query nhanh cho thống kê.

## Phần 4: Tính năng chính

**Giảng viên:** Hệ thống có những tính năng gì?

**Sinh viên:** 
- **CRUD Operations:** Quản lý đầy đủ Khoa, Môn học, Sinh viên, CTĐT, Đăng ký
- **Global Queries:** Truy vấn phức tạp trên toàn bộ dữ liệu phân tán
- **Real-time Logging:** Ghi log mọi thao tác vào MongoDB
- **Statistics Dashboard:** Biểu đồ thống kê với Chart.js
- **Interactive UI:** Giao diện responsive, modal forms, validation

**Giảng viên:** Hãy giải thích luồng dữ liệu cho một thao tác INSERT.

**Sinh viên:** Ví dụ INSERT sinh viên:
1. User nhập form trên UI
2. Request gửi đến API Gateway
3. PHP app xử lý, gọi stored procedure trên Global DB
4. INSTEAD OF trigger kiểm tra MaKhoa, route đến site tương ứng
5. Site DB thực hiện INSERT
6. Log thao tác vào MongoDB
7. Trả response về UI

## Phần 5: Triển khai và Demo

**Giảng viên:** Cách chạy hệ thống?

**Sinh viên:** 
```powershell
# Khởi động containers
docker-compose up -d

# Khởi tạo databases
.\init_databases.ps1
```

URLs:
- UI: http://localhost:8081/ui.php
- Logs: http://localhost:8081/logs.php
- Stats: http://localhost:8081/stats.php

**Giảng viên:** Demo một tính năng.

**Sinh viên:** Em có thể demo thêm/xóa/sửa khoa. Khi thêm khoa với MaKhoa='CNTT', nó sẽ tự động vào Site A. Logs sẽ ghi lại thao tác này.

## Phần 6: Thách thức và Bài học

**Giảng viên:** Những thách thức gặp phải?

**Sinh viên:** 
- Thiết lập Linked Servers phức tạp
- Viết triggers cho cross-site updates
- Đồng bộ dữ liệu giữa sites
- Xử lý transactions phân tán

**Bài học:** Hiểu sâu về distributed systems, tầm quan trọng của logging, lợi ích của containerization.

**Giảng viên:** Ý nghĩa của đồ án?

**Sinh viên:** Đồ án giúp em áp dụng lý thuyết vào thực tế, rèn kỹ năng thiết kế hệ thống lớn, làm việc với multiple databases, và DevOps với Docker.

## Phần 7: Chi tiết về Code

**Giảng viên:** Hãy nói về cấu trúc code trong dự án.

**Sinh viên:** Code được tổ chức theo mô hình MVC nhẹ:
- **app/public/**: Frontend (HTML, CSS, JS)
- **app/routes/**: Backend routes xử lý API
- **app/common.php**: Helper functions, database connections
- **app/mongo_helper.php**: Xử lý MongoDB logging
- **db/**: Scripts SQL và init data

**Giảng viên:** Ví dụ về code PHP backend.

**Sinh viên:** Trong `app/routes/khoa.php`, hàm `determineSite` xác định site dựa trên MaKhoa:

```php
function determineSite($maKhoa) {
    if ($maKhoa < 'M') return 'Site_A';
    if ($maKhoa >= 'M' && $maKhoa < 'S') return 'Site_B';
    return 'Site_C';
}
```

Hàm `handleKhoa` xử lý CRUD, sử dụng partitioned view `Khoa_Global` và triggers để route data.

**Giảng viên:** Frontend được viết như thế nào?

**Sinh viên:** Sử dụng JavaScript ES6 Modules. Trong `app/public/js/app.js`, import các module:

```javascript
import { loadData, deleteRecord } from './modules/crud.js';
import { openCreateModal, submitForm } from './modules/modal.js';
```

Điều này giúp code modular, dễ maintain. UI sử dụng DOM manipulation thuần, không framework.

**Giảng viên:** Cách xử lý database connections?

**Sinh viên:** Trong `app/common.php`, hàm `getDBConnection()` tạo PDO connection đến Global DB:

```php
function getDBConnection() {
    $dsn = "sqlsrv:Server=localhost,14333;Database=HUFLIT";
    return new PDO($dsn, 'sa', 'YourStrong!Passw0rd');
}
```

Connections được reuse, và có error handling.

**Giảng viên:** Logging được implement ra sao?

**Sinh viên:** Sử dụng `app/mongo_helper.php` với class `RequestLogger`. Mỗi request được log vào MongoDB:

```php
class RequestLogger {
    public static function start($endpoint, $method) {
        // Insert log document
    }
    public static function end($records, $status) {
        // Update log with result
    }
}
```

Điều này giúp audit và statistics.

**Giảng viên:** Những kỹ thuật coding đặc biệt?

**Sinh viên:** 
- **Prepared Statements:** Ngăn SQL injection
- **Error Handling:** Try-catch blocks, custom exceptions
- **Modular JS:** ES6 imports/exports
- **Responsive CSS:** Media queries cho mobile
- **Chart.js Integration:** Cho dashboard thống kê

Cảm ơn thầy cô đã lắng nghe!