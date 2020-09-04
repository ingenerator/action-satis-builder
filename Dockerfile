FROM php:7.4-cli-alpine

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
COPY . /repo-builder/
WORKDIR /repo-builder
ENTRYPOINT ["/repo-builder/build-package-repo.sh"]

