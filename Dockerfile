FROM php:8.3-fpm

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    libjpeg-dev \
    libfreetype6-dev \
    gnupg

# Clean cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache

# Get Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js (diperlukan untuk Vite/aset compile)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Copy source code
COPY . .

# Install dependencies PHP & JS
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN npm install && npm run build

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]