<?php

declare(strict_types=1);

namespace Nanofraim\Provider;

use Nanofraim\Provider;
use Tomrf\Session\Session;

class SessionProvider extends Provider
{
    public function createService(): Session
    {
        return new Session();
    }
}
