<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Service\DummyRouter;
use Nanofraim\Http\Middleware;
use Nanofraim\Http\ResponseFactory;
use Nanofraim\Interface\ServiceContainerAwareInterface;
use Nanofraim\Trait\ServiceContainerAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerAwareTrait;
use Tomrf\Autowire\Autowire;
use Tomrf\Autowire\Container;

class Router extends Middleware implements ServiceContainerAwareInterface
{
    use LoggerAwareTrait;
    use ServiceContainerAwareTrait;

    public function __construct(
        private DummyRouter $router,
        private Autowire $autowire,
        private ResponseFactory $responseFactory,
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

        $controller = $this->autowire->instantiateClass($class, '__construct', [$container, $this->serviceContainer]);

        $this->serviceContainer->fulfillAwaressTraits($controller);

        return $controller->{$method}();
    }
}
