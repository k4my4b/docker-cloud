#!/bin/sh

# Check if required commands are available
for cmd in "occ" "jq"; do
    command -v "$cmd" > /dev/null || { echo "Error: Command not found: $cmd"; exit 1; }
done

# Wait for nextcloud to be installed
while test "$(occ status --no-warnings --output=json | jq -r '.installed')" != "true"; do
    echo "Nextcloud is not installed! Waiting ..."
    sleep 5
done

echo "Nextcloud is installed!"

# Delete the script once Nextcloud is installed (if possible)
if [ -f "$0" ]; then
    rm -f "$0" || { echo "Failed to delete the script. Please remove it manually."; exit 1; }
fi

exit 0
