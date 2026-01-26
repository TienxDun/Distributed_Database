FROM php:8.4-cli

# System dependencies for Postgres and general tools
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git zip unzip \
    && rm -rf /var/lib/apt/lists/*

# Install pdo_pgsql
RUN docker-php-ext-install pdo pdo_pgsql

# Install MongoDB PHP driver
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Set working dir
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html