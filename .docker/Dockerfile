FROM php:8.1-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libpq-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql  \
    postgresql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd gettext

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN docker-php-ext-enable pgsql pdo_pgsql

# Install PHP extension for Redis
RUN pecl install redis && rm -rf /tmp/pear && docker-php-ext-enable redis

# Install PHP extension for Xdebug
RUN pecl install xdebug && rm -rf /tmp/pear && docker-php-ext-enable xdebug

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user
