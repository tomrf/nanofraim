<?php

declare(strict_types=1);

namespace Nanofraim;

use Tomrf\ConfigContainer\ConfigContainer;

abstract class AbstractProvider
{
    public function __construct(
        protected ConfigContainer $config,
    ) {
    }
}
