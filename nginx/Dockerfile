FROM nginx:mainline-alpine3.19 AS nginx
FROM ghcr.io/k4my4b/base:latest

RUN apk add --no-cache nginx openssl gettext-envsubst curl
RUN mkdir -p /etc/nginx/conf.d && mkdir -p /etc/nginx/templates && mkdir -p /etc/nginx/ssl

# Hosts and Ports 
ENV NC_ROOT=/usr/share/webapps/nextcloud \
    NC_HOST=nextcloud \
    NC_PORT=9000 \ 
    PUSH_HOST=nextcloud \
    PUSH_PORT=7867 \
    TALK_HOST=talk \
    TALK_PORT=8081 \
    OO_HOST=onlyoffice \
    OO_PORT=80 \
    GUAC_HOST=guacamole \
    GUAC_PORT=8080 \
    ADMINER_HOST=adminer \ 
    ADMINER_PORT=8080 

# Nginx Configurations
ENV CLIENT_MAX_BODY_SIZE=0 \
    CLIENT_BODY_BUFFER_SIZE=512k \ 
    PROXY_READ_TIMEOUT=86400s \
    FASTCGI_BUFFERS=64 \
    FASTCGI_BUFFERS_SIZE=128k \
    NGINX_ENTRYPOINT_WORKER_PROCESSES_AUTOTUNE=true \
    NGINX_ENVSUBST_OUTPUT_DIR=/etc/nginx/http.d

COPY nextcloud.conf /etc/nginx/templates/default.conf.template
COPY --chmod=755 --from=nginx /docker-entrypoint.d/* /init.d/start/
COPY --chmod=755 init.d /init.d
COPY --chmod=755 healthcheck.d /healthcheck.d

RUN rm /init.d/start/10-listen-on-ipv6-by-default.sh
