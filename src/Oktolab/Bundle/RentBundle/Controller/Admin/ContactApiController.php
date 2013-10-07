<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * @Route("/api/contact")
 */
class ContactApiController extends Controller
{

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Cache(expires="+1 week", public="yes")
     * @Method("GET")
     * @Route("/typeahead.{_format}",
     *      name="api_contact_typeahead_prefetch",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadPrefetchAction()
    {
        $contacts = $this->get('oktolab.contact_provider')->getContactsByName('*');
        $json = array();

        foreach ($contacts as $contact) {
            $json[] = array(
                'name'          => $contact->getName(),
                'value'         => $contact->getId(),
                'tokens'        => explode(' ', $contact->getName()),
                'id'            => $contact->getGuid()
            );
        }

        return new JsonResponse($json);
    }
}
