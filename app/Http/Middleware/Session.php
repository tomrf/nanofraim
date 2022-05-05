<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Nanofraim\Http\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class Session extends Middleware
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        try {
            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }
        } catch (\Exception $exception) {
            $this->logger->critical('Failed to start PHP session: '.$exception);
        }

        return $handler->handle($request);
    }
}
