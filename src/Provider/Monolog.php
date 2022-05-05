<?php

declare(strict_types=1);

namespace Nanofraim\Provider;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Nanofraim\Provider;

class Monolog extends Provider
{
    public function createService(): \Psr\Log\LoggerInterface
    {
        // log stream
        $stream = new StreamHandler($this->config->get('path'));

        // formatter
        $stream->setFormatter(new LineFormatter(
            $this->config->get('format').PHP_EOL
        ));

        // create logger
        $logger = new \Monolog\Logger('_');

        // set timezone
        $logger->setTimezone(
            new \DateTimeZone($this->config->get('timezone') ?? 'UTC')
        );

        // push stream to logger
        $logger->pushHandler($stream);

        return $logger;
    }
}
