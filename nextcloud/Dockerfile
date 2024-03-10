FROM nextcloud:27.1.3-fpm-alpine

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
    ENABLE_NOTIFICATION_SOUND=true \
    AV_HOST=clamav \
    AV_PORT=3310 \
    AV_STREAM_MAX_LENGTH=104857600 \
    AV_MAX_FILE_SIZE=104857600 \
    IMAGINARY_HOST=imaginary \
    IMAGINARY_PORT=9000 \
    FTS_HOST=elasticsearch \
    FTS_PORT=9200 \
    FTS_USER=elastic \
    FTS_PASSWORD=changeme \
    NC_DISABLED_APPS="firstrunwizard support survey_client federation nextcloud_announcements updatenotification logreader serverinfo" \
    NC_ENABLED_APPS="suspicious_login files_external admin_audit" \
    NC_ADDITIONAL_APPS="impersonate groupfolders deck forms terms_of_service calendar contacts mail quota_warning"

RUN apk add --no-cache samba-client && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS autoconf samba-dev && \
    pecl install smbclient && \
    docker-php-ext-enable smbclient && \
    apk del .build-deps

HEALTHCHECK --interval=30s --timeout=3s --start-period=300s --retries=3 \
    CMD nc -z localhost 9000 || exit 1