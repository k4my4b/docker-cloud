#!/bin/sh

# apps
php occ app:disable firstrunwizard

# config 
php occ config:system:set --type=boolean --value="$DISABLE_WEB_UPGRADE" -- upgrade.disable-web 
php occ config:system:set --type=boolean --value="$KNOWLEDGEBASE_ENABLED" -- knowledgebaseenabled 
php occ config:system:set --type=string --value="$SKELETON_DIRECTORY" -- skeletondirectory
php occ config:system:set --type=string --value="$DEFAULT_PHONE_REGION" -- default_phone_region 
php occ config:system:set --type=string --value="$FORCE_LANGUAGE" -- force_language 
php occ config:system:set --type=string --value="$FORCE_LOCALE" -- force_locale
