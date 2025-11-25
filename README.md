# 🎓 Hệ thống Cơ sở dữ liệu phân tán HUFLIT

> Đồ án môn Cơ sở dữ liệu phân tán - SQL Server (3 sites) + MongoDB (nhật ký kiểm tra) + Triển khai Docker

[![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white)](https://docker.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![SQL Server](https://img.shields.io/badge/SQL%20Server-CC2927?style=flat&logo=microsoft-sql-server&logoColor=white)](https://microsoft.com/sql-server)
[![MongoDB](https://img.shields.io/badge/MongoDB-47A248?style=flat&logo=mongodb&logoColor=white)](https://mongodb.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

---

## 📋 Mục lục

- [🚀 Khởi động nhanh](#-khởi-động-nhanh)
- [🏗️ Kiến trúc hệ thống](#️-kiến-trúc-hệ-thống)
- [✨ Tính năng chính](#-tính-năng-chính)
- [📡 API Endpoints](#-api-endpoints)
- [📁 Cấu trúc dự án](#-cấu-trúc-dự-án)
- [🎯 Demo](#-demo)
- [📄 Bản quyền](#-bản-quyền)

---

## 🚀 Khởi động nhanh

### Yêu cầu hệ thống

- **Docker Desktop** 4.0+
- **PowerShell** 7.0+
- **RAM**: 4GB+ (khuyến nghị 8GB)
- **Ổ cứng**: 10GB dung lượng trống

### Cài đặt và chạy

```powershell
# 1. Khởi động containers
docker-compose up -d

# 2. Khởi tạo cơ sở dữ liệu
.\init_databases.ps1
```

**URLs:**

- 🏠 **Giao diện chính**: [http://localhost:8081/ui.php](http://localhost:8081/ui.php)
- 📋 **Nhật ký kiểm tra**: [http://localhost:8081/logs.php](http://localhost:8081/logs.php)
- 📊 **Thống kê**: [http://localhost:8081/stats.php](http://localhost:8081/stats.php)
- 🔌 **API**: [http://localhost:8080](http://localhost:8080)

---

## 🏗️ Kiến trúc hệ thống

### Sơ đồ kiến trúc tổng quan

```mermaid
graph TB
    %% User Interface
    subgraph "🎨 Frontend Layer"
        UI[Web UI<br/>localhost:8081]
        API[API Gateway<br/>localhost:8080]
    end

    %% Application Layer
    subgraph "⚙️ Application Layer"
        PHP1[PHP App 1<br/>Container: api_php]
        PHP2[PHP App 2<br/>Container: ui_php]
    end

    %% Database Layer
    subgraph "🗄️ Database Layer"
        subgraph "SQL Server Distributed System"
            GLOBAL[Global DB<br/>Port: 14333<br/>Linked Servers<br/>Partitioned Views<br/>INSTEAD OF Triggers]

            subgraph "Site A (CNTT, KHMT)"
                SITE_A[Site A DB<br/>Port: 14334<br/>MaKhoa < 'M']
            end

            subgraph "Site B (KT, NN)"
                SITE_B[Site B DB<br/>Port: 14335<br/>'M' ≤ MaKhoa < 'S']
            end

            subgraph "Site C (SP, SH)"
                SITE_C[Site C DB<br/>Port: 14336<br/>MaKhoa ≥ 'S']
            end
        end

        subgraph "📝 Audit System"
            MONGO[MongoDB<br/>Port: 27017<br/>Audit Logs<br/>Statistics<br/>API Request Logs]
        end
    end

    %% Data Flow
    UI --> PHP2
    API --> PHP1
    PHP1 --> GLOBAL
    PHP2 --> GLOBAL

    GLOBAL --> SITE_A
    GLOBAL --> SITE_B
    GLOBAL --> SITE_C

    PHP1 --> MONGO
    PHP2 --> MONGO

    %% Styling
    classDef frontend fill:#e1f5fe,stroke:#01579b,stroke-width:2px
    classDef app fill:#f3e5f5,stroke:#4a148c,stroke-width:2px
    classDef db fill:#e8f5e8,stroke:#1b5e20,stroke-width:2px
    classDef site fill:#fff3e0,stroke:#e65100,stroke-width:2px
    classDef mongo fill:#fce4ec,stroke:#880e4f,stroke-width:2px

    class UI,API frontend
    class PHP1,PHP2 app
    class GLOBAL db
    class SITE_A,SITE_B,SITE_C site
    class MONGO mongo
```

### Luồng dữ liệu trong hệ thống

```mermaid
sequenceDiagram
    participant U as Người dùng
    participant UI as Web UI
    participant API as API Gateway
    participant PHP as PHP Application
    participant GLOBAL as Global DB
    participant SITE as Site DB (A/B/C)
    participant MONGO as MongoDB

    U->>UI: Truy cập giao diện
    UI->>API: Gửi request CRUD
    API->>PHP: Xử lý request

    PHP->>GLOBAL: Query partitioned view
    GLOBAL->>SITE: Route to correct site
    SITE-->>GLOBAL: Return data
    GLOBAL-->>PHP: Aggregated result

    PHP->>MONGO: Log operation
    MONGO-->>PHP: Confirm log

    PHP-->>API: Response data
    API-->>UI: Update interface
    UI-->>U: Hiển thị kết quả
```

### Thiết kế cơ sở dữ liệu

- **3 Sites SQL Server**: Phân mảnh theo khoảng giá trị `MaKhoa`
  - **Site A**: `MaKhoa < 'M'` (cổng 14334) - Công nghệ thông tin, Khoa học máy tính
  - **Site B**: `'M' ≤ MaKhoa < 'S'` (cổng 14335) - Kinh tế, Ngoại ngữ
  - **Site C**: `MaKhoa ≥ 'S'` (cổng 14336) - Sư phạm, Xã hội
- **Cơ sở dữ liệu toàn cục**: Máy chủ liên kết + Khung nhìn phân mảnh (cổng 14333)
- **MongoDB**: Nhật ký kiểm tra & phân tích (cổng 27017)

### Công nghệ sử dụng

- **Backend**: PHP 8.2 + PDO SQLSRV + Trình điều khiển MongoDB
- **Frontend**: JavaScript thuần (ES6 modules) + CSS3
- **Cơ sở dữ liệu**: SQL Server 2022 + MongoDB 6.0
- **Triển khai**: Docker Compose (6 containers)

---

## ✨ Tính năng chính

### 🔄 CRUD Operations

- **🏫 Khoa**: Quản lý khoa (đặc thù theo site)
- **📚 Môn Học**: Đồng bộ khóa học trên 3 sites
- **👨‍🎓 Sinh Viên**: Di chuyển sinh viên chéo sites
- **📋 Chương Trình Đào Tạo**: Chương trình học với xác thực khóa ngoại
- **✅ Đăng Ký**: Hệ thống đăng ký với JOIN phân tán

### 🌐 Tính năng phân tán

- **⚡ INSTEAD OF Triggers**: Tự động định tuyến thao tác về site đúng
- **📝 Nhật ký kiểm tra**: Theo dõi MongoDB cho tất cả thao tác
- **📊 Bảng thống kê**: Phân tích thời gian thực với biểu đồ
- **🔗 Truy vấn toàn cục**: 4 truy vấn phân tán phức tạp với JOIN

---

## 📡 API Endpoints

| Endpoint | Phương thức | Mô tả |
|----------|-------------|-------|
| `/khoa` | GET, POST, PUT, DELETE | CRUD Khoa |
| `/monhoc` | GET, POST, PUT, DELETE | Quản lý môn học (3 sites) |
| `/sinhvien` | GET, POST, PUT, DELETE | Thao tác sinh viên |
| `/ctdaotao` | GET, POST, DELETE | Quản lý chương trình đào tạo |
| `/dangky` | GET, POST, PUT, DELETE | Hệ thống đăng ký |
| `/global?type=1-4` | GET | Truy vấn toàn cục (4 loại) |
| `/logs` | GET | Nhật ký kiểm tra từ MongoDB |
| `/stats` | GET | Thống kê & phân tích |

---

## 📁 Cấu trúc dự án

```text
Distributed_Database/
├── docker-compose.yml          # 6 containers
├── init_databases.ps1          # Script thiết lập
├── app/
│   ├── public/
│   │   ├── ui.php              # Bảng điều khiển chính
│   │   ├── logs.php            # Giao diện nhật ký kiểm tra
│   │   ├── stats.php           # Giao diện thống kê
│   │   └── js/modules/         # Modules frontend
│   └── routes/                 # Xử lý API
└── db/
    ├── global/                 # Máy chủ liên kết & triggers
    ├── site_a/b/c/             # Schema theo site
    └── mongodb/init/           # Thiết lập MongoDB
```

---

## 🎯 Demo

- **Repository**: [GitHub](https://github.com/TienxDun/Distributed_Database)
- **Hệ thống trực tiếp**: [http://localhost:8081/ui.php](http://localhost:8081/ui.php) (sau khi thiết lập)

---

## 📄 Bản quyền

© 2025 - Đồ án môn Cơ sở dữ liệu phân tán HUFLIT

Dự án này được phát triển như một phần của chương trình học môn Cơ sở dữ liệu phân tán tại Trường Đại học HUFLIT.

**Giấy phép:** [MIT License](LICENSE.md)

**Công nghệ sử dụng:**

- Microsoft SQL Server 2022 (Enterprise Edition)
- MongoDB Community Edition
- PHP 8.2 với sqlsrv & mongodb extensions
- Docker & Docker Compose
- Chart.js cho trực quan hóa dữ liệu

---

🎓 Phát triển với ❤️ cho môn Cơ sở dữ liệu phân tán HUFLIT
