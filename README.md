# HUFLIT Distributed Database System

Hệ thống cơ sở dữ liệu phân tán với 3 sites địa lý, sử dụng SQL Server partitioned views, linked servers và INSTEAD OF triggers.

## 🏗️ Kiến trúc

- **3 Site Databases**: Phân vùng theo MaKhoa (A-M, M-S, S-Z)
- **Global Database**: Tổng hợp qua partitioned views + INSTEAD OF triggers
- **PHP REST API**: Full CRUD operations
- **Docker**: 6 containers (1 global + 3 sites + 2 PHP)

## 🚀 Quick Start

```bash
docker-compose up -d
.\init_databases.ps1
```

**Web UI**: http://localhost:8081/ui.php  
**API**: http://localhost:8080

## ✨ Features

### Full CRUD Interface
- ✅ **Create**: Modal forms với validation
- ✅ **Read**: Xem tất cả hoặc tìm theo ID
- ✅ **Update**: Sửa thông tin, cho phép chuyển khoa sinh viên
- ✅ **Delete**: Xóa với constraint checking
- ✅ **Triggers**: Tự động sync MonHoc across 3 sites
- ✅ **Enter to Submit**: Tất cả forms hỗ trợ phím Enter

### Distributed Transactions
- MonHoc sync across all 3 sites
- SinhVien site migration khi đổi khoa
- DangKy tự động route đến đúng site
- Foreign key validation via triggers

## 📡 API Endpoints

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET/POST/PUT/DELETE | `/khoa` | Quản lý khoa |
| GET/POST/PUT/DELETE | `/monhoc` | Quản lý môn học (sync 3 sites) |
| GET/POST/PUT/DELETE | `/sinhvien` | Quản lý sinh viên (cho phép chuyển khoa) |
| GET/POST/DELETE | `/ctdaotao` | Quản lý CTĐT |
| GET/POST/PUT/DELETE | `/dangky` | Quản lý đăng ký (PUT chỉ DiemThi) |
| GET | `/global?type=1-4` | Truy vấn toàn cục |

## 🧪 Testing

**Web UI**: `http://localhost:8081/ui.php`
- 5 modules CRUD: Khoa, Môn Học, Sinh Viên, CT Đào Tạo, Đăng Ký
- Truy vấn toàn cục: 4 queries đặc biệt
- Modal forms với error handling
- Action buttons (Edit/Delete) trên mỗi row

## 📁 Cấu trúc

```text
cdslpt/
├── docker-compose.yml
├── init_databases.ps1
├── README.md
├── ARCHITECTURE.md
├── app/ (PHP API)
│   ├── public/
│   │   ├── index.php
│   │   ├── ui.php
│   │   └── styles.css
│   └── routes/
└── db/ (SQL scripts cho 3 sites + global)
```
