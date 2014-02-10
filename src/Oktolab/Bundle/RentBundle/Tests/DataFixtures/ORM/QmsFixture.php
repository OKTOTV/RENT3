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

/**
 * Qms Fixture for EventControllerTest.
 *
 * @author rt
 */
class QmsFixture extends AbstractFixture
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

        $item2 = new Item();
        $item2->setTitle('JVC Camera 2')
            ->setDescription('A JVC Camera.')
            ->setBarcode('F00B52')
            ->setPlace($place);

        $item3 = new Item();
        $item3->setTitle('JVC Camera 3')
            ->setDescription('A JVC Camera.')
            ->setBarcode('F00B53')
            ->setPlace($place);

        $item4 = new Item();
        $item4->setTitle('JVC Camera 4')
            ->setDescription('A JVC Camera.')
            ->setBarcode('F00B54')
            ->setPlace($place);

        $item5 = new Item();
        $item5->setTitle('JVC Camera 5')
            ->setDescription('A JVC Camera.')
            ->setBarcode('F00B55')
            ->setPlace($place);

        $manager->persist($place);
        $manager->persist($item1);
        $manager->persist($item2);
        $manager->persist($item3);
        $manager->persist($item4);
        $manager->persist($item5);

        $eventType = new EventType();
        $eventType->setName('inventory');
        $manager->persist($eventType);
        $manager->flush();

        $eventObject1 = new EventObject();
        $eventObject1->setType('item')
            ->setObject($item1->getId());

        $eventObject2 = new EventObject();
        $eventObject2->setType('item')
            ->setObject($item2->getId());

        $eventObject3 = new EventObject();
        $eventObject3->setType('item')
            ->setObject($item3->getId());

        $eventObject4 = new EventObject();
        $eventObject4->setType('item')
            ->setObject($item4->getId());

        $eventObject5 = new EventObject();
        $eventObject5->setType('item')
            ->setObject($item5->getId());

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
            ->addObject($eventObject2)
            ->addObject($eventObject3)
            ->addObject($eventObject4)
            ->addObject($eventObject5);

        $eventObject1->setEvent($event);
        $eventObject2->setEvent($event);
        $eventObject3->setEvent($event);
        $eventObject4->setEvent($event);
        $eventObject5->setEvent($event);

        $manager->persist($event);
        $manager->persist($contact);
        $manager->persist($costunit);
        $manager->persist($eventObject1);
        $manager->persist($eventObject2);
        $manager->persist($eventObject3);
        $manager->persist($eventObject4);
        $manager->persist($eventObject5);
        $manager->flush();
    }
}

