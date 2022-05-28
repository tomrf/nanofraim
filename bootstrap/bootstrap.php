<?php

declare(strict_types=1);

use Nanofraim\AbstractProvider;
use Nanofraim\Application;
use Nanofraim\DotEnv;
use Nanofraim\Http\SapiEmitter;
use Psr\Http\Server\MiddlewareInterface;
use Relay\Relay;
use Tomrf\Autowire\Autowire;
use Tomrf\ConfigContainer\ConfigContainer;
use Tomrf\ServiceContainer\ServiceContainer;
use Tomrf\ServiceContainer\ServiceFactory;
use Tomrf\Session\Session;

// set basePath and storagePath, referenced in configuration files
$basePath = dirname(__DIR__);
$storagePath = $basePath.'/storage';

// autoload
require $basePath.'/vendor/autoload.php';

// load environment file if one exists, keeping existing variables
DotEnv::loadDotEnv($basePath.'/.env');

// load configuration, create ConfigContainer
$configContainer = new ConfigContainer(
    require $basePath.'/config/config.php'
);

// set PHP ini options
foreach ($configContainer->search('/phpIni\\..*/') as $key => $value) {
    $oldValue = ini_set(substr($key, 7), $value);
    if (false === $oldValue) {
        throw new RuntimeException(sprintf('Failed to set php ini option "%s"', $key));
    }
}

// create ServiceContainer
$serviceContainer = new ServiceContainer(new Autowire());

// add services and middleware to ServiceContainer
$providers = $configContainer->get('providers');
$classes = array_merge(
    array_keys($providers),
    $configContainer->get('middleware')
);

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
$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

$app = new Application(
    $configContainer,
    $serviceContainer,
    new Relay($configContainer->get('middleware'), function ($class) use ($serviceContainer): MiddlewareInterface {
        $instance = $serviceContainer->get($class);

        $serviceContainer->fulfillAwarenessTraits(
            $instance,
            [
                'Nanofraim\Trait\ServiceContainerAwareTrait' => [
                    'setServiceContainer' => fn () => $serviceContainer,
                ],
                'Psr\Log\LoggerAwareTrait' => [
                    'setLogger' => LoggerInterface::class,
                ],
                'Nanofraim\Trait\CacheAwareTrait' => [
                    'setCache' => CacheInterface::class,
                ],
                'Nanofraim\Trait\SessionAwareTrait' => [
                    'setSession' => Session::class,
                ],
            ]
        );

        return $instance;
    }),
    new SapiEmitter(),
    new \Nyholm\Psr7Server\ServerRequestCreator(
        $psr17Factory,
        $psr17Factory,
        $psr17Factory,
        $psr17Factory,
    ),
);

return $app;
