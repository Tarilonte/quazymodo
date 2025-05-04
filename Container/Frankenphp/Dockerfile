FROM dunglas/frankenphp:latest

COPY Caddyfile /etc/caddy/Caddyfile

# Install required PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    zip \
    gd \
    intl \
    opcache

# Configure PHP for worker mode
RUN echo 'opcache.enable=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.enable_cli=1' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.jit_buffer_size=100M' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'opcache.jit=1255' >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo 'memory_limit=256M' >> /usr/local/etc/php/conf.d/docker-php.ini

# Set working directory
WORKDIR /app

# Copy the application files
COPY . /app

# Set permissions for storage directories
RUN chmod -R 777 /app/app/writable

# Expose port 80
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 CMD curl -f http://localhost/ || exit 1