#!/bin/sh

command -v occ > /dev/null || {
    echo "Cannot find occ"
    exit 1
} 

while test "$(occ status --no-warnings --output=json | jq -r '.installed')" = "false"; do
    echo "Nextcloud is not installed! Waiting ..."
    sleep 5
done

echo "Nextcloud is installed!"
exit 0
