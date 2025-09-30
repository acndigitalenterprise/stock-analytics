# Use Ubuntu 24.04 as base image to mirror production
FROM ubuntu:24.04

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Jakarta

# Install system dependencies
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    wget \
    unzip \
    git \
    supervisor \
    nginx \
    mysql-client \
    redis-tools \
    cron \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update

# Install PHP 8.2 and extensions to mirror production
RUN apt-get install -y \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-zip \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-curl \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-intl \
    php8.2-redis \
    php8.2-opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js for asset compilation
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Create application directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Copy configuration files
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php/local.ini /etc/php/8.2/fpm/conf.d/99-local.ini
COPY docker/php/local.ini /etc/php/8.2/cli/conf.d/99-local.ini

# Configure PHP-FPM
RUN sed -i 's/listen = \/run\/php\/php8.2-fpm.sock/listen = 127.0.0.1:9000/' /etc/php/8.2/fpm/pool.d/www.conf

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies (if package.json exists)
RUN if [ -f package.json ]; then npm install --omit=dev; fi

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/log/supervisor \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/storage \
    && chmod -R 777 /var/www/html/bootstrap/cache

# Configure Nginx
RUN rm /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# Expose port
EXPOSE 80

# Create entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Wait for database to be ready\n\
echo "Waiting for database..."\n\
until mysql -h mysql -u root -psecret -e "SELECT 1" >/dev/null 2>&1; do\n\
    echo "MySQL is unavailable - sleeping"\n\
    sleep 2\n\
done\n\
echo "MySQL is ready!"\n\
\n\
# Generate app key if not exists\n\
if [ ! -f .env ]; then\n\
    cp .env.example .env\n\
fi\n\
\n\
php artisan key:generate --ansi --force\n\
\n\
# Clear and cache config\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
\n\
# Run migrations\n\
php artisan migrate --force\n\
\n\
# Create storage link\n\
php artisan storage:link\n\
\n\
# Start supervisor\n\
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf\n\
' > /entrypoint.sh \
    && chmod +x /entrypoint.sh

# Use entrypoint
ENTRYPOINT ["/entrypoint.sh"]