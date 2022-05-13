<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\DummyRouter as ServiceDummyRouter;
use Nanofraim\AbstractProvider;
use Tomrf\ConfigContainer\ConfigContainer;

class DummyRouter extends AbstractProvider
{
    public function __construct(
        protected ConfigContainer $config
    ) {
    }

    public function createService(): ServiceDummyRouter
    {
        return $this;
    }

    public function route(string $method, string $path): array
    {
        $controllerClass = $this->config->get(sprintf('routes.%s:%s.0', $method, $path));
        $controllerMethod = $this->config->get(sprintf('routes.%s:%s.1', $method, $path));

        return [$controllerClass, $controllerMethod];
    }
}
