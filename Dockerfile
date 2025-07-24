FROM php:8.4.8-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --link composer.* symfony.* ./

RUN composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-progress

# Set working directory
WORKDIR /mnt

# copy sources
COPY --link . ./

# Expose port 9090
EXPOSE 9090

# Set entrypoint
ENTRYPOINT ["php", "-S", "0.0.0.0:9090", "-t", "/mnt/public"]

