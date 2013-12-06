<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Description of RoomApiController
 * @TODO: move the typeahead array creation to a service
 * @Route("/api/room")
 * @author rs
 */
class RoomApiController extends Controller
{
    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Cache(expires="+1 week", public="yes")
     * @Method("GET")
     * @Route("/typeahead.{_format}",
     *      name="inventory_room_typeahead_prefetch",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadPrefetchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $rooms = $em->getRepository('OktolabRentBundle:Inventory\Room')->findAll();

        return new JsonResponse($this->getTypeaheadArrayForRooms($rooms));
    }

        /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Method("GET")
     * @Route("/typeahead.{_format}/{roomValue}",
     *      name="inventory_room_typeahead_remote_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadRemoteAction($roomValue)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('OktolabRentBundle:Inventory\Room');
        //SELECT * FROM room.i WHERE i.barcode LIKE %value% OR i.title LIKE %value%
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

        return new JsonResponse($this->getTypeaheadArrayForRooms($rooms));
    }

    private function getTypeaheadArrayForRooms($rooms)
    {
        $json = array();
        foreach ($rooms as $room) {
            $tokens = explode(' ', $room->getTitle());
            $tokens[] = $room->getTitle();

            $json[] = array(
                'name'          => $room->getTitle(),
                'value'         => sprintf('%s:%d', $room->getType(), $room->getId()),
                'type'          => $room->getType(),
                'id'            => $room->getId(),
                'description'   => $room->getDescription(),
                'barcode'       => $room->getBarcode(),
                'showUrl'       => 'inventory/room/'.$room->getId(),
                'tokens'        => $tokens
            );
        }

        return $json;
    }
}
