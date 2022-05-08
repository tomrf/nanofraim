<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Nanofraim\Http\AbstractController;
use Psr\Http\Message\ResponseInterface;

class DummyController extends AbstractController
{
    public function getHome(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(sprintf("Hello world! PHP version %s\n", PHP_VERSION));

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
