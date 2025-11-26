# 💰 DEPLOY MIỄN PHÍ - Chi phí = 0

## 🎯 Giải pháp tối ưu cho dự án của bạn

Dự án Docker với SQL Server + MongoDB + PHP có thể deploy **hoàn toàn miễn phí** bằng cách kết hợp nhiều platform:

### 🚀 **KẾ HOẠCH DEPLOY TỐI ƯU**

| Component | Platform | Cost | Setup Time |
|-----------|----------|------|------------|
| **SQL Server** (3 sites) | Railway | **$0** | 5 phút |
| **Web App** (PHP) | Render | **$0** | 10 phút |
| **MongoDB** | Render | **$0** | 2 phút |
| **Domain** | Railway/Render | **$0** | Auto |

**Tổng chi phí: $0** ✅

---

## 📋 **HƯỚNG DẪN CHI TIẾT**

### Bước 1: Railway - SQL Server (5 phút)
```bash
# 1. Truy cập railway.app
# 2. Connect GitHub repo
# 3. Railway tự detect docker-compose.yml
# 4. Add environment variables
# 5. Deploy - DONE!
```

**URL**: `https://your-app.railway.app`

### Bước 2: Render - Web App + MongoDB (12 phút)

#### 2.1 Deploy Web App
```bash
# 1. Truy cập render.com
# 2. New → Web Service
# 3. Connect GitHub repo
# 4. Environment: Docker
# 5. Add environment variables (point to Railway SQL Server)
```

#### 2.2 Deploy MongoDB
```bash
# 1. New → Managed Database → MongoDB
# 2. Free tier
# 3. Copy connection string
```

**URL**: `https://your-app.onrender.com/ui.php`

---

## ⚙️ **CẤU HÌNH ENVIRONMENT VARIABLES**

### Railway (SQL Server):
```bash
MSSQL_SA_PASSWORD=YourStrongPassword123!
MONGO_PASSWORD=YourMongoPassword456!
```

### Render (Web App):
```bash
DB_HOST=your-railway-sql-server.railway.app
DB_PORT=1433
DB_NAME=HUFLIT
DB_USER=sa
DB_PASS=YourStrongPassword123!
MONGO_HOST=your-mongo.onrender.com
MONGO_PORT=27017
MONGO_USER=admin
MONGO_PASSWORD=YourMongoPassword456!
```

---

## 📊 **FREE TIER LIMITS**

| Platform | Limit | Notes |
|----------|-------|-------|
| **Railway** | 512MB RAM, 1GB disk | Sleep after 24h inactive |
| **Render** | 750 hours/tháng (~31 ngày) | Sleep after 15min inactive |
| **Render MongoDB** | 512MB storage | Free forever |

---

## ⚠️ **LƯU Ý QUAN TRỌNG**

### Khi nào app sẽ sleep:
- **Railway**: Sau 24h không hoạt động
- **Render**: Sau 15 phút không hoạt động

### Cách wake up:
- Truy cập URL → App tự động wake up
- Thời gian: 30-60 giây

### Nếu cần 24/7:
- Railway Pro: $5/tháng
- Render Paid: $7/tháng

---

## 🚀 **QUICK START (15 phút)**

1. **Railway**: Deploy SQL Server containers ✅
2. **Render**: Deploy Web App + MongoDB ✅
3. **Config**: Environment variables ✅
4. **Test**: Access your app ✅

**🎉 Hoàn thành! Dự án online với $0 chi phí!**

---

## 🔧 **TROUBLESHOOTING**

### Lỗi kết nối database:
```bash
# Check Railway logs
# Verify environment variables
# Test connection strings
```

### App bị sleep:
- Truy cập URL để wake up
- Hoặc upgrade plan

### Hết free hours:
- Render: Reset hàng tháng
- Railway: Có thể upgrade

---

## 💡 **ALTERNATIVES NẾU CẦN**

Nếu không dùng Railway + Render:

### Tùy chọn A: Chỉ Railway
- Tất cả trên Railway (free)
- Giới hạn RAM có thể không đủ

### Tùy chọn B: Chỉ Render
- SQL Server → PostgreSQL (free)
- Cần modify code

### Tùy chọn C: Fly.io
- Global deployment
- 3 shared CPUs free

---

**🎯 Khuyến nghị: Railway + Render cho kết quả tốt nhất với $0 chi phí!**
