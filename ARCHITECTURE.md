# 🏗️ Kiến trúc hệ thống - HUFLIT Distributed Database

> Tài liệu chi tiết về cấu trúc và cơ chế hoạt động của hệ thống CSDL phân tán

---

## 📋 Mục lục

- [📊 Tổng quan kiến trúc](#-tổng-quan-kiến-trúc)
  - [Mô hình phân tán](#mô-hình-phân-tán)
- [🗄️ Database Layer - Lớp dữ liệu phân tán](#️-database-layer---lớp-dữ-liệu-phân-tán)
  - [SQL Server Cluster (4 containers)](#sql-server-cluster-4-containers)
  - [MongoDB (Port 27017)](#mongodb-port-27017)
- [🔧 Application Layer - Lớp ứng dụng](#-application-layer---lớp-ứng-dụng)
  - [PHP Backend (2 containers)](#php-backend-2-containers)
  - [Core PHP Files](#core-php-files)
  - [Route Handlers](#route-handlers)
  - [Frontend (JavaScript ES6 Modules)](#frontend-javascript-es6-modules)
- [🔄 Data Flow - Luồng dữ liệu](#-data-flow---luồng-dữ-liệu)
  - [CREATE Flow (INSERT)](#create-flow-insert)
  - [UPDATE Flow (cross-site move)](#update-flow-cross-site-move)
  - [SYNC Flow (MonHoc)](#sync-flow-monhoc)
  - [QUERY Flow (Global complex query)](#query-flow-global-complex-query)
- [🔐 Quyết định thiết kế chính](#-quyết-định-thiết-kế-chính)
- [🔐 Security Considerations - Cân nhắc bảo mật](#-security-considerations---cân-nhắc-bảo-mật)
- [📈 Performance Considerations - Cân nhắc hiệu suất](#-performance-considerations---cân-nhắc-hiệu-suất)
- [🧪 Chiến lược kiểm thử](#-chiến-lược-kiểm-thử)
- [📚 Tài liệu tham khảo](#-tài-liệu-tham-khảo)
- [🎯 Tính năng đã hoàn thành và nâng cấp tương lai](#-tính-năng-đã-hoàn-thành-và-nâng-cấp-tương-lai)

---

## 📊 Tổng quan kiến trúc

### Mô hình phân tán

```mermaid
graph TD
    subgraph "Lớp Khách hàng"
        Browser[Trình duyệt<br/>UI.php]
        Postman[Postman<br/>REST API]
        Mobile[Ứng dụng di động<br/>Tương lai]
    end

    subgraph "Lớp Ứng dụng"
        subgraph "Ứng dụng PHP (Docker)"
            API[Máy chủ API<br/>Cổng 8080]
            UI[Máy chủ giao diện web<br/>Cổng 8081]
            Router[Bộ định tuyến<br/>index.php]
            Routes[Các tuyến<br/>Trình xử lý]
            Logger[Ghi nhật ký yêu cầu<br/>Quản lý]
        end
    end

    subgraph "Database Layer"
        subgraph "SQL Server Cluster"
            Global[GLOBAL<br/>HUFLIT<br/>Linked Servers<br/>Partitioned Views<br/>INSTEAD OF Triggers]
            subgraph "Routing Logic (by MaKhoa)"
                SiteA[SITE A<br/>A-L]
                SiteB[SITE B<br/>M-R]
                SiteC[SITE C<br/>S-Z]
            end
        end
        Mongo[MongoDB<br/>Logs<br/>audit_logs<br/>query_history]
    end

    Browser --> API
    Postman --> API
    Mobile --> API
    API --> Router
    UI --> Router
    Router --> Routes
    Routes --> Logger
    Routes --> Global
    Global --> SiteA
    Global --> SiteB
    Global --> SiteC
    Routes --> Mongo
    Logger --> Mongo
```

---

## 📊 Tổng quan kiến trúc

### Mô hình phân tán

```mermaid
graph TD
    subgraph "Lớp Khách hàng"
        Browser[Trình duyệt<br/>UI.php]
        Postman[Postman<br/>REST API]
        Mobile[Ứng dụng di động<br/>Tương lai]
    end

    subgraph "Lớp Ứng dụng"
        subgraph "Ứng dụng PHP (Docker)"
            API[Máy chủ API<br/>Cổng 8080]
            UI[Máy chủ giao diện web<br/>Cổng 8081]
            Router[Bộ định tuyến<br/>index.php]
            Routes[Các tuyến<br/>Trình xử lý]
            Logger[Ghi nhật ký yêu cầu<br/>Quản lý]
        end
    end

    subgraph "Database Layer"
        subgraph "SQL Server Cluster"
            Global[GLOBAL<br/>HUFLIT<br/>Linked Servers<br/>Partitioned Views<br/>INSTEAD OF Triggers]
            subgraph "Routing Logic (by MaKhoa)"
                SiteA[SITE A<br/>A-L]
                SiteB[SITE B<br/>M-R]
                SiteC[SITE C<br/>S-Z]
            end
        end
        Mongo[MongoDB<br/>Logs<br/>audit_logs<br/>query_history]
    end

    Browser --> API
    Postman --> API
    Mobile --> API
    API --> Router
    UI --> Router
    Router --> Routes
    Routes --> Logger
    Routes --> Global
    Global --> SiteA
    Global --> SiteB
    Global --> SiteC
    Routes --> Mongo
    Logger --> Mongo
```

---

## 🗄️ Database Layer - Lớp dữ liệu phân tán

### 1. SQL Server Cluster (4 containers)

#### Global Database - HUFLIT (Port 14333)

**Vai trò**: Điểm truy cập thống nhất, quản lý phân mảnh

**Thành phần chính**:

##### a) Linked Servers

```sql
SITE_A -> mssql_site_a:1433
SITE_B -> mssql_site_b:1433
SITE_C -> mssql_site_c:1433
```

- Kết nối trực tiếp đến 3 sites
- Cho phép truy vấn cross-database với cú pháp: `[SITE_A].SiteA.dbo.TableName`
- Xác thực: tài khoản sa với mật khẩu từ biến môi trường

##### b) Partitioned Views

```sql
CREATE VIEW Khoa_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.Khoa
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.Khoa
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.Khoa;

CREATE VIEW MonHoc_Global AS
SELECT DISTINCT * FROM (
    SELECT * FROM [SITE_A].SiteA.dbo.MonHoc
    UNION ALL
    SELECT * FROM [SITE_B].SiteB.dbo.MonHoc
    UNION ALL
    SELECT * FROM [SITE_C].SiteC.dbo.MonHoc
) AS AllMonHoc;

CREATE VIEW CTDaoTao_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.CTDaoTao
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.CTDaoTao
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.CTDaoTao;

CREATE VIEW SinhVien_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.SinhVien
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.SinhVien
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.SinhVien;

CREATE VIEW DangKy_Global AS
SELECT * FROM [SITE_A].SiteA.dbo.DangKy
UNION ALL
SELECT * FROM [SITE_B].SiteB.dbo.DangKy
UNION ALL
SELECT * FROM [SITE_C].SiteC.dbo.DangKy;
```

**Đặc điểm**:
- Chỉ đọc theo mặc định (UNION ALL)
- Cho phép truy vấn như bảng thường
- Không thể INSERT/UPDATE/DELETE trực tiếp → Cần triggers

##### c) INSTEAD OF Triggers

**Nhiệm vụ**: Chặn thao tác trên Partition Views, định tuyến đến site đúng

**Logic phân mảnh theo MaKhoa**:

```sql
IF MaKhoa < 'M'        -> SITE_A (A-L)
IF MaKhoa >= 'M' AND < 'S' -> SITE_B (M-R)
IF MaKhoa >= 'S'       -> SITE_C (S-Z)
```

**15 triggers** (5 bảng × 3 thao tác):
- Khoa_Global: INSERT, UPDATE, DELETE
- MonHoc_Global: INSERT, UPDATE, DELETE (đồng bộ 3 sites)
- SinhVien_Global: INSERT, UPDATE, DELETE (cho phép di chuyển cross-site)
- CTDaoTao_Global: INSERT, UPDATE, DELETE
- DangKy_Global: INSERT, UPDATE, DELETE

#### Site Databases (Ports 14334-14336)

**Site A (Cổng 14334)** - Cơ sở dữ liệu: SiteA
- **Phân mảnh**: MaKhoa < 'M' (A, B, C, ..., L)
- **Ràng buộc kiểm tra**: `CHECK (MaKhoa < 'M')`

**Site B (Cổng 14335)** - Cơ sở dữ liệu: SiteB
- **Phân mảnh**: MaKhoa >= 'M' AND < 'S' (M, N, O, P, Q, R)
- **Ràng buộc kiểm tra**: `CHECK (MaKhoa >= 'M' AND MaKhoa < 'S')`

**Site C (Cổng 14336)** - Cơ sở dữ liệu: SiteC
- **Phân mảnh**: MaKhoa >= 'S' (S, T, U, ..., Z)
- **Ràng buộc kiểm tra**: `CHECK (MaKhoa >= 'S')`

**Schema mỗi site** (giống hệt nhau):

```sql
-- 1. Khoa (FK root)
CREATE TABLE Khoa (
  MaKhoa NVARCHAR(10) PRIMARY KEY,
  TenKhoa NVARCHAR(100) NOT NULL,
  CHECK (MaKhoa < 'M') -- Site A
  -- CHECK (MaKhoa >= 'M' AND MaKhoa < 'S') -- Site B
  -- CHECK (MaKhoa >= 'S') -- Site C
);

-- 2. MonHoc (độc lập, sao chép trên các sites)
CREATE TABLE MonHoc (
  MaMH NVARCHAR(10) PRIMARY KEY,
  TenMH NVARCHAR(100) NOT NULL
);

-- 3. SinhVien (liên kết với Khoa)
CREATE TABLE SinhVien (
  MaSV NVARCHAR(10) PRIMARY KEY,
  HoTen NVARCHAR(100) NOT NULL,
  MaKhoa NVARCHAR(10) FOREIGN KEY REFERENCES Khoa(MaKhoa),
  KhoaHoc INT NOT NULL,
  CHECK (MaKhoa < 'M') -- Same as Khoa
);

-- 4. CTDaoTao (chương trình đào tạo)
CREATE TABLE CTDaoTao (
  MaKhoa NVARCHAR(10) FOREIGN KEY REFERENCES Khoa(MaKhoa),
  KhoaHoc INT NOT NULL,
  MaMH NVARCHAR(10) FOREIGN KEY REFERENCES MonHoc(MaMH),
  PRIMARY KEY (MaKhoa, KhoaHoc, MaMH),
  CHECK (MaKhoa < 'M') -- Same as Khoa
);

-- 5. DangKy (đăng ký môn học)
CREATE TABLE DangKy (
  MaSV NVARCHAR(10) FOREIGN KEY REFERENCES SinhVien(MaSV),
  MaMon NVARCHAR(10) FOREIGN KEY REFERENCES MonHoc(MaMH),
  DiemThi DECIMAL(4,2) NULL,
  PRIMARY KEY (MaSV, MaMon)
);
```

**Quan hệ FK**:

```mermaid
erDiagram
    Khoa ||--o{ SinhVien : "1:N"
    Khoa ||--o{ CTDaoTao : "1:N"
    SinhVien ||--o{ DangKy : "1:N"
    CTDaoTao ||--|| MonHoc : "N:1"
    DangKy ||--|| MonHoc : "N:1"
```

### 2. MongoDB (Port 27017)

**Cơ sở dữ liệu**: `huflit_logs`

**Vai trò**: Audit logging & analytics

#### Collection 1: `audit_logs`

**Mục đích**: Ghi lại mọi thay đổi dữ liệu

**Schema**:

```javascript
{
  table: "Khoa|MonHoc|SinhVien|CTDaoTao|DangKy",
  operation: "INSERT|UPDATE|DELETE",
  data: {...},           // New data
  old_data: {...},       // Old data (for UPDATE/DELETE)
  timestamp: ISODate,
  site: "Site_A|Site_B|Site_C|Global",
  ip_address: "x.x.x.x",
  user_agent: "..."
}
```

**Indexes**:
- `{timestamp: -1}` - Sắp xếp thời gian
- `{table: 1, timestamp: -1}` - Lọc theo bảng
- `{operation: 1, timestamp: -1}` - Lọc theo thao tác
- `{site: 1, timestamp: -1}` - Lọc theo site

**Trường hợp sử dụng**:
- Xem lịch sử thay đổi của 1 bản ghi
- Dấu vết kiểm tra cho tuân thủ
- Khôi phục dữ liệu (thủ công)
- Phân tích về các thao tác

#### Collection 2: `query_history`

**Mục đích**: Ghi lại mọi yêu cầu API

**Schema**:

```javascript
{
  endpoint: "/khoa|/sinhvien|...",
  method: "GET|POST|PUT|DELETE",
  params: {...},          // Tham số truy vấn
  body: {...},            // Nội dung yêu cầu
  execution_time_ms: 123,
  result_count: 10,
  status_code: 200,
  timestamp: ISODate,
  ip_address: "x.x.x.x"
}
```

**Indexes**:
- `{timestamp: -1}`
- `{endpoint: 1, timestamp: -1}`
- `{method: 1, timestamp: -1}`

**Trường hợp sử dụng**:
- Giám sát hiệu suất
- Phát hiện truy vấn chậm
- Phân tích sử dụng
- Theo dõi tỷ lệ lỗi

---

## 🔧 Application Layer - Lớp ứng dụng

### 1. PHP Backend (2 containers)

#### Container 1: API Server (Port 8080)

**Entry point**: `app/public/index.php` (via router.php)

**Công nghệ sử dụng**:
- PHP 8.4-cli
- Extensions: sqlsrv, pdo_sqlsrv, mongodb
- Server: Built-in PHP server

**Luồng yêu cầu**:

```
HTTP Request
  ↓
index.php (router)
  ↓
RequestLogger::start() → Track start time
  ↓
Route mapping (/khoa -> routes/khoa.php)
  ↓
handleKhoa($method, $query)
  ↓
├─ GET    → SELECT from *_Global view
├─ POST   → INSERT into *_Global view (trigger routes)
├─ PUT    → UPDATE *_Global view (trigger routes)
└─ DELETE → DELETE from *_Global view (trigger routes)
  ↓
MongoHelper::logAudit() → Log to MongoDB
  ↓
RequestLogger::end($count, $status) → Log query stats
  ↓
sendResponse($data, $code) → Phản hồi JSON
```

#### Container 2: Web UI Server (Port 8081)

**Entry point**: `app/public/ui.php`, `logs.php`, `stats.php`

**Phục vụ**:
- HTML pages
- CSS (styles.css)
- JavaScript modules (ES6)
- Static assets

### 2. Core PHP Files

#### `common.php` - Database & utilities

```php
getDBConnection()  // PDO connection to HUFLIT (Global)
  → sqlsrv:Server=mssql_global,1433;Database=HUFLIT
  → Options: TrustServerCertificate=1, ERRMODE_EXCEPTION

sendResponse($data, $status)  // Đầu ra JSON + thoát
getJsonInput()                 // Phân tích nội dung yêu cầu
```

#### `mongo_helper.php` - Thao tác MongoDB

```php
MongoHelper::getClient()
  → Kiểm tra extension_loaded('mongodb')
  → Kết nối đến mongodb://admin:pass@mongodb:27017
  → Trả về instance MongoDB\Driver\Manager

MongoHelper::logAudit($table, $op, $data, $old, $site)
  → Insert to huflit_logs.audit_logs

MongoHelper::logQuery($endpoint, $method, ...)
  → Insert to huflit_logs.query_history

MongoHelper::getAuditLogs($filter, $limit, $skip)
MongoHelper::getQueryHistory(...)
MongoHelper::getStatistics($collection, $pipeline)
```

#### `request_logger.php` - Theo dõi yêu cầu

```php
RequestLogger::start()
  → Capture: startTime, endpoint, method, params, body

RequestLogger::end($resultCount, $statusCode)
  → Tính thời gian thực thi
  → Gọi MongoHelper::logQuery()
```

### 3. Route Handlers (`app/routes/*.php`)

#### Mẫu chung:

```php
function handle{Module}($method, $query) {
  try {
    $pdo = getDBConnection();
    switch ($method) {
      case 'GET':    // Query *_Global view
      case 'POST':   // INSERT into *_Global (trigger auto-routes)
      case 'PUT':    // UPDATE *_Global (trigger handles site routing)
      case 'DELETE': // DELETE from *_Global (trigger cascades)
    }
  } catch (Exception $e) {
    sendResponse(['error' => $e->getMessage()], 500);
  }
}
```

#### Đặc điểm từng module:

**`khoa.php`**:
- CRUD đơn giản
- Site được xác định bởi chữ cái đầu của `MaKhoa`
- Gốc FK: Xóa Khoa → cascade SinhVien, CTDaoTao

**`monhoc.php`**:
- **Đồng bộ 3 sites**: INSERT/UPDATE/DELETE đồng thời Site A, B, C
- Trigger sử dụng con trỏ để lặp qua 3 sites
- Không có cột site (dữ liệu sao chép)

**`sinhvien.php`**:
- **Di chuyển cross-site**: UPDATE cho phép đổi MaKhoa
- Trigger xử lý DELETE từ site cũ + INSERT vào site mới
- Cascade: Xóa sinh viên → xóa DangKy

**`ctdaotao.php`**:
- Khóa chính composite: (MaKhoa, KhoaHoc, MaMH)
- Xác thực FK: MaKhoa phải tồn tại, MaMH phải tồn tại
- Không UPDATE (xóa + tạo lại)

**`dangky.php`**:
- Khóa chính composite: (MaSV, MaMon)
- FK phân tán: MaSV phải tồn tại (từ 1 trong 3 sites)
- UPDATE chỉ DiemThi (không đổi MaSV, MaMon)

**`global.php`**:
- Truy vấn phức tạp sử dụng JOIN trên các khung nhìn
- 4 loại truy vấn:
  1. Môn học SV đã đạt (≥5 điểm)
  2. Khóa học của khoa
  3. Môn bắt buộc của SV (theo CTĐT)
  4. SV đủ điều kiện tốt nghiệp

**`logs.php` & `stats.php`**:
- **Nhật ký kiểm tra (logs.php)**: Hiển thị lịch sử thao tác từ MongoDB
  - Lọc theo bảng, thao tác, thời gian
  - Phân trang với 50 bản ghi/trang
  - Tìm kiếm theo mã bản ghi
  - Xuất CSV cho báo cáo
- **Thống kê (stats.php)**: Phân tích dữ liệu thời gian thực
  - Biểu đồ Chart.js cho metrics API
  - Thống kê theo ngày/tháng
  - Top queries chậm, lỗi thường gặp
  - Xuất biểu đồ PNG

### 4. Frontend (JavaScript ES6 Modules)

#### Cấu trúc thư mục

```
js/
├── app.js              # Điểm khởi đầu, khởi tạo ứng dụng
├── config.js           # Cấu hình hằng số, định nghĩa trường dữ liệu
├── modules/
│   ├── crud.js         # Các thao tác CRUD (tạo, đọc, cập nhật, xóa)
│   ├── modal.js        # Modal động cho form nhập liệu
│   ├── view.js         # Hiển thị bảng dữ liệu
│   ├── settings.js     # Cài đặt (chế độ tối, cột hiển thị)
│   └── global-query.js # Truy vấn phức tạp toàn cục
└── utils/
    ├── api.js          # Gói gọn các lệnh gọi API
    ├── dom.js          # Trợ giúp DOM, thông báo
    └── validation.js   # Xác thực dữ liệu form
```

#### Tính năng chính

**`config.js`**:
- `API_BASE = 'http://localhost:8080'` - Địa chỉ API backend
- `FIELDS_CONFIG`: Định nghĩa các trường form cho từng module
- `PRIMARY_KEYS`: Xác định khóa chính để chỉnh sửa/xóa

**`crud.js`** - Quản lý dữ liệu:

```javascript
loadData(module)        // Tải dữ liệu từ API GET /{module}
deleteRecord(id)        // Xóa bản ghi DELETE /{module}?id={id}
createRecord(data)      // Tạo mới POST /{module}
updateRecord(id, data)  // Cập nhật PUT /{module}?id={id}
```

**`modal.js`** - Form động:
- Tạo form tự động từ `FIELDS_CONFIG`
- Hộp chọn (select) tải dữ liệu từ API (quan hệ khóa ngoại)
- Xác thực: bắt buộc, độ dài tối thiểu/tối đa

**`view.js`** - Hiển thị dữ liệu:
- Render bảng HTML từ dữ liệu JSON
- Cột động dựa trên khóa dữ liệu
- Chuyển đổi hiển thị cột site
- Nút hành động: Chỉnh sửa, Xóa

**`settings.js`** - Cài đặt người dùng:
- Chuyển đổi hiển thị cột site (lưu localStorage)
- Giao diện truy vấn toàn cục
- **Mới**: Chế độ tối/sáng, cài đặt auto-refresh

#### Tính năng giao diện người dùng mới

**🎨 Giao diện responsive**:
- Thiết kế tương thích mọi thiết bị (điện thoại, máy tính bảng, máy tính)
- Sử dụng CSS Grid và Flexbox cho bố cục linh hoạt

**📊 Biểu đồ tương tác**:
- Thư viện Chart.js để hiển thị dữ liệu thời gian thực
- Biểu đồ đường, cột, tròn cho thống kê
- Cập nhật tự động mỗi 30 giây

**🔄 Tự động làm mới (Auto-refresh)**:
- Nút bật/tắt tự động làm mới dữ liệu
- Khoảng thời gian có thể cấu hình (10s - 5min)
- Chỉ hoạt động khi tab đang active

**⚙️ Bảng cài đặt**:
- Modal cài đặt với nhiều tùy chọn
- Lưu trữ cài đặt trong localStorage
- Chế độ tối/sáng với CSS variables

**📱 Trải nghiệm người dùng**:
- Loading indicators khi tải dữ liệu
- Thông báo toast cho hành động thành công/thất bại
- Xác thực form real-time
- Phím tắt bàn phím (Enter để lưu, Escape để hủy)

#### CSS kiến trúc module

```
css/
├── base.css       # Reset, typography, colors
├── layout.css     # Grid system, containers
├── components.css # Buttons, forms, modals, tables
└── responsive.css # Media queries cho mobile
```

- Sử dụng CSS custom properties (variables) cho theme
- BEM methodology cho class naming
- Dark mode với data attribute

## 🔄 Data Flow - Luồng dữ liệu

### 1. CREATE Flow (INSERT)

**Ví dụ**: Tạo Sinh Viên mới với MaKhoa = 'CNTT'

```mermaid
sequenceDiagram
    participant Browser
    participant API
    participant SQL
    participant Trigger
    participant SiteA
    participant MongoDB

    Browser->>API: POST /sinhvien<br/>body: {MaSV: "25DH001", HoTen: "Nguyen Van A", MaKhoa: "CNTT", KhoaHoc: 2025}
    API->>SQL: INSERT INTO SinhVien_Global<br/>VALUES ('25DH001', 'Nguyen Van A', 'CNTT', 2025)
    SQL->>Trigger: TR_SinhVien_Global_Insert
    Trigger->>Trigger: Xác thực: Kiểm tra MaSV duy nhất & MaKhoa tồn tại
    Trigger->>SiteA: INSERT INTO [SITE_A].SiteA.dbo.SinhVien
    SiteA-->>Trigger: Insert thành công
    Trigger-->>SQL: Thành công
    SQL-->>API: Thành công
    API->>MongoDB: MongoHelper::logAudit('SinhVien', 'INSERT', {...}, null, 'Site_A')
    API->>MongoDB: RequestLogger::end(1, 201)
    API->>Browser: sendResponse({message: 'SinhVien created successfully'}, 201)
    Browser->>Browser: Hiển thị cảnh báo, tải lại bảng
```

### 2. UPDATE Flow (cross-site move)

**Ví dụ**: Chuyển SV từ CNTT (Site A) sang MMT (Site B)

```mermaid
sequenceDiagram
    participant Browser
    participant API
    participant SQL
    participant Trigger
    participant SiteA
    participant SiteB
    participant MongoDB

    Browser->>API: PUT /sinhvien?id=25DH001<br/>body: {HoTen: "Nguyen Van A", MaKhoa: "MMT", KhoaHoc: 2025}
    API->>SQL: SELECT * FROM SinhVien_Global WHERE MaSV = '25DH001'<br/>→ old: {MaSV: "25DH001", HoTen: "...", MaKhoa: "CNTT", KhoaHoc: 2025}
    API->>SQL: UPDATE SinhVien_Global SET HoTen = '...', MaKhoa = 'MMT', KhoaHoc = 2025 WHERE MaSV = '25DH001'
    SQL->>Trigger: TR_SinhVien_Global_Update
    Trigger->>Trigger: Phát hiện thay đổi MaKhoa: 'CNTT' → 'MMT'
    Trigger->>Trigger: Site cũ: 'CNTT' < 'M' → SITE_A<br/>Site mới: 'MMT' >= 'M' AND < 'S' → SITE_B
    Trigger->>Trigger: Bắt đầu giao dịch
    Trigger->>SiteA: DELETE FROM [SITE_A].SiteA.dbo.SinhVien WHERE MaSV = '25DH001'
    Trigger->>SiteB: INSERT INTO [SITE_B].SiteB.dbo.SinhVien VALUES (...)
    Trigger->>Trigger: CASCADE: Bản ghi DangKy theo sau (xóa từ A, chèn vào B)
    Trigger->>Trigger: Cam kết giao dịch
    Trigger-->>SQL: Thành công
    SQL-->>API: Thành công
    API->>MongoDB: MongoHelper::logAudit('SinhVien', 'UPDATE', newData, oldData, 'Site_B')
    API->>Browser: Thành công, bảng được làm mới
```

### 3. SYNC Flow (MonHoc)

**Ví dụ**: Tạo môn học mới → phải có ở cả 3 sites

```mermaid
sequenceDiagram
    participant Browser
    participant SQL
    participant Trigger
    participant SiteA
    participant SiteB
    participant SiteC
    participant MongoDB

    Browser->>SQL: POST /monhoc<br/>body: {MaMH: "MH999", TenMH: "Trí tuệ nhân tạo"}
    SQL->>Trigger: INSERT INTO MonHoc_Global VALUES ('MH999', '...')
    Trigger->>Trigger: TR_MonHoc_Global_Insert<br/>Xác thực: Kiểm tra MaMH duy nhất trên 3 sites
    Trigger->>SiteA: INSERT INTO [SITE_A].SiteA.dbo.MonHoc VALUES ('MH999', '...')
    Trigger->>SiteB: INSERT INTO [SITE_B].SiteB.dbo.MonHoc VALUES ('MH999', '...')
    Trigger->>SiteC: INSERT INTO [SITE_C].SiteC.dbo.MonHoc VALUES ('MH999', '...')
    SiteA-->>Trigger: Thành công
    SiteB-->>Trigger: Thành công
    SiteC-->>Trigger: Thành công
    Trigger-->>SQL: Thành công (Nếu 1 site fail → rollback tất cả)
    SQL-->>Browser: Thành công: "MonHoc created successfully on all sites"
    Browser->>MongoDB: Ghi nhật ký kiểm tra với site='Global'
```

### 4. QUERY Flow (Global complex query)

**Ví dụ**: Tìm SV đủ điều kiện tốt nghiệp (query type 4)

```mermaid
sequenceDiagram
    participant Browser
    participant API
    participant SQL
    participant GlobalDB
    participant MongoDB

    Browser->>API: GET /global?type=4
    API->>SQL: Complex query - Logic: Số môn bắt buộc = Số môn đã hoàn thành<br/>SELECT s.MaSV, s.HoTen FROM SinhVien_Global s WHERE (...subqueries...)
    SQL->>GlobalDB: Execute query using partitioned views
    GlobalDB->>GlobalDB: SinhVien_Global → UNION Site A, B, C<br/>CTDaoTao_Global → UNION Site A, B, C<br/>DangKy_Global → UNION Site A, B, C<br/>(SQL Server optimizer handles distributed execution)
    GlobalDB-->>SQL: Results
    SQL-->>API: Results
    API->>MongoDB: Log query to query_history (execution_time, result_count)
    API->>Browser: sendResponse([{MaSV: "...", HoTen: "..."}], 200)
    Browser->>Browser: Render results in table
```
---

## 📚 Tài liệu tham khảo

### SQL Server
- [Tài liệu Linked Servers](https://learn.microsoft.com/en-us/sql/relational-databases/linked-servers/)
- [Partitioned Views](https://learn.microsoft.com/en-us/sql/t-sql/statements/create-view-transact-sql)
- [INSTEAD OF Triggers](https://learn.microsoft.com/en-us/sql/t-sql/statements/create-trigger-transact-sql)

### MongoDB
- [PHP MongoDB Driver](https://www.php.net/manual/en/set.mongodb.php)
- [Aggregation Pipeline](https://www.mongodb.com/docs/manual/aggregation/)

### Docker
- [Docker Compose Networking](https://docs.docker.com/compose/networking/)

---

**📝 Tài liệu cập nhật**: December 2024  
**✍️ Tác giả**: HUFLIT Distributed Database Team
