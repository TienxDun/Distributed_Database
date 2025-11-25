# 🎓 HUFLIT Distributed Database

> Hệ thống CSDL phân tán với **SQL Server** (3 sites), **MongoDB** (audit logs), **Linked Servers**, **Partitioned Views** và **INSTEAD OF Triggers**.

---

## 🚀 Khởi động nhanh

```powershell
docker-compose up -d
.\init_databases.ps1
```

- **Web UI**: http://localhost:8081/ui.php
- **API**: http://localhost:8080
- **Logs**: http://localhost:8081/logs.php
- **Stats**: http://localhost:8081/stats.php

---

## 🏗️ Kiến trúc

### Cơ sở dữ liệu
- **3 Sites SQL Server**: Phân mảnh dữ liệu theo `MaKhoa`
  - Site A: `MaKhoa < 'M'` (port 14334)
  - Site B: `MaKhoa >= 'M' AND < 'S'` (port 14335)
  - Site C: `MaKhoa >= 'S'` (port 14336)
- **Global DB**: Linked servers + Partitioned views (port 14333)
- **MongoDB**: Audit logs & statistics (port 27017)

### Công nghệ
- **Backend**: PHP 8.x với PDO/sqlsrv + MongoDB driver
- **Frontend**: Vanilla JavaScript (modules pattern)
- **Database**: SQL Server 2022 + MongoDB
- **Deployment**: Docker Compose (6 containers)

---

## ✨ Tính năng

### CRUD Operations
- **Khoa**: Quản lý khoa (site-specific)
- **Môn Học**: Sync 3 sites đồng thời
- **Sinh Viên**: Cross-site migration
- **Chương Trình Đào Tạo**: FK validation
- **Đăng Ký**: Distributed join queries

### Distributed Features
- **INSTEAD OF Triggers**: Tự động route operations
- **Audit Logging**: MongoDB tracking (operations + API requests)
- **Statistics**: Real-time analytics dashboard
- **Site Toggle**: Show/hide site column trong UI

---

## 📡 API Endpoints

| Endpoint | Methods | Mô tả |
|----------|---------|-------|
| `/khoa` | GET, POST, PUT, DELETE | Quản lý khoa |
| `/monhoc` | GET, POST, PUT, DELETE | Môn học (sync 3 sites) |
| `/sinhvien` | GET, POST, PUT, DELETE | Sinh viên (cross-site) |
| `/ctdaotao` | GET, POST, DELETE | Chương trình đào tạo |
| `/dangky` | GET, POST, PUT, DELETE | Đăng ký môn học |
| `/global` | GET | Truy vấn toàn cục (`?type=1-4`) |
| `/logs` | GET | Audit logs từ MongoDB |
| `/stats` | GET | Statistics & analytics |

---

## 🧪 Testing

```powershell
sqlcmd -S localhost,14333 -U sa -P "Your@STROng!Pass#Word" -i db\test_triggers.sql
```

**29 test cases** cho CRUD + constraints + cleanup.

---

## 📁 Cấu trúc dự án

```
Distributed_Database/
├── docker-compose.yml          # 6 containers
├── init_databases.ps1          # Setup script
├── app/
│   ├── Dockerfile
│   ├── common.php              # Database connections
│   ├── mongo_helper.php        # MongoDB utilities
│   ├── request_logger.php      # Audit logging
│   ├── public/
│   │   ├── index.php           # API router
│   │   ├── ui.php              # Main UI
│   │   ├── logs.php            # Audit logs page
│   │   ├── stats.php           # Statistics page
│   │   └── js/modules/         # Frontend modules
│   └── routes/                 # API handlers
└── db/
    ├── global/                 # Linked servers, views, triggers
    ├── site_a/b/c/             # Site schemas & seed data
    └── mongodb/init/           # MongoDB collections

```

---

**Made for HUFLIT Distributed Database Course**
