# Build nginx unit with njs support
FROM ghcr.io/k4my4b/base:latest AS build

RUN apk add --no-cache \
    alpine-sdk openssl-dev pcre2-dev php82-dev php82-embed

ADD https://github.com/nginx/unit/archive/refs/tags/1.33.0.tar.gz /unit/unit-1.33.0.tar.gz
ADD https://github.com/nginx/njs/archive/refs/tags/0.8.7.tar.gz /unit/njs-0.8.7.tar.gz
ADD https://gitlab.alpinelinux.org/alpine/aports/-/raw/3.19-stable/community/unit/phpver.patch /unit
RUN mkdir -p /unit/unit /unit/njs && \
    tar -xvf /unit/unit-1.33.0.tar.gz --strip-components=1 -C /unit/unit && \
    tar -xvf /unit/njs-0.8.7.tar.gz --strip-components=1 -C /unit/njs && \
    cd /unit/njs && \
    ./configure --no-zlib --no-libxml2 && make && \
    cd /unit/unit && patch -p1 < ../phpver.patch && \
    ./configure \
        --njs \
        --openssl \
        --cc-opt="-I ../njs/src/ -I ../njs/build/" \
        --ld-opt="-L ../njs/build/" \
        --user=unit \
        --group=unit \
        --prefix="/usr/local" \
		--localstatedir="/var" \
		--statedir="/var/lib/unit" \
		--control="unix:/run/control.unit.sock" \
		--pid="/run/unit.pid" \
		--log="/var/log/unit.log" \
		--tmpdir=/tmp \
		--modulesdir="/usr/local/lib/unit/modules" && \
    ./configure php \
        --module=php82 \
        --config=/usr/bin/php-config82 \
        --lib-path=/usr/lib && \
    make && make install

FROM ghcr.io/k4my4b/base:latest

# Setup and install nginx unit built in the previous stage
RUN addgroup -S -g 101 unit; \
    adduser -S -u 101 -D -H -h /var/lib/unit -s /sbin/nologin -G unit -g "NGINX Unit" unit; \
    mkdir -p /var/lib/unit /var/log/unit && \
    chown unit:unit /var/lib/unit /var/log/unit

COPY --from=build /usr/local /usr/local
COPY --from=build /usr/lib/libphp82.so /usr/lib/libphp82.so

# Install Nextcloud and dependencies
RUN apk add --no-cache \
    php82 php82-opcache php82-pcntl php82-iconv php82-ctype \
    php82-curl php82-dom php82-fileinfo php82-gd php82-mbstring \
    php82-openssl php82-posix php82-session php82-simplexml php82-xmlreader \
    php82-xmlwriter php82-zip php82-xml php82-pgsql php82-pdo \
    php82-pdo_pgsql php82-intl php82-sodium php82-pecl-redis \
    php82-ldap php82-pecl-smbclient php82-ftp php82-imap php82-pecl-apcu \
    php82-bcmath php82-gmp php82-exif php82-sysvsem php82-pecl-imagick \
    imagemagick-svg ffmpeg jq curl pv ca-certificates && \
    rm -f /etc/php82/conf.d/* && \
    addgroup -S -g 82 www-data; \
    adduser -S -u 100 -D -H -h /var/lib/nextcloud -s /sbin/nologin -G www-data -g Nextcloud nextcloud; \
    mkdir -p /usr/share/webapps; \
    curl -sfL https://download.nextcloud.com/server/releases/latest-27.tar.bz2 | \
    tar --exclude='nextcloud/apps/support' \
        --exclude='nextcloud/apps/firstrunwizard' \
        --exclude='nextcloud/apps/updatenotification' \
        --exclude='nextcloud/config/config.php' \
        -vxj -C /usr/share/webapps/ && \
    chown --recursive nextcloud:www-data /usr/share/webapps/nextcloud && \
    ln -sf /usr/share/webapps/nextcloud/config /etc/nextcloud && \
    chmod +x /usr/share/webapps/nextcloud/occ && \
    ln -sf /usr/share/webapps/nextcloud/occ /usr/local/bin/_occ && \
    mkdir -p /var/lib/nextcloud/apps /var/lib/nextcloud/data && \
    ln -sf /var/lib/nextcloud/apps /usr/share/webapps/nextcloud/apps-appstore && \
    chown --recursive nextcloud:www-data /var/lib/nextcloud

# Copy custom configuration and scripts
COPY --chmod=775 init.d /init.d
COPY --chmod=775 healthcheck.d /healthcheck.d
COPY --chown=100:82 usr/share/webapps/nextcloud /usr/share/webapps/nextcloud
COPY --chmod=755 usr/local /usr/local
COPY etc/crontabs /etc/crontabs
COPY etc/php82 /etc/php82
COPY --chmod=660 --chown=100:82 etc/nextcloud /etc/nextcloud
COPY --chmod=660 etc/unit /etc/unit

# Set environment variables
ENV PHP_MEMORY_LIMIT=1G \
    PHP_UPLOAD_LIMIT=10G \
    PHP_MAX_TIME=3600 \
    PHP_UPLOAD_FILE_LIMIT=1000 \
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
