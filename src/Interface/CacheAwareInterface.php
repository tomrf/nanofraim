<?php

declare(strict_types=1);

namespace Nanofraim\Interface;

use Psr\SimpleCache\CacheInterface;

/**
 * Interface for classes using SessionAwareTrait.
 */
interface CacheAwareInterface
{
    /**
     * Sets a cache instance on the object.
     */
    public function setCache(CacheInterface $cache): void;
}
