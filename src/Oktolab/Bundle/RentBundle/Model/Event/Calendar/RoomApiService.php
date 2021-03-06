<?php

namespace Oktolab\Bundle\RentBundle\Model\Event\Calendar;

use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;

/**
 * @author rs
 * RoomApiService provides api information for the room calendar
 * @TODO: decuple services from (inventory) calendar services.
 */
class RoomApiService
{

    const ROOM_CACHE = 'oktolab.calendar_room_api_cache';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    public function __construct(EntityManager $entityManager, Cache $cache)
    {
        $this->em = $entityManager;
        $this->cache = $cache;
    }


    /**
     * returns rooms as json for the calendar to display.
     * @return array JSON Rooms
     */
    public function getRoomsForCalendar()
    {
        $rooms = $this->em->getRepository('OktolabRentBundle:Inventory\Room')->findBy(array(), array('sortnumber' => 'asc'));
        $categorylist = array(); // rooms don't have any category yet. mehs calendar demands it though.

        $json = array('title' => 'Räume', 'objectives' => array());
        foreach ($rooms as $room) {
            $json['objectives'][] = array(
                'objective' => sprintf('%s:%d', $room->getType(), $room->getId()),
                'id'        => $room->getId(),
                'title'     => $room->getTitle(),
                'active'    => true,
                'url'       => 'inventory/room/'.$room->getId()
            );
        }

        $categorylist[] = $json;

        return $categorylist;
    }
}
