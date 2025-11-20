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

| Endpoint | Method | Mô tả | Ví dụ |
|----------|--------|-------|-------|
| `/khoa` | GET | Danh sách tất cả khoa | `/khoa` |
| `/khoa?id=<id>` | GET | Chi tiết khoa theo ID | `/khoa?id=CNTT` |
| `/monhoc` | GET | Danh sách tất cả môn học | `/monhoc` |
| `/monhoc?id=<id>` | GET | Chi tiết môn học theo ID | `/monhoc?id=MH001` |
| `/sinhvien` | GET | Danh sách tất cả sinh viên | `/sinhvien` |
| `/sinhvien?id=<id>` | GET | Chi tiết sinh viên theo ID | `/sinhvien?id=SV001` |
| `/ctdaotao` | GET | Danh sách tất cả CTDaoTao | `/ctdaotao` |
| `/ctdaotao?makhoa=<id>` | GET | Môn học của khoa | `/ctdaotao?makhoa=CNTT` |
| `/ctdaotao?khoahoc=<year>` | GET | Môn học của khóa học | `/ctdaotao?khoahoc=2018` |
| `/ctdaotao?makhoa=<id>&khoahoc=<year>` | GET | Môn học của CTDaoTao cụ thể | `/ctdaotao?makhoa=CNTT&khoahoc=2018` |
| `/dangky` | GET | Danh sách tất cả đăng ký | `/dangky` |
| `/dangky?masv=<id>` | GET | Đăng ký của sinh viên | `/dangky?masv=SV001` |

## 🧪 Test

Truy cập `http://localhost:8081/ui.php` để test API với giao diện web.

## 📁 Cấu trúc

```text
cdslpt/
├── docker-compose.yml
├── init_databases.ps1
├── README.md
├── app/ (PHP API)
└── db/ (SQL scripts cho 3 sites + global)
```
