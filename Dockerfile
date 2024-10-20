FROM php:8.2-cli-alpine
LABEL org.opencontainers.image.source https://github.com/ingenerator/action-satis-builder
RUN apk add --no-cache --upgrade \
    bash \
    curl \
    git \
    subversion \
    mercurial \
    openssh \
    openssl \
    zip \
    unzip

ENV COMPOSER_HOME /composer
COPY ./builder /repo-builder/
ENTRYPOINT ["/repo-builder/build-package-repo.sh"]
