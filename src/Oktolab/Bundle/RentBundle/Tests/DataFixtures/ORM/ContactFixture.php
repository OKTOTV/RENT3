<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Contact;
/**
 * Description of ContactFixture
 *
 * @author rs
 */
class ContactFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $contact = new Contact();
        $contact->setName('John Appleseed');
        $contact->setGuid('12345678');
        $contact->setFeePayed(false);

        $manager->persist($contact);
        $manager->flush();
    }
}
