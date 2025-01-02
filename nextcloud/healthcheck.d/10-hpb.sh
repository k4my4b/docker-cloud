#!/bin/sh

# No point in going any further if HPB has been explicitly disabled
ENABLE_HPB_LOWER=$(echo "${ENABLE_HPB:-true}" | tr '[:upper:]' '[:lower:]')
test "$ENABLE_HPB_LOWER" = "no" || test "$ENABLE_HPB_LOWER" = "false" && {
    echo "Skipping HPB (High Performance Backend)."
    exit 0
}

# Check if required commands are available
command -v occ > /dev/null || { echo "command not found: occ "; exit 1; }
command -v jq > /dev/null || { echo "command not found: jq"; exit 1; }

# Switch to nextcloud user if running as root
test "$(id -u)" -eq 0 && exec su -s /bin/sh -c "$0" nextcloud

test "$(occ status --no-warnings --output=json | jq -r '.installed')" = "false" && {
    echo "nextcloud is not installed"
    exit 0
}

occ app:getpath notify_push > /dev/null || {
    echo "High performance backend is enabled but is missing"
    exit 1
}

test "$(occ config:app:get notify_push enabled)" != "yes" && {
    echo "High performance backend has been disabled via nextcloud."
    exit 0
}

occ notify_push:self-test && {
    echo "High performance backend is healthy"
    exit 0
}

echo "High performance backend is unhealthy"
exit 1
