# 1. Use official PHP 8.2 with Apache
FROM php:8.2-apache

# 2. Install PHP extensions Laravel 10 needs
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# 3. Enable Apache mod_rewrite (required for Laravel routes)
RUN a2enmod rewrite

# 4. Copy all project files into container
COPY . /var/www/html

# 5. Set working directory
WORKDIR /var/www/html

# 6. Install Composer (for Laravel dependencies)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 7. Set Apache to listen on port 8080
EXPOSE 8080

# 8. Start Apache
CMD ["apache2-foreground"]
