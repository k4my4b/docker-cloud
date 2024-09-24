#!/usr/bin/env sh

# Default healthcheck directory is /healthcheck.d if not passed as an argument
DIR="${1:-/healthcheck.d}"

# Find executable files in the healthcheck directory and run them in parallel
find "$DIR" -type f -executable | parallel --will-cite --halt 2 'sh {}' || {
    echo "Container is unhealthy!"
    kill -TERM 1
    exit 1
}
