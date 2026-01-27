FROM php:8.4-cli

# Cài đặt các thư viện hệ thống cho Postgres và các công cụ cần thiết
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git zip unzip \
    && rm -rf /var/lib/apt/lists/*

# Cài đặt extension pdo_pgsql
RUN docker-php-ext-install pdo pdo_pgsql

# Cài đặt MongoDB PHP driver
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ nội dung thư mục app vào root của container
# Điều này giúp các lệnh trong render.yaml (như -t public) chạy chính xác
COPY app/ .

# Copy file .env vào để ứng dụng có thể đọc cấu hình (nếu cần dùng file thay vì env vars)
COPY .env .env

# Expose port 80 cho Render
EXPOSE 80
