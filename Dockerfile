# Dockerfile for ITS-BERT Intelligent Tutoring System on Render / Railway / Docker
FROM php:8.2-apache

# Install PDO MySQL, SQLite, Zip extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite zip

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files to Apache root
COPY . /var/www/html/

# Set working directory permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 777 /var/www/html/materials

EXPOSE 80
CMD ["apache2-foreground"]
