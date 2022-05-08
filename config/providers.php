<?php

declare(strict_types=1);

use App\Http\Controller\TestController;

return [
    \Nanofraim\Provider\ResponseFactoryProvider::class => null,
    \Nanofraim\Provider\SessionProvider::class => null,
    \App\Service\DummyRouter::class => [
        'routes' => [
            'GET:/' => [TestController::class, 'getHome'],
            'POST:/api' => [TestController::class, 'postApi'],
        ],
    ],
    \Nanofraim\Provider\LoggerProvider::class => [
        'path' => $storagePath.'/log/app.log',
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
];
