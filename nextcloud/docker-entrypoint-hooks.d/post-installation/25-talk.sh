#!/bin/sh

php occ app:install spreed || exit 1

php occ config:app:set \
    --value="$([ "$ENABLE_TALK_CHANGELOG" = true ] && echo 'yes' || echo 'no')" \
    -- spreed changelog || exit 1
