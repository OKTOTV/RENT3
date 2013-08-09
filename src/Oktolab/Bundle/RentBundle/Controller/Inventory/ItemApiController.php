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
        $items = $em->getRepository('OktolabRentBundle:Inventory\Item')->findBy(array('set' => null));
        $json = array();

        foreach ($items as $item) {
            $json[] = array(
                'name'          => $item->getTitle(),
                'value'         => sprintf('%s:%d', 'Item', $item->getId()),
                'id'            => $item->getId(),
                'description'   => $item->getDescription(),
                'barcode'       => $item->getBarcode(),
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