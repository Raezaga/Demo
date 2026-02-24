FROM php:8.2-apache

WORKDIR /var/www/html

COPY . .

# Install PostgreSQL dependencies
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

EXPOSE 10000

CMD ["apache2-foreground"]
