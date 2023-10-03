FROM nextcloud:production-fpm-alpine

COPY --chmod=775 docker-entrypoint-hooks.d /docker-entrypoint-hooks.d
COPY --chown=82:82 var/www/html /var/www/html

ENV PHP_MEMORY_LIMIT=512M \
    PHP_UPLOAD_LIMIT=10G \
    PHP_MAX_TIME=3600 \
    CRON_INTERVAL=5m \
    EMPTY_SKELETON=true \
    SKELETON_DIRECTORY="/var/www/html/core/skeleton" \
    KNOWLEDGEBASE_ENABLED=false \
    DISABLE_WEB_UPGRADE=true \
    DEFAULT_PHONE_REGION="GB" \ 
    DEFAULT_LANGUAGE="en_GB" \
    DEFAULT_LOCALE="en_GB" \
    THEME="docker-cloud" \
    ENABLE_TALK_CHANGELOG=false \
    ENFORCE_TWOFACTORAUTH=true \
    HARDENED_PASSWORD_POLICY=true \
    ENABLE_NOTIFICATION_SOUND=true

RUN apk add --no-cache samba-client && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS autoconf samba-dev && \
    pecl install smbclient && \
    docker-php-ext-enable smbclient && \
    apk del .build-deps

    