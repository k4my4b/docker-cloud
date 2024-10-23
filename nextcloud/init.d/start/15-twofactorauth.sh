#!/bin/sh

occ app:enable twofactor_totp || exit 1

occ twofactorauth:enforce \
    "$([ "$ENFORCE_TWOFACTORAUTH" = true ] && echo '--on' || echo '--off')" || exit 1
