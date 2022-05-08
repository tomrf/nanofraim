<?php

declare(strict_types=1);

namespace Nanofraim\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        protected ResponseFactory $responseFactory
    ) {
    }

    abstract public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}
