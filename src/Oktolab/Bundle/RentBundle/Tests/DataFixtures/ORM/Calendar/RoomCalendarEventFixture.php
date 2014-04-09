<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\Calendar;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Room;
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
class RoomCalendarEventFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $contact = new Contact();
        $contact->setName('Testcontact');
        $contact->setGuid('87654321');

        $costunit = new CostUnit();
        $costunit->setName('Testcostunit');
        $costunit->setGuid('DUMMY12345678');

        $place = new Place();
        $place->setTitle('Okto Verleih');

        $item1 = new Room();
        $item1->setTitle('Radio Studio')
            ->setDescription('New Studio with cool equipment')
            ->setBarcode('RADIOSTUD01');

        $manager->persist($place);
        $manager->persist($item1);

        $eventType = new EventType();
        $eventType->setName('room');
        $manager->persist($eventType);
        $manager->flush();

        $eventObject1 = new EventObject();
        $eventObject1->setType('room')
            ->setObject($item1->getId());

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
            ->addObject($eventObject1);

        $eventObject1->setEvent($event);

        $manager->persist($event);
        $manager->persist($contact);
        $manager->persist($costunit);
        $manager->persist($eventObject1);
        $manager->flush();
    }
}

