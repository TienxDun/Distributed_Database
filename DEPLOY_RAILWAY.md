# 🚀 Hướng dẫn Deploy lên Railway

## Bước 1: Chuẩn bị

```bash
# Clone repository
git clone https://github.com/TienxDun/Distributed_Database.git
cd Distributed_Database

# Copy file env
cp .env.example .env
# Edit .env với password mạnh
```

## Bước 2: Deploy lên Railway

1. Truy cập [Railway.app](https://railway.app)
2. Đăng ký tài khoản
3. Click "New Project" → "Deploy from GitHub"
4. Connect GitHub repo
5. Railway sẽ tự động detect docker-compose.yml

## Bước 3: Cấu hình Environment

Trong Railway dashboard:

- Variables → Add:
  - `MSSQL_SA_PASSWORD`: [password mạnh]
  - `MONGO_PASSWORD`: [password khác]

## Bước 4: Truy cập

Sau khi deploy xong, Railway sẽ cung cấp domain:

- **Web App**: `https://your-app.railway.app/ui.php`
- **API**: `https://your-app.railway.app` (port 8080)

## ⚠️ Lưu ý

- Railway free tier có limits, upgrade nếu cần
- Database sẽ mất khi redeploy (dùng persistent storage)
