<?php

declare(strict_types=1);

namespace Nanofraim\Provider;

use Nanofraim\AbstractProvider;
use Tomrf\Logger\Logger;

class LoggerProvider extends AbstractProvider
{
    public function createService(): \Psr\Log\LoggerInterface
    {
        return new Logger(
            $this->config->get('path'),
        );
    }
}
