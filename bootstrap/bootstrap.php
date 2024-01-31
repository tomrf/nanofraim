<?php

declare(strict_types=1);

use App\Http\Controller\TestController;
use App\Service\DummyRouter;
use Nanofraim\AbstractProvider;
use Nanofraim\Application;
use Nanofraim\Exception\FrameworkException;
use Nanofraim\Http\ResponseEmitter;
use Tomrf\Autowire\Autowire;
use Tomrf\ConfigContainer\ConfigContainer;
use Tomrf\DotEnv\DotEnvLoader;
use Tomrf\PhpOptions\PhpOptions;
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
$phpOptions = new PhpOptions();
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

        /** @var ReflectionNamedType */
        $returnType = $reflection->getMethod('createService')->getReturnType();

        if (null === $returnType) {
            throw new FrameworkException(
                'Service provider '.$class.'::createService() must have a return type'
            );
        }

        $serviceContainer->add(
            $returnType->getName(),
            $reflection->getMethod('createService')->getClosure($serviceProvider)
        );

        continue;
    }

    // if class is Middleware\Router, set serviceContainer
    if ('App\Http\Middleware\Router' === $class) {
        $serviceContainer->add($class, function () use ($class, $serviceContainer, $configContainer, $providers) {
            $router = new $class(new DummyRouter(new ConfigContainer(
                $providers[\App\Service\DummyRouter::class]
            )));

            $router->setServiceContainer($serviceContainer);

            return $router;
        });

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
