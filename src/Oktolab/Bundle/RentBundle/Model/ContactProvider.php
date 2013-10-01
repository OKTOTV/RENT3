<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Entity\Contact;
use Guzzle\Http\Client;
use Doctrine\ORM\EntityManager;


class ContactProvider extends Client
{
    private $entityManager;

    public function __construct(EntityManager $manager, $baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);
        $this->entityManager = $manager;
    }

    public function getContactsByName($name, $hubOnly=false)
    {
        if ($hubOnly) {
            return $this->getContactsFromHub($name);
        } else {
            $contacts = $this->entityManager->getRepository('Oktolab\Bundle\RentBundle\Entity\Contact')->findByName($name);
            if (!$contacts) {

                $contacts = $this->getContactCardsFromHub($name);
                $this->addContactsToRent($contacts);

                return $contacts;
            }
        }

    }

    public function addContactsToRent($contacts)
    {
        foreach ($contacts as $contact) {
           $this->entityManager->persist($contact);
        }
        $this->entityManager->flush();
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
            //TODO: Make Contact Entities.
            $contact = new Contact();
            $contact->setName($contactcard[]);
            $contact->setSurname($contactcard[]);
            $contact->setFeePayed(false);
            $contacts[] = $contact;
        }

        return $contacts;
    }
}
