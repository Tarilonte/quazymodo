# Stage 1: Build environment
FROM alpine:edge AS builder

# Install all dependencies for build
RUN apk add --no-cache \
    php84 \
    php84-fpm \
    php84-opcache \
    php84-pecl-apcu \
    php84-pdo \
    php84-pdo_mysql \
    php84-pdo_sqlite \
    php84-sqlite3 \
    php84-ctype \
    php84-mbstring \
    php84-xml \
    php84-session \
    php84-curl \
    php84-tokenizer

# Configure PHP-FPM
RUN sed -i 's/user = nobody/user = nginx/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/group = nobody/group = nginx/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/;listen.owner = nobody/listen.owner = nginx/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/;listen.group = nobody/listen.group = nginx/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/^pm = .*/pm = dynamic/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/^pm.max_children = .*/pm.max_children = 20/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/^pm.start_servers = .*/pm.start_servers = 6/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/^pm.min_spare_servers = .*/pm.min_spare_servers = 4/' /etc/php84/php-fpm.d/www.conf && \
    sed -i 's/^pm.max_spare_servers = .*/pm.max_spare_servers = 10/' /etc/php84/php-fpm.d/www.conf && \
    echo "clear_env = no" >> /etc/php84/php-fpm.d/www.conf

# Create app directory (optional in builder if not used, but harmless)
RUN mkdir -p /var/www/html/public

# Stage 2: Production image
FROM alpine:edge

# Install runtime dependencies
# This will create the nginx user and group
RUN apk add --no-cache \
    nginx \
    php84 \
    php84-fpm \
    php84-opcache \
    php84-pecl-apcu \
    php84-pdo \
    php84-pdo_mysql \
    php84-pdo_sqlite \
    php84-sqlite3 \
    php84-ctype \
    php84-mbstring \
    php84-xml \
    php84-session \
    php84-curl \
    php84-tokenizer \
    supervisor

# Create required directories
RUN mkdir -p /var/www/html/public \
    /run/nginx \
    /var/run/php-fpm \
    /var/log/supervisor

# Copy PHP config from builder
COPY --from=builder /etc/php84 /etc/php84

# Copy configuration files from the Container directory within the build context
COPY Container/Nginx/nginx.conf /etc/nginx/http.d/default.conf
COPY Container/Nginx/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application files from the build context root
WORKDIR /var/www/html
# This copies everything from the project root (build context)
COPY . /var/www/html

# Set proper permissions (nginx user/group already exists from apk add nginx)
RUN chown -R nginx:nginx /var/www/html && \
    chmod -R 755 /var/www/html && \
    chown -R nginx:nginx /var/run/php-fpm && \
    chown -R nginx:nginx /var/log/supervisor && \
    chown -R nginx:nginx /run/nginx

# Expose web ports
EXPOSE 80 443

# Start supervisor to manage services
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
