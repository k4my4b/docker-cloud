FROM ghcr.io/k4my4b/base:latest

# Install all necessary PHP packages and Nextcloud dependencies
RUN apk add --no-cache \
    php82-ctype php82-curl php82-dom \
    php82-fileinfo php82-gd php82-mbstring php82-openssl \
    php82-posix php82-session php82-simplexml php82-xmlreader \
    php82-xmlwriter php82-zip php82-xml php82-pgsql php82-pdo \
    php82-pdo_pgsql php82-intl php82-sodium php82-pecl-redis \
    php82-ldap php82-pecl-smbclient php82-ftp php82-imap \
    php82-bcmath php82-gmp php82-exif php82-sysvsem php82-pecl-imagick \
    imagemagick-svg ffmpeg nextcloud-files_trashbin \
    nextcloud-initscript nextcloud jq nextcloud-default-apps

# PHP and Nextcloud configuration adjustments
RUN sed -i 's/^\(memory_limit = \).*/\1512m/' /etc/php82/php.ini && \
    mkdir /run/nextcloud && chown 100:82 /run/nextcloud && \
    sed -i -e 's/\(listen = \).*/\10.0.0.0:9000/g' -e 's/listen\.mode.*//g' /etc/php82/php-fpm.d/nextcloud.conf && \
    find /usr/share/webapps/nextcloud/core/skeleton -mindepth 1 -delete

# Tweak PHP FPM configuration
# TODO remove this once these changes are refactored into their relavant config files
RUN sed -i '/^\[PHP\]/a clear_env = no' /etc/php82/php.ini && \
    sed -i '/^\[nextcloud\]/a clear_env = no' /etc/php82/php-fpm.d/nextcloud.conf && \
    sed -i '/php_admin_value\[session.save_path\]/d' /etc/php82/php-fpm.d/nextcloud.conf && \
    sed -i 's/shell_exec,//' /etc/php82/php-fpm.d/nextcloud.conf && \
    sed -i 's/proc_open,//' /etc/php82/php-fpm.d/nextcloud.conf && \
    sed -i '194,$d' /etc/php82/php-fpm.d/nextcloud.conf && \
    rm -f /etc/php82/conf.d/*

# Copy custom configuration and scripts
COPY --chmod=775 init.d /init.d
COPY --chmod=775 healthcheck.d /healthcheck.d
COPY --chown=100:82 usr/share/webapps/nextcloud /usr/share/webapps/nextcloud
COPY etc/crontabs /etc/crontabs
COPY etc/php82 /etc/php82
COPY --chown=100:82 etc/nextcloud /etc/nextcloud

# Set environment variables
ENV PHP_MEMORY_LIMIT=512M \
    PHP_UPLOAD_LIMIT=10G \
    PHP_MAX_TIME=3600 \
    CRON_INTERVAL=5m \
    EMPTY_SKELETON=true \
    SKELETON_DIRECTORY="/usr/share/webapps/nextcloud/core/skeleton" \
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
    REDIS_PORT=6379 \
    NC_DISABLED_APPS="firstrunwizard support survey_client federation nextcloud_announcements updatenotification logreader serverinfo" \
    NC_ENABLED_APPS="suspicious_login files_external admin_audit" \
    NC_ADDITIONAL_APPS="impersonate groupfolders deck forms terms_of_service calendar contacts mail quota_warning"

EXPOSE 9000
