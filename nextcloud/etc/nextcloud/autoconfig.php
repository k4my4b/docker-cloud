<?php
function getValidEnv($env, $default = null, $regex = null) {
    $value = null;

    // If no regex is provided, treat it as a boolean or return the default value
    if (empty($regex)) {
        $value = filter_var(getenv($env) ?: 'null', FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    } else {
        // Validate the environment variable against the provided regex
        $value = filter_var(getenv($env), FILTER_VALIDATE_REGEXP, [
            "options" => ["regexp" => $regex]
        ]) ?: $default;
    }

    // Throw an exception if both value and default are not set or empty
    if (($value === '' || $value === null) && ($default === '' || $default === null)) {
        throw new Exception("'$env' is not set (correctly) or is empty and no default is provided.");
    }

    // Log the environment variable and if it's using the default
    error_log("[AUTOCONFIG] '$env' => " . var_export(
        is_array($value) ? implode(' ', $value) : $value,
        true
    ) . (($default === $value) ? ' (default)' : ''));

    return $value;
}

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

$ISO_3166_1="/^[A-Z]{2}$/";
$IETF_BCP_47="/^[a-zA-Z]{2,8}(-[a-zA-Z]{2,8})*(-[a-zA-Z]{2,8})*$/";
$IANA_TIMEZONE="/^[A-Za-z]+(?:_[A-Za-z]+)?(?:\/[A-Za-z]+(?:_[A-Za-z0-9-]+)*)+$/";
$UNIX_PATH="/^\/(?:[^\/\0]+\/)*[^\/\0]*$/";
$IP_CIDR='/((\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?)/';
$STRING='/.*/';
$PORT='/^([0-9]|[1-9][0-9]{1,3}|[1-5][0-9]{4}|6[0-4][0-9]{3}|65[0-4][0-9]{2}|655[0-2][0-9]|6553[0-5])$/';

// For now at least, only PostgreSQL is supported
// TODO remove this warning when more databases are supported or ...
$AUTOCONFIG = [
    'dbtype' => 'pgsql',
    'directory' => getValidEnv('DATA_DIR', '/var/lib/nextcloud/data', $UNIX_PATH),
    'dbtableprefix' => getValidEnv('DB_TABLE_PREFIX', 'oc_', $STRING),
    'dbpersistent' => true,
];

// Database settings
try {
    $dbConfig = [
        'dbname' => getValidEnv('POSTGRES_DB', null, $STRING),
        'dbuser' => getValidEnv('POSTGRES_USER', null, $STRING),
        'dbpass' => getValidEnv('POSTGRES_PASSWORD', null, $STRING),
        'dbhost' => sprintf('%s:%s', getValidEnv('POSTGRES_HOST', null, $STRING), getValidEnv('POSTGRES_PORT', 5432, $PORT)),
    ];
    $AUTOCONFIG = array_merge($AUTOCONFIG, $dbConfig);
} catch(Exception $e) {
    error_log('[AUTOCONFIG] Skipping database settings: ' . $e->getMessage());
}

// Admin account settings
try {
    $adminConfig = [
        'adminlogin' => getValidEnv('ADMIN_LOGIN', null, $STRING),
        'adminpass' => getValidEnv('ADMIN_PASS', null, $STRING),
    ];
    $AUTOCONFIG = array_merge($AUTOCONFIG, $adminConfig);
} catch(Exception $e) {
    error_log('[AUTOCONFIG] Skipping admin account setup: ' . $e->getMessage());
}

// Reverse-proxy settings
try {
    $file = OC::$SERVERROOT.'/config/reverse-proxy.config.php';

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
    error_log('[AUTOCONFIG] Skipping reverse-proxy configuration: ' . $e->getMessage());
}

// Basic defaults and config
try {
    $file = OC::$SERVERROOT.'/config/base.config.php';

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
    error_log('[AUTOCONFIG] Skipping base configuration: ' . $e->getMessage());
}

// Theming
try {
    $file = OC::$SERVERROOT.'/config/theme.config.php';

    $config = [
        'theme' => getValidEnv("THEME", "docker-cloud", $STRING),
    ];

    writeConfigFile($file, $config);
} catch (Exception $e) {
    error_log('[AUTOCONFIG] Skipping theme configuration: ' . $e->getMessage());
}

// Preview settings
try {
    $file = OC::$SERVERROOT.'/config/preview.config.php';

    $config = [
        'enable_previews' => true,
        'preview_format' => 'webp',
        'cropimagepreviews' => false,
        'preview_max_x' => 2048,
        'preview_max_y' => 2048,
        'preview_max_filesize_image' => 50,
        'preview_max_memory' => 256,
        'enabledPreviewProviders' => [
            'OC\Preview\BMP',
            'OC\Preview\GIF',
            'OC\Preview\JPEG',
            'OC\Preview\Krita',
            'OC\Preview\MarkDown',
            'OC\Preview\MP3',
            'OC\Preview\OpenDocument',
            'OC\Preview\PNG',
            'OC\Preview\TXT',
            'OC\Preview\XBitmap',
        ],
    ];

    // Set preview concurrency based on the number of available CPU cores
    $cpuCores = intval(shell_exec('nproc') ?? 0);
    $config['preview_concurrency_all'] = $cpuCores > 0 ? $cpuCores * 2 : 8;
    $config['preview_concurrency_new'] = $cpuCores > 0 ? $cpuCores : 4;

    // Set LibreOffice path if available
    $libreofficePath = trim(shell_exec('which libreoffice') ?? '');
    if (realpath($libreofficePath)) {
        $config['preview_libreoffice_path'] = $libreofficePath;
    }

    // Set ffmpeg path if available and add movie preview provider
    $ffmpegPath = trim(shell_exec('which ffmpeg') ?? '');
    if (realpath($ffmpegPath)) {
        $config['preview_ffmpeg_path'] = $ffmpegPath;
        $config['enabledPreviewProviders'][] = 'OC\Preview\Movie';
    }

    // Add Imaginary preview if IMAGINARY_HOST and IMAGINARY_PORT environment variables are set
    try {
        $imaginaryHost = getValidEnv('IMAGINARY_HOST', null, $STRING);
        $imaginaryPort = getValidEnv('IMAGINARY_PORT', null, $PORT);
        $config['preview_imaginary_url'] = sprintf('http://%s:%s', $imaginaryHost, $imaginaryPort);
        $config['enabledPreviewProviders'][] = 'OC\Preview\Imaginary';
        $config['enabledPreviewProviders'][] = 'OC\Preview\ImaginaryPDF';

        // Add Imaginary secret key if available
        try {
            $imaginarySecret = getValidEnv('IMAGINARY_SECRET', null, $STRING);
            if (!empty($imaginarySecret)) {
                $config['preview_imaginary_key'] = $imaginarySecret;
            }
        } catch (Exception $e) {
            error_log('[AUTOCONFIG] [WARNING] ' . $e->getMessage());
        }
    } catch (Exception $e) {
        error_log('[AUTOCONFIG] Skipping "imaginary" configuration: ' . $e->getMessage());
    }

    writeConfigFile($file, $config);
} catch (Exception $e) {
    error_log('[AUTOCONFIG] Skipping preview configuration: ' . $e->getMessage());
}
