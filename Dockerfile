FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

# Copy all application files
COPY . /var/www/html

# Clean stale local bootstrap caches and ensure storage directories exist
RUN rm -f /var/www/html/bootstrap/cache/*.php \
    && mkdir -p /var/www/html/storage/framework/views \
                /var/www/html/storage/framework/cache \
                /var/www/html/storage/framework/sessions \
                /var/www/html/storage/logs \
                /var/www/html/bootstrap/cache

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

# Discover packages and set permissions
RUN php artisan package:discover --ansi || true \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

# Apply custom Laravel Nginx site configuration
RUN cp /var/www/html/conf/nginx/nginx-site.conf /etc/nginx/sites-available/default.conf \
    && (cp /var/www/html/conf/nginx/nginx-site.conf /etc/nginx/http.d/default.conf 2>/dev/null || true) \
    && (cp /var/www/html/conf/nginx/nginx-site.conf /etc/nginx/conf.d/default.conf 2>/dev/null || true)

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 0
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG true
ENV APP_KEY base64:TUI/rs1egTQjO+U3V3hMZYeY8Me8J4TzahQt6iZpcto=
ENV APP_URL https://digitalaljihad001.onrender.com
ENV LOG_CHANNEL stderr

# Default Database config (TiDB Cloud)
ENV DB_CONNECTION mysql
ENV DB_HOST gateway01.ap-southeast-1.prod.aws.tidbcloud.com
ENV DB_PORT 4000
ENV DB_DATABASE digitalv304
ENV DB_USERNAME Fjc45gFUNDbWiTq.root
ENV MYSQL_ATTR_SSL_CA /etc/ssl/certs/ca-certificates.crt

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
