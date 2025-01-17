<?php
function getEnvValue($varName, $default = null) {
    $value = trim(getenv($varName) ?: $default);

    if ($value === '' && $default === null) {
        throw new Exception("'$varName' is not set or is empty and no default is provided.");
    }

    return $value;
}

function getValidEnv($env, $default = null, $regex = null) {
    $value = null;

    if ($regex === '' || $regex === null) {
        $value = filter_var(getenv($env) ?: 'null',
                FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    } else {
        $value = filter_var(getenv($env), FILTER_VALIDATE_REGEXP, [
                "options" => ["regexp" => $regex]
        ]) ?: $default;
    }

    if (($value === '' || $value === null) && ($default === '' || $default === null)) {
        throw new Exception("'$env' is not set (correctly) or is empty and no default is provided.");
    }

    error_log("[AUTOCONFIG] '$env' => " . var_export($value, true) . (($default === $value) ? ' (default)' : ''));

    return $value;
};

function readConfigFile($uri) {
    // Invalidate opcache  if the timestamp changed
    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate($uri, false);
    }

    $filePointer = file_exists($uri) ? @fopen($uri, 'r') : false;
    if ($filePointer === false) {
        throw new Exception(sprintf('FATAL: Could not open the config file %s', $uri));
    }

    if (!flock($filePointer, LOCK_SH)) {
        throw new Exception(sprintf('Could not acquire a shared lock on the config file %s', $uri));
    }

    try {
        include $uri;
    } finally {
        flock($filePointer, LOCK_UN);
        fclose($filePointer);
    }

    return (isset($CONFIG) && is_array($CONFIG)) ? $CONFIG : false;
}

function writeConfigFile($uri, $data) {
    $filePointer = file_exists($uri) ? @fopen($uri, 'w+') : false;
    if ($filePointer === false) {
        throw new Exception(sprintf('FATAL: Could not write to the config file %s', $uri));
    }

    if (!flock($filePointer, LOCK_EX)) {
        throw new Exception(sprintf('Could not acquire an exclusive lock on the config file %s', $uri));
    }

    fwrite($filePointer, "<?php\n\$CONFIG = " . var_export($data, true) . ";\n");
    fflush($filePointer);
    flock($filePointer, LOCK_UN);
    fclose($filePointer);

    // Invalidate opcache regardless of timestamp
    if (function_exists('opcache_invalidate')) {
        @opcache_invalidate($uri, true);
    }
}

// For now at least, only PostgreSQL is supported
// TODO remove this warning when more databases are supported or ...
$AUTOCONFIG = [
    'dbtype' => 'pgsql',
    'directory' => getEnvValue('DATA_DIR', '/var/lib/nextcloud/data'),
    'dbtableprefix' => getEnvValue('DB_TABLE_PREFIX', 'oc_'),
    'dbpersistent' => true,
];

// Database settings
try {
    $dbConfig = [
        'dbname' => getEnvValue('POSTGRES_DB'),
        'dbuser' => getEnvValue('POSTGRES_USER'),
        'dbpass' => getEnvValue('POSTGRES_PASSWORD'),
        'dbhost' => sprintf('%s:%s', getEnvValue('POSTGRES_HOST'), getEnvValue('POSTGRES_PORT', 5432)),
    ];
    $AUTOCONFIG = array_merge($AUTOCONFIG, $dbConfig);
} catch(Exception $e) {
    error_log($e->getMessage());
    error_log('Skipping automatic database connection configuration.');
}

// Admin account settings
try {
    $adminConfig = [
        'adminlogin' => getEnvValue('ADMIN_LOGIN'),
        'adminpass' => getEnvValue('ADMIN_PASS'),
    ];
    $AUTOCONFIG = array_merge($AUTOCONFIG, $adminConfig);
} catch(Exception $e) {
    error_log($e->getMessage());
    error_log('Skipping automatic admin account setup.');
}

// Reverse-proxy settings
try {
    $file = OC::$SERVERROOT.'/config/reverse-proxy.config.php';

    $IP_CIDR='/((\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?)/';

    $proxyConfig = [
        'trusted_proxies' => getValidEnv('TRUSTED_PROXIES', (function() use ($file) {
            // Multiple processes may run this code concurrently during installation.
            // This means headers could contain different values at any given time.
            // We append new values to the file rather than overwriting to preserve
            // any existing configuration. We read and merge values from the file.

            // Fallback to '127.0.0.1' if proxy headers are not set. This ensures the
            // configuration always has a value, but beware in a distributed environment.
            $trustedProxies = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? '127.0.0.1'));

            // Merge with existing trusted proxies from the configuration file
            return array_unique(array_merge(readConfigFile($file)['trusted_proxies'] ?? [], $trustedProxies));
        })(), $IP_CIDR),
    ];

    writeConfigFile($file, $proxyConfig);
} catch (Exception $e) {
    error_log($e->getMessage());
    error_log('Skipping automatic reverse-proxy configuration.');
}

// Defaults
try {
    $file = OC::$SERVERROOT.'/config/base.config.php';

    $ISO_3166_1="/^[A-Z]{2}$/";
    $IETF_BCP_47="/^[a-zA-Z]{2,8}(-[a-zA-Z]{2,8})*(-[a-zA-Z]{2,8})*$/";
    $IANA_TIMEZONE="/^[A-Za-z]+(?:_[A-Za-z]+)?(?:\/[A-Za-z]+(?:_[A-Za-z0-9-]+)*)+$/";
    $UNIX_PATH="/^\/(?:[^\/\0]+\/)*[^\/\0]*$/";

    $config = [
        'server_root' => OC::$SERVERROOT,
        'default_language' => getValidEnv("DEFAULT_LANGUAGE", "en_GB", $IETF_BCP_47),
        'default_locale' => getValidEnv("DEFAULT_LOCALE", "en_GB", $IETF_BCP_47),
        'default_phone_region' => getValidEnv("DEFAULT_PHONE_REGION", "GB", $ISO_3166_1),
        'default_timezone' => getValidEnv("DEFAULT_TIMEZONE", "Etc/GMT", $IANA_TIMEZONE),
        'skeletondirectory' => getValidEnv("SKELETON_DIRECTORY", OC::$SERVERROOT.'/core/skeleton', $UNIX_PATH),
        'upgrade.disable-web' => getValidEnv("DISABLE_WEB_UPGRADE", true),
        'knowledgebaseenabled' => getValidEnv("KNOWLEDGEBASE_ENABLED", false),
    ];

    writeConfigFile($file, $config);
} catch (Exception $e) {
    error_log($e->getMessage());
    error_log('Skipping automatic defaults configuration.');
}
