# Gunakan base image PHP dengan Apache
FROM php:8.2-apache

# Set working directory di dalam container
WORKDIR /var/www/html

# Install dependencies sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip gd

# Copy file composer dari official image Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy semua file project ke dalam container
COPY . .

# Install dependensi Laravel (pastikan artisan sudah tersedia)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ganti permission storage dan bootstrap/cache
RUN chmod -R 777 storage bootstrap/cache

# Aktifkan mod_rewrite untuk Laravel routing
RUN a2enmod rewrite

# Salin konfigurasi Apache untuk Laravel
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Set environment variable untuk production
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
ENV APP_ENV=production
ENV APP_DEBUG=false

# Expose port default Apache
EXPOSE 80

# Jalankan Apache di foreground
CMD ["apache2-foreground"]