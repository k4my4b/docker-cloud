#!/bin/sh

php occ config:system:set \
    --type=string \
    --value="$THEME" \
    -- \
    theme || exit 1
