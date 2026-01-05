# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, but good for PHP apps)
RUN a2enmod rewrite

# Copy your source code into the container
COPY . /var/www/html/

# Set the working directory
WORKDIR /var/www/html/

# CRITICAL: Fix permissions for SQLite
# The web server (www-data) needs to be able to write to the 'data' folder
RUN chown -R www-data:www-data /var/www/html/data \
    && chmod -R 775 /var/www/html/data

# Expose port 80
EXPOSE 80
