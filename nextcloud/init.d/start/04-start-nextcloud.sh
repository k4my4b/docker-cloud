#!/bin/sh

# Switch to nextcloud user if running as root
test "$(id -u)" -eq 0 && exec su -s /bin/sh -c "$0" nextcloud

echo "Starting nextcloud"
# Start php-fpm82 in the background
php-fpm82 --nodaemonize --fpm-config /etc/php82/php-fpm.d/nextcloud.conf &

# Capture the PID of the last command executed (php-fpm)
PHP_FPM_PID=$!

# Wait for php-fpm to start
sleep 2

# Check if the process directory exists in /proc
test -d /proc/$PHP_FPM_PID && {
    echo "nextcloud started successfully"
    exit 0
}

echo "Failed to start nextcloud"
exit 1
