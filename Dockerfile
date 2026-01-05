# Use the official PHP image with Apache
FROM php8.2-apache

# Enable Apache mod_rewrite (optional, but good for PHP apps)
RUN a2enmod rewrite

# Copy your source code into the container
COPY . varwwwhtml

# Set the working directory
WORKDIR varwwwhtml

# CRITICAL Fix permissions for SQLite
# The web server (www-data) needs to be able to write to the 'data' folder
RUN chown -R www-datawww-data varwwwhtmldata 
    && chmod -R 775 varwwwhtmldata

# Expose port 80
EXPOSE 80