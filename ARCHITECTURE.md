# 🏗️ Kiến Trúc Hệ Thống

<div align="center">

**HUFLIT Distributed Database System Architecture**

[![SQL Server](https://img.shields.io/badge/SQL%20Server-2022-red?logo=microsoftsqlserver)](https://www.microsoft.com/sql-server)
[![Linked Servers](https://img.shields.io/badge/Linked-Servers-orange)](https://docs.microsoft.com/sql-server)
[![Partitioned Views](https://img.shields.io/badge/Partitioned-Views-blue)](https://docs.microsoft.com/sql-server)

</div>

---

## 📋 Tổng quan

> Hệ thống CSDL phân tán 3 sites cho Trường ĐH HUFLIT, sử dụng **SQL Server Linked Servers** và **Partitioned Views**.

## 🗺️ Sơ đồ tổng thể

```text
┌──────────┐  ┌──────────┐  ┌──────────┐
│  Site A  │  │  Site B  │  │  Site C  │
│  < 'M'   │  │ 'M'-'S'  │  │  >= 'S'  │
└────┬─────┘  └────┬─────┘  └────┬─────┘
     └─────────────┼─────────────┘
                   │
              ┌────┴─────┐
              │ Global   │ Linked Servers
              │ Database │ + Views + Triggers
              └────┬─────┘
                   │
              ┌────┴─────┐
              │ PHP API  │ REST
              └────┬─────┘
                   │
              ┌────┴─────┐
              │  Web UI  │ HTML/CSS/JS
              └──────────┘
```

## 🔧 Components

### 🗄️ 1. Database Layer (SQL Server 2022)

#### **3 Sites** - Horizontal Partitioning

| Site | Range | Khoa |
|------|-------|------|
| 🟦 **Site A** | `MaKhoa < 'M'` | CNTT, DLKS, KTTC, LLCT |
| 🟩 **Site B** | `'M' ≤ MaKhoa < 'S'` | NN, NVPD, QHQT, QTKD |
| 🟪 **Site C** | `MaKhoa ≥ 'S'` | SLCT, SUAT, TLKS |

**📊 Bảng**: `Khoa`, `MonHoc`, `CTDaoTao`, `SinhVien`, `DangKy`

#### **🌐 Global Database**

```sql
-- Linked Servers
SITE_A, SITE_B, SITE_C (MSOLEDBSQL)

-- Partitioned Views
<Table>_Global = UNION ALL từ 3 sites

-- INSTEAD OF Triggers
Route INSERT/UPDATE/DELETE → Sites
```

### 🔌 2. API Layer (PHP 8.x)

```php
✓ Native PHP + PDO/sqlsrv
✓ REST endpoints: /khoa, /monhoc, /sinhvien, /ctdaotao, /dangky, /global
✓ JSON responses
⚠ No auth (dev only)
```

### 🎨 3. Presentation Layer

```javascript
✓ HTML5 + CSS3 + Vanilla JS
✓ AJAX calls, Modal forms
✓ Tabbed interface
✓ Responsive design
```

### 🐳 4. Infrastructure (Docker Compose)

| Container | Port | Mô tả |
|-----------|------|-------|
| `mssql_global` | 14333 | Global Database |
| `mssql_site_a` | 14334 | Site A Database |
| `mssql_site_b` | 14335 | Site B Database |
| `mssql_site_c` | 14336 | Site C Database |
| `api_php` | 8080 | REST API Server |
| `app_php` | 8081 | Web UI Server |

**Network**: `huflit-network`

## 🔄 Data Flow

### Initialization
```
Docker Compose → Containers → init_databases.ps1 → Create DB → Seed Data
```

### CRUD Operations
```
UI → AJAX → API → Global DB → Partitioned Views → INSTEAD OF Triggers → Route to Sites → JSON Response
```

### ⚡ Đặc biệt

| Entity | Behavior |
|--------|----------|
| 📚 **MonHoc** | INSERT/UPDATE/DELETE → **Sync 3 sites** |
| 👨‍🎓 **SinhVien** | UPDATE MaKhoa → **Delete old site + Insert new site** |
| 📝 **DangKy** | JOIN SinhVien_Global → **Determine target site** |

---

## 🚀 Deployment

### Quick Start

```powershell
# 1. Start containers
docker-compose up -d

# 2. Initialize databases
.\init_databases.ps1

# 3. Access
# API:  http://localhost:8080
# UI:   http://localhost:8081/ui.php
```

### Port Mapping

| Service | Port | URL |
|---------|------|-----|
| 🔌 API | 8080 | http://localhost:8080 |
| 🖥️ UI | 8081 | http://localhost:8081/ui.php |
| 🗄️ Global DB | 14333 | localhost,14333 |
| 🗄️ Site A | 14334 | localhost,14334 |
| 🗄️ Site B | 14335 | localhost,14335 |
| 🗄️ Site C | 14336 | localhost,14336 |


<div align="center">

**[⬅️ Back to README](README.md)**

</div>
