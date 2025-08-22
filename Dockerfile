FROM php:8.2-cli-alpine
LABEL org.opencontainers.image.source=https://github.com/ingenerator/action-satis-builder
RUN apk add --no-cache --upgrade \
    bash \
    curl \
    git \
    subversion \
    mercurial \
    openssh \
    openssl \
    libzip-dev \
    zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

ENV COMPOSER_HOME=/composer
COPY ./builder /repo-builder/
ENTRYPOINT ["/repo-builder/build-package-repo.sh"]
