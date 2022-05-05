<?php

declare(strict_types=1);

namespace Nanofraim;

use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use Tomrf\Autowire\Autowire;
use Tomrf\ConfigContainer\ConfigContainer;
use Tomrf\ServiceContainer\ServiceContainer;

class Init
{
    private static Autowire $autowire;

    public static function setup(): void
    {
        self::$autowire = new Autowire();
    }

    public static function createServerRequest(): ServerRequestInterface
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

        // return PSR-7 ServerRequest from globals
        return (new \Nyholm\Psr7Server\ServerRequestCreator(
            $psr17Factory, // ServerRequestFactory
            $psr17Factory, // UriFactory
            $psr17Factory, // UploadedFileFactory
            $psr17Factory  // StreamFactory
        ))->fromGlobals();
    }

    public static function createMiddlewareQueue(
        ServiceContainer $serviceContainer,
        array $middleware,
    ): array {
        $middlewareQueue = [];
        foreach ($middleware as $class) {
            $middlewareQueue[] = self::$autowire->instantiateClass(
                $class,
                '__construct',
                [$serviceContainer]
            );
        }

        return $middlewareQueue;
    }

    public static function createConfigContainer(
        array $config
    ): ConfigContainer {
        return new ConfigContainer($config);
    }

    public static function createServiceContainer(
        ConfigContainer $configContainer,
        array $providers,
    ): ServiceContainer {
        // create ServiceContainer, add ConfigContainer, Autowire and all configured services
        $serviceContainer = new ServiceContainer(self::$autowire);
        $serviceContainer->add(ConfigContainer::class, $configContainer);
        $serviceContainer->add(Autowire::class, self::$autowire);

        foreach (array_keys($providers) as $class) {
            $serviceProvider = new $class(new ConfigContainer(
                \is_array($providers[$class]) ? $providers[$class] : []
            ));

            $reflection = new ReflectionClass($serviceProvider);

            $serviceContainer->add(
                $reflection->getMethod('createService')->getReturnType()->getName(),
                $reflection->getMethod('createService')->getClosure($serviceProvider)
            );
        }

        return $serviceContainer;
    }
}
