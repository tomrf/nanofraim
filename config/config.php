<?php

declare(strict_types=1);

return [
    'environment' => $_ENV['ENVIRONMENT'] ?? 'prod',
    'basePath' => $basePath,
    'middleware' => [
        \App\Http\Middleware\Headers::class,
        \App\Http\Middleware\Session::class,
        \App\Http\Middleware\Dummy::class,
        \Nanofraim\Http\Middleware\NotFound::class,
    ],
    'services' => require 'services.php',
    'phpIni' => require 'phpIni.php',
];
