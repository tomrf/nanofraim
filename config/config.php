<?php

declare(strict_types=1);

return [
    'environment' => $_ENV['ENVIRONMENT'] ?? 'prod',
    'basePath' => $basePath,
    'middleware' => [
        \App\Http\Middleware\Headers::class,
        \App\Http\Middleware\Session::class,
        \App\Http\Middleware\Router::class,
        \Nanofraim\Http\Middleware\NotFound::class,
    ],
    'providers' => require 'providers.php',
    'phpIni' => require 'phpIni.php',
];
