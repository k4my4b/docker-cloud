FROM nextcloud:production-fpm-alpine

COPY docker-entrypoint-hooks.d /docker-entrypoint-hooks.d
RUN find /docker-entrypoint-hooks.d -type f -name "*.sh" -exec chmod +x {} +

ENV PHP_MEMORY_LIMIT=1024M \
    PHP_UPLOAD_LIMIT=10G \
    PHP_MAX_TIME=3600 \
    CRON_INTERVAL=5m \
    DEFAULT_PHONE_REGION="GB"
