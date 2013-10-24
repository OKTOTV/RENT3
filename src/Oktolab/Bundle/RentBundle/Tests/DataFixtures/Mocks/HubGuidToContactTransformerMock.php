<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\Mocks;

use Oktolab\Bundle\RentBundle\Form\DataTransformer\HubGuidToContactTransformer;
use Oktolab\Bundle\RentBundle\Entity\Contact;
/**
 * Description of HubGuidToContactTransformerMock
 *
 * @author rs
 */
class HubGuidToContactTransformerMock extends HubGuidToContactTransformer
{
    public function reverseTransform($guid)
    {
        $contact = new Contact();
        $contact->setGuid('12345');
        $contact->setName('contact name');
        $contact->setFeePayed(true);
        return array($contact);
    }
}
