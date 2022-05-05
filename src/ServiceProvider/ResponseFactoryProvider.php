<?php

declare(strict_types=1);

namespace Nanofraim\ServiceProvider;

use Nanofraim\Http\ResponseFactory;
use Nanofraim\ServiceProvider;

class ResponseFactoryProvider extends ServiceProvider
{
    public function createService(): ResponseFactory
    {
        return new ResponseFactory();
    }
}
