#!/bin/sh

occ config:app:set \
    --value="$([ "$HARDENED_PASSWORD_POLICY" = true ] && echo 1 || echo 0)" \
    -- \
    password_policy enforceUpperLowerCase || exit 1

occ config:app:set \
    --value="$([ "$HARDENED_PASSWORD_POLICY" = true ] && echo 1 || echo 0)" \
    -- \
    password_policy enforceNumericCharacters || exit 1

occ config:app:set \
    --value="$([ "$HARDENED_PASSWORD_POLICY" = true ] && echo 1 || echo 0)" \
    -- \
    password_policy enforceSpecialCharacters || exit 1

occ config:app:set \
    --value="$([ "$HARDENED_PASSWORD_POLICY" = true ] && echo 10 || echo 0)" \
    -- \
    password_policy maximumLoginAttempts || exit 1
