<?php

declare(strict_types=1);

use Nanofraim\AbstractProvider;
use Nanofraim\Application;
use Nanofraim\Http\ResponseEmitter;
use Tomrf\Autowire\Autowire;
use Tomrf\ConfigContainer\ConfigContainer;
use Tomrf\DotEnv\DotEnvLoader;
use Tomrf\ServiceContainer\ServiceContainer;
use Tomrf\ServiceContainer\ServiceFactory;

// set basePath and storagePath, referenced in configuration files
$basePath = dirname(__DIR__);
$storagePath = $basePath.'/storage';

// autoload
require $basePath.'/vendor/autoload.php';

// load environment file if one exists, keeping existing variables
if (file_exists($basePath.'/.env')) {
    (new DotEnvLoader())->loadImmutable($basePath.'/.env');
}

// load configuration, create ConfigContainer
$configContainer = new ConfigContainer(
    require $basePath.'/config/config.php'
);

// set PHP ini options
$phpOptions = new \Tomrf\PhpOptions\PhpOptions();
foreach ($configContainer->search('/phpIni\\..*/') as $key => $value) {
    $phpOptions->set(substr($key, 7), $value);
}

// create ServiceContainer and add services and middleware
$serviceContainer = new ServiceContainer(new Autowire());
$providers = $configContainer->get('providers');
$classes = array_merge(array_keys($providers), $configContainer->get('middleware'));

foreach ($classes as $class) {
    if (is_subclass_of($class, AbstractProvider::class)) {
        $serviceProvider = new $class(new ConfigContainer(
            is_array($providers[$class]) ? $providers[$class] : []
        ));

        $reflection = new ReflectionClass($serviceProvider);

        $serviceContainer->add(
            $reflection->getMethod('createService')->getReturnType()->getName(),
            $reflection->getMethod('createService')->getClosure($serviceProvider)
        );

        continue;
    }

    $serviceContainer->add($class, new ServiceFactory($class));
}

// .....
$app = new Application(
    $serviceContainer,
    $configContainer,
    new ResponseEmitter(),
);

return $app;
