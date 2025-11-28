# 📚 Tổng quan Dự án Hệ thống Cơ sở dữ liệu Phân tán HUFLIT

> **Tác giả**: Sinh viên CNTT HUFLIT  
> **Ngày tạo**: 28/11/2025  
> **Mục đích**: Giải thích cấu trúc và chức năng của dự án cho sinh viên mới

---

## 🎯 Giới thiệu Dự án

Xin chào! Tôi là một sinh viên CNTT đang học môn Cơ sở dữ liệu phân tán. Dự án này là đồ án cuối kỳ của chúng ta, xây dựng một hệ thống quản lý sinh viên phân tán thực tế. Dự án sử dụng **Docker** để triển khai, với **3 máy chủ SQL Server** phân tán theo địa điểm và **MongoDB** để ghi nhật ký.

---

## 🏗️ Kiến trúc Tổng thể

### Mô hình Phân tán
Dự án sử dụng **horizontal partitioning** (phân mảnh ngang) theo mã khoa:
- **Site A**: Khoa có mã < 'M' (CNTT, DLKS, KTTC, ...)
- **Site B**: Khoa có mã từ 'M' đến 'R' (MMT, NNA, QTKD, ...)
- **Site C**: Khoa có mã ≥ 'S' (SPQT, TCNH, VHXH, ...)

### Các Thành phần Chính

#### 1. **Lớp Cơ sở dữ liệu (Database Layer)**
- **4 container SQL Server**: 1 Global + 3 Sites
- **1 container MongoDB**: Ghi nhật ký và thống kê
- **Linked Servers**: Kết nối giữa Global và các Sites
- **Partitioned Views**: Khung nhìn toàn cục UNION từ 3 sites
- **INSTEAD OF Triggers**: Tự động định tuyến dữ liệu đến site đúng

#### 2. **Lớp Ứng dụng (Application Layer)**
- **2 container PHP**: API server (port 8080) + Web UI (port 8081)
- **REST API**: CRUD operations cho tất cả entities
- **JavaScript ES6 Modules**: Frontend tương tác

#### 3. **Lớp Trình bày (Presentation Layer)**
- **HTML/CSS/JS**: Giao diện web responsive
- **Chart.js**: Biểu đồ thống kê thời gian thực
- **Toast notifications**: Thông báo tương tác

---

## 📁 Cấu trúc Thư mục

```
CDSLPT/
├── 📄 README.md              # Hướng dẫn sử dụng
├── 📄 ARCHITECTURE.md        # Tài liệu kiến trúc chi tiết
├── 📄 LICENSE.md             # Giấy phép MIT
├── 📄 docker-compose.yml     # Cấu hình Docker containers
├── 📄 init_databases.ps1     # Script khởi tạo database
├── 📁 app/                   # Source code PHP + Frontend
│   ├── 📄 common.php         # Database connection + utilities
│   ├── 📄 mongo_helper.php   # MongoDB operations
│   ├── 📄 request_logger.php # Theo dõi API requests
│   ├── 📁 public/            # Web root
│   │   ├── 📄 index.php      # API router
│   │   ├── 📄 router.php     # API routing logic
│   │   ├── 📄 ui.php         # Main UI page
│   │   ├── 📄 logs.php       # Audit logs viewer
│   │   ├── 📄 stats.php      # Statistics dashboard
│   │   ├── 📁 css/           # Stylesheets
│   │   └── 📁 js/            # JavaScript modules
│   └── 📁 routes/            # API handlers
└── 📁 db/                    # Database initialization
    ├── 📁 global/            # Global DB setup
    ├── 📁 site_a|b|c/        # Site-specific data
    └── 📁 mongodb/           # MongoDB init scripts
```

---

## 🔧 Chi tiết Mỗi Thành phần

### 1. **Docker Compose (docker-compose.yml)**

**Chức năng**: Định nghĩa và chạy tất cả containers
- **api_php**: Server API REST (port 8080)
- **app_php**: Server giao diện web (port 8081)
- **mssql_global**: Database toàn cục với linked servers (port 14333)
- **mssql_site_a|b|c**: 3 sites phân tán (ports 14334-14336)
- **mongodb**: Database logs (port 27017)

**Mạng**: Tất cả containers kết nối qua `huflit-network` bridge

### 2. **Script Khởi tạo (init_databases.ps1)**

**Chức năng**: Tự động setup databases theo thứ tự
1. Khởi tạo schema cho 3 sites
2. Seed dữ liệu mẫu (tùy chọn)
3. Tạo Global DB với linked servers
4. Cài đặt INSTEAD OF triggers

**Encoding**: Sử dụng UTF-8 để hỗ trợ tiếng Việt

### 3. **Core PHP Files**

#### **common.php**
```php
function getDBConnection()  // Kết nối PDO đến SQL Server Global
function sendResponse($data, $status)  // Trả về JSON response
function getJsonInput()  // Parse JSON từ request body
```

**Tác dụng**: Các hàm tiện ích dùng chung trong toàn bộ ứng dụng

#### **mongo_helper.php**
```php
class MongoHelper {
    static function logAudit()     // Ghi nhật ký thay đổi dữ liệu
    static function logQuery()     // Ghi lịch sử API requests
    static function getAuditLogs() // Lấy logs để hiển thị
    static function getStatistics() // Thống kê từ MongoDB
}
```

**Tác dụng**: Quản lý tất cả tương tác với MongoDB cho audit logging

#### **request_logger.php**
```php
class RequestLogger {
    static function start()  // Bắt đầu theo dõi request
    static function end()    // Kết thúc và log vào MongoDB
}
```

**Tác dụng**: Đo thời gian thực thi API và lưu vào MongoDB

### 4. **API Routes (app/routes/)**

#### **khoa.php** - Quản lý Khoa
- **GET**: Lấy danh sách khoa hoặc khoa cụ thể
- **POST**: Tạo khoa mới (trigger tự động route đến site đúng)
- **PUT**: Cập nhật thông tin khoa
- **DELETE**: Xóa khoa (cascade xóa sinh viên, CTDT, đăng ký)

#### **sinhvien.php** - Quản lý Sinh viên
- **GET**: Lấy danh sách SV theo khoa
- **POST**: Tạo SV mới
- **PUT**: Cập nhật SV (có thể chuyển site nếu đổi MaKhoa)
- **DELETE**: Xóa SV (cascade xóa đăng ký)

#### **monhoc.php** - Quản lý Môn học
- **Đồng bộ 3 sites**: INSERT/UPDATE/DELETE đồng thời trên A, B, C
- **Lý do**: Môn học cần cho CTDaoTao và DangKy ở mọi site

#### **ctdaotao.php** - Chương trình đào tạo
- **Khóa chính composite**: (MaKhoa, KhoaHoc, MaMH)
- **FK validation**: MaKhoa phải tồn tại, MaMH phải có

#### **dangky.php** - Đăng ký môn học
- **Khóa chính composite**: (MaSV, MaMon)
- **FK phân tán**: MaSV từ site nào đó, MaMon từ bảng đồng bộ

#### **global.php** - Truy vấn phức tạp
- **4 loại query**:
  1. Môn học SV đã đạt (≥5 điểm)
  2. Khóa học của khoa
  3. Môn bắt buộc của SV
  4. SV đủ điều kiện tốt nghiệp

### 5. **Frontend JavaScript (app/public/js/)**

#### **app.js** - Điểm khởi đầu
- Import tất cả modules
- Khởi tạo ứng dụng
- Expose functions cho HTML onclick

#### **modules/crud.js** - Thao tác CRUD
```javascript
loadData(module)        // Tải dữ liệu từ API
deleteRecord(id)        // Xóa bản ghi
createRecord(data)      // Tạo mới
updateRecord(id, data)  // Cập nhật
```

#### **modules/modal.js** - Modal động
- Tạo form tự động từ config
- Validation client-side
- Submit qua API

#### **modules/view.js** - Hiển thị dữ liệu
- Render bảng HTML từ JSON
- Phân trang
- Tìm kiếm

#### **modules/global-query.js** - Query phức tạp
- Gọi API global với tham số
- Hiển thị kết quả đặc biệt

#### **utils/api.js** - API wrapper
```javascript
apiCall(endpoint, method, data)  // Gọi API với error handling
showToast(message, type)         // Thông báo toast
```

#### **utils/dom.js** - DOM helpers
- Query selectors
- Event listeners
- DOM manipulation

#### **utils/validation.js** - Validation
- Client-side validation
- Format checking
- Required fields

### 6. **CSS Architecture (app/public/css/)**

#### **base.css** - Reset & Typography
- CSS reset
- Font families
- Color variables

#### **layout.css** - Grid System
- Flexbox layouts
- Container classes
- Responsive breakpoints

#### **components.css** - UI Components
- Buttons, forms, modals
- Tables, alerts
- Navigation

#### **responsive.css** - Mobile Support
- Media queries
- Mobile-first approach
- Touch-friendly interfaces

### 7. **Database Schema**

#### **Global Views** (Partitioned)
```sql
Khoa_Global     = UNION Khoa từ Site A, B, C
SinhVien_Global = UNION SinhVien từ Site A, B, C
MonHoc_Global   = UNION MonHoc từ Site A, B, C (đồng bộ)
CTDaoTao_Global = UNION CTDaoTao từ Site A, B, C
DangKy_Global   = UNION DangKy từ Site A, B, C
```

#### **INSTEAD OF Triggers**
- **15 triggers**: 5 bảng × 3 operations (INSERT, UPDATE, DELETE)
- **Logic routing**: Dựa trên MaKhoa để xác định site
- **Cross-site moves**: UPDATE SinhVien có thể chuyển site

#### **MongoDB Collections**
```javascript
audit_logs: {
  table, operation, data, old_data,
  timestamp, site, ip_address, user_agent
}

query_history: {
  endpoint, method, params, body,
  execution_time_ms, result_count, status_code,
  timestamp, ip_address
}
```

---

## 🔄 Luồng Dữ liệu

### 1. **CREATE Flow (INSERT)**
1. User → UI → API POST request
2. PHP route handler → INSERT vào Global view
3. Trigger INSTEAD OF → Xác định site → INSERT vào site đúng
4. MongoDB log audit
5. Response về UI

### 2. **UPDATE Cross-site**
1. User đổi MaKhoa của SinhVien
2. Trigger phát hiện thay đổi site
3. DELETE từ site cũ
4. INSERT vào site mới
5. CASCADE DangKy records

### 3. **SYNC MonHoc**
1. INSERT MonHoc_Global
2. Trigger lặp qua 3 sites
3. INSERT đồng thời vào Site A, B, C
4. Rollback nếu bất kỳ site nào fail

### 4. **Global Complex Query**
1. JOIN qua partitioned views
2. SQL Server optimizer xử lý distributed execution
3. Kết quả aggregate từ 3 sites

---

## 🎨 Giao diện Người dùng

### **ui.php** - Trang chính
- Sidebar navigation theo module
- Tab system cho từng entity
- Modal forms cho CRUD
- Real-time data tables

### **logs.php** - Nhật ký kiểm tra
- Lọc theo bảng, thao tác, thời gian
- Phân trang 50 records/page
- Tìm kiếm theo mã bản ghi
- Export CSV

### **stats.php** - Thống kê
- Chart.js cho metrics API
- Thống kê theo ngày/tháng
- Top slow queries
- Error rates

### **Tính năng Nâng cao**
- **Auto-refresh**: Tự động cập nhật dữ liệu
- **Settings panel**: Cài đặt theme, columns
- **Responsive**: Hoạt động trên mọi thiết bị
- **Toast notifications**: Feedback tức thời

---

## 🚀 Cách Chạy Dự án

### Yêu cầu
- Docker Desktop 4.0+
- PowerShell 7.0+
- RAM 4GB+ (8GB khuyến nghị)

### Các bước
```powershell
# 1. Khởi động containers
docker-compose up -d

# 2. Khởi tạo databases
.\init_databases.ps1

# 3. Truy cập
# UI: http://localhost:8081/ui.php
# API: http://localhost:8080
# Logs: http://localhost:8081/logs.php
# Stats: http://localhost:8081/stats.php
```

---

## 📊 Thống kê Dự án

- **~2000 dòng code** PHP + JavaScript
- **15 INSTEAD OF triggers** SQL Server
- **4 containers** Docker
- **5 entities** chính (Khoa, MonHoc, SinhVien, CTDaoTao, DangKy)
- **REST API** với 20+ endpoints
- **MongoDB** cho audit logging
- **Responsive UI** với Chart.js

---

## 🎓 Bài học Từ Dự án

### Kiến thức Cơ sở dữ liệu
- **Distributed databases**: Partitioning, replication
- **SQL Server**: Linked servers, triggers, views
- **MongoDB**: Document storage, aggregation
- **Transactions**: ACID properties trong distributed system

### Kiến thức Lập trình
- **PHP**: PDO, REST API, error handling
- **JavaScript ES6**: Modules, async/await, DOM manipulation
- **Docker**: Containerization, networking, volumes
- **PowerShell**: Scripting, process management

### Kiến thức Hệ thống
- **Microservices architecture**: Separated concerns
- **API design**: RESTful principles
- **Logging & monitoring**: Audit trails, performance metrics
- **Security**: Input validation, SQL injection prevention

---

## 🔮 Phát triển Tương lai

### Short-term (v1.1)
- User authentication (JWT)
- Input sanitization (XSS prevention)
- Caching layer (Redis)

### Medium-term (v2.0)
- Role-based access control
- Versioning cho audit logs
- WebSocket cho real-time updates

### Long-term (v3.0)
- Microservices architecture
- Event sourcing (Kafka)
- GraphQL API
- Machine learning analytics

---

## 📞 Liên hệ

Nếu bạn là sinh viên mới và có câu hỏi về dự án này, hãy:
1. Đọc kỹ `README.md` và `ARCHITECTURE.md`
2. Chạy thử các containers
3. Kiểm tra logs nếu gặp lỗi
4. Tham khảo code trong `app/routes/` để hiểu API

**Chúc bạn học tập hiệu quả! 🎓**

---

*Đồ án môn Cơ sở dữ liệu phân tán - HUFLIT 2025*</content>
<parameter name="filePath">C:\Users\ADMIN\Desktop\CDSLPT\OVERVIEW.md