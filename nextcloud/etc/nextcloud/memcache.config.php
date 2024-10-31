<?php
# TODO test whether "acpu" for memcache.local is any faster than redis 
// Base memcache configuration
$CONFIG = ['memcache.local' => '\OC\Memcache\Redis'];

$redisHost = getenv('REDIS_HOST');
$redisPort = getenv('REDIS_PORT');
$redisSeeds = getenv('REDIS_SEEDS');

// Ensure Redis configuration is valid: Either host and port or seeds must be set, but not both
if ((!empty($redisHost) && !empty($redisPort)) xor (!empty($redisSeeds))) {
    $CONFIG = array_merge($CONFIG, [
        'memcache.locking' => '\OC\Memcache\Redis',
        'memcache.distributed' => '\OC\Memcache\Redis',
    ]);

    $conf = [
        'timeout' => 1.5,
        'read_timeout' => 1.5,
    ];

    // Redis Authentication (optional, depending on environment variables)
    $redisUser = getenv('REDIS_USER');
    $redisPass = getenv('REDIS_PASSWORD');
    if (!empty($redisPass) && !empty($redisUser)) {
        $conf['user'] = $redisUser;
        $conf['password'] = $redisPass;
    }

    // SSL context configuration for Redis (optional, depends on environment variables)
    $redisCert = getenv('REDIS_CERT');
    $redisKey = getenv('REDIS_KEY');
    $redisCA = getenv('REDIS_CA');
    if (!empty($redisCert) && !empty($redisKey) && is_readable($redisCert) && is_readable($redisKey)) {
        $conf['ssl_context'] = [
            'local_cert' => $redisCert,
            'local_pk' => $redisKey,
        ];
        if (!empty($redisCA) && is_readable($redisCA)) {
            $conf['ssl_context']['cafile'] = $redisCA;
        }
    }

    // Configuration for Redis Cluster (with seeds) or single Redis host
    if (!empty($redisSeeds)) {
        $CONFIG['redis.cluster']['seeds'] = array_filter(array_map('trim', explode(' ', $redisSeeds)));
        $CONFIG['redis.cluster']['failover_mode'] = \RedisCluster::FAILOVER_ERROR;
        $CONFIG['redis.cluster'] = array_merge($CONFIG['redis.cluster'], $conf);
    } else {
        $CONFIG['redis']['host'] = $redisHost;
        $CONFIG['redis']['port'] = ($redisHost[0] != '/') ? $redisPort : 0;
        // Redis DB index (optional, if undefined SELECT will not run and will use Redis Server's default DB Index.)
        $redisDbIndex = getenv('REDIS_DB_INDEX');
        if (!empty($redisDbIndex)) {
            $conf['dbindex'] = intval($redisDbIndex);
        }
        $CONFIG['redis'] = array_merge($CONFIG['redis'], $conf);
    }
} else {
    throw new \Exception("Invalid Redis configuration: Either specify host and port or seeds, but not both.");
}
