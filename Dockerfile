# Use official PHP CLI image
FROM php:8.2-cli

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy all your app files
COPY . .

# Expose the port Render expects
EXPOSE 10000

# Start PHP built-in server
CMD ["php", "-S", "0.0.0.0:10000"]
