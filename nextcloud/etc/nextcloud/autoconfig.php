<?php
function getEnvValue($varName, $default = null) {
    $value = trim(getenv($varName) ?: $default);

    if ($value === '' && $default === null) {
        throw new Exception("'$varName' is not set or is empty and no default is provided.");
    }

    return $value;
}

// For now at least, only PostgreSQL is supported
// TODO remove this warning when more databases are supported or ...
$AUTOCONFIG = [
    'dbtype' => 'pgsql',
    'directory' => getEnvValue('DATA_DIR', '/var/lib/nextcloud/data'),
    'dbtableprefix' => getEnvValue('DB_TABLE_PREFIX', 'oc_'),
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
