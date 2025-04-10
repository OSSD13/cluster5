FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Update Apache config to use Laravel's public folder
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install system dependencies
RUN apt-get update && \
    apt-get install -y git zip unzip nodejs npm libzip-dev libpng-dev libonig-dev libxml2-dev curl redis-server supervisor

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Composer
WORKDIR /var/www/html
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

# Copy Laravel app
COPY . .

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependencies
RUN composer require predis/predis \
    && php artisan view:clear \
    && php artisan route:clear \
    && php artisan cache:clear \
    && php artisan config:clear \
    && php artisan config:cache \
    && php artisan optimize:clear \
    && composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install && npm run build

# Add supervisord config
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf


CMD ["/usr/bin/supervisord"]
