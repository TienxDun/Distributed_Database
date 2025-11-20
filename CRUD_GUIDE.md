# Hướng Dẫn Sử Dụng CRUD Interface

## 🎯 Tổng Quan

UI mới đã được nâng cấp với đầy đủ chức năng CRUD (Create, Read, Update, Delete) cho tất cả 5 modules:
1. **Khoa** - Quản lý khoa
2. **Môn Học** - Quản lý môn học (đồng bộ 3 sites)
3. **Sinh Viên** - Quản lý sinh viên (cho phép chuyển khoa)
4. **Chương Trình Đào Tạo** - Quản lý CTĐT
5. **Đăng Ký** - Quản lý đăng ký học phần

---

## 📋 Chức Năng Từng Module

### 1. KHOA (Quản lý Khoa)

#### ➕ Thêm Khoa Mới
- Click nút **"Thêm Khoa Mới"**
- Nhập **Mã Khoa** (tối đa 10 ký tự, ví dụ: CNTT, NN, LUAT)
- Nhập **Tên Khoa** (ví dụ: Công nghệ thông tin)
- Click **"Lưu"**

**Lưu ý:** 
- Mã Khoa < 'M' → lưu vào Site A
- Mã Khoa >= 'M' và < 'S' → lưu vào Site B  
- Mã Khoa >= 'S' → lưu vào Site C

#### ✏️ Sửa Khoa
- Click nút **"✏️ Sửa"** trên hàng muốn sửa
- Chỉ có thể sửa **Tên Khoa**
- **Mã Khoa không được thay đổi**
- Click **"Lưu"**

#### 🗑️ Xóa Khoa
- Click nút **"🗑️ Xóa"** trên hàng muốn xóa
- Xác nhận xóa
- **Không thể xóa nếu:**
  - Còn sinh viên thuộc khoa này
  - Còn chương trình đào tạo của khoa này

---

### 2. MÔN HỌC (Quản lý Môn Học)

#### ➕ Thêm Môn Học Mới
- Click **"Thêm Môn Học Mới"**
- Nhập **Mã Môn Học** (tối đa 10 ký tự)
- Nhập **Tên Môn Học**
- Click **"Lưu"**

**Đặc biệt:** Môn học sẽ được **đồng bộ tự động trên cả 3 sites**

#### ✏️ Sửa Môn Học
- Click **"✏️ Sửa"**
- Sửa **Tên Môn Học**
- Thay đổi sẽ được **cập nhật đồng bộ trên cả 3 sites**

#### 🗑️ Xóa Môn Học
- Click **"🗑️ Xóa"**
- **Không thể xóa nếu:**
  - Môn học đang có trong CTĐT
  - Có sinh viên đã đăng ký môn này

---

### 3. SINH VIÊN (Quản lý Sinh Viên)

#### ➕ Thêm Sinh Viên Mới
- Click **"Thêm Sinh Viên Mới"**
- Nhập:
  - **Mã Sinh Viên** (20 ký tự, format: xxDHxxxxxx)
  - **Họ Tên**
  - **Mã Khoa** (ví dụ: CNTT, NN)
  - **Khóa Học** (năm 2015-2030)
- Click **"Lưu"**

#### ✏️ Sửa Sinh Viên
- Click **"✏️ Sửa"**
- Có thể sửa: Họ Tên, Mã Khoa, Khóa Học
- **Đặc biệt:** Khi đổi **Mã Khoa**, sinh viên sẽ được **di chuyển giữa các sites**
- **Lưu ý:** Không thể chuyển khoa nếu sinh viên có môn học đã đăng ký

#### 🗑️ Xóa Sinh Viên
- Click **"🗑️ Xóa"**
- **Không thể xóa nếu:** Còn dữ liệu đăng ký môn học

---

### 4. CHƯƠNG TRÌNH ĐÀO TẠO

#### ➕ Thêm Môn Vào CTĐT
- Click **"Thêm Môn Vào CTĐT"**
- Nhập:
  - **Mã Khoa**
  - **Khóa Học**
  - **Mã Môn Học**
- Click **"Lưu"**

**Lưu ý:** 
- Cả 3 trường là composite key
- Phải kiểm tra Mã Khoa và Mã Môn Học tồn tại trước

#### ❌ Không Có Chức Năng Sửa
- CTĐT không cho phép sửa
- Nếu cần thay đổi: **Xóa và thêm mới**

#### 🗑️ Xóa CTĐT
- Click **"🗑️ Xóa"** trên hàng tương ứng
- Xóa môn học khỏi chương trình đào tạo

---

### 5. ĐĂNG KÝ (Đăng Ký Học Phần)

#### ➕ Đăng Ký Môn Học
- Click **"Đăng Ký Môn Học"**
- Nhập:
  - **Mã Sinh Viên**
  - **Mã Môn Học**
  - **Điểm Thi** (tùy chọn, để trống nếu chưa thi)
- Click **"Lưu"**

**Hệ thống tự động:**
- Lấy Mã Khoa từ thông tin sinh viên
- Route đăng ký đến đúng site dựa trên khoa của sinh viên

#### ✏️ Cập Nhật Điểm Thi
- Click **"✏️ Cập nhật điểm"**
- Chỉ có thể sửa **Điểm Thi**
- **Không thể thay đổi:** Mã Sinh Viên, Mã Môn Học

#### 🗑️ Hủy Đăng Ký
- Click **"🗑️ Xóa"**
- Xác nhận hủy đăng ký môn học

---

## 🔍 Truy Vấn Toàn Cục

Module **"Truy Vấn Toàn Cục"** vẫn giữ nguyên 4 truy vấn đặc biệt:

1. **Môn học đã đạt ≥5** - Nhập Mã SV
2. **Các khóa học của khoa** - Nhập Tên/Mã Khoa
3. **Môn học bắt buộc** - Nhập Mã SV
4. **SV đủ điều kiện tốt nghiệp** - Không cần input

---

## 🎨 Giao Diện & Trải Nghiệm

### Thông Báo
- ✅ **Thành công:** Nền xanh lá
- ❌ **Lỗi:** Nền đỏ
- ℹ️ **Thông tin:** Nền xanh dương

### Modal Forms
- Hiển thị ở giữa màn hình
- Có nút **X** để đóng
- Click ngoài modal cũng đóng được
- Validation tự động cho các trường bắt buộc

### Bảng Dữ Liệu
- Hiển thị số lượng bản ghi
- Có nút hành động ở cột cuối
- Hover để xem hiệu ứng
- Responsive trên mobile

---

## ⚡ Kiểm Tra Nhanh

### Test CRUD Khoa
```
1. Thêm: CNTT - Công nghệ thông tin
2. Sửa: Đổi tên thành "CNTT & Truyền thông"
3. Xóa: Xóa khoa vừa tạo (nếu chưa có dữ liệu liên quan)
```

### Test CRUD Môn Học
```
1. Thêm: MH999 - Môn học test
2. Sửa: Đổi tên thành "Môn học đã sửa"
3. Xóa: Xóa môn học test
```

### Test CRUD Sinh Viên
```
1. Thêm: 25DH999999, Nguyễn Test, CNTT, 2025
2. Sửa: Đổi MaKhoa thành NN (chuyển site)
3. Xóa: Xóa sinh viên test
```

### Test CRUD Đăng Ký
```
1. Thêm: Đăng ký môn cho sinh viên
2. Sửa: Cập nhật điểm thi = 8.5
3. Xóa: Hủy đăng ký
```

---

## 🚀 Tính Năng Nổi Bật

### 1. Distributed Transactions
- Môn Học sync across 3 sites tự động
- Sinh Viên có thể chuyển khoa (move between sites)
- Đăng Ký tự động route đến đúng site

### 2. Data Integrity
- Foreign key validation trong triggers
- Prevent cascade delete khi có dữ liệu liên quan
- Composite key validation

### 3. User Experience
- Real-time alerts
- Form validation
- Loading indicators
- Error handling với messages rõ ràng
- Auto-reload data sau mỗi thao tác

### 4. Security
- SQL injection prevention qua PDO prepared statements
- Input validation
- CORS enabled cho API

---

## 🐛 Xử Lý Lỗi Thường Gặp

### Lỗi: "Mã khoa không tồn tại"
→ Tạo khoa trước khi thêm sinh viên/CTĐT

### Lỗi: "Không thể xóa vì còn dữ liệu liên quan"
→ Xóa dữ liệu con trước (sinh viên, đăng ký) rồi mới xóa cha (khoa, môn học)

### Lỗi: "Không thể chuyển khoa vì có đăng ký"
→ Hủy tất cả đăng ký môn học của sinh viên trước khi chuyển khoa

### Lỗi: "Connection refused"
→ Kiểm tra containers đang chạy: `docker ps`

---

## 📊 Workflow Khuyến Nghị

### Setup Ban Đầu
1. Tạo **Khoa** trước
2. Thêm **Môn Học**
3. Tạo **CTĐT** (liên kết Khoa-Môn Học)
4. Thêm **Sinh Viên**
5. Đăng ký **Môn Học** cho sinh viên

### Testing Triggers
1. Test **MonHoc sync**: Thêm môn → Kiểm tra cả 3 sites
2. Test **SinhVien move**: Đổi MaKhoa → Verify data moved
3. Test **Constraints**: Thử xóa khoa có sinh viên → Expect error

---

## 🎯 URL Access

- **UI Interface:** http://localhost:8081/ui.php
- **API Endpoint:** http://localhost:8080/
- **API Documentation:** http://localhost:8080/ (GET request)

---

## ✅ Checklist Hoàn Thành

- [x] CRUD forms cho 5 modules
- [x] Modal UI với validation
- [x] JavaScript handlers (create/edit/delete)
- [x] CSS styling hoàn chỉnh
- [x] Alert notifications
- [x] Auto-reload after operations
- [x] Error handling
- [x] Triggers integration
- [x] Distributed transaction support
- [x] Responsive design

---

**Chúc bạn thao tác thành công! 🎉**
