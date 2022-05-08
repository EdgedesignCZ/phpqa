ARG COMPOSER_VERSION=1.8.0
FROM composer:$COMPOSER_VERSION

RUN apk add --update libxslt-dev && \
    docker-php-ext-install xsl

COPY ./composer.json ./composer.lock ./bin/suggested-tools.sh /phpqa/
ARG UPDATE_COMPOSER=no
RUN (cd /phpqa && \
     UPDATE_COMPOSER=$UPDATE_COMPOSER ./suggested-tools.sh install --update-no-dev && \
     ln -s /phpqa/phpqa /usr/local/bin && \
     ln -s /phpqa/vendor/bin/* /usr/local/bin && \
     ls -lA /usr/local/bin | grep phpqa)

COPY ./ /phpqa/
WORKDIR /phpqa

ENTRYPOINT ["docker-php-entrypoint"]
