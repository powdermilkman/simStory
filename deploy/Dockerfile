FROM php:8.3-fpm

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

# Set working directory
WORKDIR /var/www

# Clone from GitHub
ARG GITHUB_REPO=https://github.com/yourusername/simulationStory.git
ARG GITHUB_BRANCH=main

RUN git clone --branch ${GITHUB_BRANCH} ${GITHUB_REPO} . \
    && chown -R www-data:www-data /var/www

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install npm dependencies and build assets
RUN npm ci && npm run build && npm cache clean --force

# Create storage and cache directories
RUN mkdir -p /var/www/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Copy configurations from the cloned repo
RUN cp /var/www/docker/nginx/default.conf /etc/nginx/sites-available/default \
    && cp /var/www/docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
