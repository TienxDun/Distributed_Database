# 🚀 Hướng dẫn Triển khai Hệ thống Online (Master Guide)

Hệ thống của bạn đã được tối ưu hóa để chạy mượt mà trên các dịch vụ Cloud miễn phí. Dưới đây là lộ trình chi tiết để đưa dự án lên mạng.

---

## 1. Chuẩn bị Cơ sở dữ liệu (Database Cloud)

### A. PostgreSQL (Dữ liệu phân tán) - [Neon.tech](https://neon.tech)
1. Đăng ký tài khoản tại **Neon.tech**.
2. Tạo project mới (Ví dụ: `huflit-distributed-db`).
3. Trong tab **Dashboard**, sao chép các thông tin kết nối (Host, User, Password, Database).
4. **Quan trọng**: Truy cập vào mục **SQL Editor** trên Neon và chạy lần lượt nội dung 3 file SQL (có trong thư mục `database/global/` của project):
   - `init_postgres.sql`: Khởi tạo cấu trúc bảng và view.
   - `triggers_postgres.sql`: Cài đặt bộ não (Trigger) xử lý phân tán.
   - `seed_postgres.sql`: Nạp dữ liệu mẫu để demo.

### B. MongoDB (Audit & Stats) - [MongoDB Atlas](https://www.mongodb.com/cloud/atlas)
1. Tạo một Cluster miễn phí trên **MongoDB Atlas**.
2. Tạo Database User (Lưu lại username/password).
3. Trong mục **Network Access**, chọn "Add IP Address" -> **Allow Access From Anywhere** (0.0.0.0/0).
4. Lấy **Connection String** (Dạng `mongodb+srv://...`).

---

## 2. Triển khai Ứng dụng (Web Hosting) - [Render.com](https://render.com)

1. Đẩy code của bạn lên một repository **GitHub** (Chế độ Private hoặc Public đều được).
2. Tại Render, chọn **New +** -> **Web Service**.
3. Kết nối với repo GitHub vừa tạo.
4. Render sẽ tự động đọc file `render.yaml`. Bạn chỉ cần điền các **Environment Variables** sau:

| Biến môi trường | Ý nghĩa |
| :--- | :--- |
| `DB_HOST` | Host từ Neon.tech |
| `DB_NAME` | Thường là `neondb` |
| `DB_USER` | Username từ Neon |
| `DB_PASS` | Password từ Neon |
| `MONGO_URI` | Connection String từ Atlas |

5. Nhấn **Deploy Web Service** và đợi khoảng 2-3 phút.

---

## 3. Các kịch bản Demo ấn tượng
Để buổi thuyết trình đạt hiệu quả cao, bạn nên thực hiện theo các bước sau:
1. **Reset Hệ thống**: Vào menu **Quản trị** -> Nhấn **Reset Database** (Database sẽ sạch bóng).
2. **Chứng minh Phân tán**: Thêm 1 sinh viên mới -> Dùng **Site Explorer** để chỉ ra dữ liệu chỉ nằm ở 1 Site vật lý duy nhất.
3. **Chứng minh Thống kê**: Quay lại trang **Statistics**, biểu đồ sẽ cập nhật thời gian thực dựa trên MongoDB Atlas.

---
> [!TIP]
> **Mẹo nhỏ**: Vì sử dụng gói Render miễn phí, ứng dụng sẽ tạm nghỉ sau 15 phút không hoạt động. Nếu bạn chuẩn bị thuyết trình, hãy truy cập vào web trước khoảng 1 phút để hệ thống "thức dậy".

---
**Chúc bạn có một buổi Demo thành công rực rỡ!** 🎓🌟

