<?php

declare(strict_types=1);

namespace Nanofraim\Trait;

use Tomrf\Session\Session;

/**
 * Trait that makes the Session service available to a class via the
 * setSession() method during initial service autowiring.
 *
 * Can be used for middleware and controllers that need access to session data.
 */
trait SessionAwareTrait
{
    /**
     * The session instance.
     */
    protected ?Session $session = null;

    /**
     * Sets a session.
     */
    public function setSession(Session $session): void
    {
        $this->session = $session;
    }
}
