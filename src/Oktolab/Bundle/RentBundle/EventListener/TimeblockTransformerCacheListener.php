<?php

namespace Oktolab\Bundle\RentBundle\EventListener;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Oktolab\Bundle\RentBundle\Entity\Timeblock;

/**
 * Description of TimeblockTransformerCacheListener
 *
 * @author meh
 *
 * @TODO: After clearing Cache, call a CacheWarmer
 */
class TimeblockTransformerCacheListener
{
    /**
     * TimeblockTransformer Cache
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache = null;

    /**
     * Constructor.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Listen to postPersist Event.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->cacheClear($args);
    }

    /**
     * Listen to postRemove Event.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->cacheClear($args);
    }

    /**
     * Listen to postUpdate Event.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->cacheClear($args);
    }

    /**
     * Clears the Cache for TimeblockTransformer.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    protected function cacheClear(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Timeblock) {
            $this->cache->deleteAll();
        }
    }
}
