<?php

require_once __DIR__.'/prod.php';

$app['debug'] = true;

$app['ws'] = [
    'port' => 8085
];

$app['redis'] = [
    'server' => [
        'scheme' => 'tcp',
        'host' => 'redis-server.dev',
        'port' => 6379
    ],
    'default_channel' => 'channel:btcticker'
];