#!/bin/sh

php occ app:install external || exit 1 

php occ config:app:set \
    --value="{\"1\":{\"id\":1,\"name\":\"Remote\",\"url\":\"https:\/\/$OVERWRITEHOST\/guacamole\",\"lang\":\"\",\"type\":\"link\",\"device\":\"\",\"icon\":\"remote.svg\",\"groups\":[],\"redirect\":false}}" \
    -- external sites || exit 1
