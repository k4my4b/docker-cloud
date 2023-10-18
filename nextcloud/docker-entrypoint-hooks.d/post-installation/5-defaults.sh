#!/bin/sh

php occ config:system:set \
    --type=boolean --value="$KNOWLEDGEBASE_ENABLED" \
    -- knowledgebaseenabled || exit 1

php occ config:system:set \
    --type=string \
    --value="$DEFAULT_LANGUAGE" \
    -- \
    default_language || exit 1

php occ config:system:set \
    --type=string \
    --value="$DEFAULT_LOCALE" \
    -- \
    default_locale || exit 1

php occ config:system:set \
    --type=string \
    --value="$DEFAULT_PHONE_REGION" \
    -- \
    default_phone_region || exit 1

php occ config:system:set \
    --type=string \
    --value="$SKELETON_DIRECTORY" \
    -- \
    skeletondirectory || exit 1

php occ config:system:set \
    --type=boolean \
    --value="$DISABLE_WEB_UPGRADE" \
    -- \
    upgrade.disable-web || exit 1
