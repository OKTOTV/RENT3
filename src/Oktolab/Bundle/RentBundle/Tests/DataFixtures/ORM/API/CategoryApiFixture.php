<?php

namespace Oktolab\Bundle\RentBundle\Tests\DataFixtures\ORM\API;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Place;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Category;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Doctrine\Common\Persistence\ObjectManager;
use Oktolab\Bundle\RentBundle\Entity\CostUnit;
use Oktolab\Bundle\RentBundle\Entity\Contact;
use Oktolab\Bundle\RentBundle\Entity\EventType;


/**
 * Description of CategoryApiFixture
 *
 * @author rt
 */
class CategoryApiFixture extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $place = new Place();
        $place->setTitle('Test Title');
        $manager->persist($place);

        $category = new Category();
        $category->setTitle('Test Category');

        $item1 = new Item();
        $item1->setPlace($place);
        $item1->setCategory($category);
        $item1->setBarcode('asdf');
        $item1->setTitle('item1');
        $item1->setDescription('Descriptive description');
        $manager->persist($item1);

        $item2 = new Item();
        $item2->setPlace($place);
        $item2->setCategory($category);
        $item2->setBarcode('asdg');
        $item2->setTitle('item2');
        $item2->setDescription('Descriptive description');

        $item3 = new Item();
        $item3->setPlace($place);
        $item3->setBarcode('asdh');
        $item3->setTitle('item3');
        $item3->setDescription('Descriptive description');

        // Create event.
        $contact = new Contact();
        $contact->setName('Testcontact');
        $contact->setGuid('12345678');

        $costunit = new CostUnit();
        $costunit->setName('Testcostunit');
        $costunit->setGuid('12345678DUMMY');

        $eventType = new EventType();
        $eventType->setName('inventory');

        //replaces fixture for test
        $eventType2 = new EventType();
        $eventType2->setName('room');

        $manager->persist($category);
        $manager->persist($item2);
        $manager->persist($item3);
        $manager->flush();

        $eventObject1 = new EventObject();
        $eventObject1->setType('item')
            ->setObject($item2->getId());

        $eventObject2 = new EventObject();
        $eventObject2->setType('item')
            ->setObject($item3->getId());

        $event = new Event();
        $event->setName('Test Event')
            ->setState(Event::STATE_PREPARED)
            ->setDescription('There is a description for this event.')
            ->setBegin(new \DateTime('2013-10-14 11:00:00'))
            ->setEnd(new \DateTime('2013-10-15 17:00:00'))
            ->setContact($contact)
            ->setCostunit($costunit)
            ->setType($eventType)
            ->addObject($eventObject1)
            ->addObject($eventObject2);

        $eventObject1->setEvent($event);
        $eventObject2->setEvent($event);

        $manager->persist($contact);
        $manager->persist($costunit);
        $manager->persist($eventType);
        $manager->persist($eventType2);
        $manager->persist($item2);
        $manager->persist($item3);
        $manager->persist($event);
        $manager->persist($eventObject1);
        $manager->persist($eventObject2);
        $manager->flush();
    }
}

?>
