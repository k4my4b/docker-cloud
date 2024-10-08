#!/bin/sh

echo "Starting crond"
# Start crond in the background
crond -f -c /etc/crontabs -L /dev/stdout &

# Capture the PID of the last command executed (crond)
CROND_PID=$!

# Wait for crond to start
sleep 2

# Check if the process directory exists in /proc
test -d /proc/$CROND_PID && {
    echo "crond started successfully"
    exit 0
}

echo "Failed to start crond"
exit 1
