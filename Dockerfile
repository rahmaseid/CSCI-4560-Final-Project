#Myadmin          http://localhost:8080

FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Enable Apache rewrite mod (optional but common for frameworks)
RUN a2enmod rewrite

# Set Apache's DocumentRoot permissions
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom-permissions.conf \
    && a2enconf custom-permissions
