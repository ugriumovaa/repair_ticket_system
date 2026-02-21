FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    apt-utils \
    ca-certificates \
    curl \
    unzip \
    zip \
    libzip-dev \
    git \
  && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    bcmath \
    zip \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
  && apt-get update && apt-get install -y --no-install-recommends nodejs \
  && node -v && npm -v \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

WORKDIR /var/www
