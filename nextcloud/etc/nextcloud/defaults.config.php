<?php
$CONFIG = [
    'knowledgebaseenabled' => getenv("KNOWLEDGEBASE_ENABLED") ?: false,
    'default_language' => getenv("DEFAULT_LANGUAGE") ?: "en_GB",
    'default_locale' => getenv("DEFAULT_LOCALE") ?: "en_GB",
    'default_phone_region' => getenv("DEFAULT_PHONE_REGION") ?: "GB",
    'default_timezone' => getenv("DEFAULT_TIMEZONE") ?: "Etc/GMT",
    'skeletondirectory' => getenv("SKELETON_DIRECTORY") ?: "/usr/share/webapps/nextcloud/core/skeleton",
    'upgrade.disable-web' => getenv("DISABLE_WEB_UPGRADE") ?: true,
];
