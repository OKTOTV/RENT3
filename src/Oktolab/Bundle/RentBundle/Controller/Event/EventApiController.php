<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

/**
 * Description of EventApiController
 * @TODO: move the typeahead array creation to a service
 * @author rs
 * @Configuration\Route("/api/event")
 *
 */
class EventApiController extends Controller
{
    /**
     * Returns  events for given eventValue for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/typeahead.{_format}/{eventValue}",
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
            $tokens = explode(' ', $event->getCostunit()->getName());
            $tokens[] = $event->getContact()->getName();
            $tokens[] = $event->getBarcode();

            $json[] = array(
                'name'          => $event->getCostUnit()->getName().' '.$event->getBegin()->format('d.m.Y').' - '.$event->getEnd()->format('d.m.Y'),
                'barcode'       => $event->getBarcode(),
                'showUrl'       => 'event/'.$event->getId().'/edit',
                'tokens'        => $tokens
            );
        }

        return new JsonResponse($json);
    }

    /**
     * Returns available items for given itemValue and timerange for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/items/typeahead.{_format}/{itemValue}/{begin}/{end}",
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

        return new JsonResponse($this->getTypeaheadArrayFromObjects($items, $begin, $end));
    }

    /**
     * Returns available sets for given setValue and timerange for typeahead.js
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

        return new JsonResponse($this->getTypeaheadArrayFromObjects($sets, $begin, $end));
    }

    /**
     * Returns available rooms for given roomValue and timerange for typeahead.js
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

        return new JsonResponse($this->getTypeaheadArrayFromObjects($rooms, $begin, $end, 'room'));
    }

    /**
     * Returns Typeahead friendly Array
     * @param DoctrineCollection $objects
     * @param DateTime begin of the timerange
     * @param DateTime end of the timerange
     * @param Typename to get available status
     *
     * @return array
     */
    private function getTypeaheadArrayFromObjects($objects, $begin, $end, $type = 'inventory')
    {
        $json = array();
        $eventManager = $this->get('oktolab.event_manager');

        foreach ($objects as $object) {
            if ($eventManager->isAvailable($object, $begin, $end, $type)) {
                $tokens = explode(' ', $object->getTitle());
                $tokens[] = $object->getBarcode();

                $items = $this->getItemsToSet($object);

                $json[] = array(
                    'name'          => $object->getTitle(),
                    'value'         => sprintf('%s:%d', $object->getType(), $object->getId()),
                    'type'          => $object->getType(),
                    'items'         => $items,
                    'id'            => $object->getId(),
                    'barcode'       => $object->getBarcode(),
                    'tokens'        => $tokens
                );
            }
        }
        return $json;
    }

    /**
     * returns an array of item of the rentableObject. array is empty if it is not a set
     * @param type $set
     * @return type
     */
    private function getItemsToSet($set)
    {
        $items = array();
        if ($set->getType() === 'set') {
            foreach($set->getItems() as $item) {
                $items[] = sprintf('%s:%d', $item->getType(), $item->getId());
            }
        }
        return $items;
    }
}
