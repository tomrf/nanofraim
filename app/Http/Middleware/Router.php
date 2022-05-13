<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Service\DummyRouter;
use Nanofraim\Http\AbstractMiddleware;
use Nanofraim\Interface\ServiceContainerAwareInterface;
use Nanofraim\Trait\ServiceContainerAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Tomrf\Autowire\Container;
use Tomrf\Session\Session;

class Router extends AbstractMiddleware implements ServiceContainerAwareInterface
{
    use ServiceContainerAwareTrait;

    public function __construct(
        private DummyRouter $router,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $match = $this->router->route(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        $class = $match[0];
        $method = $match[1];

        if (null === $class || null === $method) {
            return $handler->handle($request);
        }

        $container = new Container();
        $container->set(ServerRequestInterface::class, $request);

        $controller = $this->serviceContainer->autowire()->instantiateClass(
            $class,
            '__construct',
            [$container, $this->serviceContainer]
        );

        $this->serviceContainer->fulfillAwarenessTraits(
            $controller,
            [
                'Nanofraim\Trait\ServiceContainerAwareTrait' => [
                    'setServiceContainer' => fn () => $this->serviceContainer,
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

        return $controller->{$method}();
    }
}
