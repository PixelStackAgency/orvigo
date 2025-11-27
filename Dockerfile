# --- Dockerfile ---
# Simple Dockerfile using apache + php for shared/container deployment
FROM php:8.1-apache

# Enable rewrite
RUN a2enmod rewrite

# Install common extensions (adjust as needed)
RUN apt-get update && apt-get install -y libzip-dev unzip git && docker-php-ext-install zip

# Copy project
COPY . /var/www/html/

# Set DocumentRoot to public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# Set permissions for storage and logs
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/logs || true

EXPOSE 80
CMD ["apache2-foreground"]
