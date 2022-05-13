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

        foreach (class_uses($controller) as $trait) {
            if ('Nanofraim\Trait\ServiceContainerAwareTrait' === $trait) {
                $controller->setServiceContainer($this->serviceContainer);
            }

            if ('Psr\Log\LoggerAwareTrait' === $trait) {
                $controller->setLogger($this->serviceContainer->get(LoggerInterface::class));
            }

            if ('Nanofraim\Trait\SessionAwareTrait' === $trait) {
                $controller->setSession($this->serviceContainer->get(Session::class));
            }

            if ('Nanofraim\Trait\CacheAwareTrait' === $trait) {
                $controller->setCache($this->serviceContainer->get(CacheInterface::class));
            }
        }

        return $controller->{$method}();
    }
}
