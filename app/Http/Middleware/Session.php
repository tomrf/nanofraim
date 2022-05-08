<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Nanofraim\Http\AbstractMiddleware;
use Nanofraim\Interface\SessionAwareInterface;
use Nanofraim\Trait\SessionAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Session extends AbstractMiddleware implements SessionAwareInterface
{
    use SessionAwareTrait;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        if (true === $request->hasHeader('cookie')) {
            try {
                $this->session->startSession();

                /** @var \Tomrf\Logger\Logger */
                $logger = $this->logger;

                $logger->truncateStream();
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
