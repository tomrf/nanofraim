<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Service\DummyRouter;
use Nanofraim\Http\Middleware;
use Nanofraim\Http\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tomrf\Autowire\Autowire;
use Tomrf\Autowire\Container;

class Router extends Middleware
{
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
        $container->set(ResponseFactory::class, $this->responseFactory);

        $controller = $this->autowire->instantiateClass($class, '__construct', [$container]);

        return $controller->{$method}();
    }
}
