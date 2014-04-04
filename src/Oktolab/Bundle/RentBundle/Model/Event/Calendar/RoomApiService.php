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
     * uses caching
     * @return array JSON Rooms
     */
    public function getRoomsForCalendar()
    {
        if ($this->cache->contains(self::ROOM_CACHE)) {
            return $this->cache->fetch(self::ROOM_CACHE);
        }

        $rooms = $this->em->getRepository('OktolabRentBundle:Inventory\Room')->findAll();
        $categorylist = array(); // rooms don't have any category yet. mehs calendar demands it though.

        $json = array('title' => 'Räume', 'objectives' => array());
        foreach ($rooms as $room) {
            $json['objectives'][] = array(
                'objective' => sprintf('%s:%d', $room->getType(), $room->getId()),
                'id'        => $room->getId(),
                'title'     => $room->getTitle()
            );
        }

        $categorylist[] = $json;

        $this->cache->save(self::ROOM_CACHE, $categorylist, 86400);
        return $categorylist;
    }
}
