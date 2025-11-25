# 🎓 HUFLIT Distributed Database System

> Hệ thống CSDL phân tán với SQL Server (3 sites) + MongoDB (audit logs) + Docker deployment

[![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white)](https://docker.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![SQL Server](https://img.shields.io/badge/SQL%20Server-CC2927?style=flat&logo=microsoft-sql-server&logoColor=white)](https://microsoft.com/sql-server)

---

## 🚀 Quick Start

```powershell
docker-compose up -d
.\init_databases.ps1
```

- **Main UI**: http://localhost:8081/ui.php
- **Audit Logs**: http://localhost:8081/logs.php
- **Statistics**: http://localhost:8081/stats.php
- **API**: http://localhost:8080

---

## 🏗️ Architecture

### Database Design
- **3 SQL Server Sites**: Range partitioning by `MaKhoa`
  - Site A: `MaKhoa < 'M'` (port 14334)
  - Site B: `'M' ≤ MaKhoa < 'S'` (port 14335)
  - Site C: `MaKhoa ≥ 'S'` (port 14336)
- **Global DB**: Linked servers + Partitioned views (port 14333)
- **MongoDB**: Audit logs & analytics (port 27017)

### Tech Stack
- **Backend**: PHP 8.2 + PDO SQLSRV + MongoDB driver
- **Frontend**: Vanilla JavaScript (ES6 modules) + CSS3
- **Database**: SQL Server 2022 + MongoDB 6.0
- **Deployment**: Docker Compose (6 containers)

---

## ✨ Key Features

### CRUD Operations
- **Khoa**: Department management (site-specific)
- **Môn Học**: Course sync across 3 sites
- **Sinh Viên**: Student cross-site migration
- **Chương Trình Đào Tạo**: Curriculum with FK validation
- **Đăng Ký**: Enrollment with distributed JOINs

### Distributed Features
- **INSTEAD OF Triggers**: Auto-route operations to correct site
- **Audit Logging**: MongoDB tracking for all operations
- **Statistics Dashboard**: Real-time analytics with charts
- **Global Queries**: 4 complex distributed queries with JOINs

---

## 📡 API Endpoints

| Endpoint | Methods | Description |
|----------|---------|-------------|
| `/khoa` | GET, POST, PUT, DELETE | Department CRUD |
| `/monhoc` | GET, POST, PUT, DELETE | Course management (3 sites) |
| `/sinhvien` | GET, POST, PUT, DELETE | Student operations |
| `/ctdaotao` | GET, POST, DELETE | Curriculum management |
| `/dangky` | GET, POST, PUT, DELETE | Enrollment system |
| `/global?type=1-4` | GET | Global queries (4 types) |
| `/logs` | GET | Audit logs from MongoDB |
| `/stats` | GET | Statistics & analytics |

---

## 📁 Project Structure

```
Distributed_Database/
├── docker-compose.yml          # 6 containers
├── init_databases.ps1          # Setup script
├── app/
│   ├── public/
│   │   ├── ui.php              # Main dashboard
│   │   ├── logs.php            # Audit logs UI
│   │   ├── stats.php           # Statistics UI
│   │   └── js/modules/         # Frontend modules
│   └── routes/                 # API handlers
└── db/
    ├── global/                 # Linked servers & triggers
    ├── site_a/b/c/             # Site schemas
    └── mongodb/init/           # MongoDB setup
```

---

## 🎯 Demo

- **Repository**: [GitHub](https://github.com/TienxDun/Distributed_Database)
- **Video Demo**: [YouTube](https://youtube.com/watch?v=demo-link)
- **Live System**: http://localhost:8081 (after setup)

---

**© 2025 HUFLIT Distributed Database Course Project**
