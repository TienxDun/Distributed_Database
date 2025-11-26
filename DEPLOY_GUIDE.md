# 🚀 Hướng dẫn Deploy Dự án HUFLIT Distributed Database

## 📋 Tổng quan dự án

Dự án này là hệ thống cơ sở dữ liệu phân tán với:

- **Frontend**: PHP Web UI (port 8081)
- **Backend**: PHP API (port 8080)
- **Database**: 3 SQL Server sites + MongoDB
- **Container**: Docker + Docker Compose

## 🎯 Các phương án deploy

### 💰 **TÙY CHỌN MIỄN PHÍ (Chi phí = 0)**

🎉 **CÓ THỂ!** Xem hướng dẫn chi tiết: [FREE_DEPLOY.md](FREE_DEPLOY.md)

📋 **TODO List từng bước:** [TODO_DEPLOY.md](TODO_DEPLOY.md)

#### 1. 🚂 Railway (Khuyến nghị - Hoàn toàn miễn phí)

**Free Tier**: 512MB RAM, 1GB disk, unlimited bandwidth
**Giới hạn**: Sleep after 24h inactive, cold starts
**Phù hợp**: Development, demo, testing

➡️ Xem hướng dẫn: [DEPLOY_RAILWAY.md](DEPLOY_RAILWAY.md)

#### 2. 🎨 Render (Miễn phí tốt cho Docker)

**Free Tier**: 750 hours/tháng, 512MB RAM, persistent disks
**Giới hạn**: Sleep after 15min inactive
**Phù hợp**: Production light, personal projects

➡️ Xem hướng dẫn: [DEPLOY_RENDER.md](DEPLOY_RENDER.md)

#### 3. ✈️ Fly.io (Miễn phí với shared resources)

**Free Tier**: 3 shared CPUs, 256MB RAM, 3GB disk
**Giới hạn**: Shared resources, region limits
**Phù hợp**: Global deployment, edge computing

#### 4. 🐙 Google Cloud Run (Free tier tốt)

**Free Tier**: 2M requests/tháng, 2GB egress
**Giới hạn**: Cold starts, request-based
**Phù hợp**: API-first applications

### 💳 **TÙY CHỌN TRẢ PHÍ (Khi cần production)**

#### 1. 🖥️ VPS (DigitalOcean/AWS/Linode)

**Chi phí**: $12-50/tháng
**Ưu điểm**: Full control, persistent storage, SSL miễn phí

➡️ Xem hướng dẫn: [DEPLOY_VPS.md](DEPLOY_VPS.md)

#### 2. ☁️ AWS ECS/Fargate

**Chi phí**: $20-100/tháng
**Ưu điểm**: Enterprise-grade, high availability

#### 3. 🚂 Railway Pro

**Chi phí**: $5-10/tháng
**Ưu điểm**: No cold starts, better performance

## ⚙️ Cấu hình cần thiết

### Environment Variables (`.env`)

```bash
# Copy từ .env.example
cp .env.example .env

# Edit với password mạnh (≥8 ký tự, có số, chữ hoa, ký tự đặc biệt)
MSSQL_SA_PASSWORD=YourStrongPassword123!
MONGO_PASSWORD=YourMongoPassword456!
```

### Ports cần mở

- `8080`: API Gateway
- `8081`: Web UI
- `80/443`: HTTP/HTTPS (cho reverse proxy)

## 🔧 Troubleshooting

### Database connection failed

```bash
# Check container logs
docker-compose logs mssql_global
docker-compose logs mongodb

# Restart services
docker-compose restart
```

### Web app không load

```bash
# Check PHP containers
docker-compose ps
docker-compose logs app_php
```

### Performance issues

- Tăng RAM VPS lên 8GB+
- Sử dụng SSD storage
- Cấu hình connection pooling

## 📊 Monitoring

Sau khi deploy, monitor:

- Container health: `docker stats`
- Database connections
- Response times
- Error logs

## 🔒 Security Best Practices

1. **Strong passwords** cho database
2. **SSL certificates** (Let's Encrypt free)
3. **Firewall** chỉ mở ports cần thiết
4. **Regular backups** của database
5. **Environment variables** không commit vào Git

## 💡 Khuyến nghị

- **Development/Testing**: Railway (nhanh, dễ)
- **Production**: VPS hoặc AWS (reliable, scalable)
- **Enterprise**: AWS/GCP với managed services

---

🎯 **Bắt đầu với Railway** để test nhanh, sau đó migrate sang VPS khi cần production-ready!
