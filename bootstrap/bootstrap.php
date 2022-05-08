<?php

declare(strict_types=1);

use Nanofraim\Init;

// set basePath and storagePath, referenced in configuration files
$basePath = dirname(__DIR__);
$storagePath = $basePath.'/storage';

// autoload
require $basePath.'/vendor/autoload.php';

// load environment file if one exists
if (file_exists($basePath.'/.env')) {
    $env = parse_ini_file($basePath.'/.env', true, INI_SCANNER_TYPED);
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// run Init::setup before initializing anything else
Init::setup();

// load configuration, create ConfigContainer
$config = require $basePath.'/config/config.php';
$configContainer = Init::createConfigContainer($config);

// set PHP ini options
$configContainer->setPhpIniFromConfig($config['phpIni']);

// create ServiceContainer
$serviceContainer = Init::createServiceContainer(
    $configContainer,
    $config['providers']
);

// create middleware queue using ServiceContainer for dependencies
$middlewareQueue = Init::createMiddlewareQueue(
    $serviceContainer,
    $config['middleware'],
);

return $middlewareQueue;
