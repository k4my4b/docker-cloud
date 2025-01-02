#!/bin/sh

# No point in going any further if HPB has been explicitly disabled
ENABLE_HPB_LOWER=$(echo "${ENABLE_HPB:-true}" | tr '[:upper:]' '[:lower:]')
test "$ENABLE_HPB_LOWER" = "no" || test "$ENABLE_HPB_LOWER" = "false" && {
    echo "Skipping HPB (High Performance Backend)."
    exit 0
}

# Switch to nextcloud user if running as root
test "$(id -u)" -eq 0 && exec su -s /bin/sh -c "$0" nextcloud

occ app:getpath notify_push || {
    echo "Installing high performance backend"
    occ app:install notify_push || {
        echo "Couldn't install the high performance backend."
        exit 1
    }
}

test "$(occ config:app:get notify_push enabled)" != "yes" && {
    echo "High performance backend has been disabled via nextcloud."
    exit 0
}

echo "Starting High Performance Backend"

PORT="${PUSH_PORT:-7867}"
NOTIFY_PUSH_PATH="$(occ app:getpath notify_push)/bin/$(uname -m)/notify_push"
"$NOTIFY_PUSH_PATH" --port "$PORT" /etc/nextcloud/config.php &

# Capture the PID of notify_push
PUSH_PID=$!

# Wait for notify_push to start
sleep 2

# Check if the process directory exists in /proc
test -d /proc/$PUSH_PID && {
    echo "High performance backend is running."
    echo "Configuring High Performance Backend"
    NEXTCLOUD_URL="$(occ config:system:get overwrite.cli.url)"
    for _ in $(seq 1 6); do
        occ notify_push:setup "$NEXTCLOUD_URL/push" && {
            echo "High Performance Backend was configured successfully."
            echo "Listening on port: $PORT"
            echo "Configuration URL: \"$NEXTCLOUD_URL/push\""
            exit 0
        }
        echo "Couldn't configure High Performance Backend. Retrying in 5 seconds..."
        sleep 5
    done
}

echo "Failed to start and configure High Performance Backend."
exit 1
