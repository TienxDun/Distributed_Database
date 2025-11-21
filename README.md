# 🎓 HUFLIT Distributed Database

[![SQL Server](https://img.shields.io/badge/SQL%20Server-2022-red?logo=microsoftsqlserver)](https://www.microsoft.com/sql-server)
[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php)](https://www.php.net/)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> Hệ thống CSDL phân tán 3 sites với **SQL Server**, **Linked Servers**, **Partitioned Views** và **INSTEAD OF Triggers**.

---

## 🚀 Khởi động nhanh

```powershell
docker-compose up -d
.\init_databases.ps1
```

- **Web UI**: http://localhost:8081/ui.php
- **API**: http://localhost:8080

## 🏗️ Kiến trúc

| Component | Mô tả |
|-----------|-------|
| 🗄️ **3 Sites** | Phân mảnh theo MaKhoa (A→Site A, M→Site B, S→Site C) |
| 🌐 **Global DB** | Linked servers + Partitioned views (UNION ALL) |
| ⚡ **Triggers** | INSTEAD OF cho INSERT/UPDATE/DELETE |
| 🔌 **API** | PHP REST với PDO/sqlsrv |

## ✨ Tính năng nổi bật

<table>
<tr>
<td width="50%">

### 📝 Full CRUD
- ✅ Khoa
- ✅ Môn Học
- ✅ Sinh Viên
- ✅ CTĐT
- ✅ Đăng Ký

</td>
<td width="50%">

### 🔄 Distributed Features
- 🔁 **MonHoc sync**: Đồng bộ 3 sites
- 🚚 **SinhVien migration**: Cross-site move
- 👁️ **Site toggle**: Show/hide Site column
- 🔒 **Validation**: FK + constraints

</td>
</tr>
</table>

## 📡 API Endpoints

| Endpoint | Methods | Mô tả |
|----------|---------|-------|
| `/khoa` | `GET` `POST` `PUT` `DELETE` | Quản lý khoa |
| `/monhoc` | `GET` `POST` `PUT` `DELETE` | Quản lý môn học **(sync 3 sites)** |
| `/sinhvien` | `GET` `POST` `PUT` `DELETE` | Quản lý sinh viên **(cross-site)** |
| `/ctdaotao` | `GET` `POST` `DELETE` | Quản lý CTĐT |
| `/dangky` | `GET` `POST` `PUT` `DELETE` | Quản lý đăng ký |
| `/global` | `GET` | Truy vấn toàn cục `?type=1-4` |

## 🧪 Testing

```powershell
sqlcmd -S localhost,14333 -U sa -P "Your@STROng!Pass#Word" -i db\test_triggers.sql
```

**29 test cases** bao gồm:
- ✅ Khoa (6 tests)
- ✅ MonHoc (3 tests)
- ✅ SinhVien (5 tests)
- ✅ CTĐT (6 tests)
- ✅ DangKy (6 tests)
- ✅ Constraints (3 tests)
- ✅ Cleanup

## 📁 Cấu trúc

```
Distributed_Database/
├── docker-compose.yml      # 6 containers
├── init_databases.ps1      # Setup script
├── app/
│   ├── public/             # index.php, ui.php, styles.css
│   └── routes/             # API handlers
└── db/
    ├── global/             # init.sql, triggers.sql
    └── site_a/b/c/         # init.sql, seed.sql
```

## 📚 Tài liệu

| File | Nội dung |
|------|----------|
| 📐 [ARCHITECTURE.md](ARCHITECTURE.md) | Chi tiết kiến trúc hệ thống |
| 📝 [CRUD_GUIDE.md](CRUD_GUIDE.md) | Hướng dẫn CRUD operations |
| 🧪 [TEST_GUIDE.md](TEST_GUIDE.md) | Hướng dẫn test triggers |

---

<div align="center">

**Made with ❤️ for HUFLIT**

[Report Bug](https://github.com/TienxDun/Distributed_Database/issues) · [Request Feature](https://github.com/TienxDun/Distributed_Database/issues)

</div>
