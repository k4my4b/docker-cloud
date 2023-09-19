#!/bin/sh

# apps
php occ app:disable firstrunwizard
php occ app:disable dashboard
php occ app:disable support
php occ app:disable survey_client
php occ app:disable federation
php occ app:disable weather_status
php occ app:disable nextcloud_announcements
php occ app:disable updatenotification
php occ app:enable twofactor_totp
php occ app:enable suspicious_login
php occ app:enable files_external
php occ app:enable admin_audit
php occ app:install impersonate
php occ app:install groupfolders
php occ app:install deck
php occ app:install forms
php occ app:install terms_of_service
php occ app:install calendar 
php occ app:install contacts
php occ app:install mail
php occ app:install spreed
php occ app:install quota_warning

# config 
php occ config:system:set --type=boolean --value="$DISABLE_WEB_UPGRADE" -- upgrade.disable-web 
php occ config:system:set --type=boolean --value="$KNOWLEDGEBASE_ENABLED" -- knowledgebaseenabled 
php occ config:system:set --type=string --value="$SKELETON_DIRECTORY" -- skeletondirectory
php occ config:system:set --type=string --value="$DEFAULT_PHONE_REGION" -- default_phone_region 
php occ config:system:set --type=string --value="$FORCE_LANGUAGE" -- force_language 
php occ config:system:set --type=string --value="$FORCE_LOCALE" -- force_locale
php occ config:system:set --type=string --value="$THEME" -- theme 
php occ config:app:set --value="$([ $ENABLE_TALK_CHANGELOG ] && echo yes || echo no)" -- spreed changelog
php occ twofactorauth:enforce "$([ $ENFORCE_TWOFACTORAUTH ] && echo --on || echo --off)"
php occ config:app:set --value="$([ $HARDENED_PASSWORD_POLICY ] && echo 1 || echo 0)" -- password_policy enforceUpperLowerCase
php occ config:app:set --value="$([ $HARDENED_PASSWORD_POLICY ] && echo 1 || echo 0)" -- password_policy enforceNumericCharacters
php occ config:app:set --value="$([ $HARDENED_PASSWORD_POLICY ] && echo 1 || echo 0)" -- password_policy enforceSpecialCharacters
php occ config:app:set --value="$([ $HARDENED_PASSWORD_POLICY ] && echo 10 || echo 0)" -- password_policy maximumLoginAttempts
php occ config:app:set --value="$([ $ENABLE_NOTIFICATION_SOUND ] && echo yes || echo no)" -- notifications sound_notification
php occ config:app:set --value="$([ $ENABLE_NOTIFICATION_SOUND ] && echo yes || echo no)" -- notifications sound_talk