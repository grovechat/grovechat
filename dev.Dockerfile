FROM composer/composer:latest-bin AS composer
FROM dunglas/frankenphp:1.10.1-builder-php8.4.15-trixie

ARG UID=1000
ARG GID=1000

# 安装php扩展
RUN install-php-extensions \
        pdo_pgsql \
        gd \
        intl \
        zip \
        imagick \
        opcache \
        pcntl \
        sockets \
        bcmath \
        redis

# 安装常用工具
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    ca-certificates \
    vim \
    tzdata \
    locales \
    procps \
    iputils-ping \
    unzip \
    net-tools \
    curl \
    wget \
    && usermod -u ${UID} www-data \
    && groupmod -g ${GID} www-data \
    && mkdir /config/psysh \
    && chown -R www-data:www-data /config/psysh \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 语言和时区
RUN sed -i 's/# zh_CN.UTF-8 UTF-8/zh_CN.UTF-8 UTF-8/' /etc/locale.gen \
    && locale-gen \
    && update-locale LANG=zh_CN.UTF-8 LC_CTYPE=zh_CN.UTF-8
ENV LANG=zh_CN.UTF-8 \
    LC_ALL=zh_CN.UTF-8 \
    LANGUAGE=zh_CN:zh

COPY --from=composer /composer /usr/bin/composer

USER root
