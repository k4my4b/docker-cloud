#!/bin/sh

php occ config:system:set \
    --value="OC\\Preview\\Imaginary" \
    -- \
    enabledPreviewProviders 0 || exit 1

php occ config:system:set \
    --value="http://$IMAGINARY_HOST:$IMAGINARY_PORT" \
    -- \
    preview_imaginary_url || exit 1
