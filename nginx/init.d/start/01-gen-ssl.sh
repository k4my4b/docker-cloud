#!/bin/sh

SSL_DIR="/etc/nginx/ssl"

# Check if the SSL directory exists
test -d "$SSL_DIR" || {
    echo "$SSL_DIR doesn't exist"
    exit 1
}

# Check if both certificate and key exist
if [ -f "$SSL_DIR/nginx.crt" ] && [ -f "$SSL_DIR/nginx.key" ]; then
    echo "SSL certificate already exists, skipping"
    exit 0
fi

# Generate self-signed SSL certificates
echo "Generating (self-signed) SSL certificates"
exec openssl ecparam -name prime256v1 -genkey -noout -out "$SSL_DIR/nginx.key" && \
    openssl req -x509 -new -key "$SSL_DIR/nginx.key" -out "$SSL_DIR/nginx.crt" \
    -days 36500 -subj "/C=US/ST=State/L=City/O=Organization/OU=Unit/CN=localhost"
