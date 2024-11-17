FROM php:8.1-alpine

# Copy PHP Composer
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# Install PCov
RUN apk add --no-cache autoconf build-base
RUN pecl install pcov && docker-php-ext-enable pcov

# Install ZIP
RUN apk add --no-cache libzip-dev
RUN docker-php-ext-install zip

WORKDIR /app