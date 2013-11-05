<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * @Route("/api/item")
 */
class ItemApiController extends Controller
{

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Cache(expires="+1 week", public="yes")
     * @Method("GET")
     * @Route("/typeahead.{_format}",
     *      name="inventory_item_typeahead_prefetch",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadPrefetchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('OktolabRentBundle:Inventory\Item')->findBy(array());
        $json = array();

        foreach ($items as $item) {
            $json[] = array(
                'name'          => $item->getTitle(),
                'value'         => sprintf('%s:%d', $item->getType(), $item->getId()),
                'type'          => $item->getType(),
                'id'            => $item->getId(),
                'description'   => $item->getDescription(),
                'barcode'       => $item->getBarcode(),
                'set'           => $item->getSet()->getTitle(),
                'tokens'        => array(
                    $item->getBarcode(),
                    $item->getDescription(),
                    $item->getTitle()
                )
            );
        }

        return new JsonResponse($json);
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Method("GET")
     * @Route("/typeahead.{_format}/{itemValue}",
     *      name="inventory_item_typeahead_remote_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadRemoteAction($itemValue)
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

        foreach ($items as $item) {
            $json[] = array(
                'name'          => $item->getTitle(),
                'value'         => sprintf('%s:%d', $item->getType(), $item->getId()),
                'type'          => $item->getType(),
                'id'            => $item->getId(),
                'description'   => $item->getDescription(),
                'barcode'       => $item->getBarcode(),
                'set'           => $item->getSet()->getTitle(),
                'tokens'        => array(
                    $item->getBarcode(),
                    $item->getDescription(),
                    $item->getTitle()
                )
            );
        }

        return new JsonResponse($json);
    }
}
