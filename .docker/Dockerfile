FROM composer:1.8.0

RUN apk add --update libxslt-dev && \
    docker-php-ext-install xsl

COPY ./composer.json ./composer.lock ./bin/suggested-tools.sh /phpqa/
RUN (cd /phpqa && \
     composer install --no-dev && \
     ./suggested-tools.sh install --update-no-dev && \
     ln -s /phpqa/phpqa /usr/local/bin && \
     ln -s /phpqa/vendor/bin/* /usr/local/bin && \
     ls -lA /usr/local/bin | grep phpqa)

COPY ./ /phpqa/
WORKDIR /phpqa

ENTRYPOINT ["docker-php-entrypoint"]
