FROM php:8.3-cli

# Cài đặt các thư viện hệ thống cho Postgres, MongoDB và các công cụ cần thiết
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    ca-certificates \
    git zip unzip \
    && rm -rf /var/lib/apt/lists/*

# Cài đặt extension pdo_pgsql
RUN docker-php-ext-install pdo pdo_pgsql

# Cài đặt MongoDB PHP driver
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ dự án vào container
COPY . .

# Copy file .env vào để ứng dụng có thể đọc cấu hình
# Lưu ý: Trên Render nên ưu tiên dùng Environment Variables trong Dashboard
# COPY .env .env (Already copied by COPY . .)

# Expose port (Render sẽ tự động phát hiện port từ EXPOSE hoặc dùng biến $PORT)
EXPOSE 80

# Chạy server với port từ biến môi trường của Render (mặc định 80)
# Việc đưa CMD trực tiếp vào Dockerfile giúp tránh lỗi Render chạy nhầm chế độ Interactive (php -a)
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-80} -t public public/router.php"]
