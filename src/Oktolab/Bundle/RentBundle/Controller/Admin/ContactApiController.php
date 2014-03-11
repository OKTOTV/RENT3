<?php

namespace Oktolab\Bundle\RentBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;

/**
 * @Route("/api/contact")
 */
class ContactApiController extends Controller
{

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Cache(expires="+7 days", public="yes")
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
        $contacts = $this->getDoctrine()->getManager()->getRepository('OktolabRentBundle:Contact')->findAll();
        return new JsonResponse($this->getTypeaheadArrayFromContacts($contacts));
    }

    /**
     * Returns a JSON formatted Dataset for typeahead.js
     *
     * @Method("GET")
     * @Route("/typeahead.{_format}/{name}",
     *      name="api_contact_typeahead_remote_url",
     *      defaults={"_format"="json"},
     *      requirements={"_format"="json"})
     *
     * @return JsonResponse
     */
    public function typeaheadRemoteUrlAction($name)
    {
        $contacts = $this->get('oktolab.contact_provider')->getContactsByName($name);
        return new JsonResponse($this->getTypeaheadArrayFromContacts($contacts));
    }

    /**
     * Returns a JSON with all contacts inside given costunit
     * @Method("GET")
     * @Route("/contacts_for_costunit/{costunit}", name="api_contacts_for_costunit")
     *
     * @param \Oktolab\Bundle\RentBundle\Entity\CostUnit $costunit
     * @return JsonResponse
     */
    public function contactsForCostunitAction(CostUnit $costunit)
    {
        $contacts = array();
        foreach ($costunit->getContacts() as $contact) {
            $contacts[] = array('id' => $contact->getId(), 'name' => $contact->getName());
        }
        return new JsonResponse($contacts);
    }

    /**
     * Returns typeahead friendly array
     * @param DoctrineCollection $contacts
     */
    private function getTypeaheadArrayFromContacts($contacts)
    {
        $json = array();

        foreach ($contacts as $contact) {
            $json[] = array(
                'name'          => $contact->getName(),
                'value'         => $contact->getGuid(),
                'tokens'        => explode(' ', $contact->getName()),
                'id'            => $contact->getGuid()
            );
        }
        return $json;
    }
}
