# Use the official PHP 8.3 image with Alpine as the base image
FROM php:8.3-fpm-alpine
ENV PHP_V="php83"

# Install main packages and remove default server definition
RUN apk add --no-cache \
  curl \
  wget \
  nginx \
  curl \
  zip \
  bash \
  vim \
  git \
  supervisor

# Install build dependencies
RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
        libzip-dev \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libxslt-dev \
        patch \
        openssh-client


RUN apk add --no-cache \
  ${PHP_V} \
  ${PHP_V}-cli \
  ${PHP_V}-ctype \
  ${PHP_V}-curl \
  ${PHP_V}-dom \
  ${PHP_V}-fileinfo \
  ${PHP_V}-fpm \
  ${PHP_V}-gd \
  ${PHP_V}-intl \
  ${PHP_V}-mbstring \
  ${PHP_V}-opcache \
  ${PHP_V}-openssl \
  ${PHP_V}-phar \
  ${PHP_V}-session \
  ${PHP_V}-tokenizer \
  ${PHP_V}-soap \
  ${PHP_V}-xml \
  ${PHP_V}-xmlreader \
  ${PHP_V}-xmlwriter \
  ${PHP_V}-simplexml \
  ${PHP_V}-zip \
  # Databases
  ${PHP_V}-pdo \
  ${PHP_V}-pdo_sqlite \
  ${PHP_V}-sqlite3 \
  ${PHP_V}-pdo_mysql \
  ${PHP_V}-mysqlnd \
  ${PHP_V}-mysqli \
  ${PHP_V}-redis

# PHP requires these extension from Docker
RUN docker-php-ext-install pdo pdo_mysql gd

# PHP PECL extensions
RUN apk add \
  ${PHP_V}-pecl-xdebug

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /opt/src

# Copy application files
COPY . /opt/src

# Set permissions
RUN chown -R www-data:www-data /opt/src

# Change current user to www-data
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]