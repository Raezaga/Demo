FROM php:8.2-apache

WORKDIR /var/www/html

COPY . .

RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 10000

CMD ["apache2-foreground"]