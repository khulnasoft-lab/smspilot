# Dockerfile for Zender SMS Gateway SaaS
FROM php:8.1-apache

# Install system dependencies
RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libzip-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Set recommended permissions
RUN chown -R www-data:www-data /var/www/html/Install/uploads /var/www/html/Install/system/storage

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
