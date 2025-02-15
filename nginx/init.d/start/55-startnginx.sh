#!/bin/sh

echo "Starting Nginx"
nginx -g "daemon off;" -c /etc/nginx/nginx.conf &

# Capture the PID of the last command executed (nginx)
NGINX_PID=$!

# Wait for nginx to start
sleep 2

# Check if the process directory exists in /proc
test -d /proc/$NGINX_PID && {
    echo "Nginx started successfully"
    exit 0
}

echo "Failed to start Nginx"
exit 1
