<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventApiController
 *
 * @author rs
 * @Route("/api/event")
 *
 */
class EventApiController extends Controller
{
    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Method("GET")
     * @Route("/typeahead.{_format}/{eventValue}",
     *      name="inventory_event_typeahead_remote_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadRemoteAction($eventValue)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OktolabRentBundle:Event');
        //SELECT * FROM item.i WHERE i.barcode LIKE %value% OR i.title LIKE %value%
        $dq = $repository->createQueryBuilder('i');
        $query = $dq->where(
            $dq->expr()->orX(
                    $dq->expr()->like('i.barcode', ':value'),
                    $dq->expr()->like('i.name', ':value')
                )
            )
            ->setParameter('value', '%'.$eventValue.'%')
            ->getQuery();

        $events = $query->getResult();

        $json = array();

        foreach ($events as $event) {
            $json[] = array(
                'name'          => $event->getCostUnit()->getName().' '.$event->getBegin()->format('d.m.Y').' - '.$event->getEnd()->format('d.m.Y'),
                'barcode'       => $event->getBarcode(),
                'showUrl'       => 'event/'.$event->getId().'/edit',
                'tokens'        => array(
                    $event->getBarcode(),
                    $event->getCostunit()->getName(),
                    $event->getContact()->getName()
                )
            );
        }

        return new JsonResponse($json);
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Method("GET")
     * @Route("/items/typeahead.{_format}/{itemValue}/{begin}/{end}",
     *      name="inventory_event_item_typeahead_remote_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @Configuration\ParamConverter("begin")
     * @Configuration\ParamConverter("end")
     * @return JsonResponse
     */
    public function typeaheadEventItemRemoteAction($itemValue, \DateTime $begin, \DateTime $end)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OktolabRentBundle:Inventory\Item');
        //SELECT * FROM item.i WHERE i.barcode LIKE %value% OR i.title LIKE %value%
        $dq = $repository->createQueryBuilder('i');
        $query = $dq->where(
            $dq->expr()->orX(
                    $dq->expr()->like('i.barcode', ':value'),
                    $dq->expr()->like('i.title', ':value')
                )
            )
            ->setParameter('value', '%'.$itemValue.'%')
            ->getQuery();

        $items = $query->getResult();

        $json = array();

        $eventManager = $this->get('oktolab.event_manager');

        foreach ($items as $item) {
            if ($eventManager->isAvailable($item, $begin, $end)) {
                $json[] = array(
                    'name'          => $item->getTitle(),
                    'value'         => sprintf('%s:%d', $item->getType(), $item->getId()),
                    'type'          => $item->getType(),
                    'id'            => $item->getId(),
                    'description'   => $item->getDescription(),
                    'barcode'       => $item->getBarcode(),
                    'set'           => $item->getSet() != null ? $item->getSet()->getTitle(): '',
                    'showUrl'       => 'inventory/item/'.$item->getId(),
                    'tokens'        => array(
                        $item->getBarcode(),
                        $item->getDescription(),
                        $item->getTitle()
                    )
                );
            }
        }

        return new JsonResponse($json);
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/sets/typeahead.{_format}/{setValue}/{begin}/{end}",
     *      name="inventory_event_set_typeahead_remote_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @Configuration\ParamConverter("begin")
     * @Configuration\ParamConverter("end")
     * @return JsonResponse
     */
    public function typeaheadEventSetRemoteAction($setValue, \DateTime $begin, \DateTime $end)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OktolabRentBundle:Inventory\Set');
        //SELECT * FROM set.s WHERE s.barcode LIKE %value% OR s.title LIKE %value%
        $dq = $repository->createQueryBuilder('s');
        $query = $dq->select()
            ->where(
            $dq->expr()->orX(
                    $dq->expr()->like('s.barcode', ':value'),
                    $dq->expr()->like('s.title', ':value')
                )
            )
            ->setParameter('value', '%'.$setValue.'%')
            ->getQuery();

        $sets = $query->getResult();

        $json = array();

        $eventManager = $this->get('oktolab.event_manager');

        foreach ($sets as $set) {
            if ($eventManager->isAvailable($set, $begin, $end)) {
                $items = array();
                foreach($set->getItems() as $item) {
                    $items[] = sprintf('%s:%d', $item->getType(), $item->getId());
                }

                $json[] = array(
                    'name'          => $set->getTitle(),
                    'value'         => sprintf('%s:%d', $set->getType(), $set->getId()),
                    'type'          => $set->getType(),
                    'id'            => $set->getId(),
                    'description'   => $set->getDescription(),
                    'barcode'       => $set->getBarcode(),
                    'items'         => $items,
                    'showUrl'       => 'inventory/set/'.$set->getId(),
                    'tokens'        => array(
                        $item->getBarcode(),
                        $item->getDescription(),
                        $item->getTitle()
                    )
                );
            }
        }

        return new JsonResponse($json);
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/rooms/typeahead.{_format}/{roomValue}/{begin}/{end}",
     *      name="inventory_event_room_typeahead_remote_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @Configuration\ParamConverter("begin")
     * @Configuration\ParamConverter("end")
     * @return JsonResponse
     * @param type $roomValue
     * @param \DateTime $begin
     * @param \DateTime $end
     */
    public function typeaheadEventRoomRemoteAction($roomValue, \DateTime $begin, \DateTime $end)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OktolabRentBundle:Inventory\Room');
        //SELECT * FROM item.i WHERE i.barcode LIKE %value% OR i.title LIKE %value%
        $dq = $repository->createQueryBuilder('i');
        $query = $dq->where(
            $dq->expr()->orX(
                    $dq->expr()->like('i.barcode', ':value'),
                    $dq->expr()->like('i.title', ':value')
                )
            )
            ->setParameter('value', '%'.$roomValue.'%')
            ->getQuery();

        $rooms = $query->getResult();

        $json = array();

        $eventManager = $this->get('oktolab.event_manager');

        foreach ($rooms as $room) {
            if ($eventManager->isAvailable($room, $begin, $end)) {
                $json[] = array(
                    'name'          => $room->getTitle(),
                    'value'         => sprintf('%s:%d', $room->getType(), $room->getId()),
                    'type'          => $room->getType(),
                    'id'            => $room->getId(),
                    'barcode'       => $room->getBarcode(),
                    'showUrl'       => 'inventory/room/'.$room->getId(),
                    'tokens'        => array(
                        $room->getBarcode(),
                        $room->getTitle()
                    )
                );
            }
        }

        return new JsonResponse($json);
    }
}
