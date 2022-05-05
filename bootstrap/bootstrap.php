<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Tomrf\Autowire\Autowire;
use Tomrf\ConfigContainer\ConfigContainer;
use Tomrf\ServiceContainer\ServiceContainer;

// set basePath and storagePath, used in configuration
$basePath = dirname(__DIR__);
$storagePath = $basePath.'/storage';

// autoload
require $basePath.'/vendor/autoload.php';

// load environment file if one exists
if (file_exists($basePath.'/.env')) {
    Dotenv::createImmutable($basePath)->load();
}

// load configuration, create ConfigContainer
$config = require $basePath.'/config/config.php';
$configContainer = new ConfigContainer(
    $config
);

// set PHP ini options
$configContainer->setPhpIniFromConfig(
    $config['phpIni']
);

// create Autowire instance
$autowire = new Autowire();

// create ServiceContainer, add ConfigContainer, Autowire and all configured services
$serviceContainer = new ServiceContainer($autowire);
$serviceContainer->add(ConfigContainer::class, $configContainer);
$serviceContainer->add(Autowire::class, $autowire);

foreach (array_keys($config['services']) as $class) {
    $serviceProvider = new $class(
        new ConfigContainer(
            is_array($config['services'][$class]) ? $config['services'][$class] : []
        )
    );

    $reflection = new ReflectionClass($serviceProvider);

    $serviceContainer->add(
        $reflection->getMethod('createService')->getReturnType()->getName(),
        $reflection->getMethod('createService')->getClosure($serviceProvider)
    );
}

// create middleware queue using Autowire and ServiceContainer for dependencies
$middlewareQueue = [];
foreach ($config['middleware'] as $middleware) {
    $middlewareQueue[] = $autowire->instantiateClass(
        $middleware,
        '__construct',
        [$serviceContainer]
    );
}

return $middlewareQueue;
