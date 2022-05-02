<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Nanofraim\Http\Middleware;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

class Dummy extends Middleware
{
    public function __construct(
        private LoggerInterface $logger,
        private CacheInterface $cache,
        private \Twig\Environment $twig,
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $this->cache->set('lastVisitorIpAddr', $_SERVER['REMOTE_ADDR'] ?? 'n/a');
        $this->logger->debug('visitor: '.($_SERVER['REMOTE_ADDR'] ?? 'n/a'));

        $response = new Response();
        $response->getBody()->write(
            $this->twig->render('welcome.html', [
                'phpVersion' => PHP_VERSION,
            ])
        );

        return $response;
    }
}
