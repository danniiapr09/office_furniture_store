# Gunakan base image PHP + Apache
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    # Hapus baris 'RUN docker-php-ext-install pdo pdo_mysql' yang terpisah dan gabungkan di sini
    && docker-php-ext-install pdo pdo_pgsql

# Tambahkan instalasi pdo_mysql (jika sewaktu-waktu Anda kembali menggunakan MySQL)
RUN docker-php-ext-install pdo_mysql

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

# Jalankan Migrasi dan kemudian Jalankan Laravel menggunakan Apache
# Gunakan /bin/bash -c untuk menjalankan dua perintah berurutan
CMD ["/bin/bash", "-c", "php artisan migrate --force && apache2-foreground"]