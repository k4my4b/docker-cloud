#!/bin/sh

IFS=' '
# shellcheck disable=SC2086
set -- $NC_ADDITIONAL_APPS

for app in "$@"; do
    php occ app:install "$app" || exit 1
done
