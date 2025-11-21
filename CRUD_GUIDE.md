# 📝 Hướng Dẫn CRUD Operations

<div align="center">

**Full CRUD Interface for HUFLIT Distributed Database**

[![UI](https://img.shields.io/badge/Web-UI-blue)](http://localhost:8081/ui.php)
[![API](https://img.shields.io/badge/REST-API-green)](http://localhost:8080)

</div>

---

## 📦 5 Modules

| Module | Icon | Tính năng đặc biệt |
|--------|------|--------------------|
| **Khoa** | 🏫 | Auto routing to sites |
| **Môn Học** | 📚 | **Sync 3 sites tự động** |
| **Sinh Viên** | 👨‍🎓 | **Cross-site migration** |
| **CTĐT** | 📋 | Composite key |
| **Đăng Ký** | ✍️ | Smart routing |

---

## 🔧 Chi tiết Operations

### 🏫 1. KHOA (Quản lý Khoa)

<table>
<tr>
<td width="33%">

#### ➕ CREATE
```sql
MaKhoa + TenKhoa
↓
Auto route to Site A/B/C
```

</td>
<td width="33%">

#### ✏️ UPDATE
```sql
TenKhoa only
⚠️ MaKhoa không đổi
```

</td>
<td width="34%">

#### 🗑️ DELETE
```sql
❌ Có SinhVien
❌ Có CTĐT
```

</td>
</tr>
</table>

---

### 📚 2. MÔN HỌC (Đồng bộ 3 Sites)

<table>
<tr>
<td width="33%">

#### ➕ CREATE
```sql
MaMH + TenMH
↓
✅ Sync 3 sites
```

</td>
<td width="33%">

#### ✏️ UPDATE
```sql
TenMH
↓
✅ Update 3 sites
```

</td>
<td width="34%">

#### 🗑️ DELETE
```sql
❌ Có trong CTĐT
❌ Có DangKy
```

</td>
</tr>
</table>

---

### 👨‍🎓 3. SINH VIÊN (Cross-Site Migration)

<table>
<tr>
<td width="33%">

#### ➕ CREATE
```sql
MaSV + HoTen +
MaKhoa + KhoaHoc
↓
Route to site
```

</td>
<td width="33%">

#### ✏️ UPDATE
```sql
Đổi MaKhoa
↓
🚚 Di chuyển sites
⚠️ Cần xóa DangKy
```

</td>
<td width="34%">

#### 🗑️ DELETE
```sql
❌ Có DangKy
```

</td>
</tr>
</table>

---

### 📋 4. CTĐT (Chương Trình Đào Tạo)

<table>
<tr>
<td width="33%">

#### ➕ CREATE
```sql
MaKhoa + KhoaHoc + MaMH
(Composite PK)
```

</td>
<td width="33%">

#### ❌ UPDATE
```diff
- Không hỗ trợ
+ Xóa + Thêm mới
```

</td>
<td width="34%">

#### 🗑️ DELETE
```sql
✅ Xóa môn khỏi CTĐT
```

</td>
</tr>
</table>

---

### ✍️ 5. ĐĂNG KÝ (Smart Routing)

<table>
<tr>
<td width="33%">

#### ➕ CREATE
```sql
MaSV + MaMon + DiemThi?
↓
Auto route via
SinhVien_Global
```

</td>
<td width="33%">

#### ✏️ UPDATE
```sql
DiemThi only
⚠️ Không đổi
MaSV/MaMon
```

</td>
<td width="34%">

#### 🗑️ DELETE
```sql
✅ Hủy đăng ký
```

</td>
</tr>
</table>

---

## 🔍 Truy Vấn Toàn Cục

| # | Truy vấn | Input |
|---|----------|-------|
| 1️⃣ | Môn học đã đạt ≥5 | MaSV |
| 2️⃣ | Khóa học của khoa | Tên/Mã Khoa |
| 3️⃣ | Môn học bắt buộc | MaSV |
| 4️⃣ | SV đủ điều kiện tốt nghiệp | - |

---

## ⚡ Test Nhanh

### 🏫 Khoa
```bash
1. ➕ Thêm: CNTT - Công nghệ thông tin
2. ✏️ Sửa: Đổi tên → "CNTT & Truyền thông"
3. 🗑️ Xóa: Xóa khoa CNTT
```

### 📚 MonHoc
```bash
1. ➕ Thêm: MH999 - Môn test
2. ✏️ Sửa: Đổi tên → "Môn đã sửa"
3. 🔍 Verify: Kiểm tra sync cả 3 sites
4. 🗑️ Xóa: Xóa MH999
```

### 👨‍🎓 SinhVien
```bash
1. ➕ Thêm: 25DH999999, Nguyễn Test, CNTT, 2025
2. ✏️ Sửa: Chuyển khoa CNTT → NN (cross-site)
3. 🔍 Verify: Kiểm tra di chuyển sites
4. 🗑️ Xóa: Xóa sinh viên
```

### ✍️ DangKy
```bash
1. ➕ Thêm: Đăng ký môn (DiemThi = NULL)
2. ✏️ Sửa: Cập nhật DiemThi = 8.5
3. 🗑️ Xóa: Hủy đăng ký
```

---

## ⚠️ Xử Lý Lỗi

| Lỗi | Nguyên nhân | Giải pháp |
|-----|-------------|------------|
| ❌ "Mã khoa không tồn tại" | Thiếu foreign key | ➕ Tạo khoa trước |
| ❌ "Không thể xóa" | Còn dữ liệu con | 🗑️ Xóa dữ liệu con trước |
| ❌ "Không chuyển khoa" | Còn đăng ký | 🗑️ Hủy đăng ký trước |
| ❌ "Connection refused" | Containers down | 🐳 `docker ps` |

---

## 🔗 Links

<div align="center">

[![UI](https://img.shields.io/badge/🖥️_Web_UI-http://localhost:8081/ui.php-blue?style=for-the-badge)](http://localhost:8081/ui.php)

[![API](https://img.shields.io/badge/🔌_REST_API-http://localhost:8080-green?style=for-the-badge)](http://localhost:8080)

**[⬅️ Back to README](README.md)** | **[🏗️ Architecture](ARCHITECTURE.md)** | **[🧪 Testing](TEST_GUIDE.md)**

</div>
