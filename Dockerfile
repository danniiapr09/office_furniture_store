# Gunakan image PHP + Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite untuk routing Laravel
RUN a2enmod rewrite

# Copy composer dari official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy seluruh file project ke container
COPY . .

# Install dependencies Laravel
RUN composer install --no-dev --optimize-autoloader

# Set permission agar storage dan bootstrap cache bisa diakses
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Set Apache document root ke folder public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Jalankan Laravel command saat container start
CMD php artisan migrate --force && apache2-foreground