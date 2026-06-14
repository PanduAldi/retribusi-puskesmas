FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli

# Enable Apache rewrite module for CodeIgniter routing
RUN a2enmod rewrite

# Ubah DocumentRoot Apache ke folder public CodeIgniter
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . /var/www/html

# Set proper permissions
# Hanya folder writable yang butuh akses tulis (write) bagi www-data
RUN chown -R root:root /var/www/html \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 755 /var/www/html

# Copy composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install project dependencies
# Kita jalankan ini saat build, tapi nanti akan tertimpa oleh volume jika di dev mode
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Expose port 80
EXPOSE 80

# Use the default Apache CMD
CMD ["apache2-foreground"]