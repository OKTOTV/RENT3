<?php

namespace Oktolab\Bundle\RentBundle\Tests\Model;

use Oktolab\Bundle\RentBundle\Model\EventManager;

class EventManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Oktolab\Bundle\RentBundle\Model\EventManager
     */
    protected $em = null;

    public function setUp()
    {
        parent::setUp();

        $objectManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em = new EventManager($objectManager);
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

}
