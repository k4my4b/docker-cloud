#!/usr/bin/env sh

# Check for NGINX configuration syntax issues
nginx -t 2>&1 | grep -q "successful" || {
    echo "Nginx configuration test failed"
    exit 1
}

# Check if NGINX service is running
pgrep "nginx" > /dev/null || {
    echo "Nginx is not running"
    exit 1
}

# Check if the main NGINX process is responding
curl -s --head --max-time 5 localhost > /dev/null || {
    echo "Nginx is not reachable"
    exit 1
}

# Check for recent critical errors in NGINX error log
tail -n 100 /var/log/nginx/error.log | grep -i "crit" && {
    echo "Critical error found in Nginx error log"
    exit 1
}

echo "Nginx is healthy"
exit 0
