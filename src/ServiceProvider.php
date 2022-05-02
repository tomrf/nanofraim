<?php

declare(strict_types=1);

namespace Nanofraim;

use Tomrf\ConfigContainer\ConfigContainer;

class ServiceProvider
{
    public function __construct(
        protected ?ConfigContainer $config = null,
    ) {
    }
}
