<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Model\Event\EventManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;

class EventManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetNamedRepository()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();

        $eventManager = new EventManager();
        $eventManager->addRepository('Item', $repository);
        $this->assertEquals($repository, $eventManager->getRepository('Item'));
    }

    public function testGetEventRepository()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();

        $eventManager = new EventManager();
        $eventManager->addRepository('Event', $repository);
        $this->assertEquals($repository, $eventManager->getEventRepository());
    }

    public function testGetInvalidRepositoryThrowsException()
    {
        $this->setExpectedException('\Oktolab\Bundle\RentBundle\Model\Event\Exception\RepositoryNotFoundException');

        $eventManager = new EventManager();
        $eventManager->getRepository('invalid');
    }

    public function testIsObjectAvailable()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->setMethods(array('findAllForObjectCount'))
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->any())->method('findAllForObjectCount')->will($this->onConsecutiveCalls(0, 1, 3));
        $object = $this->getMock('\Oktolab\Bundle\RentBundle\Model\RentableInterface');

        $eventManager = new EventManager();
        $eventManager->addRepository('Event', $repository);
        $this->assertTrue($eventManager->isAvailable($object, new \DateTime(), new \DateTime()));
        $this->assertFalse($eventManager->isAvailable($object, new \DateTime(), new \DateTime()));
        $this->assertFalse($eventManager->isAvailable($object, new \DateTime(), new \DateTime()));
    }

    public function testCreateEventReturnsTypeOfEvent()
    {
        $eventManager = new EventManager();
        $event = $eventManager->create();

        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\Event', $event);
        $this->assertCount(0, $event->getObjects());
    }

    public function testCreateEventReturnsPreparedEvent()
    {
        $eventManager = new EventManager();
        $event = $eventManager->create();
        $this->assertSame(Event::STATE_PREPARED, $event->getState());
    }

    public function testCreateEventWithRentableObjects()
    {
        $set = new Set();
        $item = new Item();

        $eventManager = new EventManager();
        $event = $eventManager->create(array($set, $item));
        $this->assertCount(2, $event->getObjects());
    }

    public function testCreateEventWithRentableObjectsWithCorrectAppendedValues()
    {
        $item = new Item();
        $item->setId(5);

        $eventManager = new EventManager();
        $event = $eventManager->create(array($item));

        $objects = $event->getObjects();
        $this->assertCount(1, $objects);
        $this->assertSame('item', $objects[0]->getType());
        $this->assertSame(5, $objects[0]->getObject());
    }

    public function testCreateEventWithEventObjects()
    {
        $object1 = new EventObject();
        $object2 = new EventObject();

        $eventManager = new EventManager();
        $event = $eventManager->create(array($object1, $object2));
        $this->assertCount(2, $event->getObjects());
    }

    public function testCreateEventAssignsCorrectEvent()
    {
        $object1 = new EventObject();
        $object2 = new EventObject();

        $eventManager = new EventManager();
        $event = $eventManager->create(array($object1, $object2));

        foreach ($event->getObjects() as $object) {
            $this->assertSame($event, $object->getEvent(), 'EventObject has correct Event mapped.');
        }
    }

    public function testCreateEventCanMixEventObjectsAndRentableObjects()
    {
        $object1 = new EventObject();
        $object1->setType('set')->setObject(5);
        $object2 = new Item();
        $object2->setId(3);

        $eventManager = new EventManager();
        $event = $eventManager->create(array($object1, $object2));

        $objects = $event->getObjects();
        $this->assertCount(2, $objects);
        $this->assertSame('set', $objects[0]->getType());
        $this->assertSame(5, $objects[0]->getObject());
        $this->assertSame('item', $objects[1]->getType());
        $this->assertSame(3, $objects[1]->getObject());
    }

    public function testCreateEventThrowsExceptionOnInvalidObject()
    {
        $this->setExpectedException('\BadMethodCallException');
        $object = new \stdClass();

        $eventManager = new EventManager();
        $event = $eventManager->create(array($object));
    }

    public function testRentEventReturnsTypeOfEvent()
    {
        $item = new Item();
        $eventManager = new EventManager();
        $event = $eventManager->create(array($item));
        $event = $eventManager->rent($event);

        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\Event', $event);
    }

    public function testRentEventThrowsExceptionWithZeroObjects()
    {
        $this->setExpectedException('\Oktolab\Bundle\RentBundle\Model\Event\Exception\MissingEventObjectsException');
        $eventManager = new EventManager();
        $event = $eventManager->create(array());
        $event = $eventManager->rent($event);
    }

    public function testRentEventSetsStateToLent()
    {
        // event objects (two items)
        // event is "lent"
        $this->markTestIncomplete();
    }

    /*
     * Tests:
     *  - Rent isAvailable checks
     *  - Rent checks dates (begin / end)
     *  - Rent checks cost_unit
     *  - Rent checks states of items/sets/rooms
     */

    public function testDeliverEvent()
    {
        // event is "delivered"
        $this->markTestIncomplete();
    }

    public function testCheckEvent()
    {
        // event is "checked"
        $this->markTestIncomplete();
    }

    public function testCompleteEvent()
    {
        // event is "completed"
        $this->markTestIncomplete();
    }

    public function testDeferEvent()
    {
        // event is "deferred"
        $this->markTestIncomplete();
    }

    public function testCancelEvent()
    {
        // event objects (two items)
        // event is "canceled"
        $this->markTestIncomplete();
    }

    public function testRentedEventThrowsExceptionWhenCanceling()
    {
        $this->markTestIncomplete();
    }

    public function testTransformEventObjects()
    {
        // 1 set, 2 items
        $this->markTestIncomplete();
    }


    public function testItemIsAvailableAfterCancelingEvent()
    {
        $this->markTestIncomplete('Functional Test');
    }

//
//    /**
//     * @expectedException \BadMethodCallException
//     */
//    public function testRentThrowsExceptionOnInvalidObjects()
//    {
//        $objects = array(
//            'invalid object',
//            $this->getMock('\Oktolab\Bundle\RentBundle\Model\RentableInterface'),
//        );
//
//        $this->em->rent($objects, new \DateTime(), new \DateTime());
//    }
//
//    public function testTransformEventObject()
//    {
//        $item = new Item();
//        $event = new Event();
//        $eventObject = new EventObject();
//        $eventObject->setEvent($event)->setObject($item->getId())->setType($item->getType());
//        $event->addObject($eventObject);
//
//        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
//            ->disableOriginalConstructor()
//            ->getMock();
//        $repository->expects($this->once())->method('getClassName')->will($this->returnValue('Item'));
//        $repository->expects($this->once())->method('findOneBy')->will($this->returnValue(array($item)));
//
//        $this->em->addRepository($repository);
//        $this->assertEquals(array($item), $this->em->getObjects($event));
//    }
//
//    public function testIsObjectAvailable()
//    {
//        $eventRepository = $this->getMockBuilder('Oktolab\Bundle\RentBundle\Entity\EventRepository')
//            ->disableOriginalConstructor()
//            ->getMock();
//        $eventRepository->expects($this->at(0))
//            ->method('findAllForObjectCount')
//            ->will($this->returnValue(0));
//        $eventRepository->expects($this->at(1))
//            ->method('findAllForObjectCount')
//            ->will($this->returnValue(1));
//
//        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
//            ->disableOriginalConstructor()
//            ->getMock();
//        $entityManager->expects($this->once())
//            ->method('getRepository')
//            ->will($this->returnValue($eventRepository));
//
//        $em = new EventManager($entityManager);
//        $this->assertTrue($em->isAvailable(new Item(), new \DateTime(), new \DateTime()));
//        $this->assertFalse($em->isAvailable(new Item(), new \DateTime(), new \DateTime()));
//    }
//
//    public function testRentObject()
//    {
//        $this->markTestIncomplete('Rent an Object');
//    }
}
