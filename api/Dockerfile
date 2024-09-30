FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# Copy composer files
COPY composer.json ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Install PHPUnit
RUN composer require --dev phpunit/phpunit

# Copy application files
COPY . .

# Generate autoloader and optimize
RUN composer dump-autoload --optimize

# Set permissions
