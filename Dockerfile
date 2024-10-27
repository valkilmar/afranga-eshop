# Use PHP with Apache as the base image
FROM php:8.2-apache as web

# Install Additional System Dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip

# Configure Apache DocumentRoot to point to Laravel's public directory
# and update Apache configuration files
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set the working directory
WORKDIR /var/www/html

# Copy the application code
COPY . /var/www/html

# ADD .env.docker /var/www/html/.env

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies
RUN composer install

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# RUN apt-get update && apt-get install -y \
#     software-properties-common \
#     npm


# RUN npm install npm@latest -g && \
#     npm install n -g && \
#     n latest




# RUN apt-get update && apt-get install -y npm
# RUN npm i -g n && n lts && npm i -g npm@latest

# RUN echo "NODE Version:" && node --version
# RUN echo "NPM Version:" && npm --version



# RUN echo "#!/bin/sh\n" \
#   "php artisan migrate\n" \
#   "php artisan serve --host 0.0.0.0 --port \$PORT" > /var/www/html/start.sh
# RUN chmod +x /var/www/html/start.sh
# CMD ["/var/www/html/start.sh"]
