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
    FTS_ENABLE=true \
    FTS_HOST=elasticsearch \
    FTS_PORT=9200 \
    FTS_USER=elastic \
    FTS_PASSWORD=changeme \
    POSTGRES_HOST=postgres \
    POSTGRES_DB=nextcloud \
    POSTGRES_USER=nextcloud \
    POSTGRES_PASSWORD=changeme \
    REDIS_HOST=redis \
    NC_DISABLED_APPS="firstrunwizard support survey_client federation nextcloud_announcements updatenotification logreader serverinfo" \
    NC_ENABLED_APPS="suspicious_login files_external admin_audit" \
    NC_ADDITIONAL_APPS="impersonate groupfolders deck forms terms_of_service calendar contacts mail quota_warning"

RUN apk add --no-cache samba-client ffmpeg && \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS autoconf samba-dev && \
    pecl install smbclient && \
    docker-php-ext-enable smbclient && \
    apk del .build-deps

RUN sed -i 's/\(^pm\) = dynamic/\1 = ondemand/' /usr/local/etc/php-fpm.d/www.conf; \
    sed -i 's/\(^pm.max_children\) =.*/\1 = 5000/' /usr/local/etc/php-fpm.d/www.conf;

RUN { \
	echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=64'; \
        echo 'opcache.save_comments=1'; \
        echo 'opcache.revalidate_freq=60'; \
        echo 'opcache.jit=1255'; \
        echo 'opcache.jit_buffer_size=8M'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini;

HEALTHCHECK --interval=30s --timeout=3s --start-period=300s --retries=3 \
    CMD nc -z localhost 9000 || exit 1
