# Dockerfile for Zender SaaS Platform
# Production-ready PHP + Apache image

FROM php:7.4-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mbstring intl zip curl xmlreader bcmath mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy app files
COPY . /var/www/html/

# Set permissions for uploads and system
RUN chmod -R 775 /var/www/html/uploads /var/www/html/system

# Set working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Set recommended Apache settings for Zender
RUN echo "<Directory /var/www/html/>\n\tAllowOverride All\n</Directory>" > /etc/apache2/conf-available/zender-override.conf \
    && a2enconf zender-override

# Healthcheck (optional)
HEALTHCHECK CMD curl --fail http://localhost/ || exit 1
