# Base Image (PHP 8.2 kullanarak)
FROM php:8.2-fpm

# Working Directory
WORKDIR /var/www/html

# Install Dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . .

# Install PHP dependencies
RUN composer install


# Expose Port
EXPOSE 8000

# Entrypoint
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
