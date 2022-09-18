<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Nanofraim\Http\AbstractController;
use Nanofraim\Trait\CacheAwareTrait;
use Nanofraim\Trait\ServiceContainerAwareTrait;
use Nanofraim\Trait\SessionAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareTrait;

class TestController extends AbstractController
{
    use CacheAwareTrait;
    use LoggerAwareTrait;
    use ServiceContainerAwareTrait;
    use SessionAwareTrait;

    public function getHome(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        if (null === $this->logger) {
            return $response->withStatus(500, 'MISSING LOGGER');
        }

        if (null === $this->cache) {
            return $response->withStatus(500, 'MISSING CACHE');
        }

        if (null === $this->session) {
            return $response->withStatus(500, 'MISSING SESSION');
        }

        if (null === $this->serviceContainer) {
            return $response->withStatus(500, 'MISSING SERVICECONTAINER');
        }

        $response->getBody()->write("Hello world!\n");

        return $response;
    }

    public function postApi(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode([
            'success' => true,
            'method' => $this->request->getMethod(),
            'controller' => static::class,
        ]).PHP_EOL);

        return $response->withHeader('Content-type', 'application/json; charset=utf-8');
    }
}
