<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;

/**
 * @Route("/api/costunit")
 */
class CostUnitApiController extends Controller
{

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Cache(expires="+1 day", public="yes")
     * @Method("GET")
     * @Route("/typeahead.{_format}",
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
            $tokens = explode(' ', $costunit->getName());
            $tokens[] = $costunit->getGuid();

            $json[] = array(
                'name'          => $costunit->getName(),
                'value'         => $costunit->getId(),
                'tokens'        => $tokens,
                'id'            => $costunit->getId()
            );
        }

        return new JsonResponse($json);
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Method("GET")
     * @Route("/{id}/typeahead.{_format}",
     *      name="api_costunitcontacts_typeahead_remote",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     * @ParamConverter("costunit", class="OktolabRentBundle:CostUnit")
     * @return JsonResponse
     */
    public function typeaheadRemoteAction(CostUnit $costunit)
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
