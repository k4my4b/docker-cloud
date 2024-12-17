#!/bin/sh

# Check if required commands are available
command -v occ > /dev/null || { echo "command not found: occ "; exit 1; }
command -v jq > /dev/null || { echo "command not found: jq"; exit 1; }
command -v curl > /dev/null || { echo "command not found: curl"; exit 1; }

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
    RESPONSE="$(curl -sX PUT --data-binary "@$UNITD_CONFIG" localhost:9000/config/)"
    echo "$RESPONSE" | jq -e '.success' > /dev/null && {
        echo "Nextcloud started successfully"
        while test "$(occ status --no-warnings --output=json | jq -r '.installed')" = "false"; do
            echo "Nextcloud is not installed! Waiting ..."
            sleep 5
        done
        echo "Nextcloud is installed!"
        exit 0
    }
    echo "$RESPONSE"
    echo "Failed to start nextcloud"
    exit 1
}

echo "Failed to start unitd"
exit 1
