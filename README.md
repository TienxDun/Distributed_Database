# HUFLIT Distributed Database System

Hệ thống cơ sở dữ liệu phân tán mô phỏng Đại học Ngoại thương TP.HCM với 3 sites địa lý, sử dụng SQL Server partitioned views và linked servers.

## 📖 Documentation

- [Architecture](ARCHITECTURE.md) - Chi tiết kiến trúc hệ thống

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
| `/khoa` | GET | Danh sách tất cả khoa |
| `/khoa?id=<id>` | GET | Chi tiết khoa theo ID |
| `/monhoc` | GET | Danh sách tất cả môn học |
| `/monhoc?id=<id>` | GET | Chi tiết môn học theo ID |
| `/sinhvien` | GET | Danh sách tất cả sinh viên |
| `/sinhvien?id=<id>` | GET | Chi tiết sinh viên theo ID |
| `/ctdaotao` | GET | Danh sách tất cả CTDaoTao |
| `/ctdaotao?makhoa=<id>` | GET | Môn học theo khoa |
| `/ctdaotao?khoahoc=<year>` | GET | Môn học theo khóa học |
| `/ctdaotao?makhoa=<id>&khoahoc=<year>` | GET | Môn học theo CTDaoTao cụ thể |
| `/dangky` | GET | Danh sách tất cả đăng ký |
| `/dangky?masv=<id>` | GET | Đăng ký của sinh viên |
| `/global?type=1&masv=<id>` | GET | Môn học sinh viên đã học đạt ≥5 |
| `/global?type=2&tenkhoa=<name>` | GET | Khóa học của một khoa |
| `/global?type=3&masv=<id>` | GET | Môn học bắt buộc của sinh viên |
| `/global?type=4` | GET | Sinh viên đủ điều kiện tốt nghiệp |

## 🧪 Test

Truy cập `http://localhost:8081/ui.php` để test API với giao diện web hiện đại.

## 📁 Cấu trúc

```text
cdslpt/
├── docker-compose.yml
├── init_databases.ps1
├── app/ (PHP API)
└── db/ (SQL scripts cho 3 sites + global)
```
