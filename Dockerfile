# Gunakan base image PHP dengan Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies Laravel
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Aktifkan mod_rewrite untuk Laravel route
RUN a2enmod rewrite

# Copy semua file proyek Laravel ke container
COPY . /var/www/html

# Set permission agar Laravel bisa menulis log dan cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Ubah konfigurasi Apache agar root-nya ke folder "public"
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && echo "<Directory /var/www/html/public>\n\tAllowOverride All\n\tRequire all granted\n</Directory>" >> /etc/apache2/apache2.conf

# Port default Apache
EXPOSE 80

# Jalankan Apache
CMD ["apache2-foreground"]