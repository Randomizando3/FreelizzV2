FROM php:8.3-apache

RUN a2enmod rewrite headers
RUN docker-php-ext-install pdo pdo_mysql

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && printf '\n<Directory /var/www/html/public>\n  AllowOverride All\n  Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf

WORKDIR /var/www/html
