FROM composer/composer:latest-bin AS composer
FROM dunglas/frankenphp:1.11.1-builder-php8.5.1-trixie

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
    jq \
    wget \
    git \
    openssh-client \
    && usermod -u ${UID} www-data \
    && groupmod -g ${GID} www-data \
    && mkdir /config/psysh \
    && chown -R www-data:www-data /config/psysh \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安装 Node.js LTS
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
RUN npm config set registry https://registry.npmmirror.com
RUN npm install -g @anthropic-ai/claude-code

# 语言和时区
RUN sed -i 's/# zh_CN.UTF-8 UTF-8/zh_CN.UTF-8 UTF-8/' /etc/locale.gen \
    && locale-gen \
    && update-locale LANG=zh_CN.UTF-8 LC_CTYPE=zh_CN.UTF-8
ENV LANG=zh_CN.UTF-8 \
    LC_ALL=zh_CN.UTF-8 \
    LANGUAGE=zh_CN:zh

# Go 配置
ENV GOPROXY=https://goproxy.cn,direct \
    GO111MODULE=on \
    GOPATH=/root/go \
    GOROOT=/usr/local/go \
    PATH=$PATH:/root/go/bin:/usr/local/go/bin
RUN CGO_ENABLED=0 go install -v -ldflags="-s -w" golang.org/x/tools/gopls@latest
RUN CGO_ENABLED=0 go install -v -ldflags="-s -w" honnef.co/go/tools/cmd/staticcheck@latest
COPY go.mod go.sum ./
RUN go mod download && go mod verify

COPY --from=composer /composer /usr/bin/composer

USER root
