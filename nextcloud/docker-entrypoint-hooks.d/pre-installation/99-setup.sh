#!/bin/sh

if [ "$EMPTY_SKELETON" = true ]; then
    find /usr/share/webapps/nextcloud/core/skeleton -mindepth 1 -delete || exit 1
fi