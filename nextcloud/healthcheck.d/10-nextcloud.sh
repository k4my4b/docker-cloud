#!/bin/sh

# Check if required commands are available
command -v occ > /dev/null || { echo "command not found: occ "; exit 1; }
command -v jq > /dev/null || { echo "command not found: jq"; exit 1; }
command -v curl > /dev/null || { echo "command not found: curl"; exit 1; }
command -v nc > /dev/null || { echo "command not found: nc"; exit 1; }

nc -z 127.0.0.1 9000 || {
    echo "Unit is not listening on local port 9000"
    exit 1
}

curl -sf localhost:9000 > /dev/null || {
    echo "Failed to get a status response from unit"
    exit 1
}

test "$(occ status --no-warnings --output=json | jq -r '.installed')" = "false" && {
    echo "Nextcloud is not installed"
    exit 0
}

curl -fs localhost > /dev/null || {
    echo "Failed to reach nextcloud on localhost"
    exit 1
}

echo "Nextcloud is healthy"
exit 0
