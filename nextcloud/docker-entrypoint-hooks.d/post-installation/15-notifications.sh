#!/bin/sh

php occ config:app:set \
    --value="$([ "$ENABLE_NOTIFICATION_SOUND" = true ] && echo 'yes' || echo 'no')" \
    -- \
    notifications sound_notification || exit 1

php occ config:app:set \
    --value="$([ "$ENABLE_NOTIFICATION_SOUND" = true ] && echo 'yes' || echo 'no')" \
    -- \
    notifications sound_talk || exit 1
