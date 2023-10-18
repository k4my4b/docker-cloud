#!/bin/sh

IFS=' '
# shellcheck disable=SC2086
set -- $NC_ENABLED_APPS

for app in "$@"; do
    php occ app:enable "$app" || exit 1
done
