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

# config 
php occ config:system:set --type=boolean --value="$DISABLE_WEB_UPGRADE" -- upgrade.disable-web 
php occ config:system:set --type=boolean --value="$KNOWLEDGEBASE_ENABLED" -- knowledgebaseenabled 
php occ config:system:set --type=string --value="$SKELETON_DIRECTORY" -- skeletondirectory
php occ config:system:set --type=string --value="$DEFAULT_PHONE_REGION" -- default_phone_region 
php occ config:system:set --type=string --value="$FORCE_LANGUAGE" -- force_language 
php occ config:system:set --type=string --value="$FORCE_LOCALE" -- force_locale
php occ config:system:set --type=string --value="$THEME" -- theme 
php occ config:app:set --value="$TALK_CHANGELOG" -- spreed changelog