#!/usr/bin/env sh

nc -z 127.0.0.1 9000 || {
    echo "Can't reach nextcloud on local port 9000"
    exit 1
}

echo "Nextcloud is healthy"
exit 0
