<?php

declare(strict_types=1);

return [
    \Nanofraim\ServiceProvider\TwigEnvironment::class => [
        'templatesPath' => $basePath.'/resource/template',
        'cachePath' => $storagePath.'/cache/twig',
        'cache' => false,
        'debug' => true,
    ],
    \Nanofraim\ServiceProvider\Monolog::class => [
        'path' => $storagePath.'/logs/app.log',
        'format' => '%datetime% [%level_name%] %message%',
    ],
    \Nanofraim\ServiceProvider\SimpleCacheProvider::class => [
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
