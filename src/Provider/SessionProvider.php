<?php

declare(strict_types=1);

namespace Nanofraim\Provider;

use Nanofraim\AbstractProvider;
use Tomrf\Session\Session;

class SessionProvider extends AbstractProvider
{
    public function createService(): Session
    {
        return new Session();
    }
}
