# Use the fully qualified image name
FROM docker.io/dunglas/frankenphp:latest

# Correct the source path relative to the build context (project root)
COPY Container/Frankenphp/Caddyfile /etc/caddy/Caddyfile 

# Install required PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    zip \
    gd \
    intl \
    opcache

# Configure PHP for worker mode
# Consider using a php.ini file or Caddyfile's php_ini for cleaner config
RUN echo 'opcache.enable=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.enable_cli=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.jit_buffer_size=100M' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.jit=1255' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'memory_limit=256M' >> /usr/local/etc/php/conf.d/docker-php.ini

# Set working directory
WORKDIR /app

# Copy the application files (will be overridden by volume mount in dev)
COPY . /app

# Set permissions for storage directories (consider chown www-data:www-data instead)
RUN chmod -R 777 /app/app/writable

# Expose port 80
EXPOSE 80

# Specify default worker script and number of threads for the image
ENV FRANKENPHP_CONFIG="worker ./public/index.php 3"
ENV APP_ENV=development