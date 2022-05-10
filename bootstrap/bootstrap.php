<?php

declare(strict_types=1);

use Nanofraim\Init;

// set basePath and storagePath, referenced in configuration files
$basePath = dirname(__DIR__);
$storagePath = $basePath.'/storage';

// autoload
require $basePath.'/vendor/autoload.php';

// run Init::setup before initializing anything else
Init::setup();

// load environment file if one exists, keeping existing variables
Init::loadDotEnv($basePath.'/.env');

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
