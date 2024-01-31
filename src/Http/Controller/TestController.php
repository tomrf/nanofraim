<?php

declare(strict_types=1);

namespace App\Http\Controller;

use Nanofraim\Http\AbstractController;
use Psr\Http\Message\ResponseInterface;

class TestController extends AbstractController
{
    public function getHome(): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

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
