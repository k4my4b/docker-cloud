<?php
$CONFIG = [
    'knowledgebaseenabled' => filter_var(getenv("KNOWLEDGEBASE_ENABLED") ?: 'null', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
    'default_language' => getenv("DEFAULT_LANGUAGE") ?: "en_GB",
    'default_locale' => getenv("DEFAULT_LOCALE") ?: "en_GB",
    'default_phone_region' => getenv("DEFAULT_PHONE_REGION") ?: "GB",
    'default_timezone' => getenv("DEFAULT_TIMEZONE") ?: "Etc/GMT",
    'skeletondirectory' => getenv("SKELETON_DIRECTORY") ?: "/usr/share/webapps/nextcloud/core/skeleton",
    'upgrade.disable-web' => filter_var(getenv("DISABLE_WEB_UPGRADE") ?: 'null', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
];
