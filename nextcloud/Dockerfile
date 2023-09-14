FROM nextcloud:production-fpm-alpine

COPY post-install.sh /docker-entrypoint-hooks.d/post-installation
RUN chmod +x /docker-entrypoint-hooks.d/post-installation/post-install.sh

ENV PHP_MEMORY_LIMIT 1024M 
ENV PHP_UPLOAD_LIMIT 10G 
ENV PHP_MAX_TIME 3600
ENV CRON_INTERVAL 5m

