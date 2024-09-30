FROM php:8.1-apache

# Install cURL and zip extensions
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    libzip-dev \
    && docker-php-ext-install curl zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy API files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Apache runs on port 80 by default
EXPOSE 80

# Use the default Apache command
CMD ["apache2-foreground"]
