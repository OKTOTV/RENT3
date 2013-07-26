<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Model\EventManager;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

class EventManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\EventManager
     */
    protected $em = null;

    public function setUp()
    {
        parent::setUp();

        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em = new EventManager($entityManager);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testRentThrowsExceptionOnInvalidObjects()
    {
        $objects = array(
            'invalid object',
            $this->getMock('\Oktolab\Bundle\RentBundle\Model\RentableInterface'),
        );

        $this->em->rent($objects, new \DateTime(), new \DateTime());
    }

    public function testRegisterARepository()
    {
        $repository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $repository->expects($this->once())->method('getClassName')->will($this->returnValue('TestClass'));

        $this->em->addRepository($repository);
        $this->assertSame($repository, $this->em->getRepository('TestClass'));
    }

    public function testIsObjectAvailable()
    {
        $eventRepository = $this->getMockBuilder('Oktolab\Bundle\RentBundle\Entity\EventRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $eventRepository->expects($this->once())
            ->method('findAllFromBeginToEnd')
            ->will($this->returnValue('0'));

        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($eventRepository));

        $em = new EventManager($entityManager);
        $this->assertTrue($em->isAvailable(new Item(), new \DateTime(), new \DateTime));
    }

    public function testIsObjectNotAvailable()
    {
        $eventRepository = $this->getMockBuilder('Oktolab\Bundle\RentBundle\Entity\EventRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $eventRepository->expects($this->once())
            ->method('findAllFromBeginToEnd')
            ->will($this->returnValue('1'));

        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($eventRepository));

        $em = new EventManager($entityManager);
        $this->assertFalse($em->isAvailable(new Item(), new \DateTime(), new \DateTime));
    }

}
