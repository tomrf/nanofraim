<?php

declare(strict_types=1);

namespace Nanofraim\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory
{
    protected Psr17Factory $factory;

    public function __construct()
    {
        $this->factory = new \Nyholm\Psr7\Factory\Psr17Factory();
    }

    public function createResponse(): ResponseInterface
    {
        return $this->factory->createResponse();
    }
}
