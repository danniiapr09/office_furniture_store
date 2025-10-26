# Gunakan base image PHP + Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies yang dibutuhkan (libpq-dev untuk PostgreSQL)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    # Clean up APT cache to keep image small
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql

# Aktifkan rewrite module untuk Laravel routes
RUN a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy composer files untuk memaksimalkan Docker Caching
COPY composer.json composer.lock ./

# Install dependency Laravel (Layer ini akan di-cache jika composer.json tidak berubah)
RUN composer install --no-dev --optimize-autoloader

# Copy sisa file project ke dalam container
COPY . .

# SOLUSI FIX APACHE: Gunakan Symlink untuk mengarahkan root ke folder public
# Ini mengatasi error AH01276 (Cannot serve directory)
RUN rm -rf /var/www/html
RUN ln -sf /var/www/html/public /var/www/html

# Set permission storage & bootstrap
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# START COMMAND: Jalankan Migrasi terlebih dahulu, lalu jalankan Apache
# Ini menggabungkan Fix Migrasi dan Start Server
CMD ["/bin/bash", "-c", "php artisan migrate --force && apache2-foreground"]