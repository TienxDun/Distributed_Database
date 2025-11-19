# HUFLIT Distributed Database System

## 📋 Mô tả
Hệ thống cơ sở dữ liệu phân tán mô phỏng trường Đại học HUFLIT với 3 sites địa lý, sử dụng SQL Server partitioned views và linked servers. Dữ liệu được phân vùng theo range alphabetical để hỗ trợ mở rộng dễ dàng.

## 🏗️ Kiến trúc

- **3 Site Databases**:
  - Site A: Khoa có MaKhoa < 'M' (NN, CNTT, NVPD)
  - Site B: Khoa có 'M' ≤ MaKhoa < 'S' (QHQT, QTKD, KTTC)
  - Site C: Khoa có MaKhoa ≥ 'S' (DLKS, LUAT, LLCT)
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

| Endpoint | Method | Mô tả | Query Params |
|----------|--------|-------|--------------|
| `/khoa` | GET | Lấy danh sách tất cả khoa | `id=<MaKhoa>` |
| `/monhoc` | GET | Lấy danh sách tất cả môn học | `id=<MaMon>` |
| `/sinhvien` | GET | Lấy danh sách tất cả sinh viên | `id=<MaSV>` |
| `/ctdaotao` | GET | Lấy danh sách chương trình đào tạo | - |
| `/dangky` | GET | Lấy danh sách đăng ký học | `masv=<MaSV>` (xem môn học), `mamon=<MaMon>` (xem sinh viên), hoặc cả hai |

## 🧪 Test
Truy cập UI test hiện đại: `http://localhost:8080/ui.php`

- Giao diện modular với tabs cho từng bảng
- Input fields để query theo ID cụ thể
- Hiển thị kết quả JSON format đẹp

## 📁 Cấu trúc thư mục

```text
cdslpt/
├── docker-compose.yml    # Định nghĩa services
├── init_databases.ps1    # Script khởi tạo DB
├── app/                  # PHP API
│   ├── public/
│   │   ├── index.php     # API router
│   │   └── ui.php        # Test UI hiện đại
│   └── routes/           # API handlers
├── db/                   # SQL scripts
│   ├── global/           # Global DB schema & views
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
- Phân vùng theo range cho phép thêm khoa mới mà không cần thay đổi schema
- Triggers đảm bảo tính toàn vẹn dữ liệu phân tán
