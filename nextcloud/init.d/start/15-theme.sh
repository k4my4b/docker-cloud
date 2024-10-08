#!/bin/sh

occ config:system:set \
    --type=string \
    --value="$THEME" \
    -- \
    theme || exit 1

occ config:app:set \
    --value="backgroundColor" \
    -- \
    theming backgroundMime || exit 1

occ config:app:set \
    --value="image\/svg+xml" \
    -- \
    theming logoMime || exit 1

occ config:app:set \
    --value="image\/svg+xml" \
    -- \
    theming logoheaderMime || exit 1

occ config:app:set \
    --value="image\/svg+xml" \
    -- \
    theming faviconMime || exit 1

occ config:app:set \
    --value="#3794AC" \
    -- \
    theming color || exit 1
