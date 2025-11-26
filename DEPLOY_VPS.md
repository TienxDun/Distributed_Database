# 🚀 Hướng dẫn Deploy lên VPS (DigitalOcean, AWS, etc.)

## Bước 1: Chuẩn bị VPS

### Yêu cầu

- Ubuntu 20.04+ / CentOS 8+
- RAM: 4GB+ (khuyến nghị 8GB)
- Disk: 20GB+ SSD
- Docker & Docker Compose installed

```bash
# Cài đặt Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Cài đặt Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.24.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

## Bước 2: Deploy dự án

```bash
# Clone repository
git clone https://github.com/TienxDun/Distributed_Database.git
cd Distributed_Database

# Tạo file .env
cp .env.example .env
nano .env  # Edit với password mạnh

# Khởi động services
docker-compose up -d

# Khởi tạo database
./init_databases.ps1
```

## Bước 3: Cấu hình Firewall & Reverse Proxy

```bash
# Mở ports cần thiết
sudo ufw allow 80
sudo ufw allow 443
sudo ufw allow 8080
sudo ufw allow 8081

# Cài đặt Nginx (reverse proxy)
sudo apt install nginx
```

Tạo config Nginx `/etc/nginx/sites-available/huflit-db`:

```nginx
server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://localhost:8081;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }

    location /api {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/huflit-db /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Bước 4: SSL Certificate (Let's Encrypt)

```bash
# Cài đặt Certbot
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

## ✅ URLs

- **Web App**: `http://your-domain.com/ui.php`
- **API**: `http://your-domain.com/api/`

## 💰 Chi phí ước tính

- VPS: $12-50/tháng (tùy provider & config)
