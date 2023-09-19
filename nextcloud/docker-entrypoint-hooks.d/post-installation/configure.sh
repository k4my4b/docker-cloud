#!/bin/sh

# apps
php occ app:disable firstrunwizard

# config 
php occ config:system:set --type=boolean --value="$KNOWLEDGEBASE_ENABLED" -- knowledgebaseenabled 
php occ config:system:set --type=string --value="$DEFAULT_PHONE_REGION" -- default_phone_region 