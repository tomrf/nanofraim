<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Nanofraim\Http\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Headers extends Middleware
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $response = $handler->handle($request);

        return $response->withHeader('X-Powered-By', 'http');
    }
}
