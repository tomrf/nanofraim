<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Nanofraim\Http\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class DummyController extends Controller implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getHome(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(sprintf("Hello world! PHP version %s\n", PHP_VERSION));
        $this->logger->debug('got requez!');

        return $response;
    }

    public function postApi(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode([
            'success' => true,
            'method' => $this->request->getMethod(),
            'random_number' => random_int(PHP_INT_MIN, PHP_INT_MAX),
        ]));

        return $response->withHeader('Content-type', 'application/json; charset=utf-8');
    }
}
