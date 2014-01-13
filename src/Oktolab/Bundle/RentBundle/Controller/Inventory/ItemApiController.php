<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;
/**
 * @Route("/api/item")
 */
class ItemApiController extends Controller
{
    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Cache(expires="+30 days", public="yes")
     * @Configuration\Method("GET")
     * @Configuration\Route("/typeahead.{_format}",
     *      name="inventory_item_typeahead_prefetch",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadPrefetchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('OktolabRentBundle:Inventory\Item')->findAll();

        return new JsonResponse($this->getTypeaheadArrayFromItems($items));
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/typeahead.{_format}/{itemValue}",
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

        return new JsonResponse($this->getTypeaheadArrayFromItems($items));
    }

    public function getTypeaheadArrayFromItems($items)
    {
        $json = array();

        foreach ($items as $item) {
            $tokens = explode(' ', $item->getTitle());
            $tokens[] = $item->getBarcode();

            $json[] = array(
                'name'          => $item->getTitle().$item->getId(),
                'displayName'   => $item->getTitle(),
                'value'         => sprintf('%s:%d', $item->getType(), $item->getId()),
                'type'          => $item->getType(),
                'id'            => $item->getId(),
                'description'   => $item->getDescription(),
                'barcode'       => $item->getBarcode(),
                'set'           => $item->getSet() != null ? $item->getSet()->getTitle(): '',
                'showUrl'       => 'inventory/item/'.$item->getId(),
                'tokens'        => $tokens
            );
        }

        return $json;
    }
}
