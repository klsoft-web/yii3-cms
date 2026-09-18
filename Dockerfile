FROM php:8.2-cli 

RUN apt update

# Required tools
RUN apt -y install \
    unzip \
    libicu-dev

# PHP extensions
RUN docker-php-ext-install \
    intl \
    pdo_mysql
    
WORKDIR /app

COPY . .

# Composer
COPY --from=composer:2.10.2 /usr/bin/composer /usr/bin/
RUN composer install

# Locale
ENV LC_ALL=C.UTF-8
