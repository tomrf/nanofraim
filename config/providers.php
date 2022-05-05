<?php

declare(strict_types=1);

use App\Http\Controller\DummyController;

return [
    \App\Service\DummyRouter::class => [
        'routes' => [
            'GET:/' => [DummyController::class, 'getHome'],
            'POST:/api' => [DummyController::class, 'postApi'],
        ],
    ],
    \Nanofraim\Provider\Monolog::class => [
        'path' => $storagePath.'/log/app.log',
        'format' => '%datetime% [%level_name%] %message%',
    ],
    \Nanofraim\Provider\SimpleCacheProvider::class => [
        'adapter' => 'filesystem',
        'adapters' => [
            'filesystem' => [
                'root' => $storagePath.'/cache',
                'directory' => 'filecache',
            ],
            'redis' => [
                'hostname' => $_ENV['REDIS_HOSTNAME'] ?? '127.0.0.1',
                'port' => $_ENV['REDIS_PORT'] ?? '6379',
                'auth' => $_ENV['REDIS_AUTH'] ?? null,
                'timeout' => 0,
            ],
        ],
    ],
    \Nanofraim\Provider\ResponseFactoryProvider::class => null,
];
