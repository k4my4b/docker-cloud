FROM nextcloud:production-fpm-alpine

COPY docker-entrypoint-hooks.d /docker-entrypoint-hooks.d
RUN find /docker-entrypoint-hooks.d -type f -name "*.sh" -exec chmod +x {} +

ENV PHP_MEMORY_LIMIT=512M \
    PHP_UPLOAD_LIMIT=10G \
    PHP_MAX_TIME=3600 \
    CRON_INTERVAL=5m \
    EMPTY_SKELETON=true \
    SKELETON_DIRECTORY="/var/www/html/core/skeleton" \
    KNOWLEDGEBASE_ENABLED=false \
    DISABLE_WEB_UPGRADE=true \
    DEFAULT_PHONE_REGION="GB" \ 
    FORCE_LANGUAGE="en_GB" \
    FORCE_LOCALE="en_GB" \
    THEME="example" \
    TALK_CHANGELOG=no \
    ENFORCE_TWOFACTORAUTH=true \
    HARDENED_PASSWORD_POLICY=true