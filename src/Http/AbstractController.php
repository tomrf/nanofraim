<?php

declare(strict_types=1);

namespace Nanofraim\Http;

use Nanofraim\Interface\CacheAwareInterface;
use Nanofraim\Interface\SessionAwareInterface;
use Nanofraim\Trait\CacheAwareTrait;
use Nanofraim\Trait\SessionAwareTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class AbstractController implements CacheAwareInterface, LoggerAwareInterface, SessionAwareInterface
{
    use CacheAwareTrait;
    use LoggerAwareTrait;
    use SessionAwareTrait;

    public function __construct(
        protected ServerRequestInterface $request,
        protected ResponseFactory $responseFactory,
    ) {
    }
}
