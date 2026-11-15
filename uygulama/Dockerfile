FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && a2enmod rewrite \
    && sed -i 's/Listen 80/Listen 8000/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:8000/g' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY src/ /var/www/html/

EXPOSE 8000