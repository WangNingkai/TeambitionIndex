FROM php:7.4.14-cli-alpine3.12

ENV SWOOLE_VERSION 4.6.0

RUN \
    curl -sfL http://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
    chmod +x /usr/bin/composer                                                                     && \
    composer self-update --clean-backups 2.0.8                                    && \
    composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/  && \
    sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories && \
    apk update && \
    apk add --no-cache libstdc++ && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS curl-dev openssl-dev pcre-dev pcre2-dev zlib-dev && \
    apk add --no-cache gmp gmp-dev git && \
    docker-php-ext-install gmp && \
    docker-php-ext-install bcmath && \
    docker-php-ext-install sockets && \
    docker-php-source extract && \
    mkdir /usr/src/php/ext/swoole && \
    curl -sfL https://github.com/swoole/swoole-src/archive/v${SWOOLE_VERSION}.tar.gz -o swoole.tar.gz && \
    tar xfz swoole.tar.gz --strip-components=1 -C /usr/src/php/ext/swoole && \
    docker-php-ext-configure swoole \
        --enable-http2   \
        --enable-mysqlnd \
        --enable-openssl \
        --enable-sockets --enable-swoole-curl --enable-swoole-json && \
    docker-php-ext-install -j$(nproc) swoole && \
    rm -f swoole.tar.gz $HOME/.composer/*-old.phar && \
    docker-php-source delete && \
    apk del .build-deps


WORKDIR /var/www

EXPOSE 9501
