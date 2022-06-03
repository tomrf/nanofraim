<?php

declare(strict_types=1);

namespace Nanofraim;

use Nanofraim\Interface\ResponseEmitterInterface;
use PhpCsFixer\Cache\CacheInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Relay\Relay;
use Tomrf\ServiceContainer\ServiceContainer;
use Tomrf\Session\Session;

class Application
{
    private Relay $relay;

    public function __construct(
        protected ServiceContainer $serviceContainer,
        protected ContainerInterface $configContainer,
        protected ResponseEmitterInterface $responseEmitter,
    ) {
        $this->relay = $this->createRelay();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->relay->handle($request);
    }

    public function emit(ResponseInterface $response): void
    {
        $this->responseEmitter->emit($response);
    }

    public function createServerRequestFromGlobals(): ServerRequestInterface
    {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $this->serverRequestCreator = new \Nyholm\Psr7Server\ServerRequestCreator(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
        );

        return $this->serverRequestCreator->fromGlobals();
    }

    private function createRelay(): Relay
    {
        $serviceContainer = $this->serviceContainer;

        return new Relay(
            $this->configContainer->get('middleware'),
            function ($class) use ($serviceContainer): MiddlewareInterface {
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
            }
        );
    }
}
