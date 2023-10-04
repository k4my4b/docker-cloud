FROM nginx:mainline-alpine

# Hosts and Ports 
ENV NC_HOST=nextcloud \
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
    FASTCGI_BUFFERS_SIZE=128k 

COPY nextcloud.conf /etc/nginx/templates/default.conf.template 

HEALTHCHECK --interval=30s --timeout=3s --start-period=30s --retries=3 \
    CMD curl -sfL http://localhost/status.php || exit 1
