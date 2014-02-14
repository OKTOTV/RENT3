<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Oktolab\Bundle\RentBundle\Entity\Contact;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventType;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Qms;

/**
 * Qms Fixture for EventControllerTest.
 *
 * @author rt
 */
class EventShowFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $contact = new Contact();
        $contact->setName('Testcontact');
        $contact->setGuid('12345678');

        $costunit = new CostUnit();
        $costunit->setName('Testcostunit');
        $costunit->setGuid('12345678DUMMY');

        $place = new Place();
        $place->setTitle('Okto Verleih');

        $item1 = new Item();
        $item1->setTitle('JVC Camera 1')
            ->setDescription('A JVC Camera.')
            ->setBarcode('F00B51')
            ->setPlace($place);

        $manager->persist($place);
        $manager->persist($item1);

        $eventType = new EventType();
        $eventType->setName('inventory');
        $manager->persist($eventType);
        $manager->flush();

        $eventObject1 = new EventObject();
        $eventObject1->setType('item')
            ->setObject($item1->getId());

        $qms = new Qms;
        $qms->setStatus(Qms::STATE_OKAY)
            ->setItem($item1)
            ->setCreatedAt(new \DateTime('1991-10-27 11:00:00'))
            ->setUpdatedAt(new \DateTime('1991-10-28 11:00:00'))
            ->setDescription('random description');
        $manager->persist($qms);
        $manager->flush();

        $event = new Event();
        $event->setName('My Event')
            ->setState(Event::STATE_PREPARED)
            ->setDescription('There is a description for this event.')
            ->setBegin(new \DateTime('2013-10-14 11:00:00'))
            ->setEnd(new \DateTime('2013-10-15 17:00:00'))
            ->setContact($contact)
            ->setCostunit($costunit)
            ->setType($eventType)
            ->addObject($eventObject1)
            ->addQms($qms);

        $qms->setEvent($event);
        $manager->persist($qms);

        $eventObject1->setEvent($event);

        $manager->persist($event);
        $manager->persist($contact);
        $manager->persist($costunit);
        $manager->persist($eventObject1);
        $manager->flush();
    }
}

