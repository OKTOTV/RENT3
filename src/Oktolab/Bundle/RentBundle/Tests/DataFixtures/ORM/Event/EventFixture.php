<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Event;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Oktolab\Bundle\RentBundle\Entity\Contact;

/**
 *  Loads a fixture Event.
 */
class EventFixture extends AbstractFixture
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $contact = new Contact();
        $contact->setName('Testcontact');
        $contact->setGuid('12345678');
        $contact->setFeePayed(false);

        $costunit = new CostUnit();
        $costunit->setName('Testcostunit');
        $costunit->setGuid('1234567DUMMY');


        $eventObject = new EventObject();
        $eventObject->setType('item')
            ->setObject(1);

        $event = new Event();
        $event->setName('My Event.')
            ->setState(Event::STATE_PREPARED)
            ->setDescription('There is a description for this event.')
            ->setBegin(new \DateTime('2013-10-14 11:00:00'))
            ->setEnd(new \DateTime('2013-10-15 17:00:00'))
            ->setContact($contact)
            ->setCostunit($costunit)
            ->addObject($eventObject);

        $eventObject->setEvent($event);
        $manager->persist($contact);
        $manager->persist($costunit);
        $manager->persist($eventObject);
        $manager->persist($event);
        $manager->flush();
    }
}