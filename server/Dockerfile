FROM php:8.1-apache

# Install PDO and PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy server files
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Apache runs on port 80 by default
EXPOSE 80

# Use the default Apache command
CMD ["apache2-foreground"]
