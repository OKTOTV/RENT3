<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Model\Event\EventManager;
use Oktolab\Bundle\RentBundle\Entity\Event;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

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
