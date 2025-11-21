# 🧪 Hướng Dẫn Testing

<div align="center">

**29 Test Cases for INSTEAD OF Triggers**

[![Tests](https://img.shields.io/badge/Tests-29_Passed-success)](db/test_triggers.sql)
[![SQL Server](https://img.shields.io/badge/SQL_Server-2022-red)](https://www.microsoft.com/sql-server)

</div>

---

## 🗄️ Data Fragmentation

| Site | Range | Icon |
|------|-------|------|
| **Site A** | `MaKhoa < 'M'` | 🟦 |
| **Site B** | `'M' ≤ MaKhoa < 'S'` | 🟩 |
| **Site C** | `MaKhoa ≥ 'S'` | 🟪 |

> ⚡ **Đặc biệt**: MonHoc đồng bộ 3 sites, các bảng khác phân mảnh theo site.

---

## 🚀 Chạy Test Suite

```powershell
sqlcmd -S localhost,14333 -U sa -P "Your@STROng!Pass#Word" -f 65001 -i db\test_triggers.sql
```

---

## 📋 Test Cases Overview

<table>
<tr>
<td width="50%">

### ✅ CRUD Tests (26 tests)

| Test Suite | Count | Icon |
|------------|-------|------|
| **TEST 1: KHOA_GLOBAL** | 6 | 🏫 |
| **TEST 2: MONHOC_GLOBAL** | 3 | 📚 |
| **TEST 3: SINHVIEN_GLOBAL** | 5 | 👨‍🎓 |
| **TEST 4: CTDAOTAO_GLOBAL** | 6 | 📋 |
| **TEST 5: DANGKY_GLOBAL** | 6 | ✍️ |

</td>
<td width="50%">

### 🔒 Validation Tests (4 tests)

| Test Suite | Count | Icon |
|------------|-------|------|
| **TEST 6: RÀNG BUỘC** | 3 | 🔒 |
| **TEST 7: CLEANUP** | 1 | 🧹 |

</td>
</tr>
</table>

---

## 🔬 Chi Tiết Test Cases

### 🏫 TEST 1: KHOA_GLOBAL (6 tests)

```diff
+ Insert Site A/B/C theo MaKhoa
+ Update TenKhoa
+ Delete validation
- Duplicate key error (expected)
```

### 📚 TEST 2: MONHOC_GLOBAL (3 tests)

```diff
+ Insert → sync 3 sites
+ Update → sync 3 sites
- Duplicate error (expected)
```

### 👨‍🎓 TEST 3: SINHVIEN_GLOBAL (5 tests)

```diff
+ Insert Site A/B
+ Update (không đổi khoa)
- Duplicate error (expected)
- FK validation (expected)
```

### 📋 TEST 4: CTDAOTAO_GLOBAL (6 tests)

```diff
+ Insert Site A/B
- Duplicate error (expected)
- FK validation MaKhoa (expected)
- FK validation MaMH (expected)
- No UPDATE allowed (expected)
```

### ✍️ TEST 5: DANGKY_GLOBAL (6 tests)

```diff
+ Insert Site A/B (với/không điểm)
+ Update DiemThi only
- Duplicate error (expected)
- FK validation MaSV (expected)
- FK validation MaMon (expected)
```

### 🔒 TEST 6: RÀNG BUỘC (3 tests)

```diff
- Không xóa Khoa có SinhVien (expected)
- Không xóa MonHoc có trong CTĐT (expected)
- Không xóa SinhVien có DangKy (expected)
```

### 🧹 TEST 7: CLEANUP (1 test)

```sql
✓ Xóa đúng thứ tự: DangKy → CTĐT → SinhVien → MonHoc → Khoa
```

---

## 📊 Kết Quả Test

### Expected Results

| Status | Output | Meaning |
|--------|--------|----------|
| ✅ PASS | `(X rows affected)` | Thao tác thành công |
| ✅ PASS | `✓ Đúng: <error message>` | Lỗi như mong đợi |
| ❌ FAIL | `❌ LỖI: <description>` | Không báo lỗi |

---

## 📝 Template Test SQL

### 🏫 Khoa
```sql
-- Site A (MaKhoa < 'M')
INSERT INTO Khoa_Global VALUES ('ABC', N'Khoa ABC');

-- Site B ('M' <= MaKhoa < 'S')
INSERT INTO Khoa_Global VALUES ('MNO', N'Khoa MNO');

-- Site C (MaKhoa >= 'S')
INSERT INTO Khoa_Global VALUES ('XYZ', N'Khoa XYZ');
```

### 📚 MonHoc (Sync 3 Sites)
```sql
INSERT INTO MonHoc_Global VALUES ('MH001', N'Toán Cao Cấp');
UPDATE MonHoc_Global SET TenMH = N'Toán A1' WHERE MaMH = 'MH001';
DELETE FROM MonHoc_Global WHERE MaMH = 'MH001';
```

### 👨‍🎓 SinhVien
```sql
INSERT INTO SinhVien_Global VALUES ('SV001', N'Nguyễn Văn A', 'CNTT', 2024);
UPDATE SinhVien_Global SET HoTen = N'Nguyễn Văn B' WHERE MaSV = 'SV001';
```

### ✍️ DangKy
```sql
-- Insert without DiemThi
INSERT INTO DangKy_Global VALUES ('SV001', 'MH001', NULL);

-- Update DiemThi later
UPDATE DangKy_Global SET DiemThi = 8.5 
WHERE MaSV = 'SV001' AND MaMon = 'MH001';
```

---

## 🔧 Troubleshooting

<table>
<tr>
<td width="33%">

### ❌ Duplicate Key
```powershell
# Solution
sqlcmd -i db\test_triggers.sql
# Chạy TEST 7 cleanup
```

</td>
<td width="33%">

### ❌ Linked Server
```powershell
# Solution
sqlcmd -i db\global\init.sql
# Tạo lại linked servers
```

</td>
<td width="34%">

### ❌ FK Violation
```sql
-- Solution
-- Thứ tự INSERT:
1. Khoa
2. MonHoc
3. SinhVien/CTĐT
4. DangKy
```

</td>
</tr>
</table>

---

<div align="center">

**[⬅️ Back to README](README.md)** | **[🏗️ Architecture](ARCHITECTURE.md)** | **[📝 CRUD Guide](CRUD_GUIDE.md)**

---

**Made with ❤️ for HUFLIT**

</div>
