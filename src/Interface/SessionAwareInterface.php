<?php

declare(strict_types=1);

namespace Nanofraim\Interface;

use Tomrf\Session\Session;

/**
 * Interface for classes using SessionAwareTrait.
 */
interface SessionAwareInterface
{
    /**
     * Sets a session instance on the object.
     */
    public function setSession(Session $session): void;
}
