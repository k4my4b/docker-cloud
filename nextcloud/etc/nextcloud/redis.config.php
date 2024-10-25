<?php
# TODO test whether "acpu" for memcache.local is any faster than redis 
# TODO add more logic here to handle redis clusters and more secure options
$CONFIG = array (
    'memcache.locking' => '\OC\Memcache\Redis',
    'memcache.distributed' => '\OC\Memcache\Redis',
    'memcache.local' =>'\OC\Memcache\Redis' ,
    'redis' => [
        'host' => getenv('REDIS_HOST'),
        'port' => getenv('REDIS_PORT'),
        'timeout' => 1.5,
        'read_timeout' => 1.5,
    ],
);
