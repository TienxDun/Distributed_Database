# 🎨 Hướng dẫn Deploy lên Render (MIỄN PHÍ)

## Tổng quan

Render cung cấp **750 giờ miễn phí mỗi tháng** cho web services, rất phù hợp với dự án Docker của bạn.

## Bước 1: Chuẩn bị

```bash
# Clone repository
git clone https://github.com/TienxDun/Distributed_Database.git
cd Distributed_Database

# Tạo file .env
cp .env.example .env
# Edit với password mạnh
```

## Bước 2: Deploy lên Render

### 2.1 Tạo tài khoản

1. Truy cập [render.com](https://render.com)
2. Đăng ký tài khoản (có thể dùng GitHub)

### 2.2 Deploy Web Service

1. Click "New" → "Web Service"
2. Connect GitHub repository
3. Cấu hình:
   - **Name**: `huflit-db-web`
   - **Environment**: `Docker`
   - **Region**: `Singapore` (gần Việt Nam nhất)
   - **Branch**: `main`
   - **Root Directory**: `./` (root)

### 2.3 Environment Variables

Trong phần Environment, thêm:

```bash
MSSQL_SA_PASSWORD=YourStrongPassword123!
MONGO_PASSWORD=YourMongoPassword456!
```

### 2.4 Advanced Settings

```bash
Docker Command: docker-compose up
Health Check Path: /ui.php
```

## Bước 3: Deploy Databases

### 3.1 MongoDB (Free tier)

1. New → Managed Database → MongoDB
2. Name: `huflit-mongo`
3. Database: `huflit_logs`
4. Copy connection string

### 3.2 SQL Server (Có thể cần upgrade)

**Lưu ý**: Render không hỗ trợ SQL Server free. Có 2 lựa chọn:

**Option A: Sử dụng PostgreSQL thay thế**

- Tạo PostgreSQL database trên Render
- Modify code để dùng PostgreSQL

#### Option B: Sử dụng Railway cho SQL Server

- Deploy SQL Server lên Railway (free)
- Web app lên Render

## Bước 4: Cập nhật cấu hình

Nếu dùng Railway cho SQL Server:

```bash
# Trong Render environment variables
DB_HOST=your-railway-sql-server-host
DB_PORT=1433
MONGO_HOST=your-render-mongo-host
MONGO_PORT=27017
```

## ✅ URLs sau deploy

- **Web App**: `https://huflit-db-web.onrender.com/ui.php`
- **API**: `https://huflit-db-web.onrender.com/api/`

## ⚠️ Lưu ý quan trọng

### Free Tier Limits

- **750 giờ/tháng** (~31 ngày nếu chạy 24/7)
- **Sleep after 15 minutes** không hoạt động
- **512MB RAM** (có thể không đủ cho 3 SQL Server + MongoDB)

### Giải pháp tối ưu

1. **Railway**: SQL Server containers (free)
2. **Render**: Web app + MongoDB (free)
3. **Total cost**: $0

### Nếu hết free hours

- App sẽ sleep
- Truy cập lại để wake up
- Hoặc upgrade lên paid plan ($7/tháng)

## 🚀 Quick Start

1. Railway: Deploy SQL Server (5 phút)
2. Render: Deploy Web + MongoDB (10 phút)
3. Cấu hình environment variables
4. Access your app!

**Thời gian setup: ~15 phút**
