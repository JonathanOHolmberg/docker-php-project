FROM php:8.1-apache

# Install libcurl4-openssl-dev
RUN apt-get update && apt-get install -y libcurl4-openssl-dev

# Install curl extension
RUN docker-php-ext-install curl

COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
