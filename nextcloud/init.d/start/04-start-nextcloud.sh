#!/bin/sh

echo "Starting unitd"
# Start unitd in the background
unitd --no-daemon --user unit --group www-data --control 0.0.0.0:9000 &

# Capture the PID of the last command executed (unitd)
UNITD_PID=$!

# Wait for unitd to start
sleep 2

UNITD_CONFIG="etc/unit/nextcloud.json"
# Check if the process directory exists in /proc
test -d /proc/$UNITD_PID && {
    echo "unitd started successfully"
    curl -s -X PUT --data-binary "@$UNITD_CONFIG" localhost:9000/config/ && {
        echo "Nextcloud started successfully"
        exit 0
    }
    echo "Failed to start nextcloud"
    exit 1
}

echo "Failed to start unitd"
exit 1
