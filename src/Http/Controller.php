<?php

declare(strict_types=1);

namespace Nanofraim\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareTrait;

abstract class Controller
{
    use LoggerAwareTrait;

    public function __construct(
        protected ServerRequestInterface $request,
        protected ResponseFactory $responseFactory,
    ) {
    }
}
