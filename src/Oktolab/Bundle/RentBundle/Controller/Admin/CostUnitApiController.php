<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration;

/**
 * @Route("/api/costunit")
 */
class CostUnitApiController extends Controller
{

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Cache(expires="+30 days", public="yes")
     * @Configuration\Method("GET")
     * @Configuration\Route("/typeahead.{_format}",
     *      name="api_costunit_typeahead_prefetch",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadPrefetchAction()
    {
        $costunits = $this->getDoctrine()
            ->getManager()
            ->getRepository('OktolabRentBundle:CostUnit')
            ->findAll();

        $json = array();
        foreach ($costunits as $costunit) {
            $json[] = array(
                'name'          => $costunit->getName(),
                'value'         => $costunit->getId(),
                'tokens'        => explode(' ', $costunit->getName()),
                'id'            => $costunit->getId(),
                'showUrl'       => 'admin/costunit/'.$costunit->getId()
            );
        }

        return new JsonResponse($json);
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/typeahead.{_format}/{costunitValue}",
     *      name="api_costunit_typeahead_remote",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @return JsonResponse
     */
    public function typeaheadRemoteAction($costunitValue)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:CostUnit');
        $dq = $repository->createQueryBuilder('c');
        $query = $dq->select()->where(
                $dq->expr()->like('c.name', ':value')
            )
            ->setParameter('value', '%'.$costunitValue.'%')
            ->getQuery();

        $costunits = $query->getResult();

        $json = array();
        foreach ($costunits as $costunit) {
            $json[] = array(
                'name'          => $costunit->getName(),
                'value'         => $costunit->getId(),
                'tokens'        => explode(' ', $costunit->getName()),
                'id'            => $costunit->getId(),
                'showUrl'       => 'admin/costunit/'.$costunit->getId()
            );
        }

        return new JsonResponse($json);
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Configuration\Method("GET")
     * @Configuration\Route("/{id}/typeahead.{_format}",
     *      name="api_costunitcontacts_typeahead_remote",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @Configuration\ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @return JsonResponse
     */
    public function typeaheadContactRemoteAction(CostUnit $costunit)
    {
        $contacts = $costunit->getContacts();
        $json = array();

        foreach ($contacts as $contact) {
            $json[] = array(
                'name'          => $contact->getName(),
                'value'         => $contact->getId(),
                'tokens'        => explode(' ', $contact->getName()),
                'id'            => $contact->getId()
            );
        }

        return new JsonResponse($json);
    }
}
