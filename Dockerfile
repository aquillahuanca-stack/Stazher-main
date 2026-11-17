FROM php:8.1-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpng-dev libzip-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j"$(nproc)" mysqli gd zip \
    && a2enmod rewrite ssl headers \
    && a2dissite 000-default \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache-ssl.conf /etc/apache2/sites-available/stazher-ssl.conf
RUN a2ensite stazher-ssl

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/images

EXPOSE 443
