FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Install cron
RUN apt-get update && apt-get install -y cron

# Copy crontab file to the cron.d directory
COPY crontab /etc/cron.d/populate-db-cron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/populate-db-cron

# Apply cron job
RUN crontab /etc/cron.d/populate-db-cron

# Create the log file to be able to run tail
RUN touch /var/log/cron.log

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

# Copy application files
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Add a custom command to populate the database
RUN echo '#!/bin/bash\nphp /var/www/html/populate.php' > /usr/local/bin/populate-db && \
    chmod +x /usr/local/bin/populate-db

# Start Apache and cron
CMD cron && apache2-foreground
