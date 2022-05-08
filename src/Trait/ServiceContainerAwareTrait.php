<?php

declare(strict_types=1);

namespace Nanofraim\Trait;

use Tomrf\ServiceContainer\ServiceContainer;

/**
 * Trait that makes the service container available to a class via the
 * setServiceContainer() method during initial service autowiring.
 *
 * Can be used for middleware and controllers that need access to the full service container
 * for dynamically instantiating request handlers during request processing.
 */
trait ServiceContainerAwareTrait
{
    /**
     * The ServiceContainer instance.
     */
    protected ?ServiceContainer $serviceContainer = null;

    /**
     * Sets a serviceContainer.
     */
    public function setServiceContainer(ServiceContainer $serviceContainer): void
    {
        $this->serviceContainer = $serviceContainer;
    }
}
