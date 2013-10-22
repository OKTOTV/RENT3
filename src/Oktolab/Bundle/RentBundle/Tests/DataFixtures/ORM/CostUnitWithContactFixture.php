<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Oktolab\Bundle\RentBundle\Entity\Contact;

/**
 * Description of CostUnitWithContactFixture
 *
 * @author rs
 */
class CostUnitWithContactFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $contact = new Contact();
        $contact->setName('Dummy Contact');
        $contact->setGuid('7654321DUMMY');
        $contact->setFeePayed(true);

        $costunit = new CostUnit();
        $costunit->setName('Dummy Costunit');
        $costunit->setGuid('1234567DUMMY');
        $costunit->addContact($contact);
        $contact->setCostunit($costunit);

        $manager->persist($contact);
        $manager->persist($costunit);
        $manager->flush();
    }
}
