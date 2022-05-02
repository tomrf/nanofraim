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

$config = require $basePath.'/config/config.php';
$configContainer = new \Tomrf\ConfigContainer\ConfigContainer(
    $config
);

$configContainer->setPhpIniFromConfig(
    $config['phpIni']
);

$autowire = new Autowire();
$serviceContainer = new ServiceContainer($autowire);
$serviceContainer->add(ConfigContainer::class, $configContainer);

$services = [];
$serviceKeys = $configContainer->query('services.*');
foreach ($serviceKeys as $key => $value) {
    $tok = explode('.', $key);
    $serviceName = $tok[1];
    if (!in_array($serviceName, $services, true)) {
        $services[] = $serviceName;
    }
}

foreach ($services as $serviceProviderClass) {
    $serviceConfig = $configContainer->query(sprintf('services.%s.*', $serviceProviderClass));
    foreach ($serviceConfig as $key => $value) {
        $name = str_replace(sprintf('services.%s.', $serviceProviderClass), '', $key);
        $serviceConfig[$name] = $value;
        unset($serviceConfig[$key]);
    }

    $serviceProvider = new $serviceProviderClass(
        new ConfigContainer($serviceConfig)
    );

    $reflection = new ReflectionClass($serviceProvider);

    $serviceContainer->add(
        $reflection->getMethod('createService')->getReturnType()->getName(),
        $reflection->getMethod('createService')->getClosure($serviceProvider)
    );
}

$middlewareQueue = [];
foreach ($config['middleware'] as $middleware) {
    $middlewareQueue[] = $autowire->instantiateClass($middleware, '__construct', [$serviceContainer]);
}

return $middlewareQueue;
