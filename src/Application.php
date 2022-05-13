<?php

declare(strict_types=1);

namespace Nanofraim;

use Nanofraim\Http\SapiEmitter;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\Relay;
use Tomrf\ConfigContainer\ConfigContainer;
use Tomrf\ServiceContainer\ServiceContainer;

class Application
{
    public function __construct(
        protected ConfigContainer $configContainer,
        protected ServiceContainer $serviceContainer,
        protected Relay $relay,
        protected SapiEmitter $sapiEmitter,
        protected ServerRequestCreatorInterface $serverRequestCreator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->relay->handle($request);
    }

    public function emit(ResponseInterface $response): void
    {
        $this->sapiEmitter->emit($response);
    }

    public function createServerRequestFromGlobals(): ServerRequestInterface
    {
        return $this->serverRequestCreator->fromGlobals();
    }
}
