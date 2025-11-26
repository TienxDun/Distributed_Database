# ✅ TODO LIST: DEPLOY DỰ ÁN HUFLIT DISTRIBUTED DATABASE

## 🎯 **OVERVIEW**
- **Mục tiêu**: Deploy dự án lên web online với chi phí = 0
- **Thời gian ước tính**: 15-20 phút
- **Công cụ cần**: GitHub account, Railway account, Render account
- **Kết quả**: Website online với domain miễn phí

---

## 📋 **PHASE 1: CHUẨN BỊ (5 phút)**

### 🔧 **1.1 Chuẩn bị Repository**
- [ ] Push code lên GitHub (nếu chưa có)
- [ ] Kiểm tra file `docker-compose.yml` có tồn tại
- [ ] Kiểm tra file `.env.example` có tồn tại
- [ ] Tạo file `.env` từ template:
  ```bash
  cp .env.example .env
  # Edit .env với password mạnh
  ```

### 🔑 **1.2 Tạo Environment Variables**
- [ ] Tạo password mạnh cho SQL Server (≥8 ký tự, số, chữ hoa, ký tự đặc biệt)
- [ ] Tạo password cho MongoDB
- [ ] Cập nhật file `.env`:
  ```bash
  MSSQL_SA_PASSWORD=YourStrongPassword123!
  MONGO_PASSWORD=YourMongoPassword456!
  ```

### 🧪 **1.3 Test Local (Tùy chọn)**
- [ ] Chạy `docker-compose up -d` locally
- [ ] Chạy `./init_databases.ps1` để khởi tạo DB
- [ ] Test truy cập `localhost:8081/ui.php`
- [ ] Stop containers: `docker-compose down`

---

## 🚂 **PHASE 2: DEPLOY RAILWAY (SQL Server) - 5 phút**

### 📝 **2.1 Tạo Railway Account**
- [ ] Truy cập [railway.app](https://railway.app)
- [ ] Đăng ký tài khoản (có thể dùng GitHub)
- [ ] Verify email

### 🔗 **2.2 Connect GitHub Repository**
- [ ] Click "New Project" → "Deploy from GitHub"
- [ ] Authorize Railway với GitHub
- [ ] Tìm và select repository `Distributed_Database`
- [ ] Click "Deploy"

### ⚙️ **2.3 Cấu hình Environment Variables**
- [ ] Trong Railway dashboard, vào project
- [ ] Tab "Variables"
- [ ] Add variables:
  - `MSSQL_SA_PASSWORD` = [password từ .env]
  - `MONGO_PASSWORD` = [password từ .env]
- [ ] Click "Deploy" để apply changes

### ✅ **2.4 Verify Railway Deployment**
- [ ] Chờ Railway build và deploy (2-3 phút)
- [ ] Check logs để đảm bảo không có lỗi
- [ ] Copy Railway domain (ví dụ: `huflit-db.railway.app`)
- [ ] Test truy cập domain (sẽ thấy lỗi vì chưa có web app)

---

## 🎨 **PHASE 3: DEPLOY RENDER (Web App + MongoDB) - 10 phút**

### 📝 **3.1 Tạo Render Account**
- [ ] Truy cập [render.com](https://render.com)
- [ ] Đăng ký tài khoản (có thể dùng GitHub)
- [ ] Verify email

### 🍃 **3.2 Deploy MongoDB (2 phút)**
- [ ] Click "New" → "Managed Database"
- [ ] Select "MongoDB"
- [ ] Name: `huflit-mongo`
- [ ] Database: `huflit_logs`
- [ ] Environment: Free
- [ ] Click "Create Database"
- [ ] Copy connection string (ví dụ: `mongodb://admin:password@host:27017/huflit_logs`)

### 🌐 **3.3 Deploy Web Service (8 phút)**
- [ ] Click "New" → "Web Service"
- [ ] Connect GitHub repository `Distributed_Database`
- [ ] Cấu hình:
  - **Name**: `huflit-db-web`
  - **Environment**: `Docker`
  - **Region**: `Singapore` (gần Việt Nam nhất)
  - **Branch**: `main`
  - **Root Directory**: `./` (root)

### ⚙️ **3.4 Cấu hình Environment Variables**
- [ ] Trong Render dashboard, vào web service
- [ ] Tab "Environment"
- [ ] Add variables:
  ```
  DB_HOST=your-railway-domain.railway.app
  DB_PORT=1433
  DB_NAME=HUFLIT
  DB_USER=sa
  DB_PASS=YourStrongPassword123!
  MONGO_HOST=your-mongo-host.render.com
  MONGO_PORT=27017
  MONGO_USER=admin
  MONGO_PASSWORD=YourMongoPassword456!
  ```
- [ ] Click "Save Changes" → Auto redeploy

---

## 🔗 **PHASE 4: KẾT NỐI VÀ TEST (5 phút)**

### 🔗 **4.1 Cập nhật Railway Environment (nếu cần)**
- [ ] Trong Railway, add thêm variables:
  ```
  MONGO_HOST=your-mongo-host.render.com
  MONGO_PORT=27017
  MONGO_USER=admin
  MONGO_PASSWORD=YourMongoPassword456!
  ```

### 🧪 **4.2 Test Connections**
- [ ] Chờ Render deploy xong (3-5 phút)
- [ ] Test truy cập: `https://huflit-db-web.onrender.com/ui.php`
- [ ] Kiểm tra có thể load trang chủ
- [ ] Test đăng nhập database (thêm/xóa/sửa dữ liệu)

### 📊 **4.3 Test Full Features**
- [ ] Test CRUD operations (Khoa, Môn học, Sinh viên, etc.)
- [ ] Test Global Queries
- [ ] Test Logs page: `/logs.php`
- [ ] Test Stats page: `/stats.php`
- [ ] Test Auto-refresh functionality

---

## 🎉 **PHASE 5: HOÀN THÀNH VÀ MAINTAIN**

### ✅ **5.1 Verify Everything Works**
- [ ] Tất cả pages load thành công
- [ ] Database operations hoạt động
- [ ] No errors in browser console
- [ ] No errors in Railway/Render logs

### 📝 **5.2 Document URLs**
- [ ] **Main App**: `https://huflit-db-web.onrender.com/ui.php`
- [ ] **API**: `https://huflit-db-web.onrender.com/api/`
- [ ] **Logs**: `https://huflit-db-web.onrender.com/logs.php`
- [ ] **Stats**: `https://huflit-db-web.onrender.com/stats.php`
- [ ] **Railway Dashboard**: Link để monitor SQL Server
- [ ] **Render Dashboard**: Link để monitor Web + MongoDB

### 🔄 **5.3 Setup Monitoring**
- [ ] Check Railway logs định kỳ
- [ ] Check Render logs định kỳ
- [ ] Monitor free tier usage
- [ ] Setup alerts nếu cần

### 💤 **5.4 Handle Sleep Mode**
- [ ] Hiểu về sleep mode:
  - Railway: Sleep after 24h inactive
  - Render: Sleep after 15min inactive
- [ ] Cách wake up: Truy cập URL
- [ ] Thời gian wake up: 30-60 giây

---

## 🚨 **TROUBLESHOOTING CHECKLIST**

### Nếu Railway deploy fail:
- [ ] Check GitHub repository permissions
- [ ] Verify docker-compose.yml syntax
- [ ] Check environment variables format
- [ ] Review Railway build logs

### Nếu Render deploy fail:
- [ ] Check Dockerfile exists
- [ ] Verify environment variables
- [ ] Check connection strings
- [ ] Review Render build logs

### Nếu database connection fail:
- [ ] Verify Railway domain in Render env vars
- [ ] Check MongoDB connection string
- [ ] Test network connectivity
- [ ] Check firewall settings

### Nếu app bị sleep:
- [ ] Access URL để wake up
- [ ] Wait 30-60 seconds
- [ ] Test functionality

---

## 📊 **FREE TIER LIMITS REMINDER**

| Service | Limit | Action khi hết |
|---------|-------|----------------|
| **Railway** | 512MB RAM, 1GB disk | Sleep 24h, upgrade $5/tháng |
| **Render Web** | 750h/tháng | Sleep 15min, upgrade $7/tháng |
| **Render MongoDB** | 512MB storage | Free forever |

---

## 🎯 **NEXT STEPS**

### Khi cần production features:
- [ ] Upgrade Railway: $5/tháng (no sleep)
- [ ] Upgrade Render: $7/tháng (no sleep)
- [ ] Add custom domain
- [ ] Setup monitoring alerts
- [ ] Backup strategies

### Khi cần scale:
- [ ] Load balancer setup
- [ ] Database optimization
- [ ] CDN for static assets
- [ ] Monitoring & logging

---

## 📞 **SUPPORT**

Nếu gặp vấn đề:
1. Check logs trong Railway/Render dashboard
2. Verify environment variables
3. Test connections manually
4. Check free tier limits
5. Review this checklist again

**🎉 CHÚC BẠN THÀNH CÔNG! Dự án của bạn sẽ online với $0 chi phí!**

