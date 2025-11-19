# HUFLIT Distributed Database System

## 📋 Mô tả
Hệ thống cơ sở dữ liệu phân tán mô phỏng trường Đại học HUFLIT với 3 sites địa lý, sử dụng SQL Server partitioned views và linked servers.

## 🏗️ Kiến trúc

- **3 Site Databases**: Site A (NN, CNTT, NVPD), Site B (QHQT, QTKD, KTTC), Site C (DLKS, LUAT, LLCT)
- **Global Database**: Tổng hợp dữ liệu từ các sites qua partitioned views
- **PHP API**: RESTful API để truy cập dữ liệu
- **Docker**: Containerization cho SQL Server và PHP

## 📋 Yêu cầu

- Docker & Docker Compose
- PowerShell (Windows)
- SQL Server Command Line Tools

## 🚀 Cài đặt & Chạy

### 1. Clone project

```bash
git clone <repository-url>
cd cdslpt
```

### 2. Khởi động containers

```bash
docker-compose up -d
```

### 3. Khởi tạo databases

```powershell
.\init_databases.ps1
```

## 📡 API Endpoints

### Base URL: `http://localhost:8080`

| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/khoa` | GET | Lấy danh sách tất cả khoa |
| `/monhoc` | GET | Lấy danh sách tất cả môn học |
| `/sinhvien` | GET | Lấy danh sách tất cả sinh viên |
| `/ctdaotao` | GET | Lấy danh sách chương trình đào tạo |
| `/dangky` | GET | Lấy danh sách đăng ký học |

### Query Parameters

- `id=<value>`: Lọc theo ID cụ thể

## 🧪 Test
Truy cập UI test: `http://localhost:8080/ui.php`

## 📁 Cấu trúc thư mục

```text
cdslpt/
├── docker-compose.yml    # Định nghĩa services
├── init_databases.ps1    # Script khởi tạo DB
├── app/                  # PHP API
│   ├── public/
│   │   ├── index.php     # API router
│   │   └── ui.php        # Test UI
│   └── routes/           # API handlers
├── db/                   # SQL scripts
│   ├── global/           # Global DB schema
│   ├── site_a/           # Site A (NN, CNTT, NVPD)
│   ├── site_b/           # Site B (QHQT, QTKD, KTTC)
│   └── site_c/           # Site C (DLKS, LUAT, LLCT)
└── README.md
```

## 🔧 Ports

- API: `8080`
- SQL Server Global: `14333`
- SQL Server Site A: `14334`
- SQL Server Site B: `14335`
- SQL Server Site C: `14336`

## 📝 Lưu ý

- Đảm bảo containers đang chạy trước khi init DB
- Sử dụng PowerShell với encoding UTF-8 để tránh lỗi font tiếng Việt
- Dữ liệu mẫu dựa trên các khoa thực tế của trường HUFLIT
