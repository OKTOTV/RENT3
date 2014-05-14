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
     * Returns events for given eventValue for typeahead.js
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
        //SELECT * FROM item.i WHERE i.barcode LIKE %value%
        $dq = $repository->createQueryBuilder('i');
        $query = $dq->where($dq->expr()->like('i.barcode', ':value'))
            ->setParameter('value', '%'.$eventValue.'%')
            ->getQuery();

        $events = $query->getResult();

        $json = array();

        foreach ($events as $event) {
            $tokens = explode(' ', $event->getCostunit()->getName());
            $tokens[] = $event->getBarcode();
            $showUrl = 'event/'.$event->getId();

            if ($event->getState() < 3 ) {
                $showUrl = $showUrl.'/edit';
            }
            if ($event->getState() == 3) {
                $showUrl = $showUrl.'/check';
            }
            if ($event->getState() >= 5 ) {
                $showUrl = $showUrl.'/show';
            }

            $datum = array(
                'name'          => $event->getCostunit()->getName().$event->getId(),
                'displayName'   => $event->getCostunit()->getName(),
                'id'            => $event->getId(),
                'showUrl'       => $showUrl,
                'barcode'       => $event->getBarcode(),
                'tokens'        => $tokens
            );
            $json[] = $datum;
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
            ->andWhere('i.category IS NULL')
            ->andWhere('i.active = 1')
            ->setParameter('value', '%'.$itemValue.'%')
            ->getQuery();

        $items = $query->getResult();

        return new JsonResponse($this->getTypeaheadArrayFromObjects($items, $begin, $end));
    }

    /**
     * Returns Json with all Available items without category including items in given event.
     * This is used to enable quick barcode scanning.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/items/prefetch/typeahead.{_format}/{event}/{begin}/{end}",
     *      name="inventory_event_item_typeahead_prefetch_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @Configuration\ParamConverter("begin")
     * @Configuration\ParamConverter("end")
     *
     * @param Event $event
     * @param \DateTime $begin
     * @param \DateTime $end
     */
    public function typeaheadEventItemPrefetchAction($event, \DateTime $begin, \DateTime $end)
    {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('OktolabRentBundle:Inventory\Item')->createQueryBuilder('i');
        $AllItems = $qb->select()->where('i.category IS NULL')->andWhere('i.active = 1')->getQuery()->getResult();
        $availableItems = $this->getTypeaheadArrayFromObjects($AllItems, $begin, $end);

        if ($event != "undefined") {
            $event = $em->getRepository('OktolabRentBundle:Event')->findOneBy(array('id' => $event));

            $eventRentables = $this->get('oktolab.event_manager')->convertEventObjectsToEntites($event->getObjects());
            foreach ($eventRentables as $eventRentable) {
                $availableItems[] = $this->getDatumForObject($eventRentable);
            }
        }
        return new JsonResponse($availableItems);
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
     * Returns available sets for given setValue and timerange for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/sets/prefetch/typeahead.{_format}/{begin}/{end}",
     *      name="inventory_event_set_typeahead_prefetch_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @Configuration\ParamConverter("begin")
     * @Configuration\ParamConverter("end")
     * @return JsonResponse
     */
    public function typeaheadEventSetPrefetchAction(\DateTime $begin, \DateTime $end)
    {
        $sets = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Inventory\Set')->findAll();
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
     * Returns a JSON formatted Dataset for typeahead.js
     * Returns all Items where category is not null and are free in given time range.
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/category/typeahead.{_format}/{begin}/{end}",
     *      name="inventory_category_typeahead_prefetch",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @Configuration\ParamConverter("begin")
     * @Configuration\ParamConverter("end")
     *
     * @return JsonResponse
     */
    public function typeaheadEventCategoryPrefetchAction(\DateTime $begin, \DateTime $end)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $items = $qb->select('i')
            ->from('OktolabRentBundle:Inventory\Item', 'i')
            ->where('i.category IS NOT NULL')
            ->andWhere('i.active = 1')
            ->getQuery()
            ->getResult();

        return new JsonResponse($this->getTypeaheadArrayForCategories($items, $begin, $end));
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
                $json[] = $this->getDatumForObject($object);
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

    /**
     * Creates the datum used by typeahead.
     * Caution, changing this may lead to unexpected behaviors in the whole project
     * @param RentableInterface $object
     * @return array
     */
    private function getDatumForObject($object)
    {
        $tokens = explode(' ', $object->getTitle());
        $tokens[] = $object->getBarcode();

        $items = $this->getItemsToSet($object);

        $datum = array(
            'name'          => $object->getTitle().$object->getId(),
            'displayName'   => $object->getTitle(),
            'value'         => sprintf('%s:%d', $object->getType(), $object->getId()),
            'type'          => $object->getType(),
            'items'         => $items,
            'id'            => $object->getId(),
            'barcode'       => $object->getBarcode(),
            'tokens'        => $tokens
        );

        return $datum;
    }

    private function getTypeaheadArrayForCategories($items, $begin, $end, $type = 'inventory')
    {
        $json = array();
        $eventManager = $this->get('oktolab.event_manager');

        foreach ($items as $item) {
            if ($eventManager->isAvailable($item, $begin, $end, $type) && $item->getActive()) {
                $tokens = explode(' ', $item->getCategory()->getTitle());
                $tokens[] = $item->getBarcode();
                $namepieces = explode(' ', $item->getTitle());
                foreach ($namepieces as $token) {
                    $tokens[] = $token;
                }

                $json[] = array(
                    'name'          => 'category'.$item->getTitle().$item->getId(),
                    'displayName'   => $item->getTitle(),
                    'value'         => sprintf('%s:%d', $item->getType(), $item->getId()),
                    'type'          => $item->getType(),
                    'id'            => $item->getId(),
                    'barcode'       => $item->getBarcode(),
                    //'showUrl'       => 'inventory/item/'.$item->getId(),
                    'tokens'        => $tokens
                );
            }
        }
        return $json;
    }
}
