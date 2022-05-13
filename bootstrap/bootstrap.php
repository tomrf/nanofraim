<?php

declare(strict_types=1);

use Nanofraim\Application;
use Nanofraim\DotEnv;
use Nanofraim\Http\SapiEmitter;
use Nanofraim\Init;
use Relay\Relay;

// set basePath and storagePath, referenced in configuration files
$basePath = dirname(__DIR__);
$storagePath = $basePath.'/storage';

// autoload
require $basePath.'/vendor/autoload.php';

// load environment file if one exists, keeping existing variables
DotEnv::loadDotEnv($basePath.'/.env');

// load configuration, create ConfigContainer
$config = require $basePath.'/config/config.php';
$configContainer = Init::createConfigContainer($config);

// set PHP ini options
$configContainer->setPhpIniFromConfig($config['phpIni']);

// create ServiceContainer
$serviceContainer = Init::createServiceContainer(
    $configContainer,
    $config['providers'],
    $config['middleware'],
);

// create middleware queue using ServiceContainer for dependencies
$middlewareQueue = Init::createMiddlewareQueue(
    $serviceContainer,
    $config['middleware'],
);

// .....
$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

$app = new Application(
    $configContainer,
    $serviceContainer,
    new Relay($middlewareQueue),
    new SapiEmitter(),
    new \Nyholm\Psr7Server\ServerRequestCreator(
        $psr17Factory,
        $psr17Factory,
        $psr17Factory,
        $psr17Factory,
    ),
);

return $app;
