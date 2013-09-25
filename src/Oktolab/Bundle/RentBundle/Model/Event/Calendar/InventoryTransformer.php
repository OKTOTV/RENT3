<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory as InventoryAggregator;

/**
 * Description of InventoryTransformer
 *
 * @author meh
 */
class InventoryTransformer
{
    /**
     * @var \Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory
     */
    protected $aggregator;

    /**
     * Constructor.
     *
     * @param Oktolab\Bundle\RentBundle\Model\Event\Calendar\Inventory $aggregator
     */
    public function __construct(InventoryAggregator $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Returns transformed Inventory for easily usage as a JSON-Array
     *
     * @param bool $sets if set to true, will also aggregate Sets into a Category
     *
     * @return array
     */
    public function getTransformedInventory($sets = false)
    {
        $aggregatedObjectives = $this->aggregator->getInventory($sets);
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
                );
            }

            $inventory[] = $objective;
        }

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
