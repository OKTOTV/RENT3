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
     * @Configuration\Cache(expires="+30 days", public="yes")
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
        $sets = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Inventory\Set')->findAll();
        return new JsonResponse($this->getTypeaheadArrayFromSets($sets));
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
        $repository = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Inventory\Set');
        //SELECT * FROM set s WHERE s.barcode LIKE %value% OR s.title LIKE %value%
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

        return new JsonResponse($this->getTypeaheadArrayFromSets($sets));
    }

    /**
     * return a typeahead friendly array out of a collection of sets
     * @param DoctrineCollection $sets
     * @return array
     */
    public function getTypeaheadArrayFromSets($sets)
    {
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
        return $json;
    }
}
