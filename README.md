# HUFLIT Distributed Database System

Hệ thống cơ sở dữ liệu phân tán mô phỏng Đại học Ngoại thương TP.HCM với 3 sites địa lý, sử dụng SQL Server partitioned views và linked servers.

## 🏗️ Kiến trúc

- **3 Site Databases**: Phân vùng theo range alphabetical (A-M, M-S, S-Z)
- **Global Database**: Tổng hợp dữ liệu qua partitioned views
- **PHP REST API**: Truy cập dữ liệu phân tán
- **Docker**: Containerization hoàn chỉnh

## 📊 Dữ liệu mẫu

Dựa trên 11 khoa HUFLIT thực tế: CNTT, NN, DLKS, KTTC, LLCT, NVPD, QHQT, QTKD, SLCT, SUAT, TLKS

- **Tổng**: 53 môn học, 264 CTDaoTao, 88 sinh viên, 264 DangKy (2018-2025)

## 🚀 Cài đặt

```bash
git clone <repository-url>
cd cdslpt
docker-compose up -d
.\init_databases.ps1
```

## 📡 API Endpoints

| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/khoa` | GET | Danh sách khoa |
| `/monhoc` | GET | Danh sách môn học |
| `/sinhvien` | GET | Danh sách sinh viên |
| `/ctdaotao?makhoa=<id>` | GET | Môn học theo khoa |
| `/dangky?masv=<id>` | GET | Đăng ký của sinh viên |

## 🧪 Test

Truy cập `http://localhost:8080/ui.php` để test API với giao diện web hiện đại.

## 📁 Cấu trúc

```text
cdslpt/
├── docker-compose.yml
├── init_databases.ps1
├── app/ (PHP API)
└── db/ (SQL scripts cho 3 sites + global)
```
