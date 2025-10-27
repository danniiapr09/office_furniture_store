# Gunakan base image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Set direktori kerja
WORKDIR /var/www/html

# Install dependensi yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy Composer dari official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy semua file project Laravel ke container
COPY . .

# Install dependensi Laravel tanpa dev packages
RUN composer install --no-dev --optimize-autoloader

# Generate APP_KEY & bersihkan cache Laravel
RUN php artisan key:generate --force && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Aktifkan mod_rewrite untuk Apache (agar route Laravel bisa jalan)
RUN a2enmod rewrite

# Set permission untuk folder Laravel
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache

# Ubah document root ke folder /public milik Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf && \
    echo "<Directory /var/www/html/public>\n\tAllowOverride All\n\tRequire all granted\n</Directory>" >> /etc/apache2/apache2.conf

# Buka port 80 untuk Railway
EXPOSE 80

# Jalankan Apache
CMD ["apache2-foreground"]