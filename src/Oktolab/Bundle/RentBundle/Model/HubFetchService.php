<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Guzzle\Service\Client;
use Oktolab\Bundle\RentBundle\Entity\Contact;
use Doctrine\ORM\EntityManager;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;

class HubFetchService extends Client
{
    private $entityManager;

    public function __construct(EntityManager $manager, $baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        $this->entityManager = $manager;
    }


    public function getContactsForGuids($guids)
    {
        $contacts = array();

        foreach ($guids as $guid) {

            $response = $this->get('?guid='.$guid)->send();
            $serializedString = $response->getBody(true);
            $contactcard = unserialize($serializedString);

            $contact = $this->entityManager->getRepository('OktolabRentBundle:Contact')->findOneBy(array('guid' => $contactcard['uniqueidentifier'][0]));

            if (!$contact) {
                $contact = new Contact();
                $contact->setFeePayed(false);
                $contact->setName($contactcard['cn'][0]);
                $contact->setGuid($contactcard['uniqueidentifier'][0]);
            }

            $contacts[] = $contact;
        }
        return $contacts;
    }

    public function getExtendedCostUnitByFetch(CostUnit $costUnit)
    {
        $response = $this->get('?guid='.$costUnit->getGuid())->send();
        $serializedString = $response->getBody(true);

        if ($serializedString == "0") {
            return false;
        }

        $array = unserialize($serializedString);
        $costUnit->setGuid($array['uniqueidentifier'][0]);

        return $costUnit;
    }
}