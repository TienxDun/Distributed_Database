# HUFLIT Distributed Database System - Architecture

## Tổng quan

Hệ thống HUFLIT Distributed Database là một ứng dụng web mô phỏng cơ sở dữ liệu phân tán cho Trường Đại họcc HUFLIT. Hệ thống sử dụng kiến trúc phân tán với 3 sites địa lý, tổng hợp dữ liệu qua SQL Server linked servers và partitioned views. Ứng dụng cung cấp REST API và giao diện web để truy vấn dữ liệu phân tán.

## Kiến trúc tổng thể

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Site A        │    │   Site B        │    │   Site C        │
│ (MaKhoa < 'M')  │    │ (MaKhoa 'M'-'S')│    │ (MaKhoa >= 'S') │
│                 │    │                 │    │                 │
│ - Khoa          │    │ - Khoa          │    │ - Khoa          │
│ - CTDaoTao      │    │ - CTDaoTao      │    │ - CTDaoTao      │
│ - MonHoc        │    │ - MonHoc        │    │ - MonHoc        │
│ - SinhVien      │    │ - SinhVien      │    │ - SinhVien      │
│ - DangKy        │    │ - DangKy        │    │ - DangKy        │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                    ┌─────────────────┐
                    │   Global DB     │
                    │                 │
                    │ - Linked Servers│
                    │ - Partitioned   │
                    │   Views         │
                    └─────────────────┘
                                 │
                    ┌─────────────────┐
                    │   PHP API       │
                    │   (RESTful)     │
                    └─────────────────┘
                                 │
                    ┌─────────────────┐
                    │   Web UI        │
                    │   (HTML/CSS/JS) │
                    └─────────────────┘
```

## Components

### 1. Database Layer

#### Sites Database (3 instances)

- **Công nghệ**: SQL Server 2022
- **Phân vùng**: Theo range alphabetical trên MaKhoa
  - Site A: MaKhoa < 'M' (CNTT, DLKS, KTTC, LLCT)
  - Site B: MaKhoa >= 'M' và < 'S' (NVPD, QHQT, QTKD)
  - Site C: MaKhoa >= 'S' (SLCT, SUAT, TLKS)
- **Bảng chính**:
  - `Khoa`: MaKhoa (PK), TenKhoa
  - `MonHoc`: MaMH (PK), TenMH
  - `CTDaoTao`: MaKhoa, KhoaHoc, MaMH (PK composite)
  - `SinhVien`: MaSV (PK), HoTen, MaKhoa, KhoaHoc
  - `DangKy`: MaSV, MaMon (PK composite), DiemThi

#### Global Database

- **Chức năng**: Tổng hợp dữ liệu từ 3 sites
- **Linked Servers**: Kết nối đến mssql_site_a, mssql_site_b, mssql_site_c
- **Partitioned Views**:
  - `Khoa_Global = UNION ALL` từ 3 sites
  - `MonHoc_Global = UNION ALL` từ 3 sites
  - `CTDaoTao_Global = UNION ALL` từ 3 sites
  - `SinhVien_Global = UNION ALL` từ 3 sites
  - `DangKy_Global = UNION ALL` từ 3 sites

### 2. API Layer

#### PHP REST API

- **Framework**: Native PHP (không framework)
- **Server**: Built-in PHP server (`php -S`)
- **Endpoints**:
  - `/khoa`: CRUD Khoa
  - `/monhoc`: CRUD MonHoc
  - `/sinhvien`: CRUD SinhVien
  - `/ctdaotao`: CTDaoTao + queries môn học
  - `/dangky`: DangKy
  - `/global`: Truy vấn toàn cục phức tạp
- **Connection**: PDO với SQL Server (sqlsrv driver)
- **Authentication**: Không có (dev environment)
- **Error Handling**: JSON responses với status codes

#### Routing

- File: `app/public/index.php`
- Map URL paths to handler files in `app/routes/`
- Support query parameters cho filtering

### 3. Presentation Layer

#### Web UI

- **Technologies**: HTML5, CSS3, Vanilla JavaScript
- **Features**:
  - Tabbed interface cho các modules
  - Form inputs với validation
  - AJAX calls đến API
  - Responsive design
  - Real-time results display
- **Styling**: Custom CSS với CSS Variables
- **Icons**: Unicode emojis

### 4. Infrastructure Layer

#### Docker Compose

- **Services**:
  - `mssql_global`: SQL Server cho Global DB
  - `mssql_site_a/b/c`: SQL Server cho 3 sites
  - `api_php`: PHP API server (port 8080)
  - `app_php`: PHP UI server (port 8081)
- **Networks**: `huflit-network` cho internal communication
- **Volumes**: Persistent data cho SQL Server

## Data Flow

1. **Initialization**:
   - Docker Compose khởi động containers
   - `init_databases.ps1` chạy scripts tạo DB và insert data

2. **Query Execution**:
   - User nhập data vào Web UI
   - JavaScript gửi AJAX request đến API (localhost:8080)
   - API connect đến Global DB qua PDO
   - Global DB query partitioned views (internally UNION from sites)
   - Results trả về JSON → display trên UI

3. **Complex Queries**:
   - `/global` endpoint xử lý JOIN và subqueries
   - Sử dụng Global views cho cross-site data

## Deployment

### Prerequisites

- Docker & Docker Compose
- PowerShell (cho init script)

### Steps

```bash
git clone <repo>
cd cdslpt
docker-compose up -d
.\init_databases.ps1
```

### Ports

- API: `http://localhost:8080`
- UI: `http://localhost:8081`

## Security Considerations

- **Development Only**: Không có authentication/authorization
- **Network**: Internal Docker network
- **Data**: Sample data, không sensitive
- **Production**: Cần thêm SSL, auth, input validation

## Scalability

- **Horizontal**: Thêm sites mới với range partitioning
- **Vertical**: Upgrade SQL Server instances
- **Load Balancing**: Nginx reverse proxy cho API
- **Caching**: Redis cho frequent queries

## Monitoring & Maintenance

- **Logs**: Docker logs, SQL Server logs
- **Backup**: SQL Server backup scripts
- **Updates**: Schema changes qua migration scripts
- **Testing**: Manual qua UI, API tests với Postman

## Future Enhancements

- Implement authentication (JWT)
- Add GraphQL API
- Real-time updates với WebSockets
- Advanced analytics dashboard
- Multi-region deployment
