ARG ALPINE_VERSION=edge
FROM alpine:${ALPINE_VERSION}
LABEL Maintainer="SineMah"

ARG UNAME=www-docker
ARG UID=1000
ARG GID=1000

# Setup document root
WORKDIR /var/www

# Add user for application
RUN addgroup -g $GID $UNAME
RUN adduser -u $UID -G $UNAME -h /home/$UNAME -D $UNAME

RUN apk add --no-cache \
  curl \
  php82 \
  php82-ctype \
  php82-curl \
  php82-dom \
  php82-fileinfo \
  php82-gd \
  php82-intl \
  php82-iconv \
  php82-mbstring \
  php82-mysqli \
  php82-opcache \
  php82-openssl \
  php82-phar \
  php82-pdo \
  php82-pdo_mysql \
  php82-pdo_pgsql \
  php82-sodium \
  php82-session \
  php82-tokenizer \
  php82-xml \
  php82-xmlreader \
  php82-xmlwriter \
  php82-zlib \
  php82-zip \
  php82-exif \
  php82-pear \
  php82-dev \
  zip

# Create symlink so programs depending on `php` still function
RUN ln -s /usr/bin/php82 /usr/bin/php
RUN ln -s /usr/bin/pecl82 /usr/bin/pecl

RUN apk --update --no-cache add autoconf g++ musl-dev make linux-headers && \
  pecl install -f xdebug && \
  apk del --purge autoconf g++ make

# Configure PHP-FPM
COPY config/php.ini /etc/php82/conf.d/custom.ini

# Install composer
RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

USER $UNAME