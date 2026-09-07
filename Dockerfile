FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

# Copy all application files
COPY . /var/www/html

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

# Set permissions for Laravel storage and cache
RUN chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 0
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
