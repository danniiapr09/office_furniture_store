# Gunakan base image PHP + Apache
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql

# Aktifkan rewrite module untuk Laravel routes
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy semua file project ke dalam container
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Set permission storage & bootstrap
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 80

# Jalankan Laravel menggunakan Apache
CMD ["apache2-foreground"]

RUN docker-php-ext-install pdo pdo_mysql