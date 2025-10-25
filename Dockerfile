# Gunakan base image PHP versi 8.2 (dengan ekstensi penting)
FROM php:8.2-fpm

# Install dependencies sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl && \
    docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy semua file Laravel ke dalam container
COPY . .

# Install semua dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Generate key (opsional, bisa lewat env di Render juga)
# RUN php artisan key:generate

# Set permission folder storage & bootstrap agar bisa ditulis
RUN chmod -R 775 storage bootstrap/cache

# Expose port 8000 untuk Render
EXPOSE 8000

# Jalankan Laravel pakai php artisan serve
CMD php artisan serve --host=0.0.0.0 --port=8000