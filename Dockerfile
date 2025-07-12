# Use the official PHP 8 image with Debian base
FROM php:8.2-cli

# Install PDO MySQL extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql

# Set the working directory
WORKDIR /var/www/html

# Copy all project files into container
COPY . .

# Expose Render port
EXPOSE 10000

# Start PHP’s built-in web server
CMD ["php", "-S", "0.0.0.0:10000"]
