FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Clone from GitHub
ARG GITHUB_REPO=https://github.com/yourusername/simulationStory.git
ARG GITHUB_BRANCH=main

# Clone to temporary directory and move to /var/www
RUN git clone --branch ${GITHUB_BRANCH} ${GITHUB_REPO} /tmp/app \
    && rm -rf /var/www/html \
    && cp -r /tmp/app/. /var/www/ \
    && rm -rf /tmp/app \
    && chown -R www-data:www-data /var/www

# Fix git ownership warning
RUN git config --global --add safe.directory /var/www

# Set working directory
WORKDIR /var/www

# Create .env file from .env.example
RUN cp .env.example .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install npm dependencies and build assets
RUN npm ci && npm run build && npm cache clean --force

# Create storage and cache directories
RUN mkdir -p /var/www/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache

# Set permissions (only on directories that need write access)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Set up nginx and supervisor
RUN mkdir -p /var/log/supervisor /var/log/nginx \
    && cp /var/www/docker/nginx/default.conf /etc/nginx/sites-available/default \
    && rm -f /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && cp /var/www/docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
