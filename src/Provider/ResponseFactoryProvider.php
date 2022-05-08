<?php

declare(strict_types=1);

namespace Nanofraim\Provider;

use Nanofraim\AbstractProvider;
use Nanofraim\Http\ResponseFactory;

class ResponseFactoryProvider extends AbstractProvider
{
    public function createService(): ResponseFactory
    {
        return new ResponseFactory();
    }
}
