<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Nanofraim\Http\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Tomrf\Session\Session as SessionService;

class Session extends Middleware
{
    public function __construct(
        private LoggerInterface $logger,
        private SessionService $session,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        if (true === $request->hasHeader('cookie')) {
            try {
                $this->session->startSession();
            } catch (\Exception $exception) {
                $this->logger->critical('Failed to start PHP session: '.$exception);
            }

            if ('GET' === $request->getMethod()) {
                $this->session->closeWrite();
            }
        }

        return $handler->handle($request);
    }
}
