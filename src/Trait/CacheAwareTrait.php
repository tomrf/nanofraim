<?php

declare(strict_types=1);

namespace Nanofraim\Trait;

use Psr\SimpleCache\CacheInterface;

/**
 * Trait that makes the SimpleCache service implementing CacheInterface
 * available to a class via the setCache() method during initial service
 * autowiring.
 *
 * Can be used for middleware and controllers that need access to the cache.
 */
trait CacheAwareTrait
{
    /**
     * The instance implementing CacheInterface.
     */
    protected ?CacheInterface $cache = null;

    /**
     * Sets a cache.
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }
}
