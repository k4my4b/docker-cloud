#!/bin/sh

if "$EMPTY_SKELETON"; then
    find /var/www/html/core/skeleton -mindepth 1 -delete || exit 1
fi