<?php

namespace Oktolab\Bundle\RentBundle\EventListener;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryTransformer;
use Oktolab\Bundle\RentBundle\Model\Event\Calendar\RoomApiService;

/**
 * Clears Caches used by InventoryTransformer
 * Will be called by DoctrineEvents
 *
 * @TODO: Improvement - After Cache-Clear use a Cache-Warmer
 */
class InventoryTransformerCacheListener
{
    /**
     * @var \Doctrine\Common\Cache\Cache;
     */
    protected $cache = null;

    /**
     * Constructor.
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
     * Clears the Cache for InventoryTransformer.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return null
     */
    protected function cacheClear(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Set || $entity instanceof Category || $entity instanceof Item) {
            if ($this->cache->contains(InventoryTransformer::CACHE_ID)) {
                $this->cache->delete(InventoryTransformer::CACHE_ID);
            }
        }
        if ($entity instanceof Room) {
            $this->cache->delete(RoomApiService::ROOM_CACHE);
        }

        return;
    }
}
