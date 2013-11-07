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

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/typeahead.{_format}/{setValue}",
     *      name="inventory_set_typeahead_remote_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadRemoteAction($setValue)
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

        foreach ($sets as $set) {

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

        return new JsonResponse($json);
    }
}
