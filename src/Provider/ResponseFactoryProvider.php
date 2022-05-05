<?php

declare(strict_types=1);

namespace Nanofraim\Provider;

use Nanofraim\Http\ResponseFactory;
use Nanofraim\Provider;

class ResponseFactoryProvider extends Provider
{
    public function createService(): ResponseFactory
    {
        return new ResponseFactory();
    }
}
