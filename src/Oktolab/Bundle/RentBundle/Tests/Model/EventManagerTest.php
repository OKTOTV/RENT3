<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Model\Event\EventManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Set;

/**
 * @group Event
 */
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

    public function testIsRentableObjectAvailable()
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

    public function testIsEventObjectAvailable()
    {
        $eventObject = new EventObject();
        $eventObject->setType('item')->setObject(5);

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->setMethods(array('findAllForObjectCount'))
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())
            ->method('findAllForObjectCount')
            ->with($this->equalTo($eventObject), $this->anything(), $this->anything())
            ->will($this->returnValue(0));

        $eventManager = new EventManager();
        $eventManager->addRepository('Event', $repository);
        $this->assertTrue($eventManager->isAvailable($eventObject, new \DateTime(), new \DateTime()));
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
        $eventManager = new EventManager();
        $event = $eventManager->create(array(new Set(), new Item()));
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
        $eventManager = new EventManager();
        $event = $eventManager->create(array(new EventObject(), new EventObject()));
        $this->assertCount(2, $event->getObjects());
    }

    public function testCreateEventAssignsCorrectEvent()
    {
        $eventManager = new EventManager();
        $event = $eventManager->create(array(new EventObject(), new EventObject()));

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

        $eventManager = new EventManager();
        $eventManager->create(array(new \stdClass()));
    }

    public function testRentEventReturnsInstanceOfEvent()
    {
        $eventManager = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\EventManager', array('isAvailable'));
        $eventManager->expects($this->once())->method('isAvailable')->will($this->returnValue(true));
        $event = $eventManager->create(array(new Item()));

        $event->setBegin(new \DateTime())->setEnd(new \DateTime());
        $rentedEvent = $eventManager->rent($event);
        $this->assertInstanceOf('\Oktolab\Bundle\RentBundle\Entity\Event', $rentedEvent);
    }

    public function testRentEventThrowsExceptionWithZeroObjects()
    {
        $this->setExpectedException('\Oktolab\Bundle\RentBundle\Model\Event\Exception\MissingEventObjectsException');

        $eventManager = new EventManager();
        $event = $eventManager->create(array());
        $eventManager->rent($event);
    }

    public function testRentEventThrowsExceptionWithEmptyBegin()
    {
        $this->setExpectedException('\LogicException');

        $eventManager = new EventManager();
        $event = $eventManager->create(array(new Item()));

        $event->setEnd(new \DateTime());
        $eventManager->rent($event);
    }

    public function testRentEventThrowsExceptionWithEmptyEnd()
    {
        $this->setExpectedException('\LogicException');

        $eventManager = new EventManager();
        $event = $eventManager->create(array(new Item()));

        $event->setBegin(new \DateTime());
        $eventManager->rent($event);
    }

    public function testRentEventThrowsExceptionWhenBeginIsGreatherThendEnd()
    {
        $this->setExpectedException('\LogicException');

        $eventManager = new EventManager();
        $event = $eventManager->create(array(new Item()));

        $event->setBegin(new \DateTime('2013-10-11 13:00:00'))->setEnd(new \DateTime('2013-10-11 12:00:00'));
        $eventManager->rent($event);
    }

    public function testRentEventSetsStateToLent()
    {
        $eventManager = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\EventManager', array('isAvailable'));
        $eventManager->expects($this->once())->method('isAvailable')->will($this->returnValue(true));

        $event = $eventManager->create(array(new Item()));
        $event->setBegin(new \DateTime())->setEnd(new \DateTime());

        $rentedEvent = $eventManager->rent($event);
        $this->assertSame(Event::STATE_LENT, $rentedEvent->getState());
    }

    public function testRentEventChecksAvailabilityOfObjects()
    {
        $eventManager = $this->getMock('\Oktolab\Bundle\RentBundle\Model\Event\EventManager', array('isAvailable'));
        $eventManager->expects($this->once())->method('isAvailable')->will($this->returnValue(true));

        $event = $eventManager->create(array(new Item()));
        $event->setBegin(new \DateTime())->setEnd(new \DateTime());
        $rentedEvent = $eventManager->rent($event);

        $this->assertCount(1, $rentedEvent->getObjects());
    }

    public function testRentEventThrowsExceptionIfObjectIsNotAvailable()
    {
        $this->setExpectedException('\Oktolab\Bundle\RentBundle\Model\Event\Exception\ObjectNotAvailableException');

        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->setMethods(array('findAllForObjectCount'))
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->at(0))->method('findAllForObjectCount')->will($this->returnValue(0));
        $repository->expects($this->at(1))->method('findAllForObjectCount')->will($this->returnValue(2));

        $eventManager = new EventManager();
        $eventManager->addRepository('Event', $repository);

        $event = $eventManager->create(array(new Item(), new Item()));
        $event->setBegin(new \DateTime())->setEnd(new \DateTime());

        $eventManager->rent($event);
    }

    public function testRentEventChecksCostUnit()
    {
        $this->markTestIncomplete('CostUnits currently not build-in.');
    }

    public function testRentEventCheckStateOfObject()
    {
        $this->markTestIncomplete('Ready-State currently not build-in.');
    }

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
}
