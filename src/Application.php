<?php

declare(strict_types=1);

namespace Nanofraim;

use Nanofraim\Interface\ResponseEmitterInterface;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\Relay;
use Tomrf\ServiceContainer\ServiceContainer;

class Application
{
    public function __construct(
        protected ServiceContainer $serviceContainer,
        protected Relay $relay,
        protected ResponseEmitterInterface $responseEmitter,
        protected ServerRequestCreatorInterface $serverRequestCreator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->relay->handle($request);
    }

    public function emit(ResponseInterface $response): void
    {
        $this->responseEmitter->emit($response);
    }

    public function createServerRequestFromGlobals(): ServerRequestInterface
    {
        return $this->serverRequestCreator->fromGlobals();
    }
}
