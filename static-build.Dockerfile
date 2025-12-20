FROM dunglas/frankenphp:static-builder-gnu-1.11

# 接收 GitHub Token（用于避免 API 限流）
ARG GITHUB_TOKEN
ENV GITHUB_TOKEN=${GITHUB_TOKEN}

# PHP 扩展配置
ENV PHP_VERSION=8.5
ENV PHP_EXTENSIONS=bcmath,ctype,dom,fileinfo,filter,mbregex,mbstring,opcache,openssl,pdo,pdo_sqlite,phar,posix,session,simplexml,sqlite3,tokenizer,xml,xmlreader,xmlwriter,zlib,iconv
ENV PHP_EXTENSION_LIBS=""

# 下载 static-php-cli 二进制
RUN set -eux && \
    mkdir -p /go/src/app/dist/static-php-cli && \
    cd /go/src/app/dist/static-php-cli && \
    ARCH=$(uname -m) && \
    if [[ "${ARCH}" =~ "arm" ]] || [[ "${ARCH}" =~ "aarch64" ]]; then \
        DL_ARCH="aarch64"; \
    else \
        DL_ARCH="x86_64"; \
    fi && \
    echo "下载 static-php-cli 二进制 (${DL_ARCH})..." && \
    curl -o spc -fsSL "https://dl.static-php.dev/static-php-cli/spc-bin/nightly/spc-linux-${DL_ARCH}" && \
    chmod +x spc

# 运行 doctor 检查环境
WORKDIR /go/src/app/dist/static-php-cli
RUN ./spc doctor --auto-fix

# 下载 PHP 源码和扩展
RUN rm -rf downloads && \
    ./spc download \
    --with-php=$PHP_VERSION \
    --for-extensions=$PHP_EXTENSIONS \
    --for-libs=$PHP_EXTENSION_LIBS \
    --prefer-pre-built \
    --retry 5

# 构建静态 PHP embed 库
WORKDIR /go/src/app/dist/static-php-cli
ENV SPC_DEFAULT_C_FLAGS="-fPIC -O2"
ENV SPC_CMD_VAR_PHP_MAKE_EXTRA_CFLAGS="-fPIE -fstack-protector-strong -O2 -w"
RUN ./spc build \
    --enable-zts \
    --build-embed \
    --with-frankenphp-app=/work/php-app \
    $PHP_EXTENSIONS \
    --with-libs=$PHP_EXTENSION_LIBS

# 复制项目文件
WORKDIR /work
COPY . ./php-app/
COPY go.mod go.sum ./
COPY cmd/ ./cmd/
COPY internal/ ./internal/

# 打包 PHP 应用为 tar 并放到 FrankenPHP 源码目录
RUN cd /work/php-app && \
    tar -cf /go/src/app/app.tar . && \
    sha256sum /go/src/app/app.tar | awk '{print $1}' > /go/src/app/app_checksum.txt && \
    echo "PHP 应用已打包: $(du -h /go/src/app/app.tar | cut -f1)" && \
    echo "Checksum: $(cat /go/src/app/app_checksum.txt)" && \
    cd /work && \
    echo "replace github.com/dunglas/frankenphp => /go/src/app" >> go.mod

# 编译 Go 应用，链接静态 PHP 库
ENV PHP_ROOT=/go/src/app/dist/static-php-cli/buildroot
ENV CGO_ENABLED=1

RUN mkdir -p dist && \
    export PATH="${PHP_ROOT}/bin:${PATH}" && \
    export CGO_CFLAGS="$(php-config --includes) -I${PHP_ROOT}/include" && \
    export CGO_LDFLAGS="$(php-config --ldflags) $(php-config --libs) -L${PHP_ROOT}/lib -lssl -lcrypto" && \
    go build -mod=mod -tags "netgo,osusergo,nowatcher,nobadger,nomysql,nopgx" -ldflags="-s -w" -o dist/grovechat-linux-$(uname -m) ./cmd/grovechat
