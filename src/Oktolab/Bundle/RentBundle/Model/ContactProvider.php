<?php

namespace Oktolab\Bundle\RentBundle\Model;

use Oktolab\Bundle\RentBundle\Entity\Contact;
use Guzzle\Http\Client;
use Doctrine\ORM\EntityManager;


class ContactProvider extends Client
{
    private $entityManager;

    public static $Resource_HUB=0;
    public static $Resource_RENT=1;

    public function __construct(EntityManager $manager, $baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        $this->entityManager = $manager;
    }

    /**
     * Returns all Contacts matching the given $name in the given $resource.
     * $resource shall be one of ContactProviders static Resources
     * By default, it returns Contacts matching from HUB.
     *
     * @param type $name
     * @param type $resource
     * @return type
     */
    public function getContactsByName($name, $resource=0)
    {
        switch ($resource) {
            case ContactProvider::$Resource_HUB:
                return $this->getContactsFromHub($name);
                break;
            case ContactProvider::$Resource_RENT:
                return $this->entityManager->getRepository('Oktolab\Bundle\RentBundle\Entity\Contact')->findByName($name);
                break;
            default:
                return $this->getContactsFromHub($name);
                break;
        }
    }

    /**
     * Add given contacts to the RENT Database.
     * If you dont want to write the changes right now, set flush to FALSE.
     * @param type $contacts
     * @param type $flush
     */
    public function addContactsToRent($contacts, $flush=true)
    {
        foreach ($contacts as $contact) {
           $this->entityManager->persist($contact);
        }
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    private function getContactsFromHub($name)
    {
        $response = $this->get('?name='.$name.'&type=person')->send();
            $serializedString = str_replace(
                'O:11:"ContactCard"',
                sprintf(
                    'O:%d:"%s\ContactCard"',
                    strlen(__NAMESPACE__)+12,
                    __NAMESPACE__
                ),
                $response->getBody(true)
            );

        $contactcards = unserialize($serializedString);

        $contacts = array();
        foreach ($contactcards as $contactcard) {
            $contact = new Contact();
            $contact->setName($contactcard->getDisplayName());
            $contact->setGuid($contactcard->getGuid());
            $contacts[] = $contact;
        }

        return $contacts;
    }
}
