<?php

declare(strict_types=1);

namespace Nanofraim;

use Nanofraim\Interface\ServiceContainerAwareInterface;
use Nanofraim\Interface\SessionAwareInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Tomrf\Autowire\Autowire;
use Tomrf\ConfigContainer\ConfigContainer;
use Tomrf\ServiceContainer\ServiceContainer;
use Tomrf\ServiceContainer\ServiceFactory;
use Tomrf\Session\Session;

class Init
{
    private static Autowire $autowire;

    public static function loadDotEnv(string $path): void
    {
        if (file_exists($path)) {
            $env = parse_ini_file($path, true, INI_SCANNER_TYPED);
            foreach ($env as $key => $value) {
                $_ENV[$key] = $value;
            }
        }
    }

    public static function createMiddlewareQueue(
        ServiceContainer $serviceContainer,
        array $middleware,
    ): array {
        $middlewareQueue = [];
        foreach ($middleware as $class) {
            $instance = $serviceContainer->get($class);

            if ($instance instanceof ServiceContainerAwareInterface) {
                $instance->setServiceContainer($serviceContainer);
            }

            if ($instance instanceof LoggerAwareInterface) {
                $instance->setLogger($serviceContainer->get(LoggerInterface::class));
            }

            if ($instance instanceof SessionAwareInterface) {
                $instance->setSession($serviceContainer->get(Session::class));
            }

            $middlewareQueue[] = $instance;
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
        array $middleware,
    ): ServiceContainer {
        $serviceContainer = new ServiceContainer(new Autowire());

        $serviceContainer->add(ConfigContainer::class, $configContainer);
        $serviceContainer->add(Autowire::class, new Autowire());

        foreach (array_merge(array_keys($providers), $middleware) as $class) {
            if (is_subclass_of($class, AbstractProvider::class)) {
                $serviceProvider = new $class(new ConfigContainer(
                    \is_array($providers[$class]) ? $providers[$class] : []
                ));

                $reflection = new ReflectionClass($serviceProvider);

                $serviceContainer->add(
                    $reflection->getMethod('createService')->getReturnType()->getName(),
                    $reflection->getMethod('createService')->getClosure($serviceProvider)
                );

                continue;
            }

            $factory = new ServiceFactory($class);
            $serviceContainer->add($class, $factory);
        }

        return $serviceContainer;
    }
}
