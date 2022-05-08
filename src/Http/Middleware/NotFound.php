<?php

declare(strict_types=1);

namespace Nanofraim\Http\Middleware;

use Nanofraim\Http\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class NotFound extends Middleware
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse()->withStatus(404);
        $response->getBody()->write('HTTP 404 Not Found'.PHP_EOL);

        return $response;
    }
}
