<?php

namespace Oktolab\Bundle\RentBundle\Controller\Inventory;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

/**
 * @Configuration\Route("/api/set")
 */
class SetApiController extends Controller
{

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Cache(expires="+1 week", public="yes")
     * @Configuration\Method("GET")
     * @Configuration\Route("/typeahead.{_format}",
     *      name="OktolabRentBundle_Set_Typeahead_Prefetch",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadPrefetchAction()
    {
        $sets = $this->getDoctrine()
            ->getManager()
            ->createQuery('SELECT s, i FROM OktolabRentBundle:Inventory\Set s JOIN s.items i ORDER BY s.updated_at DESC')
            ->setMaxResults(20)
            ->setFetchMode('OktolabRentBundle:Inventory\Set', 'Items', 'EAGER')
            ->setQueryCacheLifetime(3600)
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $json = array();
        foreach ($sets as $set) {
            $items = array_map(
                function ($item) {
                    return sprintf('%s:%d', 'item', $item['id']);
                },
                $set['items']
            );

            $tokens = array($set['barcode'], $set['title']);

            $json[] = array(
                'name'          => $set['title'],
                'value'         => sprintf('%s:%d', 'set', $set['id']),
                'type'          => 'set',
                'id'            => $set['id'],
                'description'   => $set['description'],
                'barcode'       => $set['barcode'],
                'items'         => $items,
                'tokens'        => $tokens,
            );
        }

        return new JsonResponse($json);
    }
}
