<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\InventoryAggregator;
use Doctrine\Common\Cache\Cache;

/**
 * Description of InventoryTransformer
 *
 * @author meh
 */
class InventoryTransformer
{
    const CACHE_ID = 'oktolab.calendar_inventory_transformer';

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory
     */
    protected $aggregator;

    /**
     * @var \Doctrine\Common\Cache\Cache;
     */
    protected $cache = null;

    /**
     * Constructor.
     *
     * @param Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory $aggregator
     */
    public function __construct(InventoryAggregator $aggregator, Cache $cache)
    {
        $this->aggregator = $aggregator;
        $this->cache = $cache;
    }

    /**
     * Returns transformed Inventory for easily usage as a JSON-Array
     *
     * @param bool $sets if set to true, will also aggregate Sets into a Category
     *
     * @return array
     */
    public function getTransformedInventory($sets = false, $setItems = false)
    {
        // if ($this->cache->contains(self::CACHE_ID)) {
        //     return $this->cache->fetch(self::CACHE_ID);
        // }

        $aggregatedObjectives = $this->aggregator->getInventory($sets, $setItems);
        $inventory = array();

        // Transform Objectives to easily read as a JSON-array
        foreach ($aggregatedObjectives as $category => $objects) {
            $objective = array('title' => $category, 'objectives' => array());

            // Add Objects to Array
            foreach ($objects as $object) {
                $objective['objectives'][] = array(
                    'objective' => sprintf('%s:%d', $object->getType(), $object->getId()),
                    'id'        => $object->getId(),
                    'title'     => $object->getTitle(),
                    'url'       => 'inventory/'.$object->getType().'/'.$object->getId(),
                    'active'    => $object->getType() == "set" ? true : $object->getActive() 
                );
            }

            $inventory[] = $objective;
        }

        $this->cache->save(self::CACHE_ID, $inventory, 3600);

        return $inventory;
    }

    /**
     * Shortcut method to get Inventory as JSON.
     *
     * @param bool $sets if set to true, will also aggregate Sets as a Category
     *
     * @return string
     */
    public function getInventoryAsJson($sets = false)
    {
        return json_encode($this->getTransformedInventory($sets));
    }
}
