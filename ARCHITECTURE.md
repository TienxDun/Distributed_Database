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
- [🔐 Key Design Decisions](#-key-design-decisions)
- [📈 Performance Considerations](#-performance-considerations)
- [🧪 Testing Strategy](#-testing-strategy)
- [🚀 Deployment](#-deployment)
- [📚 References](#-references)
- [🎯 Future Enhancements](#-future-enhancements)

---

## 📊 Tổng quan kiến trúc

### Mô hình phân tán

```mermaid
graph TD
    subgraph "Client Layer"
        Browser[Browser<br/>UI.php]
        Postman[Postman<br/>REST API]
        Mobile[Mobile App<br/>Future]
    end

    subgraph "Application Layer"
        subgraph "PHP Application (Docker)"
            API[API Server<br/>Port 8080]
            UI[Web UI Server<br/>Port 8081]
            Router[Router<br/>index.php]
            Routes[Routes<br/>Handlers]
            Logger[Request Logger<br/>Manager]
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
- Authentication: sa account với password từ .env

##### b) Partitioned Views

```sql
VIEW Khoa_Global AS
  SELECT * FROM [SITE_A].SiteA.dbo.Khoa
  UNION ALL
  SELECT * FROM [SITE_B].SiteB.dbo.Khoa
  UNION ALL
  SELECT * FROM [SITE_C].SiteC.dbo.Khoa
```

**5 Global Views**:
- `Khoa_Global` - Union tất cả khoa từ 3 sites
- `MonHoc_Global` - DISTINCT union (môn học có thể trùng)
- `SinhVien_Global` - Union sinh viên từ 3 sites
- `CTDaoTao_Global` - Union chương trình đào tạo
- `DangKy_Global` - Union đăng ký môn học

**Đặc điểm**:
- Read-only by default (UNION ALL)
- Cho phép query như bảng thường
- Không thể INSERT/UPDATE/DELETE trực tiếp → Cần triggers

##### c) INSTEAD OF Triggers

**Nhiệm vụ**: Chặn operations trên views, route đến site đúng

**Logic phân mảnh theo MaKhoa**:

```sql
IF MaKhoa < 'M'        -> SITE_A (A-L)
IF MaKhoa >= 'M' AND < 'S' -> SITE_B (M-R)
IF MaKhoa >= 'S'       -> SITE_C (S-Z)
```

**15 triggers** (5 tables × 3 operations):

1. **Khoa_Global**: 3 triggers (INSERT, UPDATE, DELETE)
2. **MonHoc_Global**: 3 triggers (sync 3 sites đồng thời)
3. **SinhVien_Global**: 3 triggers (cho phép cross-site move)
4. **CTDaoTao_Global**: 3 triggers (FK validation)
5. **DangKy_Global**: 3 triggers (distributed join validation)

**Ví dụ trigger INSERT Khoa**:

```sql
CREATE TRIGGER TR_Khoa_Global_Insert ON Khoa_Global INSTEAD OF INSERT
AS BEGIN
  -- 1. Validate: Check duplicates
  IF EXISTS (SELECT 1 FROM Khoa_Global WHERE MaKhoa IN (SELECT MaKhoa FROM inserted))
    RAISERROR('Mã khoa đã tồn tại!', 16, 1);

  -- 2. Route to appropriate site
  IF @MaKhoa < 'M'
    INSERT INTO [SITE_A].SiteA.dbo.Khoa ...
  ELSE IF @MaKhoa >= 'M' AND < 'S'
    INSERT INTO [SITE_B].SiteB.dbo.Khoa ...
  ELSE
    INSERT INTO [SITE_C].SiteC.dbo.Khoa ...
END
```

#### Site Databases (Ports 14334-14336)

**Site A (Port 14334)** - Database: SiteA
- **Phân mảnh**: MaKhoa < 'M' (A, B, C, ..., L)
- **Check constraint**: `CHECK (MaKhoa < 'M')`
- **Ví dụ khoa**: CNTT, DLKS, KTTC, KTDN, LUAT

**Site B (Port 14335)** - Database: SiteB
- **Phân mảnh**: MaKhoa >= 'M' AND < 'S' (M, N, O, P, Q, R)
- **Check constraint**: `CHECK (MaKhoa >= 'M' AND MaKhoa < 'S')`
- **Ví dụ khoa**: MMT, NNA, NNPH, NNTR, QTKD

**Site C (Port 14336)** - Database: SiteC
- **Phân mảnh**: MaKhoa >= 'S' (S, T, U, ..., Z)
- **Check constraint**: `CHECK (MaKhoa >= 'S')`
- **Ví dụ khoa**: SPQT, TCNH, VHXH

**Schema mỗi site** (giống hệt nhau):

```sql
-- 1. Khoa (FK root)
Khoa (
  MaKhoa NVARCHAR(10) PK,
  TenKhoa NVARCHAR(100) NOT NULL,
  CHECK CONSTRAINT (phân mảnh)
)

-- 2. MonHoc (independent, replicated across sites)
MonHoc (
  MaMH NVARCHAR(10) PK,
  TenMH NVARCHAR(100) NOT NULL
)

-- 3. SinhVien (foreign to Khoa)
SinhVien (
  MaSV NVARCHAR(10) PK,
  HoTen NVARCHAR(100) NOT NULL,
  MaKhoa NVARCHAR(10) FK -> Khoa(MaKhoa),
  KhoaHoc INT NOT NULL,
  CHECK CONSTRAINT (same as Khoa)
)

-- 4. CTDaoTao (curriculum)
CTDaoTao (
  MaKhoa NVARCHAR(10) FK -> Khoa(MaKhoa),
  KhoaHoc INT NOT NULL,
  MaMH NVARCHAR(10) FK -> MonHoc(MaMH),
  PK (MaKhoa, KhoaHoc, MaMH),
  CHECK CONSTRAINT (same as Khoa)
)

-- 5. DangKy (enrollment)
DangKy (
  MaSV NVARCHAR(10) FK -> SinhVien(MaSV),
  MaMon NVARCHAR(10) FK -> MonHoc(MaMH),
  DiemThi DECIMAL(4,2) NULL,
  PK (MaSV, MaMon)
)
```

**Quan hệ FK**:

```
Khoa (root)
  ↓ 1:N
  ├─> SinhVien
  │     ↓ 1:N
  │     └─> DangKy -> MonHoc
  └─> CTDaoTao -> MonHoc
```

### 2. MongoDB (Port 27017)

**Database**: `huflit_logs`

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
- `{table: 1, timestamp: -1}` - Filter theo bảng
- `{operation: 1, timestamp: -1}` - Filter theo operation
- `{site: 1, timestamp: -1}` - Filter theo site

**Use case**:
- Xem lịch sử thay đổi của 1 record
- Audit trail cho compliance
- Rollback data (manual)
- Analytics về operations

#### Collection 2: `query_history`

**Mục đích**: Ghi lại mọi API request

**Schema**:

```javascript
{
  endpoint: "/khoa|/sinhvien|...",
  method: "GET|POST|PUT|DELETE",
  params: {...},          // Query parameters
  body: {...},            // Request body
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

**Use case**:
- Performance monitoring
- Slow query detection
- Usage analytics
- Error rate tracking

---

## 🔧 Application Layer - Lớp ứng dụng

### 1. PHP Backend (2 containers)

#### Container 1: API Server (Port 8080)

**Entry point**: `app/public/index.php` (via router.php)

**Tech stack**:
- PHP 8.4-cli
- Extensions: sqlsrv, pdo_sqlsrv, mongodb
- Server: Built-in PHP server

**Request flow**:

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
sendResponse($data, $code) → JSON response
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

sendResponse($data, $status)  // JSON output + exit
getJsonInput()                 // Parse request body
```

#### `mongo_helper.php` - MongoDB operations

```php
MongoHelper::getClient()
  → Check extension_loaded('mongodb')
  → Connect to mongodb://admin:pass@mongodb:27017
  → Return MongoDB\Driver\Manager instance

MongoHelper::logAudit($table, $op, $data, $old, $site)
  → Insert to huflit_logs.audit_logs

MongoHelper::logQuery($endpoint, $method, ...)
  → Insert to huflit_logs.query_history

MongoHelper::getAuditLogs($filter, $limit, $skip)
MongoHelper::getQueryHistory(...)
MongoHelper::getStatistics($collection, $pipeline)
```

#### `request_logger.php` - Request tracking

```php
RequestLogger::start()
  → Capture: startTime, endpoint, method, params, body

RequestLogger::end($resultCount, $statusCode)
  → Calculate execution time
  → Call MongoHelper::logQuery()
```

### 3. Route Handlers (`app/routes/*.php`)

#### Pattern chung:

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
- Simple CRUD
- Site determined by `MaKhoa` first letter
- FK root: Xóa Khoa → cascade SinhVien, CTDaoTao

**`monhoc.php`**:
- **Sync 3 sites**: INSERT/UPDATE/DELETE đồng thời Site A, B, C
- Trigger uses cursor to iterate 3 sites
- No site column (replicated data)

**`sinhvien.php`**:
- **Cross-site move**: UPDATE cho phép đổi MaKhoa
- Trigger handles DELETE từ site cũ + INSERT vào site mới
- Cascade: Xóa sinh viên → xóa DangKy

**`ctdaotao.php`**:
- Composite PK: (MaKhoa, KhoaHoc, MaMH)
- FK validation: MaKhoa phải tồn tại, MaMH phải tồn tại
- No UPDATE (delete + recreate)

**`dangky.php`**:
- Composite PK: (MaSV, MaMon)
- Distributed FK: MaSV phải tồn tại (từ 1 trong 3 sites)
- UPDATE chỉ DiemThi (không đổi MaSV, MaMon)

**`global.php`**:
- Complex queries sử dụng JOIN across views
- 4 query types:
  1. Môn học SV đã đạt (≥5 điểm)
  2. Khóa học của khoa
  3. Môn bắt buộc của SV (theo CTĐT)
  4. SV đủ điều kiện tốt nghiệp

**`logs.php` & `stats.php`**:
- Read from MongoDB
- Aggregation pipelines
- Real-time analytics

### 4. Frontend (JavaScript ES6 Modules)

#### Structure:

```
js/
├── app.js              # Entry point, initialize
├── config.js           # Constants, field configs
├── modules/
│   ├── crud.js         # CRUD operations
│   ├── modal.js        # Dynamic form modals
│   ├── view.js         # Render tables
│   ├── settings.js     # Site toggle, theme
│   └── global-query.js # Complex queries
└── utils/
    ├── api.js          # Fetch wrappers
    ├── dom.js          # DOM helpers, alerts
    └── validation.js   # Form validation
```

#### Key features:

**`config.js`**:
- `API_BASE = 'http://localhost:8080'`
- `FIELDS_CONFIG`: Form field definitions cho mỗi module
- `PRIMARY_KEYS`: Xác định PK cho edit/delete

**`crud.js`**:

```javascript
loadData(module)    // GET /{module}
deleteRecord(id)    // DELETE /{module}?id={id}
createRecord(data)  // POST /{module}
updateRecord(id, data) // PUT /{module}?id={id}
```

**`modal.js`**:
- Dynamic form generation từ `FIELDS_CONFIG`
- Select boxes load options từ API (FK references)
- Validation: required, min, max, maxlength

**`view.js`**:
- Render HTML tables từ JSON data
- Dynamic columns dựa trên data keys
- Site column toggle
- Row actions: Edit, Delete buttons

**`settings.js`**:
- Site column visibility toggle (localStorage)
- Global query interface

---

## 🔄 Data Flow - Luồng dữ liệu

### 1. CREATE Flow (INSERT)

**Example**: Tạo Sinh Viên mới với MaKhoa = 'CNTT'

```
[Browser] POST /sinhvien
  body: {MaSV: "25DH001", HoTen: "Nguyen Van A", MaKhoa: "CNTT", KhoaHoc: 2025}
  ↓
[API] routes/sinhvien.php → handleSinhVien('POST', {})
  ↓
[SQL] INSERT INTO SinhVien_Global (MaSV, HoTen, MaKhoa, KhoaHoc)
      VALUES ('25DH001', 'Nguyen Van A', 'CNTT', 2025)
  ↓
[Trigger] TR_SinhVien_Global_Insert
  ├─ Validate: Check MaSV unique
  ├─ Validate: Check MaKhoa exists in Khoa_Global
  ├─ Determine site: 'CNTT' < 'M' → SITE_A
  └─ Execute: INSERT INTO [SITE_A].SiteA.dbo.SinhVien ...
  ↓
[Site A] Insert thành công vào bảng local SinhVien
  ↓
[API] MongoHelper::logAudit('SinhVien', 'INSERT', {...}, null, 'Site_A')
  ↓
[MongoDB] Insert document vào audit_logs collection
  ↓
[API] RequestLogger::end(1, 201)
  ↓
[MongoDB] Insert document vào query_history collection
  ↓
[API] sendResponse({message: 'SinhVien created successfully'}, 201)
  ↓
[Browser] Receive JSON, show alert, reload table
```

### 2. UPDATE Flow (cross-site move)

**Example**: Chuyển SV từ CNTT (Site A) sang MMT (Site B)

```
[Browser] PUT /sinhvien?id=25DH001
  body: {HoTen: "Nguyen Van A", MaKhoa: "MMT", KhoaHoc: 2025}
  ↓
[API] Get old data first:
  SELECT * FROM SinhVien_Global WHERE MaSV = '25DH001'
  → old: {MaSV: "25DH001", HoTen: "...", MaKhoa: "CNTT", KhoaHoc: 2025}
  ↓
[SQL] UPDATE SinhVien_Global
      SET HoTen = '...', MaKhoa = 'MMT', KhoaHoc = 2025
      WHERE MaSV = '25DH001'
  ↓
[Trigger] TR_SinhVien_Global_Update
  ├─ Detect MaKhoa change: 'CNTT' → 'MMT'
  ├─ Old site: 'CNTT' < 'M' → SITE_A
  ├─ New site: 'MMT' >= 'M' AND < 'S' → SITE_B
  ├─ Begin Transaction
  │   ├─ DELETE FROM [SITE_A].SiteA.dbo.SinhVien WHERE MaSV = '25DH001'
  │   ├─ INSERT INTO [SITE_B].SiteB.dbo.SinhVien VALUES (...)
  │   └─ CASCADE: DangKy records follow (deleted from A, inserted to B)
  └─ Commit Transaction
  ↓
[API] MongoHelper::logAudit('SinhVien', 'UPDATE', newData, oldData, 'Site_B')
  ↓
[Browser] Success, table refreshed
```

### 3. SYNC Flow (MonHoc)

**Example**: Tạo môn học mới → phải có ở cả 3 sites

```
[Browser] POST /monhoc
  body: {MaMH: "MH999", TenMH: "Trí tuệ nhân tạo"}
  ↓
[SQL] INSERT INTO MonHoc_Global (MaMH, TenMH) VALUES ('MH999', '...')
  ↓
[Trigger] TR_MonHoc_Global_Insert
  ├─ Validate: Check MaMH unique across 3 sites
  ├─ INSERT INTO [SITE_A].SiteA.dbo.MonHoc VALUES ('MH999', '...')
  ├─ INSERT INTO [SITE_B].SiteB.dbo.MonHoc VALUES ('MH999', '...')
  └─ INSERT INTO [SITE_C].SiteC.dbo.MonHoc VALUES ('MH999', '...')
  (Nếu 1 site fail → rollback tất cả)
  ↓
[MongoDB] Log audit with site='Global'
  ↓
[Browser] Success: "MonHoc created successfully on all sites"
```

### 4. QUERY Flow (Global complex query)

**Example**: Tìm SV đủ điều kiện tốt nghiệp (query type 4)

```
[Browser] GET /global?type=4
  ↓
[API] routes/global.php → handleGlobal('GET', {type: 4})
  ↓
[SQL] Complex query:
  SELECT s.MaSV, s.HoTen
  FROM SinhVien_Global s
  WHERE NOT EXISTS (
    -- Môn học trong CTĐT mà SV chưa học hoặc chưa đạt
    SELECT * FROM CTDaoTao_Global c
    WHERE c.MaKhoa = s.MaKhoa AND c.KhoaHoc = s.KhoaHoc
      AND NOT EXISTS (
        SELECT * FROM DangKy_Global d
        WHERE d.MaSV = s.MaSV AND d.MaMon = c.MaMH AND d.DiemThi >= 5
      )
  )
  ↓
[Global DB] Execute query using partitioned views:
  ├─ SinhVien_Global → UNION Site A, B, C
  ├─ CTDaoTao_Global → UNION Site A, B, C
  └─ DangKy_Global → UNION Site A, B, C
  (SQL Server optimizer handles distributed execution)
  ↓
[MongoDB] Log query to query_history (execution_time, result_count)
  ↓
[API] sendResponse([{MaSV: "...", HoTen: "..."}], 200)
  ↓
[Browser] Render results in table
```

---

## 🔐 Key Design Decisions

### 1. Horizontal Partitioning (Phân mảnh ngang)

**Strategy**: Range partitioning theo MaKhoa

- **Pros**:
  - Load balancing tự nhiên (phân bố đều khoa)
  - Isolation: Lỗi 1 site không ảnh hưởng sites khác
  - Scalability: Dễ thêm sites mới

- **Cons**:
  - Cross-site queries phức tạp (cần JOIN qua linked servers)
  - Data migration (chuyển khoa) tốn kém

**Alternative considered**: Hash partitioning → Bị loại vì khó query range

### 2. Replication vs Partitioning

**MonHoc: Full Replication** (có ở cả 3 sites)
- **Lý do**: Môn học cần thiết cho FK từ CTDaoTao và DangKy ở mọi site
- **Trade-off**: Tốn storage nhưng giảm cross-site queries
- **Sync**: INSTEAD OF triggers đảm bảo consistency

**Khoa, SinhVien, CTDaoTao, DangKy: Partitioning**
- **Lý do**: Dữ liệu lớn, không cần replicate
- **Advantage**: Giảm redundancy, dễ maintain

### 3. INSTEAD OF Triggers vs Application Logic

**Why triggers?**
- ✅ Centralized logic tại DB layer
- ✅ Application code đơn giản (chỉ cần INSERT vào view)
- ✅ Consistency guarantee (transaction tại DB)
- ✅ Multiple clients có thể dùng (REST, gRPC, direct SQL)

**Trade-off**:
- ❌ Harder to debug (trigger execution không visible)
- ❌ Performance overhead (cursor iteration)
- ❌ Migration phức tạp (trigger code phải sync)

### 4. MongoDB cho Audit Logs

**Why not SQL Server?**
- ✅ Schema-less: Dễ thêm fields mới (IP, user_agent, ...)
- ✅ High write throughput: Optimized cho logging
- ✅ Time-series data: Native support cho temporal queries
- ✅ Aggregation pipeline: Powerful analytics

**Use cases**:
- Compliance: Audit trail cho regulatory requirements
- Debugging: Trace lại history của 1 record
- Analytics: Usage patterns, slow queries, error rates

### 5. PHP Built-in Server (không dùng Apache/Nginx)

**Development setup**:
- ✅ Lightweight, nhanh khởi động
- ✅ Không cần config phức tạp
- ✅ Auto-reload khi code change (volume mount)

**Production**:
- ❌ **KHÔNG khuyến khích** (single-threaded)
- ✅ Nên dùng: Nginx + PHP-FPM hoặc Apache mod_php

---

## 📈 Performance Considerations

### 1. Indexes

**SQL Server**:
- Primary Keys → Clustered index tự động
- Foreign Keys → Nên thêm non-clustered index
- Views → Không thể index directly (dùng indexed views cho read-heavy)

**MongoDB**:
- `{timestamp: -1}` → Sort queries nhanh
- `{table: 1, timestamp: -1}` → Filter + sort composite

### 2. Query Optimization

**Avoid**:
- `SELECT *` từ Global views (query tất cả sites)
- N+1 queries (load danh sách rồi query detail từng record)

**Best practice**:
- Filter sớm: `WHERE MaKhoa = 'CNTT'` → chỉ query Site A
- Pagination: `LIMIT` + `OFFSET`
- Caching: Browser cache cho reference data (Khoa list)

### 3. Transaction Management

**Trigger transactions**:
- Auto-commit OFF trong trigger body
- Explicit `BEGIN TRANSACTION` ... `COMMIT` cho cross-site operations
- Rollback nếu bất kỳ site nào fail

**PHP PDO**:
- `PDO::ERRMODE_EXCEPTION` → Exception khi lỗi
- Try-catch để handle gracefully

---

## 🧪 Testing Strategy

### Unit Tests (Future)
- Mock PDO connections
- Test routing logic (determineSite)
- Validate field constraints

### Integration Tests
- `db/test_triggers.sql` - 29 test cases
- Cover: CRUD, FK violations, cross-site moves

### Load Testing (Future)
- JMeter / k6 scripts
- Concurrent inserts vào 3 sites
- Distributed query performance

---

## 🚀 Deployment

### Development (Current)

```bash
docker-compose up -d       # 6 containers
.\init_databases.ps1       # Init DB schemas + seed data
```

### Production (Recommendations)

**1. Database tier**:
- SQL Server: Always On Availability Groups (HA)
- MongoDB: Replica Set 3 nodes
- Backup: Daily full + hourly incremental

**2. Application tier**:
- PHP: Switch to Nginx + PHP-FPM
- Load balancer: HAProxy / Nginx upstream
- Caching: Redis cho session + reference data

**3. Monitoring**:
- DB: SQL Server Profiler, MongoDB Atlas
- App: Logging to ELK stack (Elasticsearch, Logstash, Kibana)
- Metrics: Prometheus + Grafana

**4. Security**:
- HTTPS (Let's Encrypt)
- SQL injection prevention (prepared statements ✅ đã có)
- MongoDB authentication ✅
- Rate limiting (Nginx limit_req)
- CORS whitelist (hiện tại `*`)

---

## 📚 References

### SQL Server
- [Linked Servers Documentation](https://learn.microsoft.com/en-us/sql/relational-databases/linked-servers/)
- [Partitioned Views](https://learn.microsoft.com/en-us/sql/t-sql/statements/create-view-transact-sql)
- [INSTEAD OF Triggers](https://learn.microsoft.com/en-us/sql/t-sql/statements/create-trigger-transact-sql)

### MongoDB
- [PHP MongoDB Driver](https://www.php.net/manual/en/set.mongodb.php)
- [Aggregation Pipeline](https://www.mongodb.com/docs/manual/aggregation/)

### Docker
- [Docker Compose Networking](https://docs.docker.com/compose/networking/)

---

## 🎯 Future Enhancements

### Short-term
- [ ] Implement caching layer (Redis)
- [ ] Add input sanitization (XSS prevention)
- [ ] Pagination cho large datasets
- [ ] Export logs (CSV, Excel)

### Mid-term
- [ ] Real-time notifications (WebSocket)
- [ ] User authentication & authorization (JWT)
- [ ] Role-based access control (RBAC)
- [ ] Versioning cho audit logs (time-travel queries)

### Long-term
- [ ] Microservices architecture (separate API per site)
- [ ] Event sourcing (Kafka)
- [ ] GraphQL API
- [ ] Machine learning analytics (anomaly detection)

---

**📝 Tài liệu cập nhật**: November 25, 2025  
**✍️ Tác giả**: HUFLIT Distributed Database Team
