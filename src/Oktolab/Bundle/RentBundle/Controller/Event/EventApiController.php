<?php

namespace Oktolab\Bundle\RentBundle\Controller\Event;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventApiController
 *
 * @author rs
 */
class EventApiController
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
            ->setParameter('value', '%'.$itemValue.'%')
            ->getQuery();

        $events = $query->getResult();

        $json = array();

        foreach ($events as $event) {
            $json[] = array(
                'name'          => $event->getTitle(),
                'value'         => sprintf('%s:%d', $item->getType(), $item->getId()),
                'type'          => $event->getType(),
                'id'            => $event->getId(),
                'barcode'       => $event->getBarcode(),
                'showUrl'       => 'event/'.$event->getId(),
                'tokens'        => array(
                    $event->getBarcode(),
                    $event->getCostunit()->getName(),
                    $event->getContact()->getName()
                )
            );
        }

        return new JsonResponse($json);
    }
}
