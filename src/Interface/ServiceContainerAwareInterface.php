<?php

declare(strict_types=1);

namespace Nanofraim\Interface;

use Tomrf\ServiceContainer\ServiceContainer;

/**
 * Interface for classes using ServiceContainerAwareTrait.
 */
interface ServiceContainerAwareInterface
{
    /**
     * Sets a ServiceContainer instance on the object.
     */
    public function setServiceContainer(ServiceContainer $ServiceContainer): void;
}
