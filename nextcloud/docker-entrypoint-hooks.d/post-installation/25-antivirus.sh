#!/bin/sh

php occ app:install files_antivirus || exit 1

php occ config:app:set \
    --value="daemon" \
    -- \
    files_antivirus av_mode || exit 1

php occ config:app:set \
    --value="$AV_HOST" \
    -- \
    files_antivirus av_host || exit 1

php occ config:app:set \
    --value="$AV_PORT" \
    -- \
    files_antivirus av_port || exit 1

php occ config:app:set \
    --value="$AV_STREAM_MAX_LENGTH" \
    -- \
    files_antivirus av_stream_max_length || exit 1

php occ config:app:set \
    --value="$AV_MAX_FILE_SIZE" \
    -- \
    files_antivirus av_max_file_size || exit 1
