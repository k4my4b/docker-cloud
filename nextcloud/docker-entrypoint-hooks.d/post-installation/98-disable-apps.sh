#!/bin/sh

IFS=' '
# shellcheck disable=SC2086
set -- $NC_DISABLED_APPS

for app in "$@"; do
    php occ app:disable "$app" || exit 1
done
