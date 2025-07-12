# Use the official PHP 8 image
FROM php:8.2-cli

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy all your app files into the container
COPY . .

# Expose the port Render expects to listen on
EXPOSE 10000

# Start the built-in PHP server on port 10000
CMD ["php", "-S", "0.0.0.0:10000"]
